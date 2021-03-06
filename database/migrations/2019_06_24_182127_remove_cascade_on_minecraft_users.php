<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->foreignId('minecraft_id')->constrained('minecraft_users')->nullOnDelete();
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
            $table->foreignId('minecraft_id')->constrained('minecraft_users')->cascadeOnDelete();
        });
    }
}
