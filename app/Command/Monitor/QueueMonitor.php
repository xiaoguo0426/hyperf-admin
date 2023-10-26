<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Command\Monitor;

use App\Queue\AbstractQueue;
use App\Queue\AmazonActionReportQueue;
use App\Queue\AmazonGetReportDocumentQueue;
use App\Queue\AmazonGetReportQueue;
use App\Queue\AmazonReportDocumentActionQueue;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;

#[Command]
class QueueMonitor extends HyperfCommand
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('monitor:queue');
    }

    /**
     * @return void
     */
    public function configure(): void
    {
        parent::configure();
        // 指令配置
        $this->setDescription('Monitor Queue');
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        /**
         * @var AbstractQueue[] $queueCollections
         */
        $queueCollections = [
            AmazonActionReportQueue::class,
            //            AmazonEvaluationQueue::class,
            AmazonGetReportDocumentQueue::class,
            AmazonGetReportQueue::class,
            //            AmazonOrderQueue::class,
            //            AmazonOrdersQueue::class,
            AmazonReportDocumentActionQueue::class,
            //            LazadaOrderQueue::class,
            //            ShopeeOrderQueue::class,
        ];

        foreach ($queueCollections as $queueCollection) {
            /**
             * @var AbstractQueue $queueCollection
             */
            $instance = new $queueCollection();

            $safety_line = $instance->safetyLine();
            if ($safety_line === 0) {
                continue;
            }

            $len = $instance->safetyLine();
            if ($instance->safetyLine() > $safety_line) {
                // TODO LOG
            }
        }
    }
}
