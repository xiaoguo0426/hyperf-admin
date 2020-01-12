<?php
declare(strict_types=1);

namespace App\Service;

use App\Constants\Constants;
use App\Model\SystemUserModel;

class UserService extends BaseService
{
    /**
     * @param $where
     * @param $fields
     * @param int $page
     * @param int $limit
     * @return \Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection
     */
    public function select($where, $fields, $page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;

        return SystemUserModel::query()->with(['role' => function ($query) {
            $query->select(['id', 'title']);
        }])->where($where)->orderByDesc('id')->offset($offset)->limit($limit)->get($fields);
    }

    /**
     * @param $where
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function get($where)
    {
        return SystemUserModel::query()->where($where)->first();
    }

    /**
     * @param $where
     * @param string $field
     * @return int
     */
    public function count($where, $field = '*')
    {
        return SystemUserModel::query()->where($where)->count($field);
    }

    /**
     * @param int $user_id
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getUser(int $user_id)
    {
        return $this->get([
            'id' => $user_id
        ]);
    }

    /**
     * @param string $username
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getUserByName(string $username)
    {
        return $this->get([
            'username' => $username
        ]);
    }

    /**
     * @param $user_id
     * @param $data
     * @return int
     */
    public function save($user_id, $data)
    {
        return SystemUserModel::query()->where('id', $user_id)->update($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function add(array $data)
    {
        $model = new SystemUserModel();
        foreach ($data as $key => $item) {
            $model->setAttribute($key, $item);
        }
//        $model->save();
        return $model->save();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function forbid(int $id): bool
    {
        return (bool)SystemUserModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_FORBID
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function resume(int $id): bool
    {
        return (bool)SystemUserModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_ACTIVE
        ]);
    }
}