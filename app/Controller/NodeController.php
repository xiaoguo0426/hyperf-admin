<?php


namespace App\Controller;

use App\Service\NodeService;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\Utils\Filesystem\FileNotFoundException;

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
//        try {
//
//            $service = new NodeService();
//
//            $list = $service->getList();
//
//            $tree = $service->toTree($list);
//
//            $multi_tree = arr2tree($tree, 'node', 'pnode', 'sub');
//
//            return $this->response->success($multi_tree);
//
//        } catch (\Exception $exception) {
//
//        }
        //读取runtime/nodes.php文件数据即可
        try {
            $nodes_file = RUNTIME_PATH . 'nodes.php';
            if (!file_exists($nodes_file)) {
                throw new FileNotFoundException('节点数据不存在！');
            }
        } catch (\FileNotFoundException $exception) {

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