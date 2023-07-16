<?php

namespace App\Util\Log;

class QueueLog extends AbstractLog
{
    public function __construct()
    {
        parent::__construct('queue', 'queue');
    }
}