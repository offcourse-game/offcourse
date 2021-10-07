<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student', function(Blueprint $table)
		{
			$table->integer('student_id')->autoIncrement();
			$table->integer('student_life');
			$table->integer('session_id')->index('session_id');
			$table->integer('player_type_id');
			$table->integer('best_streak');
			$table->integer('actual_streak');
			$table->integer('rank')->nullable();
			$table->integer('achievm_view_time')->nullable();
			$table->string('achievm_1', 250)->nullable();
			$table->string('achievm_1_color', 25)->nullable();
			$table->string('achievm_2', 250)->nullable();
			$table->string('achievm_2_color', 25)->nullable();
			$table->string('achievm_3', 250)->nullable();
			$table->string('achievm_3_color', 25)->nullable();
			$table->integer('score')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('student');
	}

}
