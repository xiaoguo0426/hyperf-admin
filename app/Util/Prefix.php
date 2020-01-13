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

}