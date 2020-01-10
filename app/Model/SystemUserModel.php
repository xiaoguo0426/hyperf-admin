<?php
declare(strict_types=1);

namespace App\Model;


class SystemUserModel extends Model
{

    protected $table = 'system_users';

    public function role()
    {
        return $this->hasOne(SystemAuthModel::class, 'id', 'role_id');
    }
}