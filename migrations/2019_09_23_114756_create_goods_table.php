<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cate_id')->default(0)->unsigned()->comment('分类ID');
            $table->string('title', 100)->nullable(false)->default('')->comment('商品名称');
            $table->text('logo')->nullable(false)->comment('logo');
            $table->text('images')->nullable(false)->comment('商品图片');
            $table->text('desc')->nullable(false)->comment('商品简述');
            $table->text('contents')->nullable(false)->comment('商品内容');
            $table->integer('sort', false)->default('0')->unsigned()->comment('排序');
            $table->smallInteger('status', false, true)->default(1)->comment('状态 0禁用 1启用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
}
