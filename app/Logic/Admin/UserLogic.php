<?php


namespace App\Logic\Admin;


use App\Model\SystemUserModel;

class UserLogic
{

    public function get($where)
    {
        return SystemUserModel::query()->where($where)->first();
    }

    /**
     *
     * @param int $user_id
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    public function getUser(int $user_id)
    {
        $where = [
            'id' => $user_id
        ];
        return SystemUserModel::query()->where($where)->first();
    }

    public function getUserByName(string $user_name)
    {
//        return
    }


}