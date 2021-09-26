<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_users', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('username', 20);
            $table->string('password', 64);
            $table->string('nickname', 20);
            $table->integer('role_id', false, true)->default('0')->comment('角色id');
            $table->string('avatar', 255)->comment('头像');
            $table->unsignedSmallInteger('gender', false)->default(0)->comment('性别 0女 1男');
            $table->string('mobile', 11)->default('')->comment('手机号');
            $table->string('email', 50)->default('')->comment('邮箱');
            $table->string('remark', 255)->default('')->comment('备注');
            $table->timestamps();
            $table->tinyInteger('status');

            // 指定表存储引擎
            $table->engine = 'InnoDB';
            // 指定数据表的默认字符集
            $table->charset = 'utf8';
            // 指定数据表默认的排序规则
            $table->collation = 'utf8_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_users', static function (Blueprint $table): void {
            
        });
    }
}
