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
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetLabels extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fulfillment-inbound:get-inbound-guidance');
    }

    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Fulfillment Inbound Get Labels Command');
    }

    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {

//            $page_type = 'PackageLabel_Letter_2';//https://developer-docs.amazon.com/sp-api/docs/fulfillment-inbound-api-v0-reference#pagetype
//
//            $label_type = 'BARCODE_2D';//https://developer-docs.amazon.com/sp-api/docs/fulfillment-inbound-api-v0-reference#labeltype
//            $number_of_packages = null;
//            $package_labels_to_print = null;
//            $number_of_pallets = null;
//            $page_size = null;
//            $page_start_index = null;
//
//            $sdk->fulfillmentInbound()->getLabels($accessToken, $region, $shipment_id, $page_type, $label_type, $number_of_packages, $package_labels_to_print, $number_of_pallets, $page_size, $page_start_index);
//
            return true;
        });
    }
}
