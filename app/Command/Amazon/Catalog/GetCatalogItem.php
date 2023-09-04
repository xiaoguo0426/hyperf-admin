<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Catalog;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonInventoryModel;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonCatalogLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetCatalogItem extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:catalog:get-catalog-items');
    }

    public function configure(): void
    {
        parent::configure();
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Get Catalog Command');
    }

    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws \JsonException
     * @throws \RedisException
     */
    public function handle()
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {
            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonCatalogLog::class);

            $amazonInventoryCollections = AmazonInventoryModel::query()->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->get();
            if ($amazonInventoryCollections->isEmpty()) {
                return true;
            }

            foreach ($amazonInventoryCollections as $amazonInventoryCollection) {
                $asin = $amazonInventoryCollection->asin;
                var_dump($asin);
                while (true) {
                    try {
                        // 指定日期范围内的财务事件组
                        $item = $sdk->catalogItem()->getCatalogItem($accessToken, $region, $asin, $marketplace_ids);

                        $asin = $item->getAsin();
                        $attributes = $item->getAttributes();
                        if (! is_null($attributes)) {
                            foreach ($attributes as $attribute) {
                                var_dump($attribute);
                            }
                        }
                        $dimensions = $item->getDimensions();
                        if (! is_null($dimensions)) {
                            foreach ($dimensions as $dimension) {
                                $dimension->getMarketplaceId();
                                $item = $dimension->getItem();
                                if (! is_null($item)) {
                                    $height = $item->getHeight();
                                    $height_unit = '';
                                    $height_value = '';
                                    if (! is_null($height)) {
                                        $height_unit = $height->getUnit();
                                        $height_value = $height->getValue();
                                    }
                                    var_dump($height_unit);
                                    var_dump($height_value);
                                    $length = $item->getLength();
                                    $length_unit = '';
                                    $length_value = '';
                                    if (! is_null($length)) {
                                        $length_unit = $length->getUnit();
                                        $length_value = $length->getValue();
                                    }
                                    var_dump($length_unit);
                                    var_dump($length_value);
                                    $weight = $item->getWeight();
                                    $weight_unit = '';
                                    $weight_value = '';
                                    if (! is_null($weight)) {
                                        $weight_unit = $weight->getUnit();
                                        $weight_value = $weight->getValue();
                                    }
                                    var_dump($weight_unit);
                                    var_dump($weight_value);
                                    $width = $item->getWidth();
                                    $width_unit = '';
                                    $width_value = '';
                                    if (! is_null($width)) {
                                        $width_unit = $width->getUnit();
                                        $width_value = $width->getValue();
                                    }
                                    var_dump($width_unit);
                                    var_dump($width_value);
                                }
                                $package = $dimension->getPackage();
                                if (! is_null($package)) {
                                    $height = $package->getHeight();
                                    $height_unit = '';
                                    $height_value = '';
                                    if (! is_null($height)) {
                                        $height_unit = $height->getUnit();
                                        $height_value = $height->getValue();
                                    }
                                    var_dump($height_unit);
                                    var_dump($height_value);
                                    $length = $package->getLength();
                                    $length_unit = '';
                                    $length_value = '';
                                    if (! is_null($length)) {
                                        $length_unit = $length->getUnit();
                                        $length_value = $length->getValue();
                                    }
                                    var_dump($length_unit);
                                    var_dump($length_value);
                                    $weight = $package->getWeight();
                                    $weight_unit = '';
                                    $weight_value = '';
                                    if (! is_null($weight)) {
                                        $weight_unit = $weight->getUnit();
                                        $weight_value = $weight->getValue();
                                    }
                                    var_dump($weight_unit);
                                    var_dump($weight_value);
                                    $width = $package->getWidth();
                                    $width_unit = '';
                                    $width_value = '';
                                    if (! is_null($width)) {
                                        $width_unit = $width->getUnit();
                                        $width_value = $width->getValue();
                                    }
                                    var_dump($width_unit);
                                    var_dump($width_value);
                                }
                            }
                        }

                        $itemIdentifiers = $item->getIdentifiers();
                        if (! is_null($itemIdentifiers)) {
                            foreach ($itemIdentifiers as $itemIdentifier) {
                                $marketplace_id = $itemIdentifier->getMarketplaceId();
                                $identifiers = $itemIdentifier->getIdentifiers();
                                foreach ($identifiers as $identifier) {
                                    $identifier_type = $identifier->getIdentifierType();
                                    $identifier_name = $identifier->getIdentifier();
                                    var_dump($identifier_type);
                                    var_dump($identifier_name);
                                }
                                var_dump($marketplace_id);
                            }
                        }
                        $itemImages = $item->getImages();
                        if (! is_null($itemImages)) {
                            foreach ($itemImages as $itemImage) {
                                $marketplace_id = $itemImage->getMarketplaceId();
                                $images = $itemImage->getImages();
                                foreach ($images as $image) {
                                    $image_variant = $image->getVariant();
                                    $image_link = $image->getLink();
                                    $image_height = $image->getHeight();
                                    $image_width = $image->getWidth();
                                    var_dump($image_variant);
                                    var_dump($image_link);
                                    var_dump($image_height);
                                    var_dump($image_width);
                                }
                                var_dump($marketplace_id);
                            }
                        }
                        $productTypes = $item->getProductTypes();
                        if (! is_null($productTypes)) {
                            foreach ($productTypes as $productType) {
                                $productType->getMarketplaceId();
                                $product_type = $productType->getProductType();
                                var_dump($product_type);
                            }
                        }
                        $relationships = $item->getRelationships();
                        if (! is_null($relationships)) {
                            foreach ($relationships as $relationship) {
                                $relationship->getMarketplaceId();
                                $itemRelationships = $relationship->getRelationships();
                                foreach ($itemRelationships as $itemRelationship) {
                                    $itemRelationship->getChildAsins();
                                    $itemRelationship->getParentAsins();
                                    $itemVariationTheme = $itemRelationship->getVariationTheme();
                                    if (! is_null($itemVariationTheme)) {
                                        $attributes = $itemVariationTheme->getAttributes();
                                        foreach ($attributes as $attribute) {
                                            var_dump($attribute);
                                        }
                                        $item_variation_theme = $itemVariationTheme->getTheme();
                                        var_dump($item_variation_theme);
                                    }
                                    $item_relation_ship_type = $itemRelationship->getType();
                                    var_dump($item_relation_ship_type);
                                }
                            }
                        }
                        $salesRanks = $item->getSalesRanks();
                        if (! is_null($salesRanks)) {
                            foreach ($salesRanks as $salesRank) {
                                $salesRank->getMarketplaceId();
                                $classificationRanks = $salesRank->getClassificationRanks();
                                if (! is_null($classificationRanks)) {
                                    foreach ($classificationRanks as $classificationRank) {
                                        $classification_id = $classificationRank->getClassificationId();
                                        $classification_title = $classificationRank->getTitle();
                                        $classification_link = $classificationRank->getLink();
                                        $classification_rank = $classificationRank->getRank();
                                        var_dump($classification_id);
                                        var_dump($classification_title);
                                        var_dump($classification_link);
                                        var_dump($classification_rank);
                                    }
                                }
                                $displayGroupRanks = $salesRank->getDisplayGroupRanks();
                                if (! is_null($displayGroupRanks)) {
                                    foreach ($displayGroupRanks as $displayGroupRank) {
                                        $display_group_rank_website = $displayGroupRank->getWebsiteDisplayGroup();
                                        $display_group_rank_title = $displayGroupRank->getTitle();
                                        $display_group_rank_link = $displayGroupRank->getLink();
                                        $display_group_rank = $displayGroupRank->getRank();
                                        var_dump($display_group_rank_website);
                                        var_dump($display_group_rank_title);
                                        var_dump($display_group_rank_link);
                                        var_dump($display_group_rank);
                                    }
                                }
                            }
                        }
                        $summaries = $item->getSummaries();
                        if (! is_null($summaries)) {
                            foreach ($summaries as $summary) {
                                $summary_marketplace_id = $summary->getMarketplaceId();
                                $summary_adult_product = $summary->getAdultProduct();
                                $summary_autographed = $summary->getAutographed();
                                $summary_brand = $summary->getBrand();
                                $summaryBrowseClassification = $summary->getBrowseClassification();
                                $summary_browse_classification_display_name = '';
                                $summary_browse_classification_id = '';
                                if (! is_null($summaryBrowseClassification)) {
                                    $summary_browse_classification_display_name = $summaryBrowseClassification->getDisplayName();
                                    $summary_browse_classification_id = $summaryBrowseClassification->getClassificationId();
                                }
                                $summary_color = $summary->getColor();
                                $summaryContributors = $summary->getContributors();
                                if (! is_null($summaryContributors)) {
                                    foreach ($summaryContributors as $summaryContributor) {
                                        $summaryContributorRole = $summaryContributor->getRole();
                                        $summary_contributor_role_display_name = $summaryContributorRole->getDisplayName();
                                        $summary_contributor_role_value = $summaryContributorRole->getValue();
                                    }
                                }
                                $summary_item_classification = $summary->getItemClassification();
                                $summary_item_name = $summary->getItemName();
                                $summary_memorabilia = $summary->getMemorabilia();
                                $summary_model_number = $summary->getModelNumber();
                                $summary_package_quantity = $summary->getPackageQuantity();
                                $summary_part_number = $summary->getPartNumber();
                                $summary_release_date = $summary->getReleaseDate();
                                $summary_size = $summary->getSize();
                                $summary_style = $summary->getStyle();
                                $summary_trade_in_eligible = $summary->getTradeInEligible();
                                $summary_website_display_group = $summary->getWebsiteDisplayGroup();
                                $summary_website_display_group_name = $summary->getWebsiteDisplayGroupName();

                                var_dump($summary_item_classification);
                                var_dump($summary_item_name);
                                var_dump($summary_memorabilia);
                                var_dump($summary_model_number);
                                var_dump($summary_package_quantity);
                                var_dump($summary_part_number);
                                var_dump($summary_release_date);
                                var_dump($summary_size);
                                var_dump($summary_style);
                                var_dump($summary_trade_in_eligible);
                                var_dump($summary_website_display_group);
                                var_dump($summary_website_display_group_name);
                            }
                        }
                        $vendorDetails = $item->getVendorDetails();
                        if (! is_null($vendorDetails)) {
                            foreach ($vendorDetails as $vendorDetail) {
                                $vendor_detail_marketplace_id = $vendorDetail->getMarketplaceId();
                                $vendor_detail_brand_code = $vendorDetail->getBrandCode();
                                $vendor_detail_manufacturer_code = $vendorDetail->getManufacturerCode();
                                $vendor_detail_manufacturer_code_parent = $vendorDetail->getManufacturerCodeParent();
                                $vendor_detail_product_category = $vendorDetail->getProductCategory();
                                $vendor_detail_product_group = $vendorDetail->getProductGroup();
                                $vendor_detail_product_subcategory = $vendorDetail->getProductSubcategory();
                                $vendor_detail_replenishment_category = $vendorDetail->getReplenishmentCategory();

                                var_dump($vendor_detail_marketplace_id);
                                var_dump($vendor_detail_brand_code);
                                var_dump($vendor_detail_manufacturer_code);
                                var_dump($vendor_detail_manufacturer_code_parent);
                                var_dump($vendor_detail_product_category);
                                var_dump($vendor_detail_product_group);
                                var_dump($vendor_detail_product_subcategory);
                                var_dump($vendor_detail_replenishment_category);
                            }
                        }

                        exit;
                        // 如果下一页没有数据，nextToken 会变成null
                        //                    $next_token = $payload->getNextToken();
                        //                    if (is_null($next_token)) {
                        //                        break;
                        //                    }
                    } catch (ApiException $e) {
                        $message = $e->getMessage();
                        //                    $retry--;
                        //                    if ($retry > 0) {
                        //                        $console->warning(sprintf('Finance ApiException listFinancialEventGroups Failed. retry:%s merchant_id: %s merchant_store_id: %s ', $retry, $merchant_id, $merchant_store_id));
                        //                        sleep(10);
                        //                        continue;
                        //                    }
                        break;
                    } catch (InvalidArgumentException $e) {
                        $log = sprintf('Finance InvalidArgumentException listFinancialEventGroups Failed. merchant_id: %s merchant_store_id: %s ', $merchant_id, $merchant_store_id);
                        $console->error($log);
                        $logger->error($log);
                        break;
                    }
                }
            }

            return true;
        });
    }
}
