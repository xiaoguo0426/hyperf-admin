<?php


namespace App\Controller;

use App\Service\NodeService;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * 节点管理
 * @AutoController()
 * Class NodeController
 * @package App\Controller
 */
class NodeController extends Controller
{

    /**
     * 获取节点数据
     */
    public function list()
    {
        try {

            $service = new NodeService();

            $list = $service->getList();

            $tree = $service->toTree($list);

            $multi_tree = arr2tree($tree, 'node', 'pnode', 'sub');

            return $this->response->success($multi_tree);

        } catch (\Exception $exception) {

        }

    }

    /**
     * 清理节点
     */
    public function clear()
    {
    }

    /**
     * 更新节点数据
     */
    public function refresh()
    {
    }

}