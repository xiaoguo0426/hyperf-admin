<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Report;

interface ReportInterface
{
    /**
     * 处理报告内容.
     */
    public function run(string $report_id, string $file): bool;
}
