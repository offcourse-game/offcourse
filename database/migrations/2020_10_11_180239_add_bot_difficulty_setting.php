<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBotDifficultySetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session', function (Blueprint $table) {
            $table->tinyInteger('bot_difficulty')->default(2)->after('use_bots');
        });

        //keeps historic data accurate without setting unfitting default value
        DB::table('session')
            ->update(['bot_difficulty' => 4]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session', function (Blueprint $table) {
            $table->dropColumn('bot_difficulty');
        });
    }
}
