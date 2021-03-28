<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    Hyperf\HttpServer\CoreMiddleware::class => App\Middleware\CoreMiddleware::class,
//    Hyperf\Crontab\Listener\OnPipeMessageListener::class => App\Util\MyCrontab\OnPipeMessageListener::class,
//    Hyperf\Crontab\Crontab::class => App\Util\MyCrontab\MyCrontab::class,
];
