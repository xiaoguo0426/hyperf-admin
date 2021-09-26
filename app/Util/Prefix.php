<?php

declare(strict_types=1);

namespace App\Util;

class Prefix
{
    public static function getLoginErrCount($unique): string
    {
        return 'system-user-err-count::' . $unique;
    }

    public static function authNodes($unique): string
    {
        return 'system-user-auth-nodes::' . $unique;
    }

    public static function ignoreNodes(): string
    {
        return 'system-ignore-nodes';
    }

    public static function webSetting(): string
    {
        return 'system-web-setting';
    }

    public static function smtpSetting(): string
    {
        return 'system-smtp-setting';
    }

    public static function productCategory(): string
    {
        return 'product-category';
    }

    public static function crontabs()
    {
        return 'system-crontabs';
    }

    public static function sendWithdrawEmailCache($unique): void
    {
    }

    public static function sendFundEmailCache($unique): void
    {
    }

    public static function getSendWithdrawEmailLimit($unique): void
    {
    }

    public static function getSendFundEmailLimit($unique): void
    {
    }

    public static function feedbackIpLimit($unique): void
    {
    }
}
