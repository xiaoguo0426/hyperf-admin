<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Crontab\Amazon;

use App\Queue\AmazonOrderItemQueue;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RedisException;

#[Command]
class GetOrderItems extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('amazon:order:get-order-items');
        // 指令配置
        $this->setDescription('Crontab Amazon Order API Get Order Items Command');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws RedisException
     * @return void
     */
    public function handle(): void
    {
        (new AmazonOrderItemQueue())->pop();
    }
}
