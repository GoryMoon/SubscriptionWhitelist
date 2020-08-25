<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSteamUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steam_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('steam_id');
            $table->string('name');
            $table->string('profile_url');
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->references('id')->on('twitch_users')->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('steam_users');
    }
}