<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_users', function (Blueprint $table) {
            //
            $table->dropColumn('logo');
            $table->string('avatar', 255)->after('role_id')->comment('logo');
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
