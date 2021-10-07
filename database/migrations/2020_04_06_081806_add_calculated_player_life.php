<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCalculatedPlayerLife extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session', function (Blueprint $table) {
            $table->boolean('use_dynamic_start_life')->default(0)->after('show_number_correct_answers');
            $table->integer('start_life_player_type_1')->default(5)->after('use_dynamic_start_life');
            $table->integer('start_life_player_type_2')->default(3)->after('start_life_player_type_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session', function (Blueprint $table) {
            $table->dropColumn('use_dynamic_start_life');
            $table->dropColumn('start_life_player_type_1');
            $table->dropColumn('start_life_player_type_2');
        });
    }
}
