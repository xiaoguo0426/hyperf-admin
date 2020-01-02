<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

return [
    'handler' => [
        'http' => [
            App\Exception\Handler\AppExceptionHandler::class,
            App\Exception\Handler\LoginExceptionHandler::class,
            App\Exception\Handler\InvalidAccessExceptionHandler::class,
            App\Exception\Handler\InvalidRequestMethodExceptionHandler::class,
            App\Exception\Handler\InvalidArgumentsExceptionHandler::class,
            App\Exception\Handler\EmptyExceptionHandler::class,
            App\Exception\Handler\UserExceptionHandler::class,
            App\Exception\Handler\StatusExceptionHandler::class,
            App\Exception\Handler\ResultExceptionHandler::class,
//            App\Exception\Handler\InvalidRequestMethodExceptionHandler::class,

        ],
    ],
];
