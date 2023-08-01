<?php

namespace App\Command\Amazon\FBA;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonInventoryModel;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonFbaInventory;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;

#[Command]
class Inventory extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fba:inventory');
    }

    public function handle()
    {
        AmazonApp::process(static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {

            $logger = ApplicationContext::getContainer()->get(AmazonFbaInventory::class);

            $startDate = new \DateTime();
            $startDate->setDate(2022, 03, 01)->setTime(00, 00, 00);

            $seller_skus = null;//最多50个
            $granularity_type = 'Marketplace';

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

            foreach ($marketplace_ids as $marketplace_id) {
                $retry = 30;
                $nextToken = null;
                while (true) {
                    $inventories = [];
                    $asin_list = [];

                    try {
                        $response = $sdk->fbaInventory()->getInventorySummaries($accessToken, $region, $granularity_type, $marketplace_id, [$marketplace_id], true, $startDate, $seller_skus, $nextToken);
                        $payload = $response->getPayload();
                        if ($payload === null) {
                            break;
                        }
                        if (!is_null($response->getErrors())) {
                            break;
                        }

                        $summaries = $payload->getInventorySummaries();

                        foreach ($summaries as $summary) {
                            $asin = $summary->getAsin();
                            $fn_sku = $summary->getFnSku();
                            $seller_sku = $summary->getSellerSku();
                            $condition = $summary->getCondition();

                            $inventoryDetails = $summary->getInventoryDetails();
                            $inventoryDetailsJson = [];
                            if ($inventoryDetails) {
                                $fulfillable_quantity = $inventoryDetails->getFulfillableQuantity() ?: 0;//可拣选，包装，运输的货品数
                                $inbound_working_quantity = $inventoryDetails->getInboundWorkingQuantity() ?: 0;//通知亚马逊入库的货品数
                                $inbound_shipped_quantity = $inventoryDetails->getInboundShippedQuantity() ?: 0;//通知亚马逊并有物流跟踪号的货品数
                                $inbound_receiving_quantity = $inventoryDetails->getInboundReceivingQuantity() ?: 0;//亚马逊物流未处理的入库货数

                                $reservedQuantity = $inventoryDetails->getReservedQuantity();
                                $reservedQuantityJson = [];
                                if ($reservedQuantity) {
                                    $total_reserved_quantity = $reservedQuantity->getTotalReservedQuantity() ?: 0;//开始配送。正在包装，运输等动态状态的货数
                                    $pending_customer_order_quantity = $reservedQuantity->getPendingCustomerOrderQuantity() ?: 0;//为客户订单保留的货品数
                                    $pending_transshipment_quantity = $reservedQuantity->getPendingTransshipmentQuantity() ?: 0;//从亚马逊库存转移到另一个亚马逊库存的货品数
                                    $fc_processing_quantity = $reservedQuantity->getFcProcessingQuantity() ?: 0;//被亚马逊物流搁置以进行其他处理的货品数
                                    $reservedQuantityJson = [
                                        'total_reserved_quantity' => $total_reserved_quantity,
                                        'pending_customer_order_quantity' => $pending_customer_order_quantity,
                                        'pending_transshipment_quantity' => $pending_transshipment_quantity,
                                        'fc_processing_quantity' => $fc_processing_quantity,
                                    ];
                                }


                                $researchingQuantity = $inventoryDetails->getResearchingQuantity();
                                $researchingQuantityJson = [];
                                if ($researchingQuantity) {
                                    $total_researching_quantity = $researchingQuantity->getTotalResearchingQuantity() ?: 0;//放错位置或损坏的货品总数
                                    $researchingQuantityBreakdowns = $researchingQuantity->getResearchingQuantityBreakdown() ?: [];//正在判断是否放错位置或损坏的货品总数和货品名称
                                    $researchingQuantityBreakdownArr = [];
                                    if ($researchingQuantityBreakdowns) {
                                        foreach ($researchingQuantityBreakdowns as $researchingQuantityBreakdown) {
                                            $name = $researchingQuantityBreakdown->getName();
                                            $quantity = $researchingQuantityBreakdown->getQuantity();
                                            $researchingQuantityBreakdownArr[] = [
                                                'name' => $name,
                                                'quantity' => $quantity
                                            ];
                                        }
                                    }
                                    $researchingQuantityJson = [
                                        'total_researching_quantity' => $total_researching_quantity,
                                        'researching_quantity_breakdown' => $researchingQuantityBreakdownArr
                                    ];
                                }

                                $unfulfillableQuantity = $inventoryDetails->getUnfulfillableQuantity();
                                $unfulfillableQuantityJson = [];
                                if ($unfulfillableQuantity) {
                                    $total_unfulfillable_quantity = $unfulfillableQuantity->getTotalUnfulfillableQuantity() ?: 0;//库存中不可售的货品数
                                    $customer_damaged_quantity = $unfulfillableQuantity->getCustomerDamagedQuantity() ?: 0;//客户损坏的货品数
                                    $warehouse_damaged_quantity = $unfulfillableQuantity->getWarehouseDamagedQuantity() ?: 0;//损坏的货品总数
                                    $distributor_damaged_quantity = $unfulfillableQuantity->getDistributorDamagedQuantity() ?: 0;//亚马逊配送途中损坏的货品数
                                    $carrier_damaged_quantity = $unfulfillableQuantity->getCarrierDamagedQuantity() ?: 0;//承运人损坏的货品数
                                    $defective_quantity = $unfulfillableQuantity->getDefectiveQuantity() ?: 0;//正在处理的损坏的货品数
                                    $expired_quantity = $unfulfillableQuantity->getExpiredQuantity() ?: 0;//已过期的货品数

                                    $unfulfillableQuantityJson = [
                                        'total_unfulfillable_quantity' => $total_unfulfillable_quantity,
                                        'customer_damaged_quantity' => $customer_damaged_quantity,
                                        'warehouse_damaged_quantity' => $warehouse_damaged_quantity,
                                        'distributor_damaged_quantity' => $distributor_damaged_quantity,
                                        'carrier_damaged_quantity' => $carrier_damaged_quantity,
                                        'defective_quantity' => $defective_quantity,
                                        'expired_quantity' => $expired_quantity,
                                    ];
                                }

                                $inventoryDetailsJson = [
                                    'fulfillable_quantity' => $fulfillable_quantity,
                                    'inbound_working_quantity' => $inbound_working_quantity,
                                    'inbound_shipped_quantity' => $inbound_shipped_quantity,
                                    'inbound_receiving_quantity' => $inbound_receiving_quantity,
                                    'reserved_quantity' => $reservedQuantityJson,
                                    'researching_quantity' => $researchingQuantityJson,
                                    'unfulfillable_quantity' => $unfulfillableQuantityJson,
                                ];
                            }

                            $lastUpdatedTime = $summary->getLastUpdatedTime();
                            $last_updated_time = '';
                            if ($lastUpdatedTime) {
                                $last_updated_time = $lastUpdatedTime->format('Y-m-d H:i:s');
                            }

                            $product_name = $summary->getProductName() ?: '';
                            $total_quantity = $summary->getTotalQuantity() ?: 0;

                            $inventories[$asin] = [
                                'merchant_id' => $merchant_id,
                                'merchant_store_id' => $merchant_store_id,
                                'asin' => $asin,
                                'fn_sku' => $fn_sku,
                                'seller_sku' => $seller_sku,
                                'condition' => $condition,
                                'inventory_details' => json_encode($inventoryDetailsJson, JSON_THROW_ON_ERROR),
                                'last_updated_time' => $last_updated_time,
                                'product_name' => $product_name,
                                'total_quantity' => $total_quantity,
                                'country_ids' => $amazonSDK->fetchCountryFromMarketplaceId($marketplace_id)
                            ];
                            $asin_list[] = $asin;
                        }
                        $amazonInventoryCollections = AmazonInventoryModel::query()
                            ->where('merchant_id', $merchant_id)
                            ->where('merchant_store_id', $merchant_store_id)
                            ->whereIn('asin', $asin_list)->get();

                        if ($amazonInventoryCollections->isEmpty()) {
                            AmazonInventoryModel::insert($inventories);
                        } else {
                            $exist_asin_list = $amazonInventoryCollections->columns('asin');

                            $diff_asin_list = array_diff($asin_list, $exist_asin_list);

                            $need_to_add_collections = [];
                            foreach ($diff_asin_list as $new_asin) {
                                $need_to_add_collections[] = $inventories[$new_asin];
                            }

                            if ($diff_asin_list) {
                                $insert = AmazonInventoryModel::insert($need_to_add_collections);

                                if (!$insert) {
                                    $log = sprintf('批量插入数据失败 merchant_id:%s merchant_store_id:%s', $merchant_id, $merchant_store_id);

                                    $console->error($log);
                                    $logger->error($log);
                                }
                            }

                            foreach ($amazonInventoryCollections as $amazonInventoryCollection) {
                                if (!empty($inventories[$amazonInventoryCollection->asin])) {
                                    if ($inventories[$amazonInventoryCollection->asin]['last_updated_time'] !== $amazonInventoryCollection->last_updated_time) {
                                        $update = $amazonInventoryCollection->update($inventories[$amazonInventoryCollection->asin]);
                                        var_dump($update);
                                    }
                                }
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
                        $retry--;
                        if ($retry <= 0) {
                            break;
                        }
                        continue;
                    } catch (InvalidArgumentException $exception) {
                        $logger->error('API请求错误', [
                            'message' => $exception->getMessage(),
                            'trace' => $exception->getTraceAsString()
                        ]);
                    }

                }
            }
        });
    }
}