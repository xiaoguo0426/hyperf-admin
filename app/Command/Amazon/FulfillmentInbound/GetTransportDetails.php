<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\FulfillmentInbound;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonShipmentModel;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonFulfillmentInboundGetTransportDetailsLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetTransportDetails extends HyperfCommand
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fulfillment-inbound:get-transport-details');
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
            ->setDescription('Amazon Fulfillment Inbound GetTransportDetails Command');
    }

    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     * @return void
     */
    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonFulfillmentInboundGetTransportDetailsLog::class);


            $amazonShipmentsCollections = AmazonShipmentModel::query()
                ->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->orderByDesc('id')
                ->get();
            if ($amazonShipmentsCollections->isEmpty()) {
                return true;
            }
            foreach ($amazonShipmentsCollections as $amazonShipmentsCollection) {

                $shipment_id = $amazonShipmentsCollection->shipment_id;
                try {
                    $getTransportDetailsResponse = $sdk->fulfillmentInbound()->getTransportDetails($accessToken, $region, $shipment_id);
                    $payload = $getTransportDetailsResponse->getPayload();
                    if (is_null($payload)) {
                        $console->warning(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s API响应 payload为空', $merchant_id, $merchant_store_id, $shipment_id));
                        continue;
                    }
                    $errorList = $getTransportDetailsResponse->getErrors();
                    $errors = [];
                    if (! is_null($errorList)) {
                        foreach ($errorList as $error) {
                            $code = $error->getCode();
                            $message = $error->getMessage();
                            $details = $error->getDetails();
                            $errors[] = [
                                'code' => $code,
                                'message' => $message,
                                'details' => $details,
                            ];
                        }
                        $console->error(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s errors:%s', $merchant_id, $merchant_store_id, $shipment_id, json_encode($errors, JSON_THROW_ON_ERROR)));
                        continue;
                    }

                    $transportContent = $payload->getTransportContent();
                    if (is_null($transportContent)) {
                        continue;
                    }

                    $transportHeader = $transportContent->getTransportHeader();
                    $seller_id = $transportHeader->getSellerId();
                    $shipment_id = $transportHeader->getShipmentId();
                    $is_partnered = $transportHeader->getIsPartnered() ?? false;
                    $shipment_type = $transportHeader->getShipmentType()->toString();

                    $transportDetails = $transportContent->getTransportDetails();
                    $partneredSmallParcelData = $transportDetails->getPartneredSmallParcelData();
                    $package_list = [];
                    $partnered_estimate = [];
                    if (! is_null($partneredSmallParcelData)) {
                        $packageList = $partneredSmallParcelData->getPackageList();
                        foreach ($packageList as $package) {
                            $dimensions = $package->getDimensions();
                            $dimensions_length = $dimensions->getLength();
                            $dimensions_width = $dimensions->getWidth();
                            $dimensions_height = $dimensions->getHeight();
                            $dimensions_unit = $dimensions->getUnit();

                            $weight = $package->getWeight();
                            $weight_unit = $weight->getUnit();
                            $weight_value = $weight->getValue();

                            $carrier_name = $package->getCarrierName();
                            $tracking_id = $package->getTrackingId() ?? '';
                            $package_status = $package->getPackageStatus()->toString();

                            $package_list[] = [
                                'dimensions' => [
                                    'length' => $dimensions_length,
                                    'width' => $dimensions_width,
                                    'height' => $dimensions_height,
                                    'unit' => $dimensions_unit,
                                ],
                                'weight' => [
                                    'unit' => $weight_unit,
                                    'value' => $weight_value
                                ],
                                'carrier_name' => $carrier_name,
                                'tracking_id' => $tracking_id,
                                'package_status' => $package_status,
                            ];
                        }

                        $partneredEstimate = $partneredSmallParcelData->getPartneredEstimate();
                        if (! is_null($partneredEstimate)) {
                            $amount = $partneredEstimate->getAmount();

                            $confirmDeadline = $partneredEstimate->getConfirmDeadline();
                            $confirm_deadline = '';
                            if (! is_null($confirmDeadline)) {
                                $confirm_deadline = $confirmDeadline->format('Y-m-d H:i:s');
                            }

                            $voidDeadline = $partneredEstimate->getVoidDeadline();
                            $void_deadline = '';
                            if (! is_null($voidDeadline)) {
                                $void_deadline = $voidDeadline->format('Y-m-d H:i:s');
                            }

                            $partnered_estimate = [
                                'amount' => [
                                    'currency' => $amount->getCurrencyCode(),
                                    'value' => $amount->getValue(),
                                ],
                                'confirm_deadline' => $confirm_deadline,
                                'void_deadline' => $void_deadline,
                            ];
                        }
                    }

                    $partneredLtlData = $transportDetails->getPartneredLtlData();
                    $partnered_ltl_data = [];
                    if (! is_null($partneredLtlData)) {
                        $contact = $partneredLtlData->getContact();

                        $box_count = $partneredLtlData->getBoxCount();

                        $sellerFreightClass = $partneredLtlData->getSellerFreightClass();
                        $seller_freight_class = '';
                        if (! is_null($sellerFreightClass)) {
                            $seller_freight_class = $sellerFreightClass->toString();
                        }

                        $palletList = $partneredLtlData->getPalletList();
                        $pallet_list = [];
                        foreach ($palletList as $pallet) {
                            $dimensions = $pallet->getDimensions();
                            $dimensions_length = $dimensions->getLength();
                            $dimensions_width = $dimensions->getWidth();
                            $dimensions_height = $dimensions->getHeight();
                            $dimensions_unit = $dimensions->getUnit();

                            $weight = $pallet->getWeight();
                            $weight_unit = $weight->getUnit();
                            $weight_value = $weight->getValue();

                            $is_stacked = $pallet->getIsStacked() ?? false;

                            $pallet_list[] = [
                                'dimensions' => [
                                    'length' => $dimensions_length,
                                    'width' => $dimensions_width,
                                    'height' => $dimensions_height,
                                    'unit' => $dimensions_unit,
                                ],
                                'weight' => [
                                    'unit' => $weight_unit,
                                    'value' => $weight_value
                                ],
                                'is_stacked' => $is_stacked,
                            ];
                        }

                        $totalWeight = $partneredLtlData->getTotalWeight();
                        $total_weight = [];
                        if (! is_null($totalWeight)) {
                            $total_weight_unit = $totalWeight->getUnit();
                            $total_weight_value = $totalWeight->getValue();
                            $total_weight = [
                                'unit' => $total_weight_unit,
                                'value' => $total_weight_value,
                            ];
                        }

                        $sellerDeclaredValue = $partneredLtlData->getSellerDeclaredValue();
                        $seller_declare_value = [];
                        if (! is_null($sellerDeclaredValue)) {
                            $seller_declare_value = [
                                'currency' => $sellerDeclaredValue->getCurrencyCode(),
                                'value' => $sellerDeclaredValue->getValue()
                            ];
                        }

                        $amazonCalculatedValue = $partneredLtlData->getAmazonCalculatedValue();
                        $amazon_calculated_value = [];
                        if (! is_null($amazonCalculatedValue)) {
                            $amazon_calculated_value = [
                                'currency' => $amazonCalculatedValue->getCurrencyCode(),
                                'value' => $amazonCalculatedValue->getValue()
                            ];
                        }

                        $previewPickupDate = $partneredLtlData->getPreviewPickupDate();
                        $preview_pickup_date = '';
                        if (! is_null($previewPickupDate)) {
                            $preview_pickup_date = $previewPickupDate->format('Y-m-d H:i:s');
                        }

                        $previewDeliveryDate = $partneredLtlData->getPreviewDeliveryDate();
                        $preview_delivery_date = '';
                        if (! is_null($previewDeliveryDate)) {
                            $preview_delivery_date = $previewDeliveryDate->format('Y-m-d H:i:s');
                        }

                        $previewFreightClass = $partneredLtlData->getPreviewFreightClass();
                        $preview_freight_class = '';
                        if (! is_null($previewFreightClass)) {
                            $preview_freight_class = $previewFreightClass->toString();
                        }

                        $amazon_reference_id = $partneredLtlData->getAmazonReferenceId() ?? '';
                        $is_bill_of_lading_available = $partneredLtlData->getIsBillOfLadingAvailable() ?? false;
                        $partneredEstimate = $partneredLtlData->getPartneredEstimate();
                        $partnered_estimate = [];
                        if (! is_null($partneredEstimate)) {
                            $amount = $partneredEstimate->getAmount();

                            $confirmDeadline = $partneredEstimate->getConfirmDeadline();
                            $confirm_deadline = '';
                            if (! is_null($confirmDeadline)) {
                                $confirm_deadline = $confirmDeadline->format('Y-m-d H:i:s');
                            }

                            $voidDeadline = $partneredEstimate->getVoidDeadline();
                            $void_deadline = '';
                            if (! is_null($voidDeadline)) {
                                $void_deadline = $voidDeadline->format('Y-m-d H:i:s');
                            }

                            $partnered_estimate = [
                                'amount' => [
                                    'currency' => $amount->getCurrencyCode(),
                                    'value' => $amount->getValue(),
                                ],
                                'confirm_deadline' => $confirm_deadline,
                                'void_deadline' => $void_deadline,
                            ];
                        }

                        $carrier_name = $partneredLtlData->getCarrierName() ?? '';

                        $partnered_ltl_data = [
                            'contact' => [
                                'name' => $contact->getName(),
                                'phone' => $contact->getPhone(),
                                'email' => $contact->getEmail(),
                                'fax' => $contact->getFax(),
                            ],
                            'box_count' => $box_count,
                            'seller_freight_class' => $seller_freight_class,
                            'pallet_list' => $pallet_list,
                            'total_weight' => $total_weight,
                            'seller_declare_value' => $seller_declare_value,
                            'amazon_calculated_value' => $amazon_calculated_value,
                            'preview_pickup_date' => $preview_pickup_date,
                            'preview_delivery_date' => $preview_delivery_date,
                            'preview_freight_class' => $preview_freight_class,
                            'amazon_reference_id' => $amazon_reference_id,
                            'is_bill_of_lading_available' => $is_bill_of_lading_available,
                            'partnered_estimate' => $partnered_estimate,
                            'carrier_name' => $carrier_name,
                        ];

                    }

                    $nonPartneredLtlData = $transportDetails->getNonPartneredLtlData();
                    $non_partnered_ltl_data = [];
                    if (! is_null($nonPartneredLtlData)) {
                        $non_partnered_ltl_data = [
                            'carrier_name' => $nonPartneredLtlData->getCarrierName() ?? '',
                            'pro_number' => $nonPartneredLtlData->getProNumber() ?? ''
                        ];
                    }

                    $transportResult = $transportContent->getTransportResult();
                    $transport_status = $transportResult->getTransportStatus()->toString();
                    $error_code = $transportResult->getErrorCode() ?? '';
                    $error_description = $transportResult->getErrorDescription() ?? '';

                    var_dump($seller_id);
                    var_dump($shipment_id);
                    var_dump($is_partnered);
                    var_dump($shipment_type);
                    var_dump($package_list);
                    var_dump($partnered_estimate);
                    var_dump($partnered_ltl_data);
                    var_dump($non_partnered_ltl_data);
                    var_dump($transport_status);
                    var_dump($error_code);
                    var_dump($error_description);

                } catch (ApiException $exception) {
                    $console->error(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s %s', $merchant_id, $merchant_store_id, $shipment_id, $exception->getMessage()));
                } catch (InvalidArgumentException $exception) {
                    $console->error('InvalidArgumentException API请求错误', [
                        'message' => $exception->getMessage(),
                        'trace' => $exception->getTraceAsString(),
                    ]);

                    $logger->error('InvalidArgumentException API请求错误', [
                        'message' => $exception->getMessage(),
                        'trace' => $exception->getTraceAsString(),
                    ]);
                }
            }

            return true;
        });
    }
}
