<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 *
 * @document https://doc.hyperf.io
 *
 * @contact  group@hyperf.io
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
//    Hyperf\ExceptionHandler\Listener\ErrorExceptionHandler::class,
    App\Listener\BootApplicationListener::class,
//    App\Util\MyCrontab\CrontabRegisterListener::class,
];
