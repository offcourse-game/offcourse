<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSessionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('session', function(Blueprint $table)
		{
			$table->foreign('prof_id', 'session_ibfk_1')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('session', function(Blueprint $table)
		{
			$table->dropForeign('session_ibfk_1');
		});
	}

}
