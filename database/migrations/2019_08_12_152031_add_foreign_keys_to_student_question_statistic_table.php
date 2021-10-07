<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToStudentQuestionStatisticTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('student_question_statistic', function(Blueprint $table)
		{
			$table->foreign('question_id', 'student_question_statistic_ibfk_1')->references('question_id')->on('questions')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('student_id', 'student_question_statistic_ibfk_2')->references('student_id')->on('student')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('student_question_statistic', function(Blueprint $table)
		{
			$table->dropForeign('student_question_statistic_ibfk_1');
			$table->dropForeign('student_question_statistic_ibfk_2');
		});
	}

}
