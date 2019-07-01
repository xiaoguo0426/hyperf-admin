<?php

declare(strict_types=1);

namespace App\Command;

use Dotenv\Dotenv;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class SaltCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('salt:init');
    }

    public function configure()
    {
        $this->setDescription('Hyperf salt Command');
    }

    public function handle()
    {
        if (!file_exists(BASE_PATH . '/.env')) {
            $this->error('.env file not exists!');
            return;
        }

        $env = Dotenv::create([BASE_PATH])->load();

        $length = 16;

        if (function_exists('openssl_random_pseudo_bytes')) {
            $app_key = bin2hex(openssl_random_pseudo_bytes(16));
        } else {
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $app_key = substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
        }
        $env['APP_KEY'] = $app_key;

        foreach ($env as $key => $val) {
            $name = strtoupper($key);
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    $item = $name . '_' . strtoupper($k);
                    putenv("$item=$v");
                }
            } else {
                putenv("$name=$val");
            }
        }

        $this->line($app_key, 'info');
        $this->line(BASE_PATH, 'info');
    }
}

