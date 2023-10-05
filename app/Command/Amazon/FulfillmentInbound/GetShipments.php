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
use App\Util\Log\AmazonFinanceLog;
use App\Util\RuntimeCalculator;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetShipments extends HyperfCommand
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fulfillment-inbound:get-shipments');
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Fulfillment Inbound Get Shipments Command');
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
            $logger = ApplicationContext::getContainer()->get(AmazonFinanceLog::class);

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

            $now = Carbon::now()->format('Y-m-d H:i:s');


            $runtimeCalculator = new RuntimeCalculator();
            $runtimeCalculator->start();

            $console->info(sprintf('FulfillmentInbound marketplace_ids:%s merchant_id:%s merchant_store_id:%s 开始处理.', implode(',', $marketplace_ids), $merchant_id, $merchant_store_id));

            $collections = new Collection();

            $shipment_ids = [];

            $query_type = 'SHIPMENT';

            $retry = 10;
            $next_token = null;
            while (true) {
                try {
                    $response = $sdk->fulfillmentInbound()->getShipments($accessToken, $region, $query_type, '', $shipment_status_list, null, $last_updated_after, $last_updated_before, $next_token);

                    $payload = $response->getPayload();
                    if ($payload === null) {
                        break;
                    }
                    $inboundShipmentList = $payload->getShipmentData();
                    if (is_null($inboundShipmentList)) {
                        break;
                    }

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

                        $destination_fulfillment_center_id = $inboundShipment->getDestinationFulfillmentCenterId() ?? '';//由Amazon创建的Amazon履行中心标识符
                        $shipmentStatus = $inboundShipment->getShipmentStatus();//发货状态
                        $shipment_status = '';
                        if (! is_null($shipmentStatus)) {
                            $shipment_status = $shipmentStatus->toString();
                        }
                        $labelPrepType = $inboundShipment->getLabelPrepType();//货件所需的标签准备类型。NO_LABEL无标签,SELLER_LABEL卖方标签,AMAZON_LABEL标签
                        $label_prep_type = '';
                        if (! is_null($labelPrepType)) {
                            $label_prep_type = $labelPrepType->toString();
                        }
                        $are_cases_required = $inboundShipment->getAreCasesRequired() ?? false;//指明入站货件是否包含装箱。对于入站货件，当AreCasesRequired = true时，入站货件中的所有项目都必须装箱。
                        $confirmedNeedByDate = $inboundShipment->getConfirmedNeedByDate();//货件必须到达亚马逊履行中心的日期，以避免预购商品的交付承诺被打破。
                        $confirmed_need_by_date = '';
                        if (! is_null($confirmedNeedByDate)) {
                            $confirmed_need_by_date = $confirmedNeedByDate->format('Y-m-d H:i:s');
                        }
                        $boxContentsSource = $inboundShipment->getBoxContentsSource();//卖方提供了装运货物的包装箱内容信息。
                        $box_contents_source = '';
                        if (! is_null($boxContentsSource)) {
                            $box_contents_source = $boxContentsSource->toString();
                        }
                        $estimatedBoxContentsFee = $inboundShipment->getEstimatedBoxContentsFee();//亚马逊对没有盒子内容信息的盒子收取的人工处理费的估计。仅当BoxContentsSource为NONE时才返回此值
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

                        $shipment_ids[] = $shipment_id;
                        $collections->offsetSet($shipment_id, [
                            'merchant_id' => $merchant_id,
                            'merchant_store_id' => $merchant_store_id,
//                            'marketplace_id' => $marketplace_id,
                            'marketplace_id' => '',
                            'shipment_id' => $shipment_id,
                            'shipment_name' => $shipment_name,
                            'shipment_from_address' => json_encode($shipment_from_address, JSON_THROW_ON_ERROR),
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
                            'created_at' => $now,
                        ]);
                    }

                    // 如果下一页没有数据，nextToken 会变成null
                    $next_token = $payload->getNextToken();
                    if (is_null($next_token)) {
                        break;
                    }
                    $query_type = 'NEXT_TOKEN';//如果有下一页，需要把query_type设置为NEXT_TOKEN
                    $retry = 10;
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

            $existShipments = AmazonShipmentModel::query()->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->whereIn('shipment_id', $shipment_ids)
                ->get();

            if (! $existShipments->isEmpty()) {
                foreach ($existShipments as $existShipment) {
                    $exist_shipment_id = $existShipment->shipment_id;
                    if ($collections->offsetExists($exist_shipment_id)) {
                        $collection = $collections->offsetGet($exist_shipment_id);

//                        $existShipment->destination_fulfillment_center_id = $collection['destination_fulfillment_center_id'];
                        $existShipment->shipment_status = $collection['shipment_status'];
//                        $existShipment->label_prep_type = $collection['label_prep_type'];
//                        $existShipment->are_cases_required = $collection['are_cases_required'];
//                        $existShipment->confirmed_need_by_date = $collection['confirmed_need_by_date'];
//                        $existShipment->box_contents_source = $collection['box_contents_source'];
//                        $existShipment->total_units = $collection['total_units'];
//                        $existShipment->fee_per_unit_currency = $collection['fee_per_unit_currency'];
//                        $existShipment->fee_per_unit_value = $collection['fee_per_unit_value'];
//                        $existShipment->total_fee_currency = $collection['total_fee_currency'];
//                        $existShipment->total_fee_value = $collection['total_fee_value'];

                        $existShipment->save();
                    } else {
                        // delete -- 一般情况下不会走到这里
                        $console->warning(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s 被标记为删除，请检查', $merchant_id, $merchant_store_id, $exist_shipment_id));
                    }
                    $collections->offsetUnset($exist_shipment_id);
                }
            }

            if (! $collections->isEmpty()) {
                //需要新增的部分
                AmazonShipmentModel::insert($collections->toArray());
            }

            $console->notice(sprintf('FulfillmentInbound merchant_id:%s merchant_store_id:%s 完成处理，耗时:%s. 更新:%s 新增:%s', $merchant_id, $merchant_store_id, $runtimeCalculator->stop(), $existShipments->count(), $collections->count()));


            return true;
        });
    }
}
