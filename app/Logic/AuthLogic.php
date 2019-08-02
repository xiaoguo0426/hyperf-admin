<?php


namespace App\Logic;

use App\Exception\EmptyException;
use App\Service\AuthService;
use App\Service\NodeService;
use App\Util\Auth;

class AuthLogic
{

    /**
     * 列表操作
     * @return array
     */
    public function list(): array
    {
        $service = new AuthService();

        $list = $service->list();

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
        $service = new AuthService();

        return $service->add($title, $desc);

    }

    /**
     * @param int $id
     * @return array
     */
    public function info(int $id): array
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
    public function edit(int $id, string $title, string $desc): bool
    {
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


}