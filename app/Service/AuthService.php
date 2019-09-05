<?php
declare(strict_types=1);

namespace App\Service;


use App\Constants\Constants;
use App\Model\SystemAuthModel;

class AuthService extends BaseService
{
    /**
     * 列表操作
     * @return array
     */
    public function list(): array
    {
        $list = SystemAuthModel::all([
            'id',
            'title',
            'desc',
            'sort',
            'status'
        ])->sortByDesc('sort')->values()->toArray();

        return $list;
    }

    /**
     * 添加操作
     * @param string $title
     * @param string $desc
     * @return bool
     */
    public function add(string $title, string $desc): bool
    {
        $model = new SystemAuthModel();

        $model->title = $title;
        $model->desc = $desc;
        $model->sort = 0;
        $model->status = 1;

        return $model->save();
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
        return SystemAuthModel::query()->where('id', $id)->delete();
    }

    public function edit(int $id, string $title, string $desc): bool
    {
        return SystemAuthModel::query()->where('id', $id)->update([
            'title' => $title,
            'desc' => $desc
        ]);
    }

    public function forbid(int $id): bool
    {
        return SystemAuthModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_FORBID
        ]);
    }

    public function resume(int $id): bool
    {
        return SystemAuthModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_ACTIVE
        ]);
    }

}