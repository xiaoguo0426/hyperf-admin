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
            $table->dropColumn('node');
            $table->dropColumn('url');
            $table->string('uri', 50)->after('title')->comment('菜单链接');
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
