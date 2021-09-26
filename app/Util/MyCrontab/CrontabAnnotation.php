<?php

declare(strict_types=1);

namespace App\Util\MyCrontab;

use Hyperf\Crontab\Annotation\Crontab;

class CrontabAnnotation extends Crontab
{
    /**
     * @var int
     */
    public $status = 1;
}
