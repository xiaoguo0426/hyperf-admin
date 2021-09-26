<?php

declare(strict_types=1);

namespace App\Util\MyCrontab;

use Hyperf\Crontab\Crontab;

class MyCrontab extends Crontab
{
    /**
     * @var int
     */
    private $status = 1;

    public function setStatus($status): MyCrontab
    {
        $this->status = $status;
        return $this;
    }

    public function setStopStatus(): MyCrontab
    {
        return $this->setStatus(0);
    }

    public function setActiveStatus(): MyCrontab
    {
        return $this->setStatus(1);
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
