<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session', function (Blueprint $table) {
            $table->boolean('use_bots')->default(0)->after('prof_id');
        });

        Schema::table('student', function (Blueprint $table) {
            $table->boolean('is_bot')->default(0)->after('session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session', function(Blueprint $table) {
            $table->dropColumn('use_bots');
        });

        Schema::table('student', function(Blueprint $table) {
            $table->dropColumn('is_bot');
        });
    }
}
