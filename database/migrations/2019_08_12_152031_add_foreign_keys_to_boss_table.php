<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToBossTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('boss', function(Blueprint $table)
		{
			$table->foreign('session_id', 'boss_ibfk_1')->references('session_id')->on('session')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('boss', function(Blueprint $table)
		{
			$table->dropForeign('boss_ibfk_1');
		});
	}

}
