<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateGoodsSkuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_sku', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id', false, true)->comment('商品id');
            $table->integer('key_id', false, true)->comment('属性id');//goods_attr_key.id
            $table->integer('value_id', false, true)->comment('属性值id');//goods_attr_val.id
            $table->string('logo')->default('')->comment('sku图片');
            $table->integer('price', false, true)->default(0)->comment('sku价格，单位分');
            $table->smallInteger('status', false, true)->comment('状态 0禁用 1启用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_sku');
    }
}
