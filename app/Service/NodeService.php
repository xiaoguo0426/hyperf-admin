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

        $merge = array_merge($nodes, $methods);

        foreach ($merge as $key_node => $node_name) {
            $lower = strtolower($key_node);
            $list[$lower] = [
                'pnode' => substr($lower, 0, strrpos($lower, '/')),
                'node' => $lower,
                'title' => $node_name
            ];
        }

        return $list;
    }

    public function toTree(array $list)
    {

    }

}