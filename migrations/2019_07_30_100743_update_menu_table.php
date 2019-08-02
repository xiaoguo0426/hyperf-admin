<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class UpdateMenuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_menu', function (Blueprint $table) {
            //
            $table->tinyInteger('test', false, false)->default(1)->comment('测试字段');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_menu', function (Blueprint $table) {
            //
        });
    }
}
