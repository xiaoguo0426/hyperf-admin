<?php
declare(strict_types=1);

namespace App\Service;

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

        return SystemUserModel::query()->where($where)->orderByDesc('id')->offset($offset)->limit($limit)->get($fields);
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
     * $data
     * @param $data
     * @return bool
     */
    public function add($data)
    {
        return SystemUserModel::query()->insert($data);
    }
}