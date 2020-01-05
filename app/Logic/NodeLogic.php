<?php
declare(strict_types=1);

namespace App\Logic;


use App\Util\Node;

class NodeLogic
{
    public function getList(): array
    {
        $controller_path = config('controller_path', '');
        $nodes = Node::getClassNodes($controller_path);

        $methods = Node::getMethodNodes($controller_path);

        $list = [];

        $merge = array_merge($nodes, $methods);

        foreach ($merge as $key_node => $node_name) {
            $lower = strtolower($key_node);
            $list[$lower] = [
                'pnode' => substr($lower, 0, strrpos($lower, '/') ?: 0),
                'node' => $lower,
                'title' => $node_name
            ];
        }

        return $list;
    }

    public function toTree(array $list): array
    {
        $new = [];
        foreach ($list as $key => $item) {
            if (false !== strpos($key, '/')) {
                $pnode = $item['pnode'];
                if (!isset($new[$pnode])) {

                    $new[$pnode] = [
                        'pnode' => '',
                        'node' => $pnode,
                        'title' => $item['title']
                    ];
                }

            }
            $new[$key] = $item;
        }

        return $new;
    }
}