<?php

declare(strict_types=1);

namespace App\Logic\Product;

use App\Exception\EmptyException;
use App\Exception\ResultException;
use App\Service\CategoryService;
use App\Util\Prefix;
use App\Util\Redis;

class CategoryLogic
{
    private $service;

    public function __construct()
    {
        $this->service = di(CategoryService::class);
    }

    /**
     * 列表操作
     *
     * @param array $query
     *
     * @return array
     */
    public function list(array $query): array
    {
        $where = [];
        $fields = ['id', 'parent_id', 'title', 'status', 'desc', 'sort'];

        $page = isset($query['page']) ? (int) $query['page'] : 1;
        $limit = isset($query['limit']) ? (int) $query['limit'] : 20;

        $count = $this->service->count($where, '*');
        $list = [];

        if ($count) {
            $list = $this->service->select($where, $fields, $page, $limit)->toArray();

            unset($item);
        }

        return [
            'list' => $list,
            'count' => $count,
        ];
    }

    public function listWithNoPage(array $query): array
    {
        $query['page'] = 0;
        $query['limit'] = 0;
        return $this->list($query);
    }

    /**
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function info(int $id)
    {
        return $this->service->info($id);
    }

    /**
     * 添加操作
     */
    public function add(int $parent_id, string $title, int $sort, string $desc): int
    {
        $add = $this->service->add($parent_id, $title, $sort, $desc);

        if (! $add) {
            throw new ResultException('新增商品分类失败！');
        }

        $this->refreshListCache();

        return (int) $add;
    }

    public function edit(int $id, int $parent_id, string $title, int $sort, string $desc): bool
    {
        $info = $this->service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        $edit = $this->service->edit($id, $parent_id, $title, $desc, $sort);

        $this->refreshListCache();

        return $edit;
    }

    public function del(int $id): bool
    {
        $info = $this->service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }
        $del = $this->service->del($id);

        $this->refreshListCache();

        return $del;
    }

    public function forbid(int $id): bool
    {
        $info = $this->service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        $forbid = $this->service->forbid($id);

        $this->refreshListCache();

        return $forbid;
    }

    public function resume(int $id): bool
    {
        $info = $this->service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        $resume = $this->service->resume($id);

        $this->refreshListCache();

        return $resume;
    }

    public function toTree($list)
    {
        return arr2table($list, 'id', 'parent_id');
    
    }

    public function getListCache(): array
    {
        $redis = Redis::getInstance();

        $json = $redis->get(Prefix::productCategory());

        if ($json === false) {
            return [];
        }

        $arr = json_decode($json, true);

        return json_last_error() === JSON_ERROR_NONE ? $arr : [];
    }

    public function refreshListCache(): void
    {
        $list = $this->listWithNoPage([
        ]);

        $this->setListCache($list['list']);
    }

    public function setListCache(array $list): bool
    {
        $redis = Redis::getInstance();

        return $redis->set(Prefix::productCategory(), json_encode($list));
    }

    public function clearListCache(): int
    {
        $redis = Redis::getInstance();

        return $redis->del(Prefix::productCategory());
    }
}
