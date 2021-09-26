<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateProductCategoryTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_category', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('顶级菜单id 默认0');
            $table->string('title', '30')->comment('分类名称');
            $table->unsignedInteger('sort', false)->default(0)->comment('排序');
            $table->string('desc', 50)->default('')->comment('描述');
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
