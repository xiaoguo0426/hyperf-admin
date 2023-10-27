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
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use Carbon\Carbon;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetOrder extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:order:get-order');
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->addArgument('order_id', InputArgument::REQUIRED, 'amazon_order_id')
            ->setDescription('Amazon Order API Get Order Command');
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
        $amazon_order_id = $this->input->getArgument('order_id');

        $that = $this;

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($that, $amazon_order_id) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

            $retry = 30;

            while (true) {
                try {

                    $response = $sdk->orders()->getOrder($accessToken, $region, $amazon_order_id);

                    $order = $response->getPayload();
                    if ($order === null) {
                        break;
                    }

                    $amazon_order_id = $order->getAmazonOrderId();

                    $orderTotal = $order->getOrderTotal();
                    $order_total_currency_code = $orderTotal === null ? '' : ($orderTotal->getCurrencyCode() ?? '');//订单的总费用(货币)
                    $order_total_amount = $orderTotal === null ? '' : ($orderTotal->getAmount() ?? '');//订单的总费用

                    $purchase_date = Carbon::createFromFormat('Y-m-d\TH:i:sZ', $order->getPurchaseDate())->format('Y-m-d H:i:s');

                    $last_update_date = Carbon::createFromFormat('Y-m-d\TH:i:sZ', $order->getLastUpdateDate())->format('Y-m-d H:i:s');

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
                            'hasAutomatedShippingSettings' => $automatedShippingSettings->getHasAutomatedShippingSettings() ?? false,
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

                    var_dump($amazon_order_id);
                    var_dump($order_total_currency_code);
                    var_dump($order_total_amount);
                    var_dump($purchase_date);
                    var_dump($last_update_date);
                    var_dump($paymentExecutionDetailJson);
                    var_dump($paymentMethodDetailsJson);
                    var_dump($defaultShipFromLocationAddressJson);
                    var_dump($buyerTaxInformationJson);
                    var_dump($fulfillmentInstructionJson);
                    var_dump($shippingAddressJson);
                    var_dump($buyerInfoJson);
                    var_dump($automatedShippingSettingsJson);
                    var_dump($marketplaceTaxInfoJson);

                    $that->table([
                        'amazon_order_id',
                        'order_total_currency_code',
                        'order_total_amount',
                        'purchase_date',
                        'last_update_date',
                        'paymentExecutionDetailJson',
                        'paymentMethodDetailsJson',
                        'defaultShipFromLocationAddressJson',
                        'buyerTaxInformationJson',
                        'fulfillmentInstructionJson',
                        'shippingAddressJson',
                        'buyerInfoJson',
                        'automatedShippingSettingsJson',
                        'marketplaceTaxInfoJson',
                    ], [[
                        'amazon_order_id' => $amazon_order_id,
                        'order_total_currency_code' => $order_total_currency_code,
                        'order_total_amount' => $order_total_amount,
                        'purchase_date' => $purchase_date,
                        'last_update_date' => $last_update_date,
                        'paymentExecutionDetailJson' => json_encode($paymentExecutionDetailJson, JSON_THROW_ON_ERROR),
                        'paymentMethodDetailsJson' => json_encode($paymentMethodDetailsJson, JSON_THROW_ON_ERROR),
                        'defaultShipFromLocationAddressJson' => json_encode($defaultShipFromLocationAddressJson, JSON_THROW_ON_ERROR),
                        'buyerTaxInformationJson' => json_encode($buyerTaxInformationJson, JSON_THROW_ON_ERROR),
                        'fulfillmentInstructionJson' => json_encode($fulfillmentInstructionJson, JSON_THROW_ON_ERROR),
                        'shippingAddressJson' => json_encode($shippingAddressJson, JSON_THROW_ON_ERROR),
                        'buyerInfoJson' => json_encode($buyerInfoJson, JSON_THROW_ON_ERROR),
                        'automatedShippingSettingsJson' => json_encode($automatedShippingSettingsJson, JSON_THROW_ON_ERROR),
                        'marketplaceTaxInfoJson' => json_encode($marketplaceTaxInfoJson, JSON_THROW_ON_ERROR),
                    ]]);

                    break;

                } catch (ApiException $e) {

                    if (! is_null($e->getResponseBody())) {
                        $body = json_decode($e->getResponseBody(), true, 512, JSON_THROW_ON_ERROR);
                        if (isset($body['errors'])) {
                            $errors = $body['errors'];
                            foreach ($errors as $error) {
                                if ($error['code'] !== 'QuotaExceeded') {
                                    $console->warning(sprintf('merchant_id:%s merchant_store_id:%s Page:%s code:%s message:%s', $merchant_id, $merchant_store_id, $page, $error['code'], $error['message']));
                                    break 2;
                                }
                            }
                        }
                    }

                    $retry--;
                    if ($retry > 0) {
                        $console->warning(sprintf('merchant_id:%s merchant_store_id:%s Page:%s 第 %s 次重试', $merchant_id, $merchant_store_id, $page, $retry));
                        sleep(3);
                        continue;
                    }

                    $console->error(sprintf('merchant_id:%s merchant_store_id:%s Page:%s 重试次数已用完', $merchant_id, $merchant_store_id, $page));
                    break;
                } catch (InvalidArgumentException $e) {
                    $console->error(sprintf('merchant_id:%s merchant_store_id:%s InvalidArgumentException %s %s', $merchant_id, $merchant_store_id, $e->getCode(), $e->getMessage()));
                    break;
                }
            }

            return true;
        });

    }
}
