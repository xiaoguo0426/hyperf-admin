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
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonFinanceLog;
use Hyperf\Collection\Collection;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetShipments extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fulfillment-inbound:get-shipments');
    }

    public function configure(): void
    {
        parent::configure();
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Fulfillment Inbound Get Shipments Command');
    }

    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');
        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {
            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonFinanceLog::class);
            $query_type = 'SHIPMENT';
            $shipment_status_list = [
                'WORKING',
                'READY_TO_SHIP',
                'SHIPPED',
                'RECEIVING',
                'CANCELLED',
                'DELETED',
                'CLOSED',
                'ERROR',
                'IN_TRANSIT',
                'DELIVERED',
                'CHECKED_IN',
            ];
//            $last_updated_after = (new \DateTime('2023-01-01', new \DateTimeZone('UTC')));
//            $last_updated_before = (new \DateTime('2023-09-01', new \DateTimeZone('UTC')));
            $last_updated_after = null;
            $last_updated_before = null;
            foreach ($marketplace_ids as $marketplace_id) {

                $collections = new Collection();

                $retry = 10;
                $next_token = null;
                while (true) {
                    try {
                        $response = $sdk->fulfillmentInbound()->getShipments($accessToken, $region, $query_type, $marketplace_id, $shipment_status_list, null, $last_updated_after, $last_updated_before, $next_token);

                        $payload = $response->getPayload();
                        if ($payload === null) {
                            break;
                        }
                        $inboundShipmentList = $payload->getShipmentData();
                        if (is_null($inboundShipmentList)) {
                            break;
                        }

                        $retry = 10;
                        foreach ($inboundShipmentList as $inboundShipment) {
                            $shipment_id = $inboundShipment->getShipmentId() ?? '';
                            $shipment_name = $inboundShipment->getShipmentName() ?? '';
                            $shipmentFromAddress = $inboundShipment->getShipFromAddress();
                            $shipment_from_address = [
                                'name' => $shipmentFromAddress->getName() ?? '',
                                'address_line1' => $shipmentFromAddress->getAddressLine1() ?? '',
                                'address_line2' => $shipmentFromAddress->getAddressLine2() ?? '',
                                'district_or_county' => $shipmentFromAddress->getDistrictOrCounty() ?? '',
                                'city' => $shipmentFromAddress->getCity() ?? '',
//                                'state_or_province_code' => $shipmentFromAddress->getStateOrProvinceCode() ?? '',
                                'country_code' => $shipmentFromAddress->getCountryCode() ?? '',
                                'postal_code' => $shipmentFromAddress->getPostalCode() ?? '',
                            ];

                            $destination_fulfillment_center_id = $inboundShipment->getDestinationFulfillmentCenterId() ?? '';
                            $shipmentStatus = $inboundShipment->getShipmentStatus();
                            $shipment_status = '';
                            if (! is_null($shipmentStatus)) {
                                $shipment_status = $shipmentStatus->toString();
                            }
                            $labelPrepType = $inboundShipment->getLabelPrepType();
                            $label_prep_type = '';
                            if (! is_null($labelPrepType)) {
                                $label_prep_type = $labelPrepType->toString();
                            }
                            $are_cases_required = $inboundShipment->getAreCasesRequired() ?? false;
                            $confirmedNeedByDate = $inboundShipment->getConfirmedNeedByDate();
                            $confirmed_need_by_date = '';
                            if (! is_null($confirmedNeedByDate)) {
                                $confirmed_need_by_date = $confirmedNeedByDate->format('Y-m-d H:i:s');
                            }
                            $boxContentsSource = $inboundShipment->getBoxContentsSource();
                            $box_contents_source = '';
                            if (! is_null($boxContentsSource)) {
                                $box_contents_source = $boxContentsSource->toString();
                            }
                            $estimatedBoxContentsFee = $inboundShipment->getEstimatedBoxContentsFee();
                            $total_units = 0;
                            $fee_per_unit_currency = '';
                            $fee_per_unit_value = 0.00;
                            $total_fee_currency = '';
                            $total_fee_value = 0.00;
                            if (! is_null($estimatedBoxContentsFee)) {
                                $total_units = $estimatedBoxContentsFee->getTotalUnits() ?? 0;
                                $feePerUnit = $estimatedBoxContentsFee->getFeePerUnit();
                                if (! is_null($feePerUnit)) {
                                    $fee_per_unit_currency = $feePerUnit->getCurrencyCode()->toString();
                                    $fee_per_unit_value = $feePerUnit->getValue();
                                }
                                $totalFee = $estimatedBoxContentsFee->getTotalFee();
                                if (! is_null($totalFee)) {
                                    $total_fee_currency = $totalFee->getCurrencyCode()->toString();
                                    $total_fee_value = $totalFee->getValue();
                                }
                            }

                            $collections->push([
                                'merchant_id' => $merchant_id,
                                'merchant_store_id' => $merchant_store_id,
                                'shipment_id' => $shipment_id,
                                'shipment_name' => $shipment_name,
                                'shipment_from_address' => $shipment_from_address,
                                'destination_fulfillment_center_id' => $destination_fulfillment_center_id,
                                'shipment_status' => $shipment_status,
                                'label_prep_type' => $label_prep_type,
                                'are_cases_required' => $are_cases_required,
                                'confirmed_need_by_date' => $confirmed_need_by_date,
                                'box_contents_source' => $box_contents_source,
                                'total_units' => $total_units,
                                'fee_per_unit_currency' => $fee_per_unit_currency,
                                'fee_per_unit_value' => $fee_per_unit_value,
                                'total_fee_currency' => $total_fee_currency,
                                'total_fee_value' => $total_fee_value,
                            ]);
                        }

                        // 如果下一页没有数据，nextToken 会变成null
                        $next_token = $payload->getNextToken();
                        if (is_null($next_token)) {
                            break;
                        }
                        $query_type = 'NEXT_TOKEN';//如果有下一页，需要把query_type设置为NEXT_TOKEN
                    } catch (ApiException $e) {
                        --$retry;
                        if ($retry > 0) {
                            $console->warning(sprintf('FulfillmentInbound ApiException GetShipments Failed. retry:%s merchant_id: %s merchant_store_id: %s ', $retry, $merchant_id, $merchant_store_id));
                            sleep(10);
                            continue;
                        }

                        $log = sprintf('FulfillmentInbound ApiException GetShipments Failed. merchant_id: %s merchant_store_id: %s ', $merchant_id, $merchant_store_id);
                        $console->error($log);
                        $logger->error($log);
                        break;
                    } catch (InvalidArgumentException $e) {
                        $log = sprintf('FulfillmentInbound InvalidArgumentException GetShipments Failed. merchant_id: %s merchant_store_id: %s ', $merchant_id, $merchant_store_id);
                        $console->error($log);
                        $logger->error($log);
                        break;
                    }

                }

                $query_type = 'SHIPMENT';//重置query_type
            }

            return true;
        });
    }
}
