<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhitelists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whitelists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username');
            $table->boolean('valid')->default(true);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreignId("user_id")->constrained("twitch_users")->cascadeOnDelete();
            $table->unsignedBigInteger('channel_id');
            $table->foreignId("channel_id")->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('whitelists');
    }
}
