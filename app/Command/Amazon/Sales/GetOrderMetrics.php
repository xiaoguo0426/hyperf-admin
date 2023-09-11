<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Sales;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Constants\AmazonConstants;
use App\Model\AmazonSalesOrderMetricsModel;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonSalesGetOrderMetricsLog;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Model\ModelNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetOrderMetrics extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:sales:get-order-metrics');
    }

    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Sales Get Order Metrics');
    }

    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonSalesGetOrderMetricsLog::class);

            $interval = sprintf('%s--%s', Carbon::now('UTC')->subDays(7)->format('Y-m-d\T00:00:00+00:00'), Carbon::yesterday('UTC')->format('Y-m-d\T23:59:59+00:00'));
//            $interval = '2023-01-01T00:00:00+00:00--2023-09-08T23:59:59+00:00';
            $granularity = AmazonConstants::INTERVAL_TYPE_DAY;
            $granularity_time_zone = 'UTC';
            $now = Carbon::now()->format('Y-m-d H:i:s');

            foreach ($marketplace_ids as $marketplace_id) {

                $console->info(sprintf('interval:%s merchant_id:%s merchant_store_id:%s', $interval, $merchant_id, $merchant_store_id));

                $collections = new Collection();

                $retry = 10;

                while (true) {

                    try {
                        //https://developer-docs.amazon.com/sp-api/docs/sales-api-v1-reference
                        $response = $sdk->sales()->getOrderMetrics($accessToken, $region, [$marketplace_id], $interval, $granularity, $granularity_time_zone, 'All', null, 'monday');
                        $payload = $response->getPayload();
                        if ($payload === null) {
                            break;
                        }

                        foreach ($payload as $orderMetricsInterval) {
                            $interval_new = str_replace('T', ' ', mb_substr($orderMetricsInterval->getInterval(), 0, 16));


                            try {
                                $model = AmazonSalesOrderMetricsModel::query()
                                    ->where('merchant_id', $merchant_id)
                                    ->where('merchant_store_id', $merchant_store_id)
                                    ->where('marketplace_id', $marketplace_id)
                                    ->where('interval', $interval_new)
                                    ->firstOrFail();
                            } catch (ModelNotFoundException $exception) {
                                $item = [
                                    'merchant_id' => $merchant_id,
                                    'merchant_store_id' => $merchant_store_id,
                                    'marketplace_id' => $marketplace_id,
                                    'interval_type' => $granularity,
                                    'interval' => $interval_new,
                                    'unit_count' => $orderMetricsInterval->getUnitCount(),
                                    'order_count' => $orderMetricsInterval->getOrderCount(),
                                    'order_item_count' => $orderMetricsInterval->getOrderItemCount(),
                                    'avg_unit_price_currency_code' => $orderMetricsInterval->getAverageUnitPrice()->getCurrencyCode(),
                                    'avg_unit_price' => $orderMetricsInterval->getAverageUnitPrice()->getAmount(),
                                    'total_sales_currency_code' => $orderMetricsInterval->getTotalSales()->getCurrencyCode(),
                                    'total_sales_amount' => $orderMetricsInterval->getTotalSales()->getAmount(),
                                    'created_at' => $now
                                ];
                                $collections->push($item);
                                continue;
                            }

                            $model->merchant_id = $merchant_id;
                            $model->merchant_store_id = $merchant_store_id;
                            $model->marketplace_id = $marketplace_id;
                            $model->interval_type = $granularity;
                            $model->interval = $interval_new;
                            $model->unit_count = $orderMetricsInterval->getUnitCount();
                            $model->order_count = $orderMetricsInterval->getOrderCount();
                            $model->order_item_count = $orderMetricsInterval->getOrderItemCount();
                            $model->avg_unit_price_currency_code = $orderMetricsInterval->getAverageUnitPrice()->getCurrencyCode();
                            $model->avg_unit_price = $orderMetricsInterval->getAverageUnitPrice()->getAmount();
                            $model->total_sales_currency_code = $orderMetricsInterval->getTotalSales()->getCurrencyCode();
                            $model->total_sales_amount = $orderMetricsInterval->getTotalSales()->getAmount();

                            $model->save();

                        }
                        break;

                    } catch (ApiException $e) {
                        --$retry;
                        if ($retry > 0) {
                            $console->warning(sprintf('GetOrderMetrics merchant_id:%s merchant_store_id:%s retry:%s', $merchant_id, $merchant_store_id, $retry));
                            continue;
                        }

                        $log = sprintf('GetOrderMetrics 重试机会已耗尽. merchant_id:%s merchant_store_id:%s', $merchant_id, $merchant_store_id);
                        $console->error($log);
                        $logger->error($log, [
                            'message' => $e->getMessage(),
                            'response body' => $e->getResponseBody(),
                        ]);

                        break;
                    } catch (InvalidArgumentException $e) {
                        $log = 'GetOrderMetrics 请求出错 %s merchant_id:% merchant_store_id:%s ' . $e->getMessage();
                        $console->error($log);
                        $logger->error($log, ['marketplace_id' => $marketplace_id]);
                        continue;
                    }
                }

                if ($collections->isEmpty()) {
                    continue;
                }

                AmazonSalesOrderMetricsModel::insert($collections->all());

            }

            return true;
        });
    }
}
