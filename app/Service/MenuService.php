<?php
declare(strict_types=1);

namespace App\Service;

use App\Constants\Constants;
use App\Exception\EmptyException;
use App\Model\SystemMenuModel;

class MenuService extends BaseService
{

    public function list()
    {
        $list = SystemMenuModel::all([
            'id',
            'pid',
            'title',
            'uri',
            'sort',
            'icon',
            'status'
        ])->sortByDesc('sort')->values()->toArray();

        return $list;

    }

    public function add(int $pid, string $title, string $uri = '', string $params = '#', string $icon = '', $sort = 0)
    {
        $model = new SystemMenuModel();

        $model->pid = $pid;
        $model->title = $title;
        $model->uri = $uri;
        $model->params = $params;
        $model->icon = $icon;
        $model->status = 1;
        $model->sort = $sort;

        return $model->save();

    }


    public function edit(int $id, int $pid, string $title, string $uri = '', string $params = '#', string $icon = '', $sort = 0)
    {
        return SystemMenuModel::query()->where('id', $id)->update([
            'pid' => $pid,
            'title' => $title,
            'uri' => $uri,
            'params' => $params,
            'icon' => $icon,
            'sort' => $sort
        ]);
    }

    public function info(int $id)
    {
        return SystemMenuModel::query()->where('id', $id)->first();
    }

    /**
     * @param int $id
     * @return int|mixed
     */
    public function del(int $id)
    {
        return SystemMenuModel::query()->where('id', $id)->delete();

    }

    public function forbid(int $id): int
    {
        $info = $this->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return SystemMenuModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_FORBID
        ]);
    }

    public function resume(int $id): int
    {
        $info = $this->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return SystemMenuModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_ACTIVE
        ]);

    }
}