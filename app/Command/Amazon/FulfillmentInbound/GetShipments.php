<?php

namespace App\Command\Amazon\FulfillmentInbound;

use AmazonPHP\SellingPartner\AccessToken;
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Constants\AmazonConstants;
use App\Model\AmazonFinancialGroupModel;
use App\Queue\AmazonFinanceFinancialListEventsByGroupIdQueue;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonFinanceLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetShipments extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fulfillment-inbound:get-inbound-guidance');
    }

    public function configure(): void
    {
        parent::configure();
        $this->setDescription('Amazon Fulfillment Inbound Get Inbound Guidance Command');
    }

    public function handle(): void
    {
    }
}