<?php

declare(strict_types=1);

namespace App\Util\MyCrontab;

use App\Util\Prefix;
use App\Util\Redis;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\Crontab;
use Hyperf\Crontab\CrontabManager;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Process\Event\BeforeProcessHandle;

class CrontabRegisterListener implements ListenerInterface
{
    /**
     * @var \Hyperf\Crontab\CrontabManager
     */
    protected $crontabManager;

    /**
     * @var \Hyperf\Contract\StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @var \Hyperf\Contract\ConfigInterface
     */
    private $config;

    public function __construct(CrontabManager $crontabManager, StdoutLoggerInterface $logger, ConfigInterface $config)
    {
        $this->crontabManager = $crontabManager;
        $this->logger = $logger;
        $this->config = $config;
    }

    public function listen(): array
    {
        return [
            BeforeProcessHandle::class,
        ];
    }

    public function process(object $event): void
    {
        $crontabs = $this->parseCrontabs();
        foreach ($crontabs as $crontab) {
            if ($crontab instanceof Crontab) {
                $this->logger->debug(sprintf('Crontab %s have been registered.', $crontab->getName()));
                $this->crontabManager->register($crontab);
            }
        }
    }

    private function parseCrontabs(): array
    {
        $configCrontabs = $this->config->get('crontab.crontab', []);

        $annotationCrontabs = AnnotationCollector::getClassByAnnotation(CrontabAnnotation::class);
        $crontabs = [];

        $key = Prefix::crontabs();
        $redis = Redis::getInstance();

        foreach (array_merge($configCrontabs, $annotationCrontabs) as $crontab) {
            if ($crontab instanceof CrontabAnnotation) {
                $crontab = $this->buildCrontabByAnnotation($crontab);
            }

            if ($crontab instanceof MyCrontab) {
                $crontabs[$crontab->getName()] = $crontab;

                $reflection = new \ReflectionObject($crontab);

                $properties = $reflection->getProperties();

                $hash = [];
                foreach ($properties as $property) {
                    $name = $property->getName();
                    $method = 'get' . ucfirst($property->getName());

                    if (method_exists($crontab, $method)) {
                        $med = new \ReflectionMethod($crontab, $method);

                        $value = $med->invoke($crontab);

                        $hash[$name] = $value;
                    }
                }

                $redis->hSet($key, $crontab->getName(), json_encode($hash));
            }
        }
        return array_values($crontabs);
    }

    private function buildCrontabByAnnotation(CrontabAnnotation $annotation): Crontab
    {
        $crontab = new MyCrontab();
        isset($annotation->name) && $crontab->setName($annotation->name);
        isset($annotation->type) && $crontab->setType($annotation->type);
        isset($annotation->rule) && $crontab->setRule($annotation->rule);
        isset($annotation->singleton) && $crontab->setSingleton($annotation->singleton);
        isset($annotation->mutexPool) && $crontab->setMutexPool($annotation->mutexPool);
        isset($annotation->mutexExpires) && $crontab->setMutexExpires($annotation->mutexExpires);
        isset($annotation->onOneServer) && $crontab->setOnOneServer($annotation->onOneServer);
        isset($annotation->callback) && $crontab->setCallback($annotation->callback);
        isset($annotation->memo) && $crontab->setMemo($annotation->memo);
        isset($annotation->status) && $crontab->setStatus($annotation->status);
        return $crontab;
    }
}
