<?php


namespace App\Listener;


use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;

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
    }
}