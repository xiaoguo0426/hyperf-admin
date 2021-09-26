<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateGoodsAttrKeyTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_attr_key', static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->integer('product_id', false, true)->nullable(false)->comment('商品ID');
            $table->string('key', 30)->nullable(false)->comment('属性');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_attr_key');
    }
}
