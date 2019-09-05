<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\SystemUserModel;

class UserService extends BaseService
{

    public function getUserByName(string $username)
    {
        return SystemUserModel::query()->where('username', $username)->first();
    }

}