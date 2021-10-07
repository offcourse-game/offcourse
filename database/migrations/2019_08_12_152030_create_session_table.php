<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSessionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('session', function(Blueprint $table)
		{
			$table->integer('session_id')->autoIncrement();
			$table->timestamp('session_time')->nullable();
			$table->boolean('is_active')->default(0);
			$table->string('session_name', 100);
			$table->integer('session_duration')->default(5);
			$table->timestamp('session_creation_date')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->integer('prof_id')->index('prof_id');
			$table->integer('session_option_rank')->default(3);
			$table->boolean('show_badges')->default(1);
			$table->boolean('is_training')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('session');
	}

}
