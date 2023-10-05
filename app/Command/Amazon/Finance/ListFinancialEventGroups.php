<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Finance;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Constants\AmazonConstants;
use App\Model\AmazonFinancialGroupModel;
use App\Queue\AmazonFinanceFinancialListEventsByGroupIdQueue;
use App\Queue\Data\AmazonFinanceListFinancialEventsByGroupIdData;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonFinanceLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class ListFinancialEventGroups extends HyperfCommand
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:finance:list-financial-event-groups');
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Finance List Financial Event Groups Command');
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

            $retry = 10;
            $max_results_per_page = 100;
            $financial_event_group_started_before = date_create_from_format(DATE_ATOM, date(DATE_ATOM, strtotime('now -1 year')));
            $financial_event_group_started_after = date_create_from_format(DATE_ATOM, date(DATE_ATOM, strtotime('now -3 minute')));
            $next_token = null;

            /**
             * @var AmazonFinanceListFinancialEventsByGroupIdData $queueData
             */
            $queueData = \Hyperf\Support\make(AmazonFinanceListFinancialEventsByGroupIdData::class);

            /**
             * @var AmazonFinanceFinancialListEventsByGroupIdQueue $queue
             */
            $queue = \Hyperf\Support\make(AmazonFinanceFinancialListEventsByGroupIdQueue::class);

            while (true) {
                try {
                    // 指定日期范围内的财务事件组
                    $response = $sdk->finances()->listFinancialEventGroups($accessToken, $region, $max_results_per_page, $financial_event_group_started_after, $financial_event_group_started_before, $next_token);

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

                    $financialEventGroupList = $payload->getFinancialEventGroupList();
                    foreach ($financialEventGroupList as $financialEventGroup) {
                        $financial_event_group_id = $financialEventGroup->getFinancialEventGroupId() ?? '';
                        if ($financial_event_group_id === '') {
                            continue;
                        }

                        $processing_status = $financialEventGroup->getProcessingStatus() ?? '';
                        $fund_transfer_status = $financialEventGroup->getFundTransferStatus() ?? '';

                        $originalTotal = $financialEventGroup->getOriginalTotal();
                        $original_total_currency_code = '';
                        $original_total_currency_amount = '';
                        if (! is_null($originalTotal)) {
                            $original_total_currency_code = $originalTotal->getCurrencyCode();
                            $original_total_currency_amount = $originalTotal->getCurrencyAmount();
                        }

                        $convertedTotal = $financialEventGroup->getConvertedTotal();
                        $converted_total_currency_code = '';
                        $converted_total_currency_amount = '';
                        if (! is_null($convertedTotal)) {
                            $converted_total_currency_code = $convertedTotal->getCurrencyCode();
                            $converted_total_currency_amount = $convertedTotal->getCurrencyAmount();
                        }

                        $fundTransferDate = $financialEventGroup->getFundTransferDate();
                        $fund_transfer_date = '';
                        if (! is_null($fundTransferDate)) {
                            $fund_transfer_date = $fundTransferDate->format('Y-m-d H:i:s');
                        }

                        $trace_id = $financialEventGroup->getTraceId() ?? '';
                        $account_tail = $financialEventGroup->getAccountTail() ?? '';

                        $beginningBalance = $financialEventGroup->getBeginningBalance();
                        $beginning_balance_currency_code = '';
                        $beginning_balance_currency_amount = '';
                        if (! is_null($beginningBalance)) {
                            $beginning_balance_currency_code = $beginningBalance->getCurrencyCode();
                            $beginning_balance_currency_amount = $beginningBalance->getCurrencyAmount();
                        }

                        $financialEventGroupStart = $financialEventGroup->getFinancialEventGroupStart();
                        $financial_event_group_start = '';
                        if (! is_null($financialEventGroupStart)) {
                            $financial_event_group_start = $financialEventGroupStart->format('Y-m-d H:i:s');
                        }

                        $financialEventGroupEnd = $financialEventGroup->getFinancialEventGroupEnd();
                        $financial_event_group_end = '';
                        if (! is_null($financialEventGroupEnd)) {
                            $financial_event_group_end = $financialEventGroupEnd->format('Y-m-d H:i:s');
                        }

                        $collection = AmazonFinancialGroupModel::query()->where('merchant_id', $merchant_id)
                            ->where('merchant_store_id', $merchant_store_id)
                            ->where('financial_event_group_id', $financial_event_group_id)
                            ->first();

                        $can_queue = false; // 是否可以入队
                        if (is_null($collection)) {
                            $collection = new AmazonFinancialGroupModel();
                            $collection->merchant_id = $merchant_id;
                            $collection->merchant_store_id = $merchant_store_id;
                            $collection->financial_event_group_id = $financial_event_group_id;
                            $collection->processing_status = $processing_status;
                            $collection->fund_transfer_status = $fund_transfer_status;
                            $collection->original_total_amount = $original_total_currency_amount;
                            $collection->original_total_code = $original_total_currency_code;
                            $collection->converted_total_amount = $converted_total_currency_amount;
                            $collection->converted_total_code = $converted_total_currency_code;
                            $collection->fund_transfer_date = $fund_transfer_date;
                            $collection->trace_id = $trace_id;
                            $collection->account_tail = $account_tail;
                            $collection->beginning_balance_amount = $beginning_balance_currency_amount;
                            $collection->beginning_balance_code = $beginning_balance_currency_code;
                            $collection->financial_event_group_start = $financial_event_group_start;
                            $collection->financial_event_group_end = $financial_event_group_end;

                            $can_queue = true;
                        } elseif ($processing_status === AmazonConstants::FINANCE_GROUP_PROCESS_STATUS_CLOSED && $processing_status !== $collection->processing_status) {
                            // 重新拉取该财务组数据
                            $can_queue = true;
                        }

                        $collection->save();

                        if ($can_queue) {
                            $queueData->setMerchantId($merchant_id);
                            $queueData->setMerchantStoreId($merchant_store_id);
                            $queueData->setFinancialEventGroupId($financial_event_group_id);

                            $queue->push($queueData);
                        }
                    }

                    // 如果下一页没有数据，nextToken 会变成null
                    $next_token = $payload->getNextToken();
                    if (is_null($next_token)) {
                        break;
                    }
                } catch (ApiException $e) {
                    --$retry;
                    if ($retry > 0) {
                        $console->warning(sprintf('Finance ApiException listFinancialEventGroups Failed. retry:%s merchant_id: %s merchant_store_id: %s ', $retry, $merchant_id, $merchant_store_id));
                        sleep(10);
                        continue;
                    }

                    $log = sprintf('Finance ApiException listFinancialEventGroups Failed. merchant_id: %s merchant_store_id: %s ', $merchant_id, $merchant_store_id);
                    $console->error($log);
                    $logger->error($log);
                    break;
                } catch (InvalidArgumentException $e) {
                    $log = sprintf('Finance InvalidArgumentException listFinancialEventGroups Failed. merchant_id: %s merchant_store_id: %s ', $merchant_id, $merchant_store_id);
                    $console->error($log);
                    $logger->error($log);
                    break;
                }
            }

            return true;
        });
    }
}
