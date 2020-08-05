<?php


namespace App\Util\MiniProgram;


use App\Exception\InvalidConfigException;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class MiniProgramFactory
{
    /**
     * @var MiniProgramProxy[]
     */
    protected array $proxies;

    public function __construct(ConfigInterface $config, ContainerInterface $container)
    {
        $miniProgramConfig = $config->get('mini_program.config');
        foreach ($miniProgramConfig as $name => $item) {
            $this->proxies[$name] = make(MiniProgramProxy::class, [
                'miniProgramName' => $name,
                'config' => $item,
                'container' => $container
            ]);
        }
    }

    /**
     * @param string $miniProgramName
     *
     * @return MiniProgramProxy
     */
    public function get(string $miniProgramName = 'default'): ?MiniProgramProxy
    {
        $proxy = $this->proxies[$miniProgramName] ?? NULL;
        if (! $proxy instanceof MiniProgramProxy) {
            throw new InvalidConfigException('Invalid MiniProgram proxy.');
        }
        return $proxy;
    }
}