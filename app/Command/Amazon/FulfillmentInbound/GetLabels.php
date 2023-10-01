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
use App\Util\Log\AmazonFulfillmentInboundGetLabelsLog;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class GetLabels extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:fulfillment-inbound:get-labels');
    }

    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->addArgument('merchant_id', InputArgument::REQUIRED, '商户id')
            ->addArgument('merchant_store_id', InputArgument::REQUIRED, '店铺id')
            ->setDescription('Amazon Fulfillment Inbound Get Labels Command');
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
            $logger = ApplicationContext::getContainer()->get(AmazonFulfillmentInboundGetLabelsLog::class);

            //https://developer-docs.amazon.com/sp-api/docs/fulfillment-inbound-api-v0-reference#pagetype
            $page_type = 'PackageLabel_Letter_2';//用于打印标签的页面类型
            //https://developer-docs.amazon.com/sp-api/docs/fulfillment-inbound-api-v0-reference#labeltype
            $label_type = 'BARCODE_2D';//请求的标签类型。
            $number_of_packages = null;//货件中的包裹数。可选
            $package_labels_to_print = null;//指定要为其打印包标签的包的标识符列表。最大999
            $number_of_pallets = null;//货物中托盘的数量。这会为每个托盘返回四个相同的标签。
            $page_size = 10;//用于在总包裹标签中分页的页面大小。这是非合作LTL运输的必需参数。最大值：1000。
            $page_start_index = null;//用于对总包裹标签进行分页的页面起始索引。这是非合作LTL Shipments的必需参数。

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
                    $getLabelsResponse = $sdk->fulfillmentInbound()->getLabels($accessToken, $region, $shipment_id, $page_type, $label_type, $number_of_packages, $package_labels_to_print, $number_of_pallets, $page_size, $page_start_index);
                    $payload = $getLabelsResponse->getPayload();
                    if (is_null($payload)) {
                        $console->warning(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s API响应 payload为空', $merchant_id, $merchant_store_id, $shipment_id));
                        continue;
                    }
                    $errorList = $getLabelsResponse->getErrors();
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

                    $console->info(sprintf('merchant_id:%s merchant_store_id:%s shipment_id:%s url:%s', $merchant_id, $merchant_store_id, $shipment_id, $payload->getDownloadUrl()));
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
