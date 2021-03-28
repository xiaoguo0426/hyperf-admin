<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Model;

use App\Constants\Constants;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;

abstract class Model extends BaseModel implements CacheableInterface
{
    use Cacheable;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Constants::STATUS_ACTIVE);
    }

    public function scopeForbid(Builder $query): Builder
    {
        return $query->where('status', Constants::STATUS_FORBID);
    }

    public function scopeDesc(Builder $query, string $field = 'sort'): Builder
    {
        return $query->where($field, Constants::STATUS_FORBID);
    }

}
