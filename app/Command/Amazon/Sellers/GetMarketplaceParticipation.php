<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Sellers;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonSalesOrderMetricsModel;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonSellerGetMarketplaceParticipationLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Model\ModelNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetMarketplaceParticipation extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:sellers:get-marketplace-participation');
    }


    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Sellers Get Marketplace Participation');
    }

    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function handle()
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');
        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonSellerGetMarketplaceParticipationLog::class);

            $retry = 10;

            while (true) {

                try {
                    //https://developer-docs.amazon.com/sp-api/docs/sales-api-v1-reference
                    $response = $sdk->sellers()->getMarketplaceParticipations($accessToken, $region);
                    $marketplaceParticipationList = $response->getPayload();
                    if (is_null($marketplaceParticipationList)) {
                        break;
                    }

                    $errors = $response->getErrors();
                    if (! is_null($errors)) {
                        break;
                    }

                    foreach ($marketplaceParticipationList as $marketplaceParticipation) {
                        $marketplace = $marketplaceParticipation->getMarketplace();
                        $marketplace_id = $marketplace->getId();
                        $name = $marketplace->getName();
                        $country_code = $marketplace->getCountryCode();
                        $default_currency_code = $marketplace->getDefaultCurrencyCode();
                        $default_language_code = $marketplace->getDefaultLanguageCode();
                        $domain_name = $marketplace->getDomainName();
                        $participation = $marketplaceParticipation->getParticipation();
                        $is_participating = $participation->getIsParticipating();
                        $has_suspended_listings = $participation->getHasSuspendedListings();

                        var_dump($marketplace_id);
                        var_dump($name);
                        var_dump($country_code);
                        var_dump($default_currency_code);
                        var_dump($default_language_code);
                        var_dump($domain_name);
                        var_dump($is_participating);
                        var_dump($has_suspended_listings);
                        var_dump('*******************');
                    }

                    break;

                } catch (ApiException $e) {
                    --$retry;
                    if ($retry > 0) {
                        continue;
                    }
                    break;
                } catch (InvalidArgumentException $e) {
                    continue;
                }
            }

            return true;
        });
    }
}
