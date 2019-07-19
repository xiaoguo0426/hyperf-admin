<?php


namespace App\Service;


use App\Util\Node;

class NodeService extends BaseService
{
    public function getList()
    {
        $controller_path = config('controller_path', '');
        $nodes = Node::getClassNodes($controller_path);

        $methods = Node::getMethodNodes($controller_path);

        $list = [];

        foreach ($nodes as $key_node => $node_name) {
            $list[$key_node] = [
                'pnode' => substr($key_node, 0, strrpos($key_node, '/')),
                'node' => $key_node,
                'title' => $node_name
            ];
        }

        foreach ($methods as $key_node => $methodwn) {
            $list[$key_node] = [
                'pnode' => substr($key_node, 0, strrpos($key_node, '/')),
                'node' => $key_node,
                'title' => $node_name
            ];
        }

        var_dump($list);



    }

}