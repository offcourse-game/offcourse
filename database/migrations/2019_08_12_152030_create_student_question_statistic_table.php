<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentQuestionStatisticTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student_question_statistic', function(Blueprint $table)
		{
			$table->integer('student_id');
			$table->integer('question_id')->index('question_id');
			$table->boolean('correct');
			$table->float('duration', 10, 0)->nullable();
			$table->primary(['student_id','question_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('student_question_statistic');
	}

}
