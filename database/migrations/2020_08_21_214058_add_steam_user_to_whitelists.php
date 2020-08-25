<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSteamUserToWhitelists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('whitelists', function (Blueprint $table) {
            $table->unsignedBigInteger("steam_id")->nullable();
            $table->foreign("steam_id")->references('id')->on('steam_users')->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('whitelists', function (Blueprint $table) {
            $table->dropForeign(['steam_id']);
            $table->dropColumn('steam_id');
        });
    }
}
