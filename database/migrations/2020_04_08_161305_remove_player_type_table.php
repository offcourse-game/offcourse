<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemovePlayerTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('player_type');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('player_type', function(Blueprint $table)
        {
            $table->integer('player_type_id')->primary();
            $table->float('damage', 10, 0);
            $table->integer('player_type_life');
        });

        // add default values for player_type
        DB::table('player_type')->insert([
            ['player_type_id' => '1',
                'damage' => '1',
                'player_type_life' => '5'],
            ['player_type_id' => '2',
                'damage' => '2',
                'player_type_life' => '3']

        ]);
    }
}
