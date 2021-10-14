<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatreonUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patreon_users', function (Blueprint $table) {
            $table->id();
            $table->string('patreon_id');
            $table->string('vanity')->nullable();
            $table->string('url')->nullable();
            $table->string('campaign_id')->nullable();
            $table->string('access_token', 500);
            $table->string('refresh_token', 500);
            $table->foreignId('user_id')->constrained('twitch_users')->cascadeOnDelete();
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
        Schema::dropIfExists('patreon_users');
    }
}
