<?php

declare(strict_types=1);

namespace App\Crontab;

class TestCrontab
{
    public function execute(): void
    {
        var_dump(111);
    }
}
