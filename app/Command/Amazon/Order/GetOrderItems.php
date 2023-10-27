<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Amazon\Order;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\Model\Orders\Order;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonOrderItemModel;
use App\Util\Amazon\OrderItemCreator;
use App\Util\Amazon\OrderItemEngine;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Constants;
use App\Util\Log\AmazonOrdersLog;
use Carbon\Carbon;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use JsonException;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetOrderItems extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:order:get-order-items');
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->addArgument('order_ids', InputArgument::REQUIRED, 'order_ids集合')
            ->setDescription('Amazon Order API Get Order Items');
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
        $amazon_order_ids = $this->input->getArgument('order_ids');
        $amazon_order_ids = explode(',', $amazon_order_ids);

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) use ($amazon_order_ids) {

            $orderItemCreator = new OrderItemCreator();
            $orderItemCreator->setAmazonOrderIds($amazon_order_ids);

            \Hyperf\Support\make(OrderItemEngine::class)->launch($amazonSDK, $sdk, $accessToken, $orderItemCreator);

            return true;
        });
    }
}
