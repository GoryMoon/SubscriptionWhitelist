<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveCascadeOnMinecraftUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('whitelists', function (Blueprint $table) {
            $table->dropForeign(['minecraft_id']);
            $table->foreign("minecraft_id")->references('id')->on('minecraft_users')->onDelete('set null');
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
            $table->foreign("minecraft_id")->references('id')->on('minecraft_users')->onDelete('cascade');
        });
    }
}