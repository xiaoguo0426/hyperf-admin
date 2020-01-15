<?php
return [
    'default' => [
        'accessKeyId' => env('OSS_ACCESS_KEY_ID'),
        'accessKeySecret' => env('OSS_ACCESS_KEY_SECRET'),
        'endpoint' => env('OSS_ENDPOINT'),
        'isCname' => env('OSS_IS_CNAME'),
        'maxSize' => env('OSS_MAX_SIZE'),
    ]
];