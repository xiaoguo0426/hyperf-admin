<?php


namespace App\Util;


use Hyperf\Contract\StdoutLoggerInterface;

class StdOutLog
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

}