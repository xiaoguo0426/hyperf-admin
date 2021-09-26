<?php

declare(strict_types=1);

namespace App\Logic;

use App\Exception\EmptyException;
use App\Exception\ResultException;
use App\Service\AuthService;
use App\Util\Auth;

class AuthLogic
{
    /**
     * 列表操作
     *
     * @param array $query
     *
     * @return array
     */
    public function list(array $query): array
    {
        $where = [];
        $fields = ['*'];

        $page = isset($query['page']) ? (int) $query['page'] : 1;
        $limit = isset($query['limit']) ? (int) $query['limit'] : 20;

        $service = di(AuthService::class);

        $count = $service->count($where, '*');
        $list = [];

        if ($count) {
            $list = $service->select($where, $fields, $page, $limit)->toArray();

            foreach ($list as &$item) {
                $item['LAY_DISABLED'] = $item['id'] === 1;
            }

            unset($item);
        }

        return [
            'list' => $list,
            'count' => $count,
        ];
    }

    /**
     * @param array $query
     * @param array $fields
     *
     * @return array
     */
    public function listWithNoPage(array $query = [], array $fields = []): array
    {
        $where = [];

        $service = di(AuthService::class);

        $list = $service->select($where, $fields, 0)->toArray();

        unset($item);

        return $list;
    }

    /**
     * 添加操作
     *
     * @param array $nodes
     */
    public function add(string $title, array $nodes, string $desc): bool
    {
        $service = di(AuthService::class);

        $add = $service->add($title, $desc);

        if (! $add) {
            throw new ResultException('新增角色失败！');
        }

        return (bool) Auth::save($add, $nodes);
    }

    /**
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function info(int $id)
    {
        $service = di(AuthService::class);

        return $service->info($id);
    }

    /**
     * @param array $nodes
     */
    public function edit(int $id, string $title, array $nodes, string $desc): bool
    {
        Auth::save($id, $nodes);

        $service = di(AuthService::class);

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->edit($id, $title, $desc);
    }

    public function del(int $id): bool
    {
        $service = di(AuthService::class);

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->del($id);
    }

    public function forbid(int $id): bool
    {
        $service = di(AuthService::class);

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->forbid($id);
    }

    public function resume(int $id): bool
    {
        $service = di(AuthService::class);

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->resume($id);
    }
}
