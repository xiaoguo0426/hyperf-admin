<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Listener;

use App\Util\Log;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @Listener
 */
class DbQueryExecutedListener implements ListenerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $stdLog;

    public function __construct(ContainerInterface $container)
    {
//        $this->logger = $container->get(LoggerFactory::class)->get('sql', 'sql');
        $this->logger = Log::get('sql', 'sql');
        $this->stdLog = di(StdoutLoggerInterface::class);
    }


    public function listen(): array
    {
        return [
            QueryExecuted::class,
        ];
    }

    /**
     * @param QueryExecuted $event
     */
    public function process(object $event)
    {
        if ($event instanceof QueryExecuted) {
            $sql = $event->sql;
            $time = $event->time;

            if (!Arr::isAssoc($event->bindings)) {
                foreach ($event->bindings as $key => $value) {
                    $sql = Str::replaceFirst('?', "'{$value}'", $sql);
                }
            }
            //检测sql执行时间大于某个值，需要提醒

            $method = $time > 100 ? 'warning' : 'info';

            $log = sprintf('[%s ms] %s', $time, $sql);
            //毫秒
            $this->stdLog->$method($log);

            $this->logger->$method($log);
        }
    }
}
