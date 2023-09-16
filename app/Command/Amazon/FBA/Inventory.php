<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\FBA;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonInventoryModel;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonFbaInventoryLog;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class Inventory extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fba:inventory');
    }

    public function configure(): void
    {
        parent::configure();
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon FBA Inventory Command');
    }


    /**
     * @throws ApiException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \JsonException
     */
    public function handle()
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {
            $logger = ApplicationContext::getContainer()->get(AmazonFbaInventoryLog::class);

            $startDate = new \DateTime();
            $startDate->setDate(2022, 03, 01)->setTime(00, 00, 00);

            $seller_skus = null; // 最多50个
            $granularity_type = 'Marketplace';

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

            $now = Carbon::now()->format('Y-m-d H:i:s');

            foreach ($marketplace_ids as $marketplace_id) {
                $retry = 30;
                $nextToken = null;

                while (true) {
                    $asin_list = [];

                    $collections = new Collection();

                    try {
                        $response = $sdk->fbaInventory()->getInventorySummaries($accessToken, $region, $granularity_type, $marketplace_id, [$marketplace_id], true, $startDate, $seller_skus, $nextToken);
                        $payload = $response->getPayload();
                        if ($payload === null) {
                            break;
                        }
                        if (! is_null($response->getErrors())) {
                            break;
                        }

                        $summaries = $payload->getInventorySummaries();

                        foreach ($summaries as $summary) {
                            $asin = $summary->getAsin();
                            $fn_sku = $summary->getFnSku();
                            $seller_sku = $summary->getSellerSku();
                            $condition = $summary->getCondition();

                            $fulfillable_quantity = 0;
                            $inbound_working_quantity = 0;
                            $inbound_shipped_quantity = 0;
                            $inbound_receiving_quantity = 0;
                            $total_reserved_quantity = 0;
                            $pending_customer_order_quantity = 0;
                            $pending_transshipment_quantity = 0;
                            $fc_processing_quantity = 0;
                            $total_researching_quantity = 0;
                            $researching_quantity_breakdown = [];
                            $researching_quantity_in_short_term = 0;
                            $researching_quantity_in_mid_term = 0;
                            $researching_quantity_in_long_term = 0;
                            $total_unfulfillable_quantity = 0;
                            $customer_damaged_quantity = 0;
                            $warehouse_damaged_quantity = 0;
                            $distributor_damaged_quantity = 0;
                            $carrier_damaged_quantity = 0;
                            $defective_quantity = 0;
                            $expired_quantity = 0;
                            $last_updated_time = 0;
                            $total_quantity = 0;

                            $inventoryDetails = $summary->getInventoryDetails();
                            $inventoryDetailsJson = [];
                            if ($inventoryDetails) {
                                $fulfillable_quantity = $inventoryDetails->getFulfillableQuantity() ?: 0; // 可拣选，包装，运输的货品数
                                $inbound_working_quantity = $inventoryDetails->getInboundWorkingQuantity() ?: 0; // 通知亚马逊入库的货品数
                                $inbound_shipped_quantity = $inventoryDetails->getInboundShippedQuantity() ?: 0; // 通知亚马逊并有物流跟踪号的货品数
                                $inbound_receiving_quantity = $inventoryDetails->getInboundReceivingQuantity() ?: 0; // 亚马逊物流未处理的入库货数

                                $reservedQuantity = $inventoryDetails->getReservedQuantity();
                                //                                $reservedQuantityJson = [];
                                if ($reservedQuantity) {
                                    $total_reserved_quantity = $reservedQuantity->getTotalReservedQuantity() ?: 0; // 开始配送。正在包装，运输等动态状态的货数
                                    $pending_customer_order_quantity = $reservedQuantity->getPendingCustomerOrderQuantity() ?: 0; // 为客户订单保留的货品数
                                    $pending_transshipment_quantity = $reservedQuantity->getPendingTransshipmentQuantity() ?: 0; // 从亚马逊库存转移到另一个亚马逊库存的货品数
                                    $fc_processing_quantity = $reservedQuantity->getFcProcessingQuantity() ?: 0; // 被亚马逊物流搁置以进行其他处理的货品数
                                    //                                    $reservedQuantityJson = [
                                    //                                        'total_reserved_quantity' => $total_reserved_quantity,
                                    //                                        'pending_customer_order_quantity' => $pending_customer_order_quantity,
                                    //                                        'pending_transshipment_quantity' => $pending_transshipment_quantity,
                                    //                                        'fc_processing_quantity' => $fc_processing_quantity,
                                    //                                    ];
                                }

                                $researchingQuantity = $inventoryDetails->getResearchingQuantity();
                                $researchingQuantityJson = [];
                                if ($researchingQuantity) {
                                    $total_researching_quantity = $researchingQuantity->getTotalResearchingQuantity() ?: 0; // 放错位置或损坏的货品总数
                                    $researchingQuantityBreakdowns = $researchingQuantity->getResearchingQuantityBreakdown() ?: []; // 正在判断是否放错位置或损坏的货品总数和货品名称
                                    if ($researchingQuantityBreakdowns) {
                                        foreach ($researchingQuantityBreakdowns as $researchingQuantityBreakdown) {
                                            $name = $researchingQuantityBreakdown->getName();
                                            $quantity = $researchingQuantityBreakdown->getQuantity();

                                            match ($name) {
                                                'researchingQuantityInShortTerm' => $researching_quantity_in_short_term = $quantity,
                                                'researchingQuantityInMidTerm' => $researching_quantity_in_mid_term = $quantity,
                                                'researchingQuantityInLongTerm' => $researching_quantity_in_long_term = $quantity,
                                            };
                                            //                                            $researching_quantity_breakdown[] = [
                                            //                                                'name' => $name,
                                            //                                                'quantity' => $quantity
                                            //                                            ];
                                        }
                                    }
                                    //                                    $researchingQuantityJson = [
                                    //                                        'total_researching_quantity' => $total_researching_quantity,
                                    //                                        'researching_quantity_breakdown' => $researchingQuantityBreakdownArr
                                    //                                    ];
                                }

                                $unfulfillableQuantity = $inventoryDetails->getUnfulfillableQuantity();
                                $unfulfillableQuantityJson = [];
                                if ($unfulfillableQuantity) {
                                    $total_unfulfillable_quantity = $unfulfillableQuantity->getTotalUnfulfillableQuantity() ?: 0; // 库存中不可售的货品数
                                    $customer_damaged_quantity = $unfulfillableQuantity->getCustomerDamagedQuantity() ?: 0; // 客户损坏的货品数
                                    $warehouse_damaged_quantity = $unfulfillableQuantity->getWarehouseDamagedQuantity() ?: 0; // 损坏的货品总数
                                    $distributor_damaged_quantity = $unfulfillableQuantity->getDistributorDamagedQuantity() ?: 0; // 亚马逊配送途中损坏的货品数
                                    $carrier_damaged_quantity = $unfulfillableQuantity->getCarrierDamagedQuantity() ?: 0; // 承运人损坏的货品数
                                    $defective_quantity = $unfulfillableQuantity->getDefectiveQuantity() ?: 0; // 正在处理的损坏的货品数
                                    $expired_quantity = $unfulfillableQuantity->getExpiredQuantity() ?: 0; // 已过期的货品数

                                    //                                    $unfulfillableQuantityJson = [
                                    //                                        'total_unfulfillable_quantity' => $total_unfulfillable_quantity,
                                    //                                        'customer_damaged_quantity' => $customer_damaged_quantity,
                                    //                                        'warehouse_damaged_quantity' => $warehouse_damaged_quantity,
                                    //                                        'distributor_damaged_quantity' => $distributor_damaged_quantity,
                                    //                                        'carrier_damaged_quantity' => $carrier_damaged_quantity,
                                    //                                        'defective_quantity' => $defective_quantity,
                                    //                                        'expired_quantity' => $expired_quantity,
                                    //                                    ];
                                }
                            }

                            $lastUpdatedTime = $summary->getLastUpdatedTime();
                            $last_updated_time = '';
                            if ($lastUpdatedTime) {
                                $last_updated_time = $lastUpdatedTime->format('Y-m-d H:i:s');
                            }

                            $product_name = $summary->getProductName() ?: '';

                            $total_quantity = $summary->getTotalQuantity() ?: 0;

                            $collections->offsetSet($asin, [
                                'merchant_id' => $merchant_id,
                                'merchant_store_id' => $merchant_store_id,
                                'asin' => $asin,
                                'fn_sku' => $fn_sku,
                                'seller_sku' => $seller_sku,
                                'product_name' => $product_name,
                                'condition' => $condition,
                                'fulfillable_quantity' => $fulfillable_quantity,
                                'inbound_working_quantity' => $inbound_working_quantity,
                                'inbound_shipped_quantity' => $inbound_shipped_quantity,
                                'inbound_receiving_quantity' => $inbound_receiving_quantity,
                                'total_reserved_quantity' => $total_reserved_quantity,
                                'pending_customer_order_quantity' => $pending_customer_order_quantity,
                                'pending_transshipment_quantity' => $pending_transshipment_quantity,
                                'fc_processing_quantity' => $fc_processing_quantity,
                                'total_researching_quantity' => $total_researching_quantity,
                                'researching_quantity_in_short_term' => $researching_quantity_in_short_term,
                                'researching_quantity_in_mid_term' => $researching_quantity_in_mid_term,
                                'researching_quantity_in_long_term' => $researching_quantity_in_long_term,
                                'total_unfulfillable_quantity' => $total_unfulfillable_quantity,
                                'customer_damaged_quantity' => $customer_damaged_quantity,
                                'warehouse_damaged_quantity' => $warehouse_damaged_quantity,
                                'distributor_damaged_quantity' => $distributor_damaged_quantity,
                                'carrier_damaged_quantity' => $carrier_damaged_quantity,
                                'defective_quantity' => $defective_quantity,
                                'expired_quantity' => $expired_quantity,
                                'last_updated_time' => $last_updated_time,
                                'total_quantity' => $total_quantity,
                                'country_ids' => $amazonSDK->fetchCountryFromMarketplaceId($marketplace_id),
                                'created_at' => $now,
                            ]);

                            $asin_list[] = $asin;
                        }
                        $existAmazonInventoryCollections = AmazonInventoryModel::query()
                            ->where('merchant_id', $merchant_id)
                            ->where('merchant_store_id', $merchant_store_id)
                            ->whereIn('asin', $asin_list)->get();

                        if ($existAmazonInventoryCollections->isEmpty()) {
                            AmazonInventoryModel::insert($collections->all());
                        } else {
                            foreach ($existAmazonInventoryCollections as $existAmazonInventoryCollection) {
                                $country_id = $amazonSDK->fetchCountryFromMarketplaceId($marketplace_id);

                                if ($collections->offsetExists($existAmazonInventoryCollection->asin)) {
                                    // update
                                    if (! str_contains($existAmazonInventoryCollection->country_ids, $country_id)) {
                                        $existAmazonInventoryCollection->country_ids = trim($existAmazonInventoryCollection->country_ids . ',' . $country_id, ',');
                                    }

                                    $existAmazonInventoryCollection->save();
                                } else {
                                    // delete -- 一般情况下不会走到这里
                                    $console->warning('merchant_id:%s merchant_store_id:%s asin:%s 被标记为删除，请检查');
                                }

                                $collections->offsetUnset($existAmazonInventoryCollection->asin);
                            }
                            // 需要新增的数据
                            if (! $collections->isEmpty()) {
                                AmazonInventoryModel::insert($collections->toArray());
                            }
                        }
                        $pagination = $response->getPagination();
                        if (is_null($pagination)) {
                            break;
                        }

                        $nextToken = $pagination->getNextToken();
                        if (is_null($nextToken)) {
                            break;
                        }
                    } catch (ApiException $exception) {
                        --$retry;
                        if ($retry > 0) {
                            $console->warning(sprintf('ApiException Inventory API retry:%s Exception:%s', $retry, $exception->getMessage()));
                            sleep(10);
                            continue;
                        }
                        $console->error('ApiException Inventory API 重试次数耗尽', [
                            'message' => $exception->getMessage(),
                            'trace' => $exception->getTraceAsString(),
                        ]);

                        $logger->error('ApiException Inventory API 重试次数耗尽', [
                            'message' => $exception->getMessage(),
                            'trace' => $exception->getTraceAsString(),
                        ]);

                        continue;
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
            }
        });
    }
}
