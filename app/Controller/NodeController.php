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

//            $tree = $service->toTree($list);

            $tree = arr2tree($list, 'node', 'pnode', 'list');

            return $this->response->success($tree);

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