<?php

namespace App\models;
use Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class Answer{
    /**
     * Gives all answers from a question.
     *
     * @param  int $questionId int
	 * @param  int $hideCorrect boolean 1/0
     * @return \Collection int answer_id, string answer_text, int (boolean 1/0) correct
	 * 		 | \Collection int answer_id, string answer_text
     */
    public static function getAnswers($questionId, $hideCorrect){
    	if($hideCorrect == 1) {
			$answers = DB::table('answers')
				->select('answer_id', 'answer_text')
				->where('question_id', $questionId)
				->get();
		} else {
			$answers = DB::table('answers')
				->where('question_id', $questionId)
				->get();
		}

        return $answers;
    }

    /**
     * Save the answerInformations of students into the DB.
     *
     * @param  int $studentId
     * @param  int $questionId
     * @param  int $correct boolean 1/0
     * @return int correct will be 0 if the answers needs more ten 45 sec. || -1 when we have an error
     */
    public static function insertAnswerInformation($studentId, $questionId, $correct){
        // Check if we have already given out questions
        if (session('questionIds') == Null){
            Log::warning("No Question send to player yet: given question: " . $questionId . " was not send out for player: " . $studentId);
            return -1;
        }

        // Check if the answer is for a given question.
        if ( ! session('questionIds')->has($questionId)){
            Log::warning("QuestionId mismatch: given question: " . $questionId . " was not send out for player: " . $studentId);
            return -1;
        }

        $startTime = session('questionIds')->get($questionId);
        date_default_timezone_set('Europe/Berlin');
        $endTime = date('Y-m-d H:i:s');
        $duration = strtotime($endTime)-strtotime($startTime);

        /*********************************************************************************************************************
         * Cheat protection:                                                                                                 *
         *                                                                                                                   *
         * The problem we have is that the user can disable the local question timer via js console: "clearInterval(timer);" *
         * However we can detect if the question needs too long to be answerd.                                               *
         * This can be caused by a bad network connection or by a cheating user, we can not easily distinguish them.         *
         *                                                                                                                   *
         * Thats why we just say, if the answer needs more then 35 seconds after the time is up, it will be treated as wrong!*
         *********************************************************************************************************************/

        if ($duration > 65){
            Log::warning("Student" . $studentId . "send answer for question" . $questionId . "with $duration > 45. Cheat protection was actived!");
            $duration = 30;
            $correct = 0;
        }elseif($duration <= 65 && $duration > 30){
            $duration = 30; // make sure we dont get over the maximum
        }

        try {
            DB::table('student_question_statistic')->insert(
                ['student_id' => $studentId, 'question_id' => $questionId, 'correct' => $correct, 'duration' => $duration]
            );
        } catch (QueryException $ex) {
            Log::warning("insert of question answer failed:" . $ex);
            return -1;
        }
        return $correct;
    }
}
