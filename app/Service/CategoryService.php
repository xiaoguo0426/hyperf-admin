<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Constants;
use App\Model\Product\CategoryModel;

class CategoryService extends BaseService
{
    /**
     * 列表操作
     *
     * @param array $where
     * @param array $fields
     *
     * @return array<\Hyperf\Database\Model\Builder>|\Hyperf\Database\Model\Collection
     */
    public function select(array $where, array $fields, int $page = 1, int $limit = 20)
    {
        $model = CategoryModel::query();

        $model->where($where)->orderBy('id');

        if ($limit > 0) {
            $model->forPage($page, $limit);
        }
        return $model->get($fields);
    }

    /**
     * @param array $where
     */
    public function count(array $where, string $field = '*'): int
    {
        return CategoryModel::query()->where($where)->count($field);
    }

    /**
     * 添加操作
     *
     * @return bool|\Carbon\CarbonInterface|float|\Hyperf\Utils\Collection|\Hyperf\Utils\HigherOrderTapProxy|int|mixed|string
     */
    public function add(int $parent_id, string $title, int $sort, string $desc)
    {
        $model = new CategoryModel();

        $model->parent_id = $parent_id;
        $model->title = $title;
        $model->desc = $desc;
        $model->sort = $sort;
        $model->status = 1;
        $model->save();
        return $model->getKey();
    }

    /**
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function info(int $id)
    {
        return CategoryModel::query()->where('id', $id)->first();
    }

    /**
     * 删除操作
     */
    public function del(int $id): bool
    {
        return (bool) CategoryModel::query()->where('id', $id)->delete();
    }

    /**
     * 编辑
     */
    public function edit(int $id, int $parent_id, string $title, string $desc, int $sort): bool
    {
        return (bool) CategoryModel::query()->where('id', $id)->update([
            'parent_id' => $parent_id,
            'title' => $title,
            'sort' => $sort,
            'desc' => $desc,
        ]);
    }

    public function forbid(int $id): bool
    {
        return (bool) CategoryModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_FORBID,
        ]);
    }

    public function resume(int $id): bool
    {
        return (bool) CategoryModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_ACTIVE,
        ]);
    }
}
