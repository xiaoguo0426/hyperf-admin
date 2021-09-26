<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateAuthTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_auth', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('title', '30')->comment('权限名称');
            $table->string('desc', 255)->comment('权限描述');
            $table->integer('sort', false, true)->default(0)->comment('排序');
            $table->smallInteger('status', false, true)->comment('状态 0禁用 1启用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_auth');
    }
}
