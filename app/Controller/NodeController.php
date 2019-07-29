<?php


namespace App\Controller;

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
        //读取runtime/nodes.php文件数据即可
        try {
            $nodes_file = RUNTIME_PATH . 'nodes.php';
            if (!file_exists($nodes_file)) {
                throw new FileNotFoundException('节点数据不存在！', 200);
            }

            $nodes = include $nodes_file;

            return $this->response->success($nodes);

        } catch (FileNotFoundException $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->response->fail($exception->getCode(), $exception->getMessage());
        }
    }
}