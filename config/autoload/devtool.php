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
    'generator' => [
        'amqp' => [
            'consumer' => [
                'namespace' => 'App\\Amqp\\Consumer',
            ],
            'producer' => [
                'namespace' => 'App\\Amqp\\Producer',
            ],
        ],
        'aspect' => [
            'namespace' => 'App\\Aspect',
        ],
        'command' => [
            'namespace' => 'App\\Command',
        ],
        'controller' => [
            'namespace' => 'App\\Controller',
            'stub' => BASE_PATH . '/config/stubs/controller.stub'
        ],
        'job' => [
            'namespace' => 'App\\Job',
        ],
        'listener' => [
            'namespace' => 'App\\Listener',
        ],
        'middleware' => [
            'namespace' => 'App\\Middleware',
        ],
        'Process' => [
            'namespace' => 'App\\Processes',
        ]

    ],
    'auto' => [
        'service' => [
            'namespace' => 'App\\Service',
            'stub' => BASE_PATH . '/config/stubs/service.stub'
        ]
    ]
];
