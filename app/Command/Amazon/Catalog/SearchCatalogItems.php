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
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonCatalogLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class SearchCatalogItems extends HyperfCommand
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:catalog:search-catalog-items');
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Search Catalog Items Command');
    }

    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {
            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonCatalogLog::class);
            $identifiers = null;
            $identifiers_type = null;
            $included_data = null;
            $locale = null;
            $seller_id = null;//TODO
            $keywords = null;
            $brand_names = null;
            $classification_ids = null;
            $page_size = 10;
            $page_token = null;
            $keywords_locale = null;

            $retry = 10;
            while (true) {
                try {
                    $itemSearchResults = $sdk->catalogItem()->searchCatalogItems($accessToken, $region, $marketplace_ids, $identifiers, $identifiers_type, $included_data, $locale, $seller_id, $keywords, $brand_names, $classification_ids, $page_size, $page_token, $keywords_locale);

                    $number_of_results = $itemSearchResults->getNumberOfResults();

                    $refinements = $itemSearchResults->getRefinements();
                    $classification_refinements = [];
                    $brands_refinements = [];
                    if (! is_null($refinements)) {
                        $brandRefinements = $refinements->getBrands();
                        foreach ($brandRefinements as $brandRefinement) {
                            $brands_refinements[] = [
                                'number_of_results' => $brandRefinement->getNumberOfResults(),
                                'brand_name' => $brandRefinement->getBrandName(),
                            ];
                        }
                        $classificationRefinements = $refinements->getClassifications();
                        foreach ($classificationRefinements as $classificationRefinement) {
                            $number_of_results = $classificationRefinement->getNumberOfResults();
                            $display_name = $classificationRefinement->getDisplayName();
                            $classification_id = $classificationRefinement->getClassificationId();
                            $classification_refinements[] = [
                                'number_of_results' => $number_of_results,
                                'display_name' => $display_name,
                                'classification_id' => $classification_id,
                            ];
                        }
                    }

                    $items_list = [];
                    $items = $itemSearchResults->getItems();
                    foreach ($items as $item) {
                        $asin = $item->getAsin();
                        $attributes = $item->getAttributes();

                        $itemDimensionsByMarketplace = $item->getDimensions();
                        $dimensions = [];
                        if (! is_null($itemDimensionsByMarketplace)) {
                            foreach ($itemDimensionsByMarketplace as $itemDimensionByMarketplace) {
                                $marketplace_id = $itemDimensionByMarketplace->getMarketplaceId();
                                $item = $itemDimensionByMarketplace->getItem();
                                $item_height_unit = '';
                                $item_height = '';
                                $item_length_unit = '';
                                $item_length = '';
                                $item_weight_unit = '';
                                $item_weight = '';
                                $item_width_unit = '';
                                $item_width = '';
                                if (! is_null($item)) {
                                    $itemHeight = $item->getHeight();
                                    if (! is_null($itemHeight)) {
                                        $item_height_unit = $itemHeight->getUnit() ?? '';
                                        $item_height = $itemHeight->getValue() ?? 0.00;
                                    }
                                    $itemLength = $item->getLength();
                                    if (! is_null($itemLength)) {
                                        $item_length_unit = $itemLength->getUnit() ?? '';
                                        $item_length = $itemLength->getValue() ?? 0.00;
                                    }
                                    $itemWeight = $item->getWeight();
                                    if (! is_null($itemWeight)) {
                                        $item_weight_unit = $itemWeight->getUnit() ?? '';
                                        $item_weight = $itemWeight->getValue() ?? 0.00;
                                    }
                                    $itemWidth = $item->getWidth();
                                    if (! is_null($itemWidth)) {
                                        $item_width_unit = $itemWidth->getUnit() ?? '';
                                        $item_width = $itemWidth->getValue() ?? 0.00;
                                    }
                                }

                                $package = $itemDimensionByMarketplace->getPackage();
                                $package_height_unit = '';
                                $package_height = '';
                                $package_length_unit = '';
                                $package_length = '';
                                $package_weight_unit = '';
                                $package_weight = '';
                                $package_width_unit = '';
                                $package_width = '';
                                if (! is_null($package)) {
                                    $packageHeight = $package->getHeight();
                                    if (! is_null($packageHeight)) {
                                        $package_height_unit = $packageHeight->getUnit() ?? '';
                                        $package_height = $packageHeight->getValue() ?? 0.00;
                                    }
                                    $packageLength = $package->getLength();
                                    if (! is_null($packageLength)) {
                                        $package_length_unit = $packageLength->getUnit() ?? '';
                                        $package_length = $packageLength->getValue() ?? 0.00;
                                    }
                                    $packageWeight = $package->getWeight();
                                    if (! is_null($packageWeight)) {
                                        $package_weight_unit = $packageWeight->getUnit() ?? '';
                                        $package_weight = $packageWeight->getValue() ?? 0.00;
                                    }
                                    $packageWidth = $package->getWidth();
                                    if (! is_null($packageWidth)) {
                                        $package_width_unit = $packageWidth->getUnit() ?? '';
                                        $package_width = $packageWidth->getValue() ?? 0.00;
                                    }
                                }

                                $dimensions[] = [
                                    'marketplace_id' => $marketplace_id,
                                    'item' => [
                                        'height_unit' => $item_height_unit,
                                        'height' => $item_height,
                                        'length_unit' => $item_length_unit,
                                        'length' => $item_length,
                                        'weight_unit' => $item_weight_unit,
                                        'weight' => $item_weight,
                                        'width_unit' => $item_width_unit,
                                        'width' => $item_width,
                                    ],
                                    'package' => [
                                        'height_unit' => $package_height_unit,
                                        'height' => $package_height,
                                        'length_unit' => $package_length_unit,
                                        'length' => $package_length,
                                        'weight_unit' => $package_weight_unit,
                                        'weight' => $package_weight,
                                        'width_unit' => $package_width_unit,
                                        'width' => $package_width,
                                    ]
                                ];
                            }
                        }

                        $itemIdentifiersByMarketplace = $item->getIdentifiers();
                        $identifiers = [];
                        if (! is_null($itemIdentifiersByMarketplace)) {
                            foreach ($itemIdentifiersByMarketplace as $itemIdentifierByMarketplace) {
                                $marketplace_id = $itemIdentifierByMarketplace->getMarketplaceId();
                                $itemIdentifiers = $itemIdentifierByMarketplace->getIdentifiers();
                                $type = '';
                                $identifier = '';
                                foreach ($itemIdentifiers as $itemIdentifier) {
                                    $type = $itemIdentifier->getIdentifierType();
                                    $identifier = $itemIdentifier->getIdentifier();
                                }
                                $identifiers[] = [
                                    'marketplace_id' => $marketplace_id,
                                    'type' => $type,
                                    'identifier' => $identifier,
                                ];
                            }
                        }

                        $itemImagesByMarketplace = $item->getImages();
                        $images = [];
                        if (! is_null($itemImagesByMarketplace)) {
                            foreach ($itemImagesByMarketplace as $itemImageByMarketplace) {
                                $marketplace_id = $itemImageByMarketplace->getMarketplaceId();
                                $itemImages = $itemImageByMarketplace->getImages();
                                $item_images = [];
                                foreach ($itemImages as $itemImage) {
                                    $item_images[] = [
                                        'variant' => $itemImage->getVariant(),
                                        'link' => $itemImage->getLink(),
                                        'height' => $itemImage->getHeight(),
                                        'width' => $itemImage->getWidth(),
                                    ];
                                }
                                $images[] = [
                                    'marketplace_id' => $marketplace_id,
                                    'item_images' => $item_images,
                                ];
                            }
                        }

                        $itemProductTypesByMarketplace = $item->getProductTypes();
                        $product_types = [];
                        if (! is_null($itemProductTypesByMarketplace)) {
                            foreach ($itemProductTypesByMarketplace as $itemProductTypeByMarketplace) {
                                $product_types[] = [
                                    'marketplace_id' => $itemProductTypeByMarketplace->getMarketplaceId() ?? '',
                                    'product_type' => $itemProductTypeByMarketplace->getProductType() ?? '',
                                ];
                            }
                        }

                        $itemRelationshipsByMarketplace = $item->getRelationships();
                        $relationships = [];
                        if (! is_null($itemRelationshipsByMarketplace)) {
                            foreach ($itemRelationshipsByMarketplace as $itemRelationshipByMarketplace) {
                                $marketplace_id = $itemRelationshipByMarketplace->getMarketplaceId();
                                $itemRelationsShip = $itemRelationshipByMarketplace->getRelationships();
                                $item_relations_ship = [];
                                foreach ($itemRelationsShip as $itemRelationShip) {
                                    $childAsins = $itemRelationShip->getChildAsins();
                                    $child_asins = [];
                                    if (! is_null($childAsins)) {
                                        foreach ($childAsins as $childAsin) {
                                            $child_asins[] = $childAsin;
                                        }
                                    }
                                    $parentAsins = $itemRelationShip->getParentAsins();
                                    $parent_asins = [];
                                    if (! is_null($parentAsins)) {
                                        foreach ($parentAsins as $parentAsin) {
                                            $parent_asins[] = $parentAsin;
                                        }
                                    }
                                    $itemVariationTheme = $itemRelationShip->getVariationTheme();
                                    $item_variation_theme = [];
                                    if (! is_null($itemVariationTheme)) {
                                        $attributes = $itemVariationTheme->getAttributes() ?? [];
                                        $theme = $itemVariationTheme->getTheme() ?? '';
                                        $item_variation_theme = [
                                            'attributes' => $attributes,
                                            'theme' => $theme,
                                        ];
                                    }
                                    $type = $itemRelationShip->getType();

                                    $item_relations_ship[] = [
                                        'child_asins' => $child_asins,
                                        'parent_asins' => $parent_asins,
                                        'item_variation_theme' => $item_variation_theme,
                                        'type' => $type,
                                    ];
                                }

                                $relationships[] = [
                                    'marketplace_id' => $marketplace_id,
                                    'relationships' => $item_relations_ship,
                                ];
                            }
                        }

                        $itemSalesRanksByMarketplace = $item->getSalesRanks();
                        $sales_ranks = [];
                        if (! is_null($itemSalesRanksByMarketplace)) {
                            foreach ($itemSalesRanksByMarketplace as $itemSalesRankByMarketplace) {
                                $marketplace_id = $itemSalesRankByMarketplace->getMarketplaceId();
                                $itemClassificationSalesRank = $itemSalesRankByMarketplace->getClassificationRanks();
                                $classification_sales_rank = [];
                                if (! is_null($itemClassificationSalesRank)) {
                                    foreach ($itemClassificationSalesRank as $itemClassificationSaleRank) {
                                        $classification_id = $itemClassificationSaleRank->getClassificationId();
                                        $title = $itemClassificationSaleRank->getTitle();
                                        $link = $itemClassificationSaleRank->getLink();
                                        $rank = $itemClassificationSaleRank->getRank();

                                        $classification_sales_rank[] = [
                                            'classification_id' => $classification_id,
                                            'title' => $title,
                                            'link' => $link,
                                            'rank' => $rank,
                                        ];
                                    }
                                }
                                $itemDisplayGroupSalesRank = $itemSalesRankByMarketplace->getDisplayGroupRanks();
                                $display_group_sales_rank = [];
                                if (! is_null($itemDisplayGroupSalesRank)) {
                                    foreach ($itemDisplayGroupSalesRank as $itemDisplayGroupSaleRank) {
                                        $website_display_group = $itemDisplayGroupSaleRank->getWebsiteDisplayGroup();
                                        $title = $itemDisplayGroupSaleRank->getTitle();
                                        $link = $itemDisplayGroupSaleRank->getLink();
                                        $rank = $itemDisplayGroupSaleRank->getRank();

                                        $display_group_sales_rank[] = [
                                            'website_display_group' => $website_display_group,
                                            'title' => $title,
                                            'link' => $link,
                                            'rank' => $rank,
                                        ];
                                    }
                                }

                                $sales_ranks[] = [
                                    'marketplace_id' => $marketplace_id,
                                    'classification_sales_rank' => $classification_sales_rank,
                                    'display_group_sales_rank' => $display_group_sales_rank,
                                ];
                            }
                        }

                        $itemSummariesByMarketplace = $item->getSummaries();
                        $summaries = [];
                        if (! is_null($itemSummariesByMarketplace)) {
                            foreach ($itemSummariesByMarketplace as $itemSummaryByMarketplace) {
                                $marketplace_id = $itemSummaryByMarketplace->getMarketplaceId();
                                $adult_product = $itemSummaryByMarketplace->getAdultProduct() ?? false;
                                $autographed = $itemSummaryByMarketplace->getAutographed() ?? false;
                                $brand = $itemSummaryByMarketplace->getBrand() ?? '';
                                $itemBrowseClassification = $itemSummaryByMarketplace->getBrowseClassification();
                                if (! is_null($itemBrowseClassification)) {
                                    $itemBrowseClassification->getDisplayName();
                                    $itemBrowseClassification->getClassificationId();
                                }

                                $color = $itemSummaryByMarketplace->getColor();
                                $itemContributors = $itemSummaryByMarketplace->getContributors();
                                $item_contributors = [];
                                if (! is_null($itemContributors)) {
                                    foreach ($itemContributors as $itemContributor) {
                                        $itemContributorRole = $itemContributor->getRole();
                                        $role = [
                                            'display_name' => $itemContributorRole->getDisplayName(),
                                            'value' => $itemContributorRole->getValue(),
                                        ];
                                        $value = $itemContributor->getValue();

                                        $item_contributors[] = [
                                            'role' => $role,
                                            'value' => $value
                                        ];
                                    }
                                }

                                $item_classification = $itemSummaryByMarketplace->getItemClassification() ?? '';
                                $item_name = $itemSummaryByMarketplace->getItemName() ?? '';
                                $manufacturer = $itemSummaryByMarketplace->getManufacturer() ?? '';
                                $memorabilia = $itemSummaryByMarketplace->getMemorabilia() ?? false;
                                $model_number = $itemSummaryByMarketplace->getModelNumber() ?? '';
                                $package_quantity = $itemSummaryByMarketplace->getPackageQuantity() ?? 0;
                                $part_number = $itemSummaryByMarketplace->getPartNumber() ?? '';
                                $releaseDate = $itemSummaryByMarketplace->getReleaseDate();
                                $release_date = '';
                                if (! is_null($releaseDate)) {
                                    $release_date = $releaseDate->format('Y-m-d H:i:s');
                                }

                                $size = $itemSummaryByMarketplace->getSize() ?? '';
                                $style = $itemSummaryByMarketplace->getStyle() ?? '';
                                $trade_in_eligible = $itemSummaryByMarketplace->getTradeInEligible() ?? false;
                                $website_display_group = $itemSummaryByMarketplace->getWebsiteDisplayGroup() ?? '';
                                $website_display_group_name = $itemSummaryByMarketplace->getWebsiteDisplayGroupName() ?? '';

                                $summaries[] = [
                                    'marketplace_id' => $marketplace_id,
                                    'adult_product' => $adult_product,
                                    'autographed' => $autographed,
                                    'brand' => $brand,
                                    'color' => $color,
                                    'item_contributors' => $item_contributors,
                                    'item_classification' => $item_classification,
                                    'item_name' => $item_name,
                                    'manufacturer' => $manufacturer,
                                    'memorabilia' => $memorabilia,
                                    'model_number' => $model_number,
                                    'package_quantity' => $package_quantity,
                                    'part_number' => $part_number,
                                    'release_date' => $release_date,
                                    'size' => $size,
                                    'style' => $style,
                                    'trade_in_eligible' => $trade_in_eligible,
                                    'website_display_group' => $website_display_group,
                                    'website_display_group_name' => $website_display_group_name,
                                ];
                            }
                        }

                        $itemVendorDetailsByMarketplace = $item->getVendorDetails();
                        $vendor_details = [];
                        if (! is_null($itemVendorDetailsByMarketplace)) {
                            foreach ($itemVendorDetailsByMarketplace as $itemVendorDetailByMarketplace) {
                                $marketplace_id = $itemVendorDetailByMarketplace->getMarketplaceId();
                                $brand_code = $itemVendorDetailByMarketplace->getBrandCode() ?? '';
                                $manufacturer_code = $itemVendorDetailByMarketplace->getManufacturerCode() ?? '';
                                $manufacturer_code_parent = $itemVendorDetailByMarketplace->getManufacturerCodeParent() ?? '';

                                $itemVendorDetailsCategory = $itemVendorDetailByMarketplace->getProductCategory();
                                $product_category = [];
                                if (! is_null($itemVendorDetailsCategory)) {
                                    $product_category = [
                                        'display_name' => $itemVendorDetailsCategory->getDisplayName() ?? '',
                                        'value' => $itemVendorDetailsCategory->getValue() ?? '',
                                    ];
                                }

                                $product_group = $itemVendorDetailByMarketplace->getProductGroup() ?? '';

                                $itemVendorDetailsCategory = $itemVendorDetailByMarketplace->getProductSubcategory();
                                $product_subcategory = [];
                                if (! is_null($itemVendorDetailsCategory)) {
                                    $product_subcategory = [
                                        'display_name' => $itemVendorDetailsCategory->getDisplayName() ?? '',
                                        'value' => $itemVendorDetailsCategory->getValue() ?? '',
                                    ];
                                }

                                $replenishment_category = $itemVendorDetailByMarketplace->getReplenishmentCategory() ?? '';//https://developer-docs.amazon.com/sp-api/docs/catalog-items-api-v2022-04-01-reference#replenishmentcategory

                                $vendor_details[] = [
                                    'marketplace_id' => $marketplace_id,
                                    'brand_code' => $brand_code,
                                    'manufacturer_code' => $manufacturer_code,
                                    'manufacturer_code_parent' => $manufacturer_code_parent,
                                    'product_category' => $product_category,
                                    'product_group' => $product_group,
                                    'product_subcategory' => $product_subcategory,
                                    'replenishment_category' => $replenishment_category,
                                ];
                            }
                        }

                        $items_list[] = [
                            'asin' => $asin,
                            'attributes' => $attributes,
                            'dimensions' => $dimensions,
                            'identifiers' => $identifiers,
                            'images' => $images,
                            'product_types' => $product_types,
                            'relationships' => $relationships,
                            'sales_ranks' => $sales_ranks,
                            'summaries' => $summaries,
                            'vendor_details' => $vendor_details,
                        ];

                    }

                    var_dump($number_of_results);
                    var_dump($classification_refinements);
                    var_dump($brands_refinements);
                    var_dump($items_list);

                    $pagination = $itemSearchResults->getPagination();
                    if (is_null($pagination)) {
                        break;
                    }
                    $next_token = $pagination->getNextToken();
                    $previous_token = $pagination->getPreviousToken();
                    var_dump($next_token);
                    var_dump($previous_token);
                    $retry = 10;
                } catch (ApiException $e) {
                    $message = $e->getMessage();
                    $retry--;
                    if ($retry > 0) {
                        $console->warning(sprintf('Catalog Items ApiException searchCatalogItems Failed. retry:%s merchant_id: %s merchant_store_id: %s %s', $retry, $merchant_id, $merchant_store_id, $message));
                        sleep(10);
                        continue;
                    }
                    $console->error(sprintf('Catalog Items ApiException searchCatalogItems Failed. merchant_id: %s merchant_store_id: %s %s', $merchant_id, $merchant_store_id, $message));
                } catch (InvalidArgumentException $e) {
                    $log = sprintf('Catalog Items InvalidArgumentException searchCatalogItems Failed. merchant_id: %s merchant_store_id: %s ', $merchant_id, $merchant_store_id);
                    $console->error($log);
                    $logger->error($log);
                }
            }

            return true;
        });
    }
}
