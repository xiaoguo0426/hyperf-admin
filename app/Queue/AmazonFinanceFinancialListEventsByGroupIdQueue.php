<?php

namespace App\Queue;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Queue\Data\AmazonFinanceListFinancialEventsByGroupIdData;
use App\Queue\Data\QueueDataInterface;
use App\Util\Amazon\Finance\AdhocDisbursementEventList;
use App\Util\Amazon\Finance\AdjustmentEventList;
use App\Util\Amazon\Finance\AffordabilityExpenseEventList;
use App\Util\Amazon\Finance\AffordabilityExpenseReversalEventList;
use App\Util\Amazon\Finance\CapacityReservationBillingEventList;
use App\Util\Amazon\Finance\ChargebackEventList;
use App\Util\Amazon\Finance\ChargeRefundEventList;
use App\Util\Amazon\Finance\CouponPaymentEventList;
use App\Util\Amazon\Finance\DebtRecoveryEventList;
use App\Util\Amazon\Finance\FailedAdhocDisbursementEventList;
use App\Util\Amazon\Finance\FbaLiquidationEventList;
use App\Util\Amazon\Finance\FinanceFactory;
use App\Util\Amazon\Finance\GuaranteeClaimEventList;
use App\Util\Amazon\Finance\ImagingServicesFeeEventList;
use App\Util\Amazon\Finance\LoanServicingEventList;
use App\Util\Amazon\Finance\NetworkComminglingTransactionEventList;
use App\Util\Amazon\Finance\PayWithAmazonEventList;
use App\Util\Amazon\Finance\ProductAdsPaymentEventList;
use App\Util\Amazon\Finance\RefundEventList;
use App\Util\Amazon\Finance\RemovalShipmentAdjustmentEventList;
use App\Util\Amazon\Finance\RemovalShipmentEventList;
use App\Util\Amazon\Finance\RentalTransactionEventList;
use App\Util\Amazon\Finance\RetrochargeEventList;
use App\Util\Amazon\Finance\SafetReimbursementEventList;
use App\Util\Amazon\Finance\SellerDealPaymentEventList;
use App\Util\Amazon\Finance\SellerReviewEnrollmentPaymentEventList;
use App\Util\Amazon\Finance\ServiceFeeEventList;
use App\Util\Amazon\Finance\ServiceProviderCreditEventList;
use App\Util\Amazon\Finance\ShipmentEventList;
use App\Util\Amazon\Finance\ShipmentSettleEventList;
use App\Util\Amazon\Finance\TaxWithholdingEventList;
use App\Util\Amazon\Finance\TdsReimbursementEventList;
use App\Util\Amazon\Finance\TrialShipmentEventList;
use App\Util\Amazon\Finance\ValueAddedServiceChargeEventList;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonFinanceLog;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Dag\Dag;

class AmazonFinanceFinancialListEventsByGroupIdQueue extends Queue
{
    public function getQueueName(): string
    {
        return 'amazon-financial-list-events-by-group-id';
    }

    public function getQueueDataClass(): string
    {
        return AmazonFinanceListFinancialEventsByGroupIdData::class;
    }

    public function handleQueueData(QueueDataInterface $queueData): bool
    {
        /**
         * @var AmazonFinanceListFinancialEventsByGroupIdData $queueData
         */
        $merchant_id = $queueData->getMerchantId();
        $merchant_store_id = $queueData->getMerchantStoreId();
        $financial_event_group_id = $queueData->getFinancialEventGroupId();
        var_dump('当前财务组id:' . $financial_event_group_id);

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($financial_event_group_id) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonFinanceLog::class);

            $retry = 10;
            $max_results_per_page = 100;
            $next_token = null;

            while (true) {
                try {
                    //指定日期范围内的财务事件组
                    $response = $sdk->finances()->listFinancialEventsByGroupId($accessToken, $region, $financial_event_group_id, $max_results_per_page, null, null, $next_token);

                    $errorList = $response->getErrors();
                    if (! is_null($errorList)) {
                        foreach ($errorList as $error) {
                            $code = $error->getCode();
                            $msg = $error->getMessage();
                            $detail = $error->getDetails();

                            $log = sprintf('Finance InvalidArgumentException listFinancialEventGroups Failed. code:%s msg:%s detail:%s merchant_id: %s merchant_store_id: %s ', $code, $msg, $detail, $merchant_id, $merchant_store_id);
                            $console->error($log);
                            $logger->error($log);
                        }
                        break;
                    }

                    $payload = $response->getPayload();
                    if (is_null($payload)) {
                        break;
                    }

                    //TODO
                    $financialEvents = $payload->getFinancialEvents();
                    if (is_null($financialEvents)) {
                        break;
                    }

                    $eventList = [
                        ShipmentEventList::class => $financialEvents->getShipmentEventList(),
//                        ShipmentSettleEventList::class => $financialEvents->getShipmentSettleEventList(),
//                        RefundEventList::class => $financialEvents->getRefundEventList(),
//                        GuaranteeClaimEventList::class => $financialEvents->getGuaranteeClaimEventList(),
//                        ChargebackEventList::class => $financialEvents->getChargebackEventList(),
//                        PayWithAmazonEventList::class => $financialEvents->getPayWithAmazonEventList(),
//                        ServiceProviderCreditEventList::class => $financialEvents->getServiceProviderCreditEventList(),
//                        RetrochargeEventList::class => $financialEvents->getRetrochargeEventList(),
//                        RentalTransactionEventList::class => $financialEvents->getRentalTransactionEventList(),
//                        ProductAdsPaymentEventList::class => $financialEvents->getProductAdsPaymentEventList(),
//                        ServiceFeeEventList::class => $financialEvents->getServiceFeeEventList(),
//                        SellerDealPaymentEventList::class => $financialEvents->getSellerDealPaymentEventList(),
//                        DebtRecoveryEventList::class => $financialEvents->getDebtRecoveryEventList(),
//                        LoanServicingEventList::class => $financialEvents->getLoanServicingEventList(),
//                        AdjustmentEventList::class => $financialEvents->getAdjustmentEventList(),
//                        SafetReimbursementEventList::class => $financialEvents->getSafetReimbursementEventList(),
//                        SellerReviewEnrollmentPaymentEventList::class => $financialEvents->getSellerReviewEnrollmentPaymentEventList(),
//                        FbaLiquidationEventList::class => $financialEvents->getFbaLiquidationEventList(),
//                        CouponPaymentEventList::class => $financialEvents->getCouponPaymentEventList(),
//                        ImagingServicesFeeEventList::class => $financialEvents->getImagingServicesFeeEventList(),
//                        NetworkComminglingTransactionEventList::class => $financialEvents->getNetworkComminglingTransactionEventList(),
//                        AffordabilityExpenseEventList::class => $financialEvents->getAffordabilityExpenseEventList(),
//                        AffordabilityExpenseReversalEventList::class => $financialEvents->getAffordabilityExpenseReversalEventList(),
//                        RemovalShipmentEventList::class => $financialEvents->getRemovalShipmentEventList(),
//                        RemovalShipmentAdjustmentEventList::class => $financialEvents->getRemovalShipmentAdjustmentEventList(),
//                        TrialShipmentEventList::class => $financialEvents->getTrialShipmentEventList(),
//                        TdsReimbursementEventList::class => $financialEvents->getTdsReimbursementEventList(),
//                        AdhocDisbursementEventList::class => $financialEvents->getAdhocDisbursementEventList(),
//                        TaxWithholdingEventList::class => $financialEvents->getTaxWithholdingEventList(),
//                        ChargeRefundEventList::class => $financialEvents->getChargeRefundEventList(),
//                        FailedAdhocDisbursementEventList::class => $financialEvents->getFailedAdhocDisbursementEventList(),
//                        ValueAddedServiceChargeEventList::class => $financialEvents->getValueAddedServiceChargeEventList(),
//                        CapacityReservationBillingEventList::class => $financialEvents->getCapacityReservationBillingEventList(),
                    ];

                    $dag = new Dag();
                    foreach ($eventList as $eventName => $financialEvents) {
                        $dag->addVertex(\Hyperf\Dag\Vertex::make(static function () use ($merchant_id, $merchant_store_id, $eventName, $financialEvents) {
                            $finance = FinanceFactory::getInstance($merchant_id, $merchant_store_id, $eventName);
                            $finance->run($financialEvents);
                        }));
                    }
                    $dag->run();

                    //如果下一页没有数据，nextToken 会变成null
                    $next_token = $payload->getNextToken();
                    if (is_null($next_token)) {
                        var_dump('当前财务组数据已处理完成');
                        break;
                    }

                    var_dump('获取下一页数据');
                } catch (ApiException $e) {
                    $retry--;
                    if ($retry > 0) {
                        $console->warning(sprintf('Finance ApiException listFinancialEventsByGroupId Failed. retry:%s merchant_id: %s merchant_store_id: %s ', $retry, $merchant_id, $merchant_store_id));
                        sleep(10);
                        continue;
                    }
                    break;
                } catch (InvalidArgumentException $e) {
                    $log = sprintf('Finance InvalidArgumentException listFinancialEventsByGroupId Failed. merchant_id: %s merchant_store_id: %s ', $merchant_id, $merchant_store_id);
                    $console->error($log);
                    $logger->error($log);
                    break;
                }
            }

            return true;
        });

        return true;
    }

    public function safetyLine(): int
    {
        return 70;
    }
}