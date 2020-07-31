<?php
declare(strict_types=1);

namespace App\Service;

use App\Constants\Constants;
use App\Model\SystemUserModel;

class UserService extends BaseService
{
    /**
     * @param $query
     * @param string[] $fields
     * @param int $page
     * @param int $limit
     * @return \Hyperf\Contract\LengthAwarePaginatorInterface
     */
    public function select($query, $fields = ['*'], $page = 1, $limit = 20)
    {

        $username = empty($query['username']) ? '' : $query['username'];
        $mobile = empty($query['mobile']) ? '' : $query['mobile'];
        $email = empty($query['email']) ? '' : $query['email'];
        $role = empty($query['role']) ? '' : $query['role'];

        $model = SystemUserModel::query();

        $paginator = $model->where(
            'username', '!=', 'admin'//不允许查询超管
        )->with(['role' => function ($query) {
            $query->select(['id', 'title']);
        }])->when($username, function ($query, $username) {
            return $query->where('username', $username);
        })->when($mobile, function ($query, $mobile) {
            return $query->where('mobile', $mobile);
        })->when($email, function ($query, $email) {
            return $query->where('email', $email);
        })->when($role, function ($query, $role) {
            return $query->where('role_id', $role);
        })->paginate($limit, $fields, 'page', $page);

        return $paginator;
    }

    /**
     * @param $where
     * @param $fields
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function get($where, $fields = ['*'])
    {
        return SystemUserModel::query()->where($where)->first($fields);
    }

    /**
     * @param $where
     * @param string $field
     * @return int
     */
    public function count($where, $field = '*'): int
    {
        return SystemUserModel::query()->where($where)->count($field);
    }

    /**
     * @param int $user_id
     * @param array $fields
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getUser(int $user_id, array $fields = ['*'])
    {
        return $this->get([
            'id' => $user_id
        ], $fields);
    }

    /**
     * @param string $username
     * @param array $fields
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getUserByName(string $username, array $fields = ['*'])
    {
        return $this->get([
            'username' => $username
        ], $fields);
    }

    /**
     * @param $user_id
     * @param $data
     * @return int
     */
    public function save($user_id, $data): int
    {
        return SystemUserModel::query()->where('id', $user_id)->update($data);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function add(array $data): bool
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
        return (bool) SystemUserModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_FORBID
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function resume(int $id): bool
    {
        return (bool) SystemUserModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_ACTIVE
        ]);
    }
}