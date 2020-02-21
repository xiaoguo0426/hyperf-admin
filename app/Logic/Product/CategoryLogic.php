<?php
declare(strict_types=1);
namespace App\Logic\Product;

use App\Service\CategoryService;

class CategoryLogic
{
    /**
     * 列表操作
     * @param array $query
     * @return array
     */
    public function list(array $query): array
    {
        $where = [];
        $fields = ['*'];

        $page = (int)$query['page'] ?: 1;
        $limit = isset($query['limit']) ? (int)$query['limit'] : 20;

        $service = di(CategoryService::class);

        $count = $service->count($where, '*');
        $list = [];

        if ($count) {
            $list = $service->select($where, $fields, $page, $limit)->toArray();

            unset($item);
        }

        return [
            'list' => $list,
            'count' => $count
        ];
    }
}