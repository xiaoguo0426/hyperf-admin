<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateProductCategoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_category', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', '30')->comment('分类名称');
            $table->unsignedInteger('sort', false)->default(0)->comment('排序');
            $table->unsignedTinyInteger('status', false)->comment('状态 0禁用 1启用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_category');
    }
}
