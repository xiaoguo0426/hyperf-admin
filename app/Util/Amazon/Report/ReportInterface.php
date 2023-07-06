<?php

namespace App\Util\Amazon\Report;

interface ReportInterface
{

    /**
     * 处理报告内容
     */
    public function run($file): void;
}