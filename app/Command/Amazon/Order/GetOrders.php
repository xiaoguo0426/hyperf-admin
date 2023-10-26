<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Order;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonOrderModel;
use App\Queue\AmazonOrderItemQueue;
use App\Queue\Data\AmazonOrderItemData;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonOrdersLog;
use Carbon\Carbon;
use DateInterval;
use DateTimeZone;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class GetOrders extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:order:get-orders');
    }


    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->addOption('order_ids', null, InputOption::VALUE_OPTIONAL, 'order_ids集合', null)
            ->setDescription('Amazon Order API Get Orders');
    }

    /**
     * @throws ApiException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @return void
     */
    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');
        $amazon_order_ids = $this->input->getOption('order_ids');

        $that = $this;

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($that, $amazon_order_ids) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonOrdersLog::class);

            $last_create_date = AmazonOrderModel::query()
                ->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->orderBy('purchase_date', 'DESC')
                ->value('purchase_date');
            if (is_null($last_create_date)) {
                $created_after = (new \DateTime('-1 year', new DateTimeZone('UTC')))->format('Y-01-01\T00:00:00\Z');
            } else {
                $created_after = (new \DateTime($last_create_date, new DateTimeZone('UTC')))->sub(new DateInterval('P5D'))->format('Y-m-d\T00:00:00\Z');
            }
            //查询指定的订单id
            if (! is_null($amazon_order_ids)) {
                $amazon_order_ids = explode(',', $amazon_order_ids);
            }

            $console->info(sprintf('merchant_id:%s merchant_store_id:%s created_after:%s', $merchant_id, $merchant_store_id, $created_after));

            $orderItemQueue = ApplicationContext::getContainer()->get(AmazonOrderItemQueue::class);

            $nextToken = null;
            $retry = 10;
            $max_results_per_page = 100;

            $page = 1;//分页数

            $cur_date = Carbon::now()->format('Y-m-d H:i:s');

            while (true) {

                try {

                    $response = $sdk->orders()->getOrders($accessToken, $region, $marketplace_ids, $created_after, null, null, null, null, null, null, null, null, $max_results_per_page, null, null, $nextToken, $amazon_order_ids);

                    $payload = $response->getPayload();
                    if ($payload === null) {
                        break;
                    }

                    $orders = $payload->getOrders();

                } catch (ApiException $e) {
                    $retry--;
                    if ($retry <= 0) {
                        $console->error(sprintf('merchant_id:%s merchant_store_id:%s Page:%s 重试次数已用完', $merchant_id, $merchant_store_id, $page));
                        break;
                    }
                    $console->warning(sprintf('merchant_id:%s merchant_store_id:%s Page:%s 第 %s 次重试', $merchant_id, $merchant_store_id, $page, $retry));
                    sleep(3);
                    continue;
                } catch (InvalidArgumentException $e) {
                    continue;
                }

                $data = [];//插入数据
                $order_ids = [];//亚马逊订单id集合

                foreach ($orders as $order) {

                    $orderTotal = $order->getOrderTotal();

                    $purchase_date = $order->getPurchaseDate() ?? '';
                    if ($purchase_date) {
                        $purchase_date = Carbon::createFromFormat('Y-m-d\TH:i:sZ', $purchase_date)->format('Y-m-d H:i:s');
                    }

                    $last_update_date = $order->getLastUpdateDate() ?? '';
                    if ($last_update_date) {
                        $last_update_date = Carbon::createFromFormat('Y-m-d\TH:i:sZ', $last_update_date)->format('Y-m-d H:i:s');
                    }

                    $paymentExecutionDetail = $order->getPaymentExecutionDetail();
                    $paymentExecutionDetailJson = [];
                    if ($paymentExecutionDetail) {
                        foreach ($paymentExecutionDetail as $paymentExecutionDetailItem) {
                            $paymentExecutionDetailJson[] = [
                                $paymentExecutionDetailItem->getPayment(),//订单的货币价值
                                $paymentExecutionDetailItem->getPaymentMethod()//COD订单的子付款方式。 COD - Cash On Delivery货到付款,GC - Gift Card礼品卡.,PointsAccount - Amazon Points亚马逊积分.
                            ];
                        }
                    }

                    $paymentMethodDetails = $order->getPaymentMethodDetails();
                    $paymentMethodDetailsJson = [];
                    if ($paymentMethodDetails) {
                        foreach ($paymentMethodDetails as $paymentMethodDetail) {
                            $paymentMethodDetailsJson[] = $paymentMethodDetail;
                        }
                    }

                    $defaultShipFromLocationAddress = $order->getDefaultShipFromLocationAddress();
                    $defaultShipFromLocationAddressJson = [];
                    if ($defaultShipFromLocationAddress) {
                        $defaultShipFromLocationAddressJson = [
                            'name' => '',
                            'address_line1' => $defaultShipFromLocationAddress->getAddressLine1() ?? '',
                            'address_line2' => $defaultShipFromLocationAddress->getAddressLine2() ?? '',
                            'address_line3' => $defaultShipFromLocationAddress->getAddressLine3() ?? '',
                            'city' => $defaultShipFromLocationAddress->getCity() ?? '',
                            'county' => $defaultShipFromLocationAddress->getCounty() ?? '',
                            'district' => $defaultShipFromLocationAddress->getDistrict() ?? '',
                            'state_or_region' => $defaultShipFromLocationAddress->getStateOrRegion() ?? '',
                            'municipality' => $defaultShipFromLocationAddress->getMunicipality() ?? '',
                            'postal_code' => $defaultShipFromLocationAddress->getPostalCode() ?? '',
                            'country_code' => $defaultShipFromLocationAddress->getCountryCode() ?? '',
                            'phone' => $defaultShipFromLocationAddress->getPhone() ?? '',
                            'address_type' => $defaultShipFromLocationAddress->getAddressType() ?? '',
                        ];
                    }

                    $buyerTaxInformation = $order->getBuyerTaxInformation();
                    $buyerTaxInformationJson = [];
                    if ($buyerTaxInformation) {
                        $buyerTaxInformationJson = [
                            $buyerTaxInformation->getBuyerLegalCompanyName() ?? '',
                            $buyerTaxInformation->getBuyerBusinessAddress() ?? '',
                            $buyerTaxInformation->getBuyerTaxRegistrationId() ?? '',
                            $buyerTaxInformation->getBuyerTaxOffice() ?? '',
                        ];
                    }

                    $fulfillmentInstruction = $order->getFulfillmentInstruction();
                    $fulfillmentInstructionJson = [];
                    if ($fulfillmentInstruction) {
                        $fulfillmentInstructionJson = [
                            $fulfillmentInstruction->getFulfillmentSupplySourceId() //Denotes the recommended sourceId where the order should be fulfilled from
                        ];
                    }

                    $shippingAddress = $order->getShippingAddress();
                    $shippingAddressJson = [];
                    if ($shippingAddress) {
                        $shippingAddressJson = [
//                                'name' => $shippingAddress->getName() ?? '',
                            'name' => '',
                            'address_line1' => $shippingAddress->getAddressLine1() ?? '',
                            'address_line2' => $shippingAddress->getAddressLine2() ?? '',
                            'address_line3' => $shippingAddress->getAddressLine3() ?? '',
                            'city' => $shippingAddress->getCity() ?? '',
                            'county' => $shippingAddress->getCounty() ?? '',
                            'district' => $shippingAddress->getDistrict() ?? '',
                            'state_or_region' => $shippingAddress->getStateOrRegion() ?? '',
                            'municipality' => $shippingAddress->getMunicipality() ?? '',
                            'postal_code' => $shippingAddress->getPostalCode() ?? '',
                            'country_code' => $shippingAddress->getCountryCode() ?? '',
                            'phone' => $shippingAddress->getPhone() ?? '',
                            'address_type' => $shippingAddress->getAddressType() ?? '',
                        ];
                    }

                    $buyerInfo = $order->getBuyerInfo();
                    $buyerInfoJson = [];
                    if ($buyerInfo) {
                        $buyerInfoTaxInfo = $buyerInfo->getBuyerTaxInfo();
                        $buyerInfoTaxInfoJson = [];
                        if ($buyerInfoTaxInfo) {
                            $taxClassifications = $buyerInfoTaxInfo->getTaxClassifications();
                            $taxClassificationsJson = [];
                            if ($taxClassifications) {
                                foreach ($taxClassifications as $taxClassification) {
                                    $taxClassificationsJson[] = [
                                        'name' => $taxClassification->getName(),
                                        'value' => $taxClassification->getValue()
                                    ];
                                }
                            }
                            $buyerInfoTaxInfoJson = [
                                'companyLegalName' => $buyerInfoTaxInfo->getCompanyLegalName(),
                                'taxingRegion' => $buyerInfoTaxInfo->getTaxingRegion(),
                                'taxClassifications' => $taxClassificationsJson
                            ];
                        }

                        $buyerInfoJson = [
                            'buyer_info_email' => $buyerInfo->getBuyerEmail() ?? '',
                            'buyer_info_county' => $buyerInfo->getBuyerCounty() ?? '',
                            'buyer_info_tax_info' => $buyerInfoTaxInfoJson,
                            'buyer_info_purchase_order_number' => $buyerInfo->getPurchaseOrderNumber() ?? '',
                        ];
                    }

                    $automatedShippingSettings = $order->getAutomatedShippingSettings();
                    $automatedShippingSettingsJson = [];
                    if ($automatedShippingSettings) {
                        $automatedShippingSettingsJson = [
                            'hasAutomatedShippingSettings' => $automatedShippingSettings->getHasAutomatedShippingSettings() ?? '',
                            'automatedCarrier' => $automatedShippingSettings->getAutomatedCarrier() ?? '',
                            'automatedShipMethod' => $automatedShippingSettings->getAutomatedShipMethod() ?? ''
                        ];
                    }

                    $marketplaceTaxInfo = $order->getMarketplaceTaxInfo();
                    $marketplaceTaxInfoJson = [];
                    if ($marketplaceTaxInfo) {
                        $taxClassifications = $marketplaceTaxInfo->getTaxClassifications();
                        $taxClassificationsJson = [];
                        if ($taxClassifications) {
                            foreach ($taxClassifications as $taxClassification) {
                                $taxClassificationsJson[] = [
                                    'name' => $taxClassification->getName(),
                                    'value' => $taxClassification->getValue(),
                                ];
                            }
                        }
                        $marketplaceTaxInfoJson = [
                            'tax_classifications' => $taxClassificationsJson
                        ];
                    }

                    $amazon_order_id = $order->getAmazonOrderId();

                    //订单数据字段解释 https://developer-docs.amazon.com/sp-api/docs/orders-api-v0-reference#orderlist
                    $data[$amazon_order_id] = [
                        'merchant_id' => $merchant_id,
                        'merchant_store_id' => $merchant_store_id,
                        'amazon_order_id' => $amazon_order_id,//亚马逊定义的订单标识符，格式为3-7-7
                        'seller_order_id' => $order->getSellerOrderId() ?? '',//卖家定义的订单标识符
                        'purchase_date' => $purchase_date,//订单创建时间
                        'last_update_date' => $last_update_date,//上次更新订单的日期
                        'order_status' => $order->getOrderStatus() ?? '', //https://developer-docs.amazon.com/sp-api/docs/orders-api-v0-reference#orderstatus
                        'fulfillment_channel' => $order->getFulfillmentChannel() ?? '',//订单是由亚马逊（AFN）还是由卖方（MFN）完成     MFN,AFN
                        'sales_channel' => $order->getSalesChannel() ?? '',//订单中第一项的销售渠道
                        'order_channel' => $order->getOrderChannel() ?? '',//订单中第一项的订单通道
                        'ship_service_level' => $order->getShipServiceLevel() ?? '',//订单的发货服务级别
                        'order_total_currency' => $orderTotal === null ? '' : ($orderTotal->getCurrencyCode() ?? ''),//订单的总费用(货币)
                        'order_total_amount' => $orderTotal === null ? '' : ($orderTotal->getAmount() ?? ''),//订单的总费用
                        'number_of_items_shipped' => $order->getNumberOfItemsShipped(),//装运的项目数
                        'number_of_items_unshipped' => $order->getNumberOfItemsUnshipped(),//未装运的项目数
                        'payment_execution_detail' => json_encode($paymentExecutionDetailJson, JSON_THROW_ON_ERROR),//关于货到付款（COD）订单的子付款方式的信息
                        'payment_method' => $order->getPaymentMethod() ?? '',//订单的付款方式。COD,CVS,Other   此属性仅限于货到付款（COD）和便利店（CVS）付款方式。除非您需要PaymentExecutionDetailItem对象提供的特定COD付款信息，否则建议使用PaymentMethodDetails属性获取付款方式信息。
                        'payment_method_details' => implode('|', $paymentMethodDetailsJson),//订单的付款方式列表
                        'marketplace_id' => $order->getMarketplaceId(),//下订单的市场的标识符
                        'shipment_service_level_category' => $order->getShipmentServiceLevelCategory() ?? '',//订单的装运服务级别类别。
                        'easy_ship_shipment_status' => $order->getEasyShipShipmentStatus() ? $order->getEasyShipShipmentStatus()->toString() : '',//Amazon Easy Ship订单的状态。此属性仅适用于Amazon Easy Ship订单。
                        'cba_displayable_shipping_label' => $order->getCbaDisplayableShippingLabel() ?? '',//亚马逊（CBA）结账的定制发货标签
                        'order_type' => $order->getOrderType() ?? '',//订单类型 StandardOrder包含销售伙伴当前有库存的项目的订单,LongLeadTimeOrder订单包含需要较长交货期的项目订单,Preorder包含发布日期为未来的项目的订单,BackOrder包含已在市场上发布但目前缺货且将在未来可用的商品的订单,SourcingOnDemandOrder按需采购订单。
                        'earliest_ship_date' => $order->getEarliestShipDate() ?? '',//您承诺发货订单的时间段的开始。采用ISO 8601日期时间格式。仅针对卖方完成的订单退回。
                        'latest_ship_date' => $order->getLatestShipDate() ?? '',//您承诺发货订单的时间段结束
                        'earliest_delivery_date' => $order->getEarliestDeliveryDate() ?? '',//您承诺履行订单的时间段的开始。采用ISO 8601日期时间格式。仅针对卖方完成的订单退回。
                        'latest_delivery_date' => $order->getLatestDeliveryDate() ?? '',//您承诺履行订单的期限结束。采用ISO 8601日期时间格式。仅针对卖家完成的订单返回，这些订单没有挂起可用性、挂起或取消状态
                        'is_business_order' => $order->getIsBusinessOrder(),//如果为true，则订单为Amazon Business订单。亚马逊商业订单是指买方是经验证的商业买家的订单
                        'is_prime' => $order->getIsPrime(),//如果为true，则订单是卖家完成的亚马逊Prime订单。
                        'is_premium_order' => $order->getIsPremiumOrder(),//如果为true，则订单具有“高级配送服务级别协议”。有关高级配送订单的更多信息，请参阅您所在市场的卖家中心帮助中的“高级配送选项”
                        'is_global_express_enabled' => $order->getIsGlobalExpressEnabled(),//如果为true，则订单为GlobalExpress订单
                        'replaced_order_id' => $order->getReplacedOrderId() ?? '',//正在替换的订单的订单ID值。仅当IsReplacementOrder=true时返回。
                        'is_replacement_order' => $order->getIsReplacementOrder(),//如果为true，则这是替换订单。
                        'promise_response_due_date' => $order->getPromiseResponseDueDate() ?? '',//表示卖方必须以预计发货日期回复买方的日期。仅针对按需采购订单退回。
                        'is_estimated_ship_date_set' => $order->getIsEstimatedShipDateSet() ?? false,//如果为true，则为订单设置预计发货日期。仅针对按需采购订单退回
                        'is_sold_by_ab' => $order->getIsSoldByAb(),//如果为true，则此订单中的商品由Amazon Business EU SARL（ABEU）购买并转售。通过购买并立即转售您的物品，ABEU成为记录的卖家，使您的库存可供不从第三方卖家购买的客户出售。
                        'is_iba' => $order->getIsIba() ?? false,//如果为true，则此订单中的商品由Amazon Business EU SARL（ABEU）购买并转售。通过购买并立即转售您的物品，ABEU成为记录的卖家，使您的库存可供不从第三方卖家购买的客户出售。
                        'default_ship_from_location_address' => json_encode($defaultShipFromLocationAddressJson, JSON_THROW_ON_ERROR),//卖方装运物品的推荐地点。结账时计算。卖方可以选择或不选择从该地点发货
                        'buyer_invoice_preference' => $order->getBuyerInvoicePreference() ?? '',//买方的发票偏好。仅在TR市场上可用
                        'buyer_tax_information' => json_encode($buyerTaxInformationJson, JSON_THROW_ON_ERROR),//包含业务发票税务信息
                        'fulfillment_instruction' => json_encode($fulfillmentInstructionJson, JSON_THROW_ON_ERROR),//包含有关履行的说明，如从何处履行
                        'is_ispu' => $order->getIsIspu(),//如果为true，则此订单标记为从商店提货，而不是交付
                        'is_access_point_order' => $order->getIsAccessPointOrder(),//如果为true，则将此订单标记为要交付给接入点。访问位置由客户选择。接入点包括亚马逊中心储物柜、亚马逊中心柜台和运营商运营的取货点。
                        'marketplace_tax_info' => json_encode($marketplaceTaxInfoJson, JSON_THROW_ON_ERROR),//有关市场的税务信息
                        'seller_display_name' => $order->getSellerDisplayName() ?? '',//卖家在市场上注册的友好名称。
                        'shipping_address' => json_encode($shippingAddressJson, JSON_THROW_ON_ERROR),//订单的发货地址。
                        'buyer_email' => $buyerInfoJson['buyer_info_email'] ?? '',// 买家邮箱信息
                        'buyer_info' => json_encode($buyerInfoJson, JSON_THROW_ON_ERROR),//买方信息
                        'automated_shipping_settings' => json_encode($automatedShippingSettingsJson, JSON_THROW_ON_ERROR),//包含有关配送设置自动程序的信息，例如订单的配送设置是否自动生成，以及这些设置是什么
                        'has_regulated_items' => $order->getHasRegulatedItems() ?? false,//订单是否包含在履行之前可能需要额外批准步骤的监管项目
                        'electronic_invoice_status' => $order->getElectronicInvoiceStatus() ? $order->getElectronicInvoiceStatus()->toString() : '',//电子发票的状态 NotRequired,NotFound,Processing,Errored,Accepted
                        'created_at' => $cur_date
                    ];

                    $order_ids[] = $amazon_order_id;

                }

                if (empty($order_ids)) {
                    break;
                }
                if (empty($data)) {
                    break;
                }

                //检查数组中的amazon_order_id是否已插入
                $existOrders = AmazonOrderModel::query()
                    ->where('merchant_id', $merchant_id)
                    ->where('merchant_store_id', $merchant_store_id)
                    ->whereIn('amazon_order_id', array_keys($data))->get();

                if ($existOrders->isEmpty()) {
                    AmazonOrderModel::insert($data);
                } else {
                    $real_order_ids = [];
                    $existOrdersCollections = $existOrders->toArray();
                    $exist_orders_collections = array_column($existOrdersCollections, null, 'amazon_order_id');//数据库已存在的订单id集合
                    $exist_order_ids = array_keys($exist_orders_collections);

                    foreach ($data as $amazon_order_id => $item) {

                        if (! in_array($amazon_order_id, $exist_order_ids, true)) {
                            $create = AmazonOrderModel::insert($item);

                            $real_order_ids[] = $item['amazon_order_id'];
                        } else {
                            //判断是否order需要更新
                            foreach ($existOrders as $existOrder) {
                                if ($existOrder->amazon_order_id !== $item['amazon_order_id']) {
                                    continue;
                                }

//                                    if ($existOrder->last_update_date === $data[$existOrder->amazon_order_id]['last_update_date']) {
//                                        continue;
//                                    }

                                $save = $existOrder->save($data[$existOrder->amazon_order_id]);

                                $real_order_ids[] = $existOrder->amazon_order_id;
                            }
                        }
                    }
                    $order_ids = $real_order_ids;
                }

                $chunks = array_chunk($order_ids, 50);
                foreach ($chunks as $chunk) {
                    $amazonOrderData = new AmazonOrderItemData();
                    $amazonOrderData->setMerchantId($merchant_id);
                    $amazonOrderData->setMerchantStoreId($merchant_store_id);
                    $amazonOrderData->setOrderId($chunk);
                    $orderItemQueue->push($amazonOrderData);
                }

                //如果下一页没有数据，nextToken 会变成null
                $nextToken = $payload->getNextToken();
                if (is_null($nextToken)) {
                    break;
                }

                $retry = 30;//重置重试次数
                $page++;

                $that->output->newLine();
            }

            return true;
        });
    }
}
