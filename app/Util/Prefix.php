<?php


namespace App\Util;


class Prefix
{
    public static function amazonApp(int $merchant_id, int $merchant_store_id): string
    {
        return 'amazon-app:' . $merchant_id . ':' . $merchant_store_id;
    }

    public static function amazonAccessToken(int $merchant_id, int $merchant_store_id): string
    {
        return 'amazon-access-token:' . $merchant_id . ':' . $merchant_store_id;
    }

    public static function amazonSessionToken(int $merchant_id, int $merchant_store_id): string
    {
        return 'amazon-session-token:' . $merchant_id . ':' . $merchant_store_id;
    }
}