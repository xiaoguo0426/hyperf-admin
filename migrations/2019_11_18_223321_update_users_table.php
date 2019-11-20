<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_users', function (Blueprint $table) {
            //
            $table->string('logo', 255)->after('role_id')->comment('logo');
            $table->unsignedSmallInteger('gender', false)->default(0)->after('logo')->comment('性别 0女 1男');
            $table->string('mobile', 11)->default('')->after('gender')->comment('手机号');
            $table->string('email', 50)->default('')->after('mobile')->comment('邮箱');
            $table->string('remark', 255)->default('')->after('email')->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_users', function (Blueprint $table) {
            //
        });
    }
}
