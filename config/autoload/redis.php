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
    'default' => [
        'host' => env('REDIS_HOST', 'localhost'),
        'auth' => env('REDIS_AUTH', null),
        'port' => (int)env('REDIS_PORT', 6379),
        'db' => (int)env('REDIS_DB', 0),
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float)env('REDIS_MAX_IDLE_TIME', 60),
        ],
        'cluster' => [
            'enable' => (bool)env('REDIS_CLUSTER_ENABLE', false),
            'name' => null,
            'seeds' => [],
        ],
        'sentinel' => [
            'enable' => (bool)env('REDIS_SENTINEL_ENABLE', false),
            'master_name' => env('REDIS_MASTER_NAME', 'mymaster'),
            'nodes' => explode(';', env('REDIS_SENTINEL_NODE', '')),
            'persistent' => '',
            'read_timeout' => 0,
        ],
    ],
    'test' => [
        'host' => env('REDIS_HOST', 'localhost'),
        'auth' => env('REDIS_AUTH', null),
        'port' => (int)env('REDIS_PORT', 6379),
        'db' => 1,
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float)env('REDIS_MAX_IDLE_TIME', 60),
        ],
    ],
];
