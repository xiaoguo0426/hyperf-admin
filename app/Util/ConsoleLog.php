<?php

namespace App\Util;

use Hyperf\Contract\StdoutLoggerInterface;

/**
 * Class ConsoleLog
 * @package extension\Log
 * @method void info($message)
 * @method void error($message)
 * @method void comment($message)
 * @method void warning($message)
 * @method void highlight($message)
 */
class ConsoleLog
{
    /**
     * @var StdoutLoggerInterface
     */
    protected StdoutLoggerInterface $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}