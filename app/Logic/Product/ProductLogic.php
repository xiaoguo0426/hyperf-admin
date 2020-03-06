<?php
declare(strict_types=1);

namespace App\Logic\Product;


use App\Service\ProductService;

class ProductLogic
{

    private $service;

    public function __construct()
    {
        $this->service = di(ProductService::class);
    }

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

        $count = $this->service->count($where, '*');
        $list = [];

        if ($count) {
            $list = $this->service->select($where, $fields, $page, $limit)->toArray();

            unset($item);
        }

        return [
            'list' => $list,
            'count' => $count
        ];
    }

    /**
     *
     * @param int $id
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function info(int $id)
    {

        return $this->service->info($id);
    }

}