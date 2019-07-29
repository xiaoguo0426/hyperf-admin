<?php

namespace App\Service;


use App\Constants\Constants;
use App\Exception\EmptyException;
use App\Model\SystemAuthModel;
use App\Util\Auth;
use App\Util\Prefix;
use App\Util\Redis;

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
        $del = SystemAuthModel::query()->where('id', $id)->delete();
        return $del;
    }

    public function edit(int $id, string $title, string $desc): bool
    {
        $info = $this->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return SystemAuthModel::query()->where('id', $id)->update([
            'title' => $title,
            'desc' => $desc
        ]);
    }

    public function forbid(int $id): bool
    {

        $info = $this->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return SystemAuthModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_FORBID
        ]);
    }

    public function resume(int $id): bool
    {
        $info = $this->info($id);

        if (!$info) {
            throw new EmptyException();
        }

        return SystemAuthModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_ACTIVE
        ]);
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

    /**
     * 保存节点数据
     * @param int $id
     * @param array $nodes
     * @return bool
     * @throws \Exception
     */
    public function saveAuthNodes(int $id, array $nodes): bool
    {

        $nodes_data = [];
        foreach ($nodes as $node) {
            $nodes_data[] = md5($node);
        }

        $redis = Redis::getInstance();

        $key = Prefix::authNodes($id);

        $add = $redis->sAddArray($key, $nodes_data);

        if (false === $add) {
            throw new \Exception('保存失败！');
        }

        return true;
    }


}