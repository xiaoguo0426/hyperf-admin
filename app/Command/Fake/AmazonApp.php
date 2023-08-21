<?php

namespace App\Command\Fake;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonAppModel;
use App\Util\AmazonSDK;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class AmazonApp extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('fake:amazon-app');
    }

    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon App');
    }

    public function handle(): void
    {

        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        \App\Util\AmazonApp::tick($merchant_id, $merchant_store_id, static function (AmazonAppModel $amazonAppCollection) {
            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

//            var_dump($amazonAppCollection->getRegionRefreshTokenConfigs());
            return true;
        });

        \App\Util\AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, string $seller_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {
            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $console->info($amazonSDK->getAppId());
            $console->info($amazonSDK->getRegion());
            return true;
        });

    }

}