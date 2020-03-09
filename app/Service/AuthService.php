<?php
declare(strict_types=1);

namespace App\Service;


use App\Constants\Constants;
use App\Model\SystemAuthModel;

class AuthService extends BaseService
{
    /**
     * 列表操作
     * @param array $where
     * @param array $fields
     * @param int $page
     * @param int $limit
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function select(array $where, array $fields, int $page = 1, int $limit = 20)
    {
        $model = SystemAuthModel::query();

        $model->where($where)->orderBy('id');

        if ($page > 0){
            $model->forPage($page, $limit);
        }
        return $model->get($fields);
    }

    /**
     *
     * @param array $where
     * @param string $field
     * @return int
     */
    public function count(array $where, $field = '*'): int
    {
        return SystemAuthModel::query()->where($where)->count($field);
    }

    /**
     * 添加操作
     * @param string $title
     * @param string $desc
     * @return bool|\Carbon\CarbonInterface|float|\Hyperf\Utils\Collection|\Hyperf\Utils\HigherOrderTapProxy|int|mixed|string
     */
    public function add(string $title, string $desc)
    {
        $model = new SystemAuthModel();

        $model->title = $title;
        $model->desc = $desc;
        $model->sort = 0;
        $model->status = 1;
        $model->save();
        return $model->getKey();
    }

    public function info(int $id)
    {
        return SystemAuthModel::query()->where('id', $id)->first();
    }

    /**
     * 删除操作
     * @param int $id
     * @return bool
     */
    public function del(int $id): bool
    {
        return (bool)SystemAuthModel::query()->where('id', $id)->delete();
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $desc
     * @return bool
     */
    public function edit(int $id, string $title, string $desc): bool
    {
        return (bool)SystemAuthModel::query()->where('id', $id)->update([
            'title' => $title,
            'desc' => $desc
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function forbid(int $id): bool
    {
        return (bool)SystemAuthModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_FORBID
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function resume(int $id): bool
    {
        return (bool)SystemAuthModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_ACTIVE
        ]);
    }

}