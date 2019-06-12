<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMinecraftUserToWhitelists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('whitelists', function (Blueprint $table) {
            $table->unsignedBigInteger("minecraft_id")->nullable();
            $table->foreign("minecraft_id")->references('id')->on('minecraft_users')->onDelete("cascade");
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
            $table->dropForeign(['minecraft_id']);
            $table->dropColumn('minecraft_id');
        });
    }
}
