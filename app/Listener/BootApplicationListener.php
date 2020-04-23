<?php


namespace App\Listener;


use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Sentry;

class BootApplicationListener implements ListenerInterface
{

    /**
     * @inheritDoc
     */
    public function listen(): array
    {
        // TODO: Implement listen() method.
        return [
            BootApplication::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function process(object $event)
    {
        // TODO: Implement process() method.
        if ($event instanceof BootApplication) {
            Sentry\init(['dsn' => 'http://6ad3bdc5904a4f909b81e379c73ff92f@sentry-web:9000/2']);
        }
    }
}