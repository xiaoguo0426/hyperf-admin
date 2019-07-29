<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_users', function (Blueprint $table) {
            //
            $table->dropColumn('role');

            $table->integer('role_id', false, true)->default('0')->after('nickname')->comment('角色id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_users', function (Blueprint $table) {
            //
        });
    }
}
