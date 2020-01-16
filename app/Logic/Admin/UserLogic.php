<?php

namespace App\Logic\Admin;

use App\Exception\EmptyException;
use App\Exception\ResultException;
use App\Exception\UserNotFoundException;
use App\Model\SystemUserModel;
use App\Service\UserService;

class UserLogic
{
    /**
     *
     * @param array $query
     * @return array
     */
    public function getList(array $query): array
    {
        $where = [
            [
                'username', '!=', 'admin'//不允许查询超管
            ]
        ];

        if (!empty($query['username'])) {
            $where[] = [
                'username', '=', $query['username']
            ];
        }

        if (!empty($query['mobile'])) {
            $where[] = [
                'mobile', '=', $query['mobile']
            ];
        }

        if (!empty($query['email'])) {
            $where[] = [
                'email', '=', $query['email']
            ];
        }

        if (!empty($query['role'])) {
            $where[] = [
                'role_id', '=', $query['role']
            ];
        }
        $page = $query['page'] ?: 1;
        $limit = $query['limit'] ?: 20;

        $di = di(UserService::class);

        $count = $di->count($where, '*');
        $list = [];

        if ($count) {
            $list = $di->select($where, [
                'id', 'username', 'nickname', 'role_id', 'avatar', 'gender', 'mobile', 'email', 'remark', 'status', 'created_at'
            ], $page, $limit)->toArray();

            //判断id是否为1 默认是超管角色
            foreach ($list as &$item) {
                $item['LAY_DISABLED'] = 1 === $item['id'];
            }
            unset($item);
        }

        return [
            'list' => $list,
            'count' => $count
        ];
    }

    /**
     *
     * @param int $user_id
     * @return array
     */
    public function getUser(int $user_id): array
    {

        $di = di(UserService::class);

        $user = $di->getUser($user_id);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        return $user->toArray();
    }

    public function getUserByName(string $user_name)
    {
        return di(UserService::class)->getUserByName($user_name);
    }

    /**
     * @param int $user_id
     * @param int $role_id
     * @param string $nickname
     * @param int $gender
     * @param string $avatar
     * @param string $mobile
     * @param string $email
     * @param string $remark
     * @return int
     */
    public function save(int $user_id, int $role_id, string $nickname, int $gender, string $avatar, string $mobile, string $email, string $remark): int
    {

        $di = di(UserService::class);

        $user = $di->getUser($user_id);

        if (empty($user)) {
            throw new UserNotFoundException();
        }

        return $di->save($user_id, [
            'role_id' => $role_id,
            'nickname' => $nickname,
            'gender' => $gender,
            'avatar' => $avatar,
            'mobile' => $mobile,
            'email' => $email,
            'remark' => $remark
        ]);

    }


    public function add(string $username, string $password, int $role_id, string $nickname, int $gender, string $avatar, string $mobile, string $email, int $status, string $remark)
    {
        $data = [
            'username' => $username,
            'password' => $this->createPassword($password),
            'role_id' => $role_id,
            'nickname' => $nickname,
            'gender' => $gender,
            'avatar' => $avatar,
            'mobile' => $mobile,
            'email' => $email,
            'status' => $status,
            'remark' => $remark,
        ];

        return di(UserService::class)->add($data);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function forbid(int $id)
    {
        $service = di(UserService::class);

        $info = $service->getUser($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->forbid($id);
    }

    public function resume(int $id): bool
    {
        $service = di(UserService::class);

        $info = $service->getUser($id);

        if (empty($info)) {
            throw new EmptyException();
        }

        return $service->resume($id);
    }

    /**
     * 修改密码
     * @param int $user_id
     * @param string $oldPassword
     * @param $password
     * @return int
     */
    public function password(int $user_id, string $oldPassword, $password)
    {
        $di = di(UserService::class);
        //验证password
        $user = $di->getUser($user_id);

        if (empty($user)) {
            throw new UserNotFoundException('账号不存在！', 1);
        }

        if (!$this->verifyPassword($oldPassword, $user->password)) {
            throw new ResultException('当前密码不正确！');
        }

        return $di->save($user_id, [
            'password' => $this->createPassword($password)
        ]);
    }

    /**
     * 设置密码
     * @param int $user_id
     * @param $password
     * @return int
     */
    public function setPassword(int $user_id, $password): int
    {
        $di = di(UserService::class);
        //验证password
        $user = $di->getUser($user_id);

        if (empty($user)) {
            throw new UserNotFoundException('账号不存在！', 1);
        }

        return $di->save($user_id, [
            'password' => $this->createPassword($password)
        ]);
    }

    /**
     * 验证password
     * @param string $inputPassword
     * @param string $passwordHash
     * @return bool
     */
    public function verifyPassword(string $inputPassword, string $passwordHash): bool
    {
        return password_verify($inputPassword, $passwordHash);
    }

    public function createPassword(string $str)
    {
        return password_hash($str, PASSWORD_DEFAULT);
    }

}