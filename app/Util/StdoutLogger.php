<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util;

use Carbon\Carbon;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Default logger for logging server start and requests.
 * PSR-3 logger implementation that logs to STDOUT, using a newline after each
 * message. Priority is ignored.
 */
class StdoutLogger implements StdoutLoggerInterface
{
    private OutputInterface $output;

    private array $tags = [
        //        'component',
        'context',
        'extra',
    ];

    public function __construct(private ConfigInterface $config, ?OutputInterface $output = null)
    {
        $this->output = $output ?? new ConsoleOutput();
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $config = $this->config->get(StdoutLoggerInterface::class, ['log_level' => []]);
        if (! in_array($level, $config['log_level'], true)) {
            return;
        }
        $keys = array_keys($context);
        $tags = [];
        foreach ($keys as $k => $key) {
            if (in_array($key, $this->tags, true)) {
                $tags[$key] = $context[$key];
                unset($keys[$k]);
            }
        }
        $search = array_map(function ($key) {
            return \sprintf('{%s}', $key);
        }, $keys);

        $message = \str_replace($search, $context, $this->getMessage((string) $message, $level, $tags));

        $this->output->writeln($message . ' ' . json_encode($context));
    }

    protected function getMessage(string $message, string $level = LogLevel::INFO, array $tags = [])
    {
        $tag = match ($level) {
            LogLevel::EMERGENCY, LogLevel::ALERT, LogLevel::CRITICAL => 'error',
            LogLevel::ERROR => 'fg=red',
            LogLevel::WARNING, LogLevel::NOTICE => 'comment',
            default => 'info',
        };

        $datetime = Carbon::now()->format('Y-m-d H:i:s-v');

        $template = \sprintf('[%s] <%s>[%s]</>', $datetime, $tag, strtoupper($level));

        $implodedTags = '';
        foreach ($tags as $value) {
            $implodedTags .= (' [' . $value . ']');
        }

        return \sprintf($template . ' %s' . $implodedTags, $message);
    }
}
