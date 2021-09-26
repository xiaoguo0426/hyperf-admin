<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\Constants;
use App\Model\SystemUserModel;

class UserService extends BaseService
{
    /**
     * @param $query
     * @param array<string> $fields
     */
    public function select($query, array $fields = ['*'], int $page = 1, int $limit = 20): \Hyperf\Contract\LengthAwarePaginatorInterface
    {
        $username = empty($query['username']) ? '' : $query['username'];
        $mobile = empty($query['mobile']) ? '' : $query['mobile'];
        $email = empty($query['email']) ? '' : $query['email'];
        $role = empty($query['role']) ? '' : $query['role'];

        $model = SystemUserModel::query();

        return $model->where(
            'username',
            '!=',
            'admin'//不允许查询超管
        )->with(['role' => static function ($query): void {
            $query->select(['id', 'title']);
        },
        ])->when($username, static function ($query, $username) {
            return $query->where('username', $username);
        })->when($mobile, static function ($query, $mobile) {
            return $query->where('mobile', $mobile);
        })->when($email, static function ($query, $email) {
            return $query->where('email', $email);
        })->when($role, static function ($query, $role) {
            return $query->where('role_id', $role);
        })->paginate($limit, $fields, 'page', $page);
    }

    /**
     * @param $where
     * @param $fields
     *
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function get($where, $fields = ['*'])
    {
        return SystemUserModel::query()->where($where)->first($fields);
    }

    public function count($where, string $field = '*'): int
    {
        return SystemUserModel::query()->where($where)->count($field);
    }

    /**
     * @param array $fields
     *
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getUser(int $user_id, array $fields = ['*'])
    {
        return $this->get([
            'id' => $user_id,
        ], $fields);
    }

    /**
     * @param array $fields
     *
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getUserByName(string $username, array $fields = ['*'])
    {
        return $this->get([
            'username' => $username,
        ], $fields);
    }

    public function save($user_id, $data): int
    {
        return SystemUserModel::query()->where('id', $user_id)->update($data);
    }

    /**
     * @param array $data
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

    public function forbid(int $id): bool
    {
        return (bool) SystemUserModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_FORBID,
        ]);
    }

    public function resume(int $id): bool
    {
        return (bool) SystemUserModel::query()->where('id', $id)->update([
            'status' => Constants::STATUS_ACTIVE,
        ]);
    }
}
