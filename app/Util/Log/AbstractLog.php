<?php

namespace App\Util\Log;

use DateTimeZone;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Log基础类
 * Class AbstractConsoleLog
 * @package extension\Log
 * @method void log(mixed $level, string $message, array $context = [])
 * @method void debug($message, array $context = [])
 * @method void info($message, array $context = [])
 * @method void notice($message, array $context = [])
 * @method void warning($message, array $context = [])
 * @method void error($message, array $context = [])
 * @method void critical($message, array $context = [])
 * @method void alert($message, array $context = [])
 * @method void emergency($message, array $context = [])
 * @method self setTimezone(DateTimeZone $tz)
 * @method DateTimeZone getTimezone
 */
abstract class AbstractLog
{

    public string $channel;

    private Logger $logger;

    public function __construct()
    {
        $path = '/home/laradock/erp/' . $this->channel;

        $dateFormat = "Y-m-d\TH:i:sP";

        $output = "[%datetime%] %channel%.%level_name%: %message% %context% \n";

        $formatter = new LineFormatter($output, $dateFormat);

        $this->logger = new Logger('console');

        $stream = new StreamHandler($path . '/' . date('Y-m-d') . '.log');
        $stream->setFormatter($formatter);

        $this->logger->pushHandler($stream);
    }

    public function __call($name, $arguments)
    {
        return $this->logger->$name(...$arguments);
    }
}