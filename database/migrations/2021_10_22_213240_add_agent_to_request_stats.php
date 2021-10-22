<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgentToRequestStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request_stats', function (Blueprint $table) {
            $table->text('agent')->after('channel_id')->nullable();
            $table->string('ip', 64)->after('agent')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request_stats', function (Blueprint $table) {
            $table->dropColumn('agent', 'ip');
        });
    }
}
