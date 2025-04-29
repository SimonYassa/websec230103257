<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialAuthColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add columns for social authentication if they don't exist
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable();
            }
            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id', 'avatar']);
        });
    }
}