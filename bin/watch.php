#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

use Swoole\Process as SwooleProcess;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$rootPath = dirname(__DIR__);

// 判断watcher是否已启动，如果已启动，则禁止重复启动
$watcherPidFile = $rootPath . '/runtime/watch.pid';
if (is_running($watcherPidFile)) {
    console_error('Error: Watch is running at ' . file_get_contents($watcherPidFile));
    exit(1);
}
@cli_set_process_title('hyperf watcher');
file_put_contents($watcherPidFile, getmypid());
register_shutdown_function(static function () use ($watcherPidFile): void {
    if (is_file($watcherPidFile)) {
        unlink($watcherPidFile);
    }
});

// hyperf server pid文件路径
$sererPidFile = $rootPath . '/runtime/hyperf.pid';

// 判断hyperf server是否已启动，如果已启动，则禁止重复启动
if (is_running($sererPidFile)) {
    console_error('Error: Hyperf server is running at ' . file_get_contents($sererPidFile));
    exit(1);
}

// hyperf启动命令
$cmd = $argv;
array_shift($cmd);
if (empty($cmd)) {
    $cmd = ['php', './bin/hyperf.php', 'start'];
}

// 启动watch
$watchCmd = [
    'fswatch',
    '-rtx',
    '--utc-time', // 可以考虑改成UTC+8
    '-e',
    '/vendor/',
    '-e',
    '\\.git',
    '-e',
    '\\.idea',
    '-e',
    '/runtime/',
    '-i',
    $rootPath . '\\.env$',
    '-r',
    $rootPath . '/app/',
    '-r',
    $rootPath . '/config/',
    '-m',
    'poll_monitor',
];
$watchProcess = new SwooleProcess(static function () use ($watchCmd, $rootPath, $sererPidFile): void {
    console_info('watcher started at ' . getmypid());

    $symfonyProcess = new Process($watchCmd, $rootPath);
    $watchLock = false;
    $symfonyProcess->setTimeout(0)->run(static function ($type, $buffer) use (&$watchLock, $sererPidFile): void {
        // 只监听以下四种事件
        $logs = fswatch_event_parser($buffer, ['Created', 'Updated', 'Removed', 'Renamed']);
        if (! $watchLock && $logs) {
            $watchLock = true;

            // 输出日志
            foreach ($logs as $log) {
                console_warning($log); // 进程重启，警告日志
            }

            // kill掉server，另外一个进程会拉起
            if (is_running($sererPidFile)) {
                SwooleProcess::kill((int) file_get_contents($sererPidFile), SIGTERM);
            }
            $watchLock = false;
        }
    });
}, false, 1);

@$watchProcess->name('hyperf watcher for files');

$watchProcess->start();

// 管理server的进程
$process = new Process($cmd, $rootPath);
$process->setTimeout(0);
while (true) {
    try {
        console_info('Hyperf process is ready to start');
        $process->run(static function ($type, $buffer): void {
            echo $buffer;
        });
    } catch (ProcessSignaledException $e) {
        continue;
    }
}

/**
 * 进程是否在运行中.
 */
function is_running(string $pidFile): bool
{
    if (! is_file($pidFile)) {
        return false;
    }
    $pid = file_get_contents($pidFile);
    try {
        return SwooleProcess::kill((int) $pid, 0);
    } catch (\Throwable $e) {
        return false;
    }
}

function console_warning($message): void
{
    echo sprintf("\033[33m%s\033[39m\n", $message);
}

function console_error($message): void
{
    echo sprintf("\033[31m%s\033[0m\n", $message);
}

function console_info($message): void
{
    echo sprintf("\033[32m%s\033[0m\n", $message);
}

/**
 * fswatch event parser.
 */
function fswatch_event_parser(string $event, array $eventTypes): array
{
    $logs = [];
    foreach ($eventTypes as $eventType) {
        if (str_contains($event, $eventType)) {
            $logs[] = $event;
            break;
        }
    }

    return $logs;
}
