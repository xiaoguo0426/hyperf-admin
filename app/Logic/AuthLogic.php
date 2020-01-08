<?php
declare(strict_types=1);

namespace App\Logic;

use App\Exception\EmptyException;
use App\Service\AuthService;
use App\Util\Auth;

class AuthLogic
{

    /**
     * 列表操作
     * @param array $query
     * @return array
     */
    public function list(array $query): array
    {
        $where = [];
        $fields = '*';

        $page = $query['page'] ?: 1;
        $limit = $query['limit'] ?: 20;

        $service = di(AuthService::class);

        $count = $service->count($where, '*');
        $list = [];

        if ($count) {
            $list = $service->select($where, $fields, $page, $limit)->toArray();

            foreach ($list as &$item) {
                $item['LAY_DISABLED'] = 1 === $item['id'];
            }

            unset($item);
        }

        return [
            'list' => $list,
            'count' => $count
        ];
    }

    /**
     * 添加操作
     * @param string $title
     * @param string $desc
     * @return bool
     */
    public function add(string $title, string $desc): bool
    {
        $service = new AuthService();

        return $service->add($title, $desc);

    }

    /**
     * @param int $id
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function info(int $id)
    {
        $service = new AuthService();

        return $service->info($id);
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $desc
     * @return bool
     */
    public function edit(int $id, string $title, array $nodes, string $desc): bool
    {
        Auth::save($id, $nodes);

        return true;
        $service = new AuthService();

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->edit($id, $title, $desc);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function del(int $id)
    {
        $service = new AuthService();

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->del($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function forbid(int $id)
    {
        $service = new AuthService();

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->forbid($id);
    }

    public function resume(int $id): bool
    {
        $service = new AuthService();

        $info = $service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->resume($id);
    }


    public function getAuthNodes(int $id): array
    {

        $nodeService = new NodeService();

        $list = $nodeService->getList();

        foreach ($list as &$item) {
            $item['is_check'] = Auth::checkNode($id, $item['node']);
        }

        unset($item);

        $tree = $nodeService->toTree($list);

        $multi_tree = arr2tree($tree, 'node', 'pnode', 'sub');

        return $multi_tree;

    }

    public function save(int $id, string $title, array $nodes, string $desc)
    {

    }


}