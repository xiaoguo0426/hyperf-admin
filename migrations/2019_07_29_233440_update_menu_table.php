<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class UpdateMenuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_menu', static function (Blueprint $table): void {
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
        Schema::table('system_menu', static function (Blueprint $table): void {
            
        });
    }
}
