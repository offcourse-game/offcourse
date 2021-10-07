<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerTimestamp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_question_statistic', function (Blueprint $table) {
            $table->timestamp('answer_date_time')->useCurrent()->nullable();
        });

        // Keep the old values at NULL
        DB::statement('UPDATE student_question_statistic SET answer_date_time = NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_question_statistic', function (Blueprint $table) {
            $table->dropColumn('answer_date_time');
        });
    }
}
