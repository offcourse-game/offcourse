<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBossTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('boss', function(Blueprint $table)
		{
			$table->integer('boss_id')->autoIncrement();
			$table->integer('boss_life');
			$table->integer('boss_life_start');
			$table->integer('session_id')->index('session_id');
			$table->float('boss_health_option', 10, 0)->nullable()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('boss');
	}

}
