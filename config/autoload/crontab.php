<?php

use App\Util\MyCrontab\MyCrontab;

return [
    // 是否开启定时任务
    'enable' => false,
    // 通过配置文件定义的定时任务
    'crontab' => [
        // Callback类型定时任务（默认）
//        (new MyCrontab())->setName('Foo')->setRule('* * * * *')->setCallback([\App\Crontab\TestCrontab::class, 'execute'])->setMemo('这是一个输出111的定时任务'),
//        (new MyCrontab())->setName('Bar')->setStopStatus()->setRule('* * * * *')->setCallback([\App\Crontab\TestCrontab2::class, 'execute'])->setMemo('这是一个输出222的定时任务'),
        // Command类型定时任务
//        (new Crontab())->setType('command')->setName('Bar')->setRule('* * * * *')->setCallback([
//            'command' => 'swiftmailer:spool:send',
//            // (optional) arguments
//            'fooArgument' => 'barValue',
//            // (optional) options
//            '--message-limit' => 1,
//        ]),
    ],
];