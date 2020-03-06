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

    public function listWithNoPage(array $query): array
    {
        $query['page'] = 0;
        return $this->list($query);
    }

    /**
     * @param int $id
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function info(int $id)
    {
        return $this->service->info($id);
    }

    /**
     * 添加操作
     * @param int $parent_id
     * @param string $title
     * @param int $sort
     * @param string $desc
     * @return int
     */
    public function add(int $parent_id, string $title, int $sort, string $desc): int
    {

        $add = $this->service->add($parent_id, $title, $sort, $desc);

        if (!$add) {
            throw new ResultException('新增角色失败！');
        }

        return (int)$add;
    }


    /**
     * @param int $id
     * @param int $parent_id
     * @param string $title
     * @param int $sort
     * @param string $desc
     * @return bool
     */
    public function edit(int $id, int $parent_id, string $title, int $sort, string $desc): bool
    {

        $info = $this->service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $this->service->edit($id, $parent_id, $title, $desc, $sort);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function del(int $id): bool
    {

        $info = $this->service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $this->service->del($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function forbid(int $id): bool
    {

        $info = $this->service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $this->service->forbid($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function resume(int $id): bool
    {

        $info = $this->service->info($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $this->service->resume($id);
    }

    public function toTree($list)
    {
        $list = arr2table($list, 'id', 'parent_id');

        return $list;
    }

    public function getListCache(): array
    {

        $redis = Redis::getInstance();

        $json = $redis->get(Prefix::productCategory());

        if (false === $json) {
            return [];
        }

        $arr = json_decode($json, true);

        return JSON_ERROR_NONE === json_last_error() ? $arr : [];

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