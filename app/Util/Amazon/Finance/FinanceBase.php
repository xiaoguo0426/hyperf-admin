<?php

declare(strict_types=1);
/**
 *
 * @author   xiaoguo0426
 * @contact  740644717@qq.com
 * @license  MIT
 */

namespace App\Util\Amazon\Finance;

abstract class FinanceBase
{
    public function __construct(public int $merchant_id, public int $merchant_store_id)
    {
    }

    abstract public function run($financialEvents): bool;

    public function getEventName(): string
    {
        return str_replace(__NAMESPACE__ . '\\', '', static::class);
    }
}
