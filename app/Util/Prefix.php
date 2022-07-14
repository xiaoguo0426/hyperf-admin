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

    public static function sendWithdrawEmailCache($unique): string
    {
        return '';
    }

    public static function sendFundEmailCache($unique): string
    {
        return '';
    }

    public static function getSendWithdrawEmailLimit($unique): string
    {
        return '';
    }

    public static function getSendFundEmailLimit($unique): string
    {
        return '';
    }

    public static function feedbackIpLimit($unique): string
    {
        return '';
    }

    public static function getSendEmailLimit($unique): string
    {
        return '';
    }
}
