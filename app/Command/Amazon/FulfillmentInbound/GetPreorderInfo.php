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
use AmazonPHP\SellingPartner\Exception\ApiException;
use AmazonPHP\SellingPartner\Exception\InvalidArgumentException;
use AmazonPHP\SellingPartner\SellingPartnerSDK;
use App\Model\AmazonShipmentModel;
use App\Util\AmazonApp;
use App\Util\AmazonSDK;
use App\Util\Log\AmazonFulfillmentInboundGetPreorderInfoLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetPreorderInfo extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fulfillment-inbound:get-preorder-info');
    }

    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Fulfillment Inbound GetPreorderInfo Command');
    }


    /**
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws \JsonException
     */
    public function handle(): void
    {
        $merchant_id = (int) $this->input->getArgument('merchant_id');
        $merchant_store_id = (int) $this->input->getArgument('merchant_store_id');

        AmazonApp::tok($merchant_id, $merchant_store_id, static function (AmazonSDK $amazonSDK, int $merchant_id, int $merchant_store_id, SellingPartnerSDK $sdk, AccessToken $accessToken, string $region, array $marketplace_ids) {

            $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
            $logger = ApplicationContext::getContainer()->get(AmazonFulfillmentInboundGetPreorderInfoLog::class);

            $amazonShipmentsCollections = AmazonShipmentModel::query()
                ->where('merchant_id', $merchant_id)
                ->where('merchant_store_id', $merchant_store_id)
                ->orderByDesc('id')
                ->get();
            if ($amazonShipmentsCollections->isEmpty()) {
                return true;
            }

            foreach ($amazonShipmentsCollections as $amazonShipmentsCollection) {

                $shipment_id = $amazonShipmentsCollection->shipment_id;
                try {
                    $getPreorderInfoResponse = $sdk->fulfillmentInbound()->getPreorderInfo($accessToken, $region, $shipment_id, implode(',', $marketplace_ids));
                    $payload = $getPreorderInfoResponse->getPayload();
                    if (is_null($payload)) {
                        $console->warning(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s API响应 payload为空', $merchant_id, $merchant_store_id, $shipment_id));
                        continue;
                    }
                    $errorList = $getPreorderInfoResponse->getErrors();
                    $errors = [];
                    if (! is_null($errorList)) {
                        foreach ($errorList as $error) {
                            $code = $error->getCode();
                            $message = $error->getMessage();
                            $details = $error->getDetails();
                            $errors[] = [
                                'code' => $code,
                                'message' => $message,
                                'details' => $details,
                            ];
                        }
                        $console->error(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s errors:%s', $merchant_id, $merchant_store_id, $shipment_id, json_encode($errors, JSON_THROW_ON_ERROR)));
                        continue;
                    }

                    $shipment_contains_preorderable_items = $payload->getShipmentContainsPreorderableItems() ?? false;
                    $shipment_confirmed_for_preorder = $payload->getShipmentConfirmedForPreorder() ?? false;
                    $needByDate = $payload->getNeedByDate();
                    $need_by_date = '';
                    if (! is_null($needByDate)) {
                        $need_by_date = $needByDate->format('Y-m-d H:i:s');
                    }
                    $confirmedFulfillableDate = $payload->getConfirmedFulfillableDate();
                    $confirmed_fulfillable_date = '';
                    if (! is_null($confirmedFulfillableDate)) {
                        $confirmed_fulfillable_date = $confirmedFulfillableDate->format('Y-m-d H:i:s');
                    }
                    var_dump($shipment_contains_preorderable_items);
                    var_dump($shipment_confirmed_for_preorder);
                    var_dump($need_by_date);
                    var_dump($confirmed_fulfillable_date);
                } catch (ApiException $exception) {
                    $console->error(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s %s', $merchant_id, $merchant_store_id, $shipment_id, $exception->getMessage()));
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

            return true;
        });
    }
}
