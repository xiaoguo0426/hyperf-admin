<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\Utils\Filesystem\FileNotFoundException;

/**
 * 节点管理
 *
 * @AutoController()
 * Class NodeController
 *
 * @package App\Controller
 */
class NodeController extends AbstractController
{
    public function test()
    {
        return $this->response->success([], '编辑成功！');
    }

    /**
     * 获取节点数据
     */
    public function list(): ?\Psr\Http\Message\ResponseInterface
    {
        //读取runtime/nodes.php文件数据即可
        $nodes_file = RUNTIME_PATH . 'nodes.php';
        if (! file_exists($nodes_file)) {
            throw new FileNotFoundException('节点数据不存在！', 200);
        }

        $nodes = include $nodes_file;

        return $this->response->success($nodes);
    }
}
