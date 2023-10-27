<?php

namespace App\Util\Amazon;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonOrderItemModel;
use App\Util\AmazonSDK;
use App\Util\Constants;
use App\Util\Log\AmazonOrdersLog;
use Carbon\Carbon;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class OrderItemEngine implements EngineInterface
{
    /**
     * @param AmazonSDK $amazonSDK
     * @param SellingPartnerSDK $sdk
     * @param AccessToken $accessToken
     * @param CreatorInterface $creator
     * @throws JsonException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return bool
     */
    public function launch(AmazonSDK $amazonSDK, SellingPartnerSDK $sdk, AccessToken $accessToken, CreatorInterface $creator): bool
    {


        $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
        $logger = ApplicationContext::getContainer()->get(AmazonOrdersLog::class);

        $merchant_id = $amazonSDK->getMerchantId();
        $merchant_store_id = $amazonSDK->getMerchantStoreId();
        $region = $amazonSDK->getRegion();

        /**
         * @var OrderItemCreator $creator
         */
        $amazon_order_ids = $creator->getAmazonOrderIds();

        foreach ($amazon_order_ids as $amazon_order_id) {

            $retry = 30;
            $orderItems = [];

            $cur_date = Carbon::now()->format('Y-m-d H:i:s');

            //https://developer-docs.amazon.com/sp-api/docs/orders-api-v0-reference#getorderitems
            while (true) {
                try {
                    $response = $sdk->orders()->getOrderItems(
                        $accessToken,
                        $region,
                        $amazon_order_id
                    );
                    $payload = $response->getPayload();
                    if (is_null($payload)) {
                        //TODO Log
                        break;
                    }
                    $orderItems = $payload->getOrderItems();
                    break;
                } catch (ApiException $e) {

                    if (! is_null($e->getResponseBody())) {
                        $body = json_decode($e->getResponseBody(), true, 512, JSON_THROW_ON_ERROR);
                        if (isset($body['errors'])) {
                            $errors = $body['errors'];
                            foreach ($errors as $error) {
                                if ($error['code'] !== 'QuotaExceeded') {
                                    $console->warning(sprintf('merchant_id:%s merchant_store_id:%s code:%s message:%s', $merchant_id, $merchant_store_id, $error['code'], $error['message']));
                                    break 2;
                                }
                            }
                        }
                    }

                    $retry--;
                    if ($retry <= 0) {
                        $console->error(sprintf('merchant_id:%s merchant_store_id:%s amazon_order_id:%s 重试次数已用完', $merchant_id, $merchant_store_id, $amazon_order_id));
                        break;
                    }
                    $console->warning(sprintf('merchant_id:%s merchant_store_id:%s amazon_order_id:%s 第 %s 次重试', $merchant_id, $merchant_store_id, $amazon_order_id, $retry));
                    sleep(3);
                    continue;
                } catch (InvalidArgumentException $e) {
                    $console->error(sprintf('merchant_id:%s merchant_store_id:%s InvalidArgumentException %s %s', $merchant_id, $merchant_store_id, $e->getCode(), $e->getMessage()));
                    break;
                }
            }

            if (empty($orderItems)) {
                continue;//继续处理下一个order_id
            }

            $list = [];
            foreach ($orderItems as $orderItem) {

                $productInfo = $orderItem->getProductInfo();

                $amazon_order_item_id = $orderItem->getOrderItemId();

                $asin = $orderItem->getAsin();
                $seller_sku = $orderItem->getSellerSku();
                $quantity_ordered = $orderItem->getQuantityOrdered();

                $pointsGranted = $orderItem->getPointsGranted();
                $pointsGrantedJson = [];
                if ($pointsGranted) {
                    $pointsMonetaryValue = $pointsGranted->getPointsMonetaryValue();
                    $pointsMonetaryValueJson = [];
                    if ($pointsMonetaryValue) {
                        $pointsMonetaryValueJson = [
                            'amount' => $pointsMonetaryValue->getAmount() ?? '0.00',
                            'currency_code' => $pointsMonetaryValue->getCurrencyCode() ?? ''
                        ];
                    }

                    $pointsGrantedJson = [
                        'points_number' => $pointsGranted->getPointsNumber() ?? 0,
                        'points_monetary_value' => json_encode($pointsMonetaryValueJson, JSON_THROW_ON_ERROR)
                    ];
                }

                $itemPrice = $orderItem->getItemPrice();
                $itemPriceJson = [];
                if ($itemPrice) {
                    $itemPriceJson = [
                        'currency_code' => $itemPrice->getCurrencyCode() ?? '',
                        'amount' => $itemPrice->getAmount() ?? '0.00'
                    ];
                }

                $is_pending = false;
                if (empty($itemPriceJson)) {
                    //查找原始订单的状态

                }

                $shippingPrice = $orderItem->getShippingPrice();
                $shippingPriceJson = [];
                if ($shippingPrice) {
                    $shippingPriceJson = [
                        'currency_code' => $shippingPrice->getCurrencyCode() ?? '',
                        'amount' => $shippingPrice->getAmount() ?? '0.00'
                    ];
                }

                $itemTax = $orderItem->getItemTax();
                $itemTaxJson = [];
                if ($itemTax) {
                    $itemTaxJson = [
                        'currency_code' => $itemTax->getCurrencyCode() ?? '',
                        'amount' => $itemTax->getAmount() ?? '0.00'
                    ];
                }

                $shippingTax = $orderItem->getShippingTax();
                $shippingTaxJson = [];
                if ($shippingTax) {
                    $shippingTaxJson = [
                        'currency_code' => $shippingTax->getCurrencyCode() ?? '',
                        'amount' => $shippingTax->getAmount() ?? '0.00'
                    ];
                }

                $shippingDiscount = $orderItem->getShippingDiscount();
                $shippingDiscountJson = [];
                if ($shippingDiscount) {
                    $shippingDiscountJson = [
                        'currency_code' => $shippingDiscount->getCurrencyCode() ?? '',
                        'amount' => $shippingDiscount->getAmount() ?? '0.00'
                    ];
                }
                $shippingDiscountTax = $orderItem->getShippingDiscountTax();
                $shippingDiscountTaxJson = [];
                if ($shippingDiscountTax) {
                    $shippingDiscountTaxJson = [
                        'currency_code' => $shippingDiscountTax->getCurrencyCode() ?? '',
                        'amount' => $shippingDiscountTax->getAmount() ?? '0.00'
                    ];
                }

                $promotion_ids = $orderItem->getPromotionIds() ? implode('|', $orderItem->getPromotionIds()) : '';

                $promotionDiscount = $orderItem->getPromotionDiscount();
                $promotionDiscountJson = [];
                if ($promotionDiscount) {
                    $promotionDiscountJson = [
                        'currency_code' => $promotionDiscount->getCurrencyCode() ?? '',
                        'amount' => $promotionDiscount->getAmount() ?? '0.00'
                    ];
                }

                $promotionDiscountTax = $orderItem->getPromotionDiscountTax();
                $promotionDiscountTaxJson = [];
                if ($promotionDiscountTax) {
                    $promotionDiscountTaxJson = [
                        'currency_code' => $promotionDiscountTax->getCurrencyCode() ?? '',
                        'amount' => $promotionDiscountTax->getAmount() ?? '0.00'
                    ];
                }

                $codFee = $orderItem->getCodFee();
                $codFeeJson = [];
                if ($codFee) {
                    $codFeeJson = [
                        'currency_code' => $codFee->getCurrencyCode() ?? '',
                        'amount' => $codFee->getAmount() ?? '0.00'
                    ];
                }

                $codFeeDiscount = $orderItem->getCodFeeDiscount();
                $codFeeDiscountJson = [];
                if ($codFeeDiscount) {
                    $codFeeDiscountJson = [
                        'currency_code' => $codFeeDiscount->getCurrencyCode() ?? '',
                        'amount' => $codFeeDiscount->getAmount() ?? '0.00'
                    ];
                }

                $taxCollection = $orderItem->getTaxCollection();
                $taxCollectionJson = [];
                if ($taxCollection) {
                    $taxCollectionJson = [
                        'model' => $taxCollection->getModel() ?? '',//应用于物料的征税模型   MarketplaceFacilitator税款由亚马逊代表卖方代扣代缴至税务机关
                        'responsible_party' => $taxCollection->getResponsibleParty() ?? ''//负责扣缴税款并将税款汇给税务机关的一方
                    ];
                }

                $buyerInfo = $orderItem->getBuyerInfo();
                $buyerInfoJson = [];
                if ($buyerInfo) {
                    $buyerCustomizedInfo = $buyerInfo->getBuyerCustomizedInfo();
                    $buyerCustomizedInfoJson = [];
                    if ($buyerCustomizedInfo) {
                        $buyerCustomizedInfoJson = [
                            'customized_url' => $buyerCustomizedInfo->getCustomizedUrl(),//包含Amazon Custom数据的zip文件的位置。
                        ];
                    }

                    $giftWrapPrice = $buyerInfo->getGiftWrapPrice();
                    $giftWrapPriceJson = [];
                    if ($giftWrapPrice) {
                        $giftWrapPriceJson = [
                            'amount' => $giftWrapPrice->getAmount(),//物品的礼品包装价格
                            'currency_code' => $giftWrapPrice->getCurrencyCode(),
                        ];
                    }

                    $giftWrapTax = $buyerInfo->getGiftWrapTax();
                    $giftWrapTaxJson = [];
                    if ($giftWrapTax) {
                        $giftWrapTaxJson = [
                            'amount' => $giftWrapTax->getAmount(),//礼品包装价格税
                            'currency_code' => $giftWrapTax->getCurrencyCode(),
                        ];
                    }

                    $buyerInfoJson = [
                        'buyer_customized_info' => $buyerCustomizedInfoJson,
                        'gift_wrap_price' => $giftWrapPriceJson,
                        'gift_wrap_tax' => $giftWrapTaxJson,
                        'gift_message_text' => $buyerInfo->getGiftMessageText() ?? '',//买方提供的礼品信息
                        'gift_wrap_level' => $buyerInfo->getGiftWrapLevel() ?? ''//买方指定的礼品包装级别
                    ];
                }

                $buyerRequestedCancel = $orderItem->getBuyerRequestedCancel();
                $buyerRequestedCancelJson = [];
                if ($buyerRequestedCancel) {
                    $buyerRequestedCancelJson = [
                        'is_buyer_requested_cancel' => (int) ($buyerRequestedCancel->getIsBuyerRequestedCancel() ?? false),//如果为真，买方已请求取消
                        'buyer_cancel_reason' => $buyerRequestedCancel->getBuyerCancelReason() ?? ''//买方要求取消的原因。
                    ];
                }

                $list[$amazon_order_item_id] = [
                    'merchant_id' => $merchant_id,
                    'merchant_store_id' => $merchant_store_id,
                    'order_id' => $amazon_order_id,
                    'asin' => $asin,//物品的亚马逊标准标识号（ASIN）
                    'seller_sku' => $orderItem->getSellerSku(),//商品的卖方库存单位（SKU）
                    'order_item_id' => $amazon_order_item_id,//Amazon定义的订单项标识符
                    'title' => $orderItem->getTitle() ?? '',//标题
                    'quantity_ordered' => $orderItem->getQuantityOrdered(),//商品数量
                    'quantity_shipped' => $orderItem->getQuantityShipped() ?? 0,//装运的商品数量
                    'product_info_number_of_items' => $productInfo ? ($productInfo->getNumberOfItems() ?? 0) : 0,//ASIN中包含的项目总数。
                    'points_granted' => json_encode($pointsGrantedJson, JSON_THROW_ON_ERROR),//购买商品时获得的亚马逊积分的数量和价值
                    'item_price' => json_encode($itemPriceJson, JSON_THROW_ON_ERROR),//订单项的销售价格。请注意，订单项目是项目和数量。这意味着ItemPrice的值等于商品的售价乘以订购数量。请注意，ItemPrice不包括ShippingPrice和GiftWrapPrice。
                    'shipping_price' => json_encode($shippingPriceJson, JSON_THROW_ON_ERROR),//项目的装运价格
                    'item_tax' => json_encode($itemTaxJson, JSON_THROW_ON_ERROR),//项目价格的税
                    'shipping_tax' => json_encode($shippingTaxJson, JSON_THROW_ON_ERROR),//运费税
                    'shipping_discount' => json_encode($shippingDiscountJson, JSON_THROW_ON_ERROR),//运费折扣
                    'shipping_discount_tax' => json_encode($shippingDiscountTaxJson, JSON_THROW_ON_ERROR),//运费折扣税
                    'promotion_discount' => json_encode($promotionDiscountJson, JSON_THROW_ON_ERROR),//优惠中所有促销折扣的总和
                    'promotion_discount_tax' => json_encode($promotionDiscountTaxJson, JSON_THROW_ON_ERROR),
                    'promotion_ids' => $promotion_ids,//创建促销时由卖家提供的促销标识符列表
                    'cod_fee' => json_encode($codFeeJson, JSON_THROW_ON_ERROR),
                    'cod_fee_discount' => json_encode($codFeeDiscountJson, JSON_THROW_ON_ERROR),
                    'is_gift' => (int) ($orderItem->getIsGift() ?? false),//如果为true，则该物品是礼物
                    'condition_note' => (int) ($orderItem->getConditionNote() ?? false),//卖方描述的物品状况
                    'condition_id' => (int) ($orderItem->getConditionId() ?? false),//项目的状态。可能的值：New新建、Used二手、Collectible可收藏、Refurbished翻新、Preorder预购、Club俱乐部
                    'condition_subtype_id' => (int) ($orderItem->getConditionSubtypeId() ?? false),//项目的子条件
                    'scheduled_delivery_start_date' => (int) ($orderItem->getScheduledDeliveryStartDate() ?? false),//订单目的地时区中计划交货窗口的开始日期。采用ISO 8601日期时间格式
                    'scheduled_delivery_end_date' => (int) ($orderItem->getScheduledDeliveryEndDate() ?? false),//订单目的地时区中计划交货窗口的结束日期。采用ISO 8601日期时间格式
                    'price_designation' => (int) ($orderItem->getPriceDesignation() ?? false),//表示销售价格是仅适用于亚马逊业务订单的特殊价格
                    'tax_collection' => json_encode($taxCollectionJson, JSON_THROW_ON_ERROR),//代扣税款信息
                    'serial_number_required' => (int) ($orderItem->getSerialNumberRequired() ?? false),//如果为true，则此项目的产品类型具有序列号。 仅亚马逊Easy Ship订单退回
                    'is_transparency' => (int) ($orderItem->getIsTransparency() ?? false),//如果为true，则需要透明度代码
                    'ioss_number' => (int) ($orderItem->getIossNumber() ?? false),//市场的IOSS编号。从欧盟以外地区运往欧盟（EU）的卖家必须在亚马逊收取销售增值税后向其承运人提供此IOSS编号。
                    'store_chain_store_id' => (int) ($orderItem->getStoreChainStoreId() ?? false),//存储链存储标识符。链接到连锁店中的特定商店
                    'deemed_reseller_category' => (int) ($orderItem->getDeemedResellerCategory() ?? false),//被视为经销商的类别。这适用于不在欧盟的销售合作伙伴，用于帮助他们符合欧盟和英国的增值税视同经销商税法。
                    'buyer_info' => json_encode($buyerInfoJson, JSON_THROW_ON_ERROR),
                    'buyer_requested_cancel' => json_encode($buyerRequestedCancelJson, JSON_THROW_ON_ERROR),
                    'is_estimated_fba_fee' => Constants::YES,//默认 fba_fee 为预估费用
//                        'fba_fee' => '',//预估FBA 费用
//                        'fba_fee_currency' => '',//预估FBA 费用 货币符号(与商品价格货币符号保持一致)
                    'is_estimated_commission' => Constants::YES,//预估佣金 费用 货币符号(与商品价格货币符号保持一致)
//                        'commission' => '',//预估佣金
//                        'commission_currency' => '',//预估佣金 货币符号(与商品价格货币符号保持一致)
                    'created_at' => $cur_date
                ];

            }

            if (empty($list)) {
                continue;//继续处理下一个order_id
            }

            $amazonOrderItemCollections = AmazonOrderItemModel::query()
                ->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->where('order_id', $amazon_order_id)
                ->get();

            if ($amazonOrderItemCollections->isEmpty()) {
                AmazonOrderItemModel::insert($list);
            } else {
                foreach ($amazonOrderItemCollections as $amazonOrderItemCollection) {
                    if (array_key_exists($amazonOrderItemCollection->order_item_id, $list)) {
                        //如果fba_fee值不为空，则表明以前已写入，则不再更新此值(因为不同的时间获取的值不一样)

                        $item = $list[$amazonOrderItemCollection->order_item_id];

                        $amazonOrderItemCollection->quantity_ordered = $item['quantity_ordered'];
                        $amazonOrderItemCollection->quantity_shipped = $item['quantity_shipped'];
                        $amazonOrderItemCollection->product_info_number_of_items = $item['product_info_number_of_items'];
                        $amazonOrderItemCollection->points_granted = $item['points_granted'];
                        $amazonOrderItemCollection->item_price = $item['item_price'];
                        $amazonOrderItemCollection->shipping_price = $item['shipping_price'];
                        $amazonOrderItemCollection->item_tax = $item['item_tax'];
                        $amazonOrderItemCollection->shipping_tax = $item['shipping_tax'];
                        $amazonOrderItemCollection->shipping_discount = $item['shipping_discount'];
                        $amazonOrderItemCollection->shipping_discount_tax = $item['shipping_discount_tax'];
                        $amazonOrderItemCollection->promotion_discount = $item['promotion_discount'];
                        $amazonOrderItemCollection->promotion_discount_tax = $item['promotion_discount_tax'];
                        $amazonOrderItemCollection->promotion_ids = $item['promotion_ids'];
                        $amazonOrderItemCollection->cod_fee = $item['cod_fee'];
                        $amazonOrderItemCollection->cod_fee_discount = $item['cod_fee_discount'];
                        $amazonOrderItemCollection->is_gift = $item['is_gift'];
                        $amazonOrderItemCollection->condition_note = $item['condition_note'];
                        $amazonOrderItemCollection->condition_id = $item['condition_id'];
                        $amazonOrderItemCollection->condition_subtype_id = $item['condition_subtype_id'];
                        $amazonOrderItemCollection->scheduled_delivery_start_date = $item['scheduled_delivery_start_date'];
                        $amazonOrderItemCollection->scheduled_delivery_end_date = $item['scheduled_delivery_end_date'];
                        $amazonOrderItemCollection->price_designation = $item['price_designation'];
                        $amazonOrderItemCollection->tax_collection = $item['tax_collection'];
                        $amazonOrderItemCollection->serial_number_required = $item['serial_number_required'];
                        $amazonOrderItemCollection->is_transparency = $item['is_transparency'];
                        $amazonOrderItemCollection->ioss_number = $item['ioss_number'];
                        $amazonOrderItemCollection->store_chain_store_id = $item['store_chain_store_id'];
                        $amazonOrderItemCollection->deemed_reseller_category = $item['deemed_reseller_category'];
                        $amazonOrderItemCollection->buyer_info = $item['buyer_info'];
                        $amazonOrderItemCollection->buyer_requested_cancel = $item['buyer_requested_cancel'];
//                            $amazonOrderItemCollection->is_estimated_fba_fee = $item['is_estimated_fba_fee'];
//                            $amazonOrderItemCollection->fba_fee = $item['fba_fee'];
//                            $amazonOrderItemCollection->fba_fee_currency = $item['fba_fee_currency'];
//                            $amazonOrderItemCollection->is_estimated_commission = $item['is_estimated_commission'];
//                            $amazonOrderItemCollection->commission = $item['commission'];
//                            $amazonOrderItemCollection->commission_currency = $item['commission_currency'];

                        $amazonOrderItemCollection->save();

                        unset($list[$amazonOrderItemCollection->order_item_id]);
                    } else if (! empty($list[$amazonOrderItemCollection->order_item_id])) {
                        $create = AmazonOrderItemModel::insert($list[$amazonOrderItemCollection->order_item_id]);
                        unset($list[$amazonOrderItemCollection->order_item_id]);
                    }
                }
                if (! empty($list)) {
                    foreach ($list as $item) {
                        $create = AmazonOrderItemModel::create($item);
                    }
                }
            }
        }

        return true;
    }
}