<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Listings;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\Marketplace;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonInventoryModel;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonSalesGetOrderMetricsLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class GetListingsItem extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:listings:get-listings-item');
    }

    public function configure(): void
    {
        parent::configure();

        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->addOption('asin', null, InputOption::VALUE_OPTIONAL, 'asin集合', '')
            ->setDescription('Amazon Listings Get Listings Item Command');
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
        $asin = (string) $this->input->getOption('asin');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($asin) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonSalesGetOrderMetricsLog::class);

            $seller_id = $amazonSDK->getSellerId();
            $amazonInventoryCollections = AmazonInventoryModel::query()->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->when($asin, function ($query, $asin) {
                    return $query->where('asin', $asin);
                })->get();
            if ($amazonInventoryCollections->isEmpty()) {
                $console->warning('库存没有符合条件的数据');
                return true;
            }

            $retry = 10;

            foreach ($amazonInventoryCollections as $amazonInventoryCollection) {
                $asin = $amazonInventoryCollection->asin;
                $seller_sku = $amazonInventoryCollection->seller_sku;
                $country_ids = $amazonInventoryCollection->country_ids;

                foreach ($marketplace_ids as $marketplace_id) {

//                    if ($marketplace_id !== Marketplace::US()->id()) {
//                        continue;
//                    }

                    while (true) {
                        $console->info(sprintf('GetListingsItem merchant_id:%s merchant_store_id:%s marketplace_id:%s asin:%s', $merchant_id, $merchant_store_id, $marketplace_id, $asin));

                        try {
                            $response = $sdk->listingsItems()->getListingsItem($accessToken, $region, $seller_id, $seller_sku, [$marketplace_id], null, null);
                            $seller_sku = $response->getSku();
                            $summaries = $response->getSummaries();
                            if (! is_null($summaries)) {
                                foreach ($summaries as $summary) {
                                    $marketplace_id = $summary->getMarketplaceId();
                                    $asin = $summary->getAsin();
                                    $product_type = $summary->getProductType();
                                    $condition_type = $summary->getConditionType() ?? '';
                                    $status = $summary->getStatus();
                                    $fn_sku = $summary->getFnSku() ?? '';
                                    $item_name = $summary->getItemName();
                                    $created_date = $summary->getCreatedDate()->format('Y-m-d H:i:s');
                                    $last_updated_date = $summary->getLastUpdatedDate()->format('Y-m-d H:i:s');
                                    $itemImage = $summary->getMainImage();
                                    $link = '';
                                    if (! is_null($itemImage)) {
                                        $link = $itemImage->getLink();
                                        $height = $itemImage->getHeight();
                                        $width = $itemImage->getWidth();
                                    }
//                                    var_dump($asin);
//                                    var_dump($product_type);
//                                    var_dump($condition_type);
//                                    var_dump($status);
//                                    var_dump($fn_sku);
//                                    var_dump($item_name);
//                                    var_dump($created_date);
//                                    var_dump($last_updated_date);

                                    $country_id = Marketplace::fromId($marketplace_id)->countryCode();
                                    if (! Str::contains($country_ids, $country_id)) {
                                        $country_ids = trim($country_ids . ',' . $country_id, ',');
                                    }
                                    if ($marketplace_id === Marketplace::US()->id()) {
                                        $amazonInventoryCollection->main_image = $link;
                                        $amazonInventoryCollection->product_type = $product_type;
                                        $amazonInventoryCollection->created_date = $created_date;
                                        $amazonInventoryCollection->last_updated_date = $last_updated_date;
                                    }

                                    $amazonInventoryCollection->country_ids = $country_ids;

                                    $amazonInventoryCollection->save();

                                    break 2;
                                }
                            }
//                            var_dump($response->getAttributes());
//                            var_dump($response->getIssues());
//                            var_dump($response->getOffers());
//                            var_dump($response->getFulfillmentAvailability());
//                            var_dump($response->getProcurement());

                            break;

                        } catch (ApiException $e) {
                            if (! is_null($e->getResponseBody())) {
                                $body = json_decode($e->getResponseBody(), true, 512, JSON_THROW_ON_ERROR);
                                if (isset($body['errors'])) {
                                    $errors = $body['errors'];
                                    foreach ($errors as $error) {
                                        if ($error['code'] === 'NOT_FOUND') {
                                            $console->warning(sprintf('GetListingsItem merchant_id:%s merchant_store_id:%s asin:%s error:%s', $merchant_id, $merchant_store_id, $asin, $error['message']));
                                            break 2;
                                        }
                                    }
                                }
                            }

                            --$retry;
                            if ($retry > 0) {
                                $console->warning(sprintf('GetListingsItem merchant_id:%s merchant_store_id:%s asin:%s retry:%s', $merchant_id, $merchant_store_id, $asin, $retry));
                                continue;
                            }

                            $log = sprintf('GetListingsItem 重试机会已耗尽. merchant_id:%s merchant_store_id:%s asin:%s', $merchant_id, $merchant_store_id, $asin);
                            $console->error($log, [
                                'message' => $e->getMessage(),
                                'response body' => $e->getResponseBody(),
                            ]);
                            $logger->error($log, [
                                'message' => $e->getMessage(),
                                'response body' => $e->getResponseBody(),
                            ]);
                            $retry = 10;
                            break;
                        } catch (InvalidArgumentException $e) {
                            $log = 'GetOrderMetrics 请求出错 InvalidArgumentException %s merchant_id:% merchant_store_id:%s ' . $e->getMessage();
                            $console->error($log);
                            break;
                        }
                    }
                }
            }

            return true;
        });
    }

}
