<?php

declare(strict_types=1);

return [
    'default' => [
        'accessKeyId' => env('OSS_ACCESS_KEY_ID'),
        'accessKeySecret' => env('OSS_ACCESS_KEY_SECRET'),
        'host' => env('OSS_HOST'),
        'isCname' => env('OSS_IS_CNAME'),
        'maxSize' => env('OSS_MAX_SIZE'),
    ],
];
