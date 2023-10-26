<?php

namespace App\Command\Crontab\Amazon;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Util\Amazon\OrderCreator;
use App\Util\Amazon\OrderEngine;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

#[Command]
class RefreshPendingOrder extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('crontab:amazon:refresh-pending-order');
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->addOption('order_ids', null, InputOption::VALUE_OPTIONAL, 'order_ids集合', null)
            ->setDescription('Crontab Amazon Refresh Pending Order Command');
    }

    /**
     * @throws ApiException
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @return void
     */
    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');
        $amazon_order_ids = $this->input->getOption('order_ids');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($amazon_order_ids) {

            if (! is_null($amazon_order_ids)) {
                $amazon_order_ids = explode(',', $amazon_order_ids);
            }

            $created_after = null;
            $nextToken = null;
            $max_results_per_page = 100;

            $orderCreator = new OrderCreator();
            $orderCreator->setMarketplaceIds($marketplace_ids);
            $orderCreator->setMaxResultsPerPage($max_results_per_page);
            $orderCreator->setCreatedAfter($created_after);
            $orderCreator->setNextToken($nextToken);
            $orderCreator->setAmazonOrderIds($amazon_order_ids);

            \Hyperf\Support\make(OrderEngine::class)->launch($amazonSDK, $sdk, $accessToken, $orderCreator);

            return true;
        });

    }
}