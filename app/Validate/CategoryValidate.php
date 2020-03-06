<?php
declare(strict_types=1);

namespace App\Validate;

use think\Validate;

class CategoryValidate extends Validate
{

    protected $rule = [
        'id' => 'require',
        'parent_id' => 'require',
        'title' => 'require|min:2|max:30',
        'desc' => 'require|min:1|max:255',
        'sort' => 'require|number|min:0|max:99'
    ];

    protected $message = [
        'id.require' => 'id不能为空！',
        'parent_id.require' => '上级分类不能为空！',
        'title.require' => '权限名称不能为空！',
        'title.min' => '分类名称长度不能少于2位有效字符！',
        'title.max' => '分类名称长度不能大于30位有效字符！',
        'desc.require' => '分类描述不能为空！',
        'desc.min' => '分类描述长度不能少于5位有效字符！',
        'desc.max' => '分类描述长度不能大于255位有效字符！',
        'sort.require' => '排序不能为空！',
        'sort.number' => '排序只能为数字！',
        'sort.min' => '排序最小为0！',
        'sort.max' => '排序最大为99！',
    ];

    protected $scene = [
        'base' => ['id'],
        'add' => ['title', 'desc', 'desc', 'sort'],
        'edit' => ['id', 'title', 'desc', 'sort']
    ];

//    protected function checkAuthNodes($fieldValue)
//    {
//        var_dump($fieldValue);
//    }

}