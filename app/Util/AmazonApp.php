<?php

namespace App\Util;

use AmazonPHP\SellingPartner\Exception\ApiException;
use App\Model\AmazonAppModel;
use App\Util\RedisHash\AmazonAppHash;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Model\ModelNotFoundException;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class AmazonApp
{
    public static function tick(int $merchant_id, int $merchant_store_id, callable $func): bool
    {

        if (! is_callable($func)) {
            return true;//直接终止处理
        }
        $appHash = \Hyperf\Support\make(AmazonAppHash::class, ['merchant_id' => $merchant_id, 'merchant_store_id' => $merchant_store_id]);
        $id = $appHash->id;

        if ($id) {
            $status = $appHash->status;

            if (Constants::STATUS_ACTIVE !== $appHash->status) {
                return true;
            }

            $amazonAppCollection = new AmazonAppModel();
            $amazonAppCollection->id = $id;
            $amazonAppCollection->merchant_id = $appHash->merchant_id;
            $amazonAppCollection->merchant_store_id = $appHash->merchant_store_id;
            $amazonAppCollection->seller_id = $appHash->seller_id;
            $amazonAppCollection->app_id = $appHash->app_id;
            $amazonAppCollection->app_name = $appHash->app_name;
            $amazonAppCollection->aws_access_key = $appHash->aws_access_key;
            $amazonAppCollection->aws_secret_key = $appHash->aws_secret_key;
            $amazonAppCollection->user_arn = $appHash->user_arn;
            $amazonAppCollection->role_arn = $appHash->role_arn;
            $amazonAppCollection->lwa_client_id = $appHash->lwa_client_id;
            $amazonAppCollection->lwa_client_id_secret = $appHash->lwa_client_id_secret;
            $amazonAppCollection->region = $appHash->region;
//            $amazonAppCollection->country_ids = $appHash->country_ids;
            $amazonAppCollection->refresh_token = $appHash->refresh_token;
            $amazonAppCollection->country_ids = $appHash->country_ids;
            $amazonAppCollection->status = $status;

        } else {
            //缓存不存在
            try {
                $amazonAppCollection = AmazonAppModel::query()->where('merchant_id', $merchant_id)->where('merchant_store_id', $merchant_store_id)->firstOrFail();
            } catch (ModelNotFoundException $exception) {
                return true;
            }
            $appHash->load($amazonAppCollection->toArray());
        }

        return $func($amazonAppCollection);
    }

    public static function tok(int $merchant_id, int $merchant_store_id, callable $func): bool
    {
        return self::tick($merchant_id, $merchant_store_id, static function (AmazonAppModel $amazonAppModel) use ($func) {

            if (! is_callable($func)) {
                return false;
            }

            $merchant_id = $amazonAppModel->merchant_id;
            $merchant_store_id = $amazonAppModel->merchant_store_id;
            $seller_id = $amazonAppModel->seller_id;

            $amazonSDK = make(AmazonSDK::class, [$amazonAppModel]);

            $sdk = $amazonSDK->getSdk();

            $console = di(StdoutLoggerInterface::class);

            try {
                $accessToken = $amazonSDK->getToken();
            } catch (ApiException $exception) {
                $console->error($exception->getMessage());
                return true;
            } catch (ClientExceptionInterface $e) {
                $console->error($e->getMessage());
                return true;
            }

            $region = $amazonSDK->getRegion();
            $marketplace_ids = $amazonSDK->getMarketplaceIds();

            return $func($amazonSDK, $merchant_id, $merchant_store_id, $seller_id, $sdk, $accessToken, $region, $marketplace_ids);
        });
    }

    /**
     * @param callable $func
     * @return bool
     */
    public static function trigger(callable $func): bool
    {

        $amazonAppCollections = AmazonAppModel::query()->where('status', Constants::STATUS_ACTIVE)->get();
        if ($amazonAppCollections->isEmpty()) {
            return true;
        }

        foreach ($amazonAppCollections as $amazonAppCollection) {
            $func($amazonAppCollection);
        }

        return true;
    }

    public static function process(callable $func): void
    {
        self::trigger(static function (AmazonAppModel $amazonAppCollection) use ($func) {

            if (! is_callable($func)) {
                return false;
            }

            $merchant_id = $amazonAppCollection->merchant_id;
            $merchant_store_id = $amazonAppCollection->merchant_store_id;
            $seller_id = $amazonAppCollection->seller_id;

            $amazonSDK = new AmazonSDK($amazonAppCollection);

            try {
                $sdk = $amazonSDK->getSdk();
            } catch (ApiException|JsonException|ClientExceptionInterface  $e) {
                return true;
            }

            try {
                $accessToken = $amazonSDK->getToken();
            } catch (ApiException|ClientExceptionInterface $exception) {
                return true;
            }

            $region = $amazonSDK->getRegion();

            $marketplace_ids = $amazonSDK->getMarketplaceIds();

            return $func($amazonSDK, $merchant_id, $merchant_store_id, $seller_id, $sdk, $accessToken, $region, $marketplace_ids);

        });
    }
}