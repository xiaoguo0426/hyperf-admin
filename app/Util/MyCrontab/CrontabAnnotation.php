<?php

namespace App\Util\MyCrontab;

use Hyperf\Crontab\Annotation\Crontab;


class CrontabAnnotation extends Crontab
{
    /**
     * @var int
     */
    public $status = 1;

}