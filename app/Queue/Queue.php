<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Queue;

use App\Queue\Data\QueueData;
use App\Queue\Data\QueueDataInterface;
use App\Util\Log\QueueLog;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;
use RuntimeException;

class Queue extends AbstractQueue
{
    #[Inject]
    private QueueLog $queueLog;

    public function getQueueName(): string
    {
        return '';
    }

    /**
     * @throws RedisException
     */
    public function push(QueueDataInterface $queueData): int
    {
        return (int) $this->redis->lpush($this->queue_name, $queueData->toJson());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @throws \Exception
     */
    public function pop(): bool
    {
        $console = ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);

        $pid = posix_getpid();

        $process_title = $this->queue_name . '-' . $pid;
        cli_set_process_title($process_title);

        $signal_handler = static function ($sig_no) use ($console) {
            $pid = posix_getpid();
            $title = cli_get_process_title();
            $console->warning(sprintf('进程[%s] pid:%s 收到 %s 命令，进程退出...', $title, $pid, $sig_no));
            exit;
        };

        pcntl_signal(SIGTERM, $signal_handler); // kill -15 pid 信号值:15
        pcntl_signal(SIGINT, $signal_handler); // Ctrl-C        信号值:2  方便本地调试
        pcntl_signal(SIGUSR1, $signal_handler); // 自定义信号     信号值:10
        pcntl_signal(SIGUSR2, $signal_handler); // 自定义信号     信号值:12

        $timeout = $this->timeout;

        $retryInterval = $this->retryInterval; // 消息重试次数

        while (true) {
            try {
                $pop = $this->redis->brpop($this->queue_name, $timeout);
                if (empty($pop)) {
                    pcntl_signal_dispatch();
                    $console->info(sprintf('进程[%s] pid:%s 队列为空，自动退出', cli_get_process_title(), $pid));
                    break;
                }
            } catch (RedisException $exception) {
                $this->queueLog->error(sprintf('队列：%s 连接Redis异常.%s', $this->queue_name, $exception->getMessage()));
                break;
            }

            $data = $pop[1];
            $this->queueLog->info(sprintf('队列：%s 消费数据. data:%s', $this->queue_name, $data));
            //            $decode = json_decode($data, true);
            //            if (json_last_error() !== JSON_ERROR_NONE) {
            //                Log::record('队列：' . $this->queue_name . ' 数据格式不是合法的JSON格式. data:' . $data);
            //                continue;
            //            }

            $class = $this->getQueueDataClass();
            /**
             * @var QueueData $dataObject
             */
            $dataObject = new $class();

            $arr = $dataObject->toArr($data);
            $dataObject->parse($arr);

            $t1 = microtime(true);
            $handle = $this->handleQueueData($dataObject);
            $t2 = microtime(true);

            if ($this->isLogHandleDataTime) {
                $this->queueLog->debug(sprintf('队列：%s 消费数据. data:%s  耗时:%s 秒', $this->queue_name, $data, round($t2 - $t1, 3)));
            }

            if ($handle === false) {
                $retry = $dataObject->getRetry();
                if ($retry < $retryInterval) {
                    ++$retry;
                    $dataObject->setRetry($retry);
                    $json = $dataObject->toJson();
                    $this->queueLog->warning(sprintf('队列：%s  消费失败，重新入队. data:%s', $this->queue_name, $json));
                    $this->push($dataObject);
                }
            } else {
                $this->queueLog->info(sprintf('队列：%s  消费成功. data:%s', $this->queue_name, $data));
            }

            pcntl_signal_dispatch();
        }
        return true;
    }

    /**
     * @throws RuntimeException
     */
    public function handleQueueData(QueueDataInterface $queueData): bool
    {
        throw new RuntimeException('请在子类实现 handleQueueData 方法');
    }

    /**
     * @throws RuntimeException
     */
    public function getQueueDataClass(): string
    {
        throw new RuntimeException('请在子类实现 getQueueDataClass 方法');
    }
}
