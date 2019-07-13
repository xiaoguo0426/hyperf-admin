<?php


namespace App\Service;


use App\Util\Node;

class NodeService extends BaseService
{

    public function getList()
    {
        $controller_path = config('controller_path', '');
        return Node::getClassTreeNode($controller_path);

    }

}