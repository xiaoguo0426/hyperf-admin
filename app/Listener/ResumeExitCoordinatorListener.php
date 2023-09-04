<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Listener;

use Hyperf\Command\Event\AfterExecute;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class ResumeExitCoordinatorListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            AfterExecute::class,
        ];
    }

    public function process(object $event): void
    {
        CoordinatorManager::until(Constants::WORKER_EXIT)->resume();
    }
}
