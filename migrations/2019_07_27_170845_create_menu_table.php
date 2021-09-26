<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_menu', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->bigInteger('pid')->default(0)->unsigned();
            $table->string('title', 20)->comment('名称');
            $table->string('node', 50)->comment('节点代码');
            $table->string('icon', 50)->comment('图标');
            $table->string('url', 100)->comment('链接');
            $table->string('params', 50)->comment('链接参数');
            $table->integer('sort', false, true)->default(0)->comment('排序');
            $table->smallInteger('status', false, true)->default(1)->comment('状态 0禁用 1启用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_menu');
    }
}
