<?php
declare(strict_types=1);

namespace App\Command\Auto;

use Hyperf\Command\Annotation\Command;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Devtool\Generator\GeneratorCommand;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;

/**
 * @Command
 */
class ServiceCommand extends GeneratorCommand
{
    public function __construct()
    {
        parent::__construct('auto:service');
        $this->setDescription('Create a new service class');
    }

    protected function getConfig(): array
    {

        $class = Arr::last(explode('\\', static::class));
        $class = Str::replaceLast('Command', '', $class);
        $key = 'devtool.auto.' . Str::snake($class, '.');
        return $this->getContainer()->get(ConfigInterface::class)->get($key) ?? [];
    }

    /**
     * @return string
     */
    protected function getStub(): string
    {
        return $this->getConfig()['stub'] ?? __DIR__ . '/stubs/service.stub';
    }

    /**
     * @return string
     */
    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Service';
    }

}