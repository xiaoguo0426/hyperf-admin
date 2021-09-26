<?php

declare(strict_types=1);

namespace App\Util;

use Hyperf\Utils\ApplicationContext;

class Log
{
    public static function get(string $name = 'app', string $group = 'default')
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, $group);
    }
}
