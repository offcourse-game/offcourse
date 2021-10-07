<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\BaseController;
use App\models\Question;
use App\models\Student;
use App\models\Answer;
use App\models\Boss;
use App\models\GameSession;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;

/**
 * Controls the mobile screens during the game. Send and receives the questions/answers.
 */
class QuestionController extends Controller{

    /**
     * Returns the students the questions.
     *
     * @return \View question
     */
    public function openQuestionView(){
        // Check if session is active

        $studentId = session('studentId');
        $sessionId = session('sessionId');

        if ($studentId == Null || $sessionId == Null){
            Log::notice("Student tried to get question, but session('studentId') == Null || session('sessionId') == Null");
            return view('mobile.Error')->with(['error' => 'Du bist nicht spielberechtigt!']);
        }

        $isActive = GameSession::isSessionActive($sessionId);
        $endGameTime = GameSession::getEndDateTime($sessionId);
        $isTraining = GameSession::isTraining($sessionId);
        $showStory = GameSession::getSessionOptionStory($sessionId);
        $isGame = GameSession::getSessionOptionGame($sessionId);

        // We have a $endGameTime so the session is just over
        if ($isActive == 0 && $endGameTime){
            return redirect('gameFinish');
        }elseif ($isActive == 0){
            Log::notice("Student " . $studentId . " tried get a question, but " . $sessionId . " is not active");
            return view('mobile.Error')->with(['error' => 'Die Session ist nicht aktiv!']);
        }

        $question = Question::getNewQuestion($studentId, $sessionId);
        if ($question == null){
            return redirect('gameOver');
        }

        $questionId = $question->question_id;
        $questionText = $question->question_text;
        $imagePath = $question->image_path;

        if (GameSession::getSessionOptionNumberCorrectAnswers($sessionId) == 0){
            $numberCorrectAnswers = -1;
        }else{
            $numberCorrectAnswers = $question->number_correct_answers;
        }

        $studentLife = Student::getStudentLife($studentId);
        $answers = Answer::getAnswers($questionId, 0);
        if ($studentLife <= 0) return redirect('gameOver');

        date_default_timezone_set('Europe/Berlin');
        $actualDateTime = date('Y-m-d H:i:s');
        $questionIds = session('questionIds');

        if (is_null($questionIds) || $questionIds->isEmpty()){
            session(['questionIds' => collect([$questionId => $actualDateTime])]);
        }else{
            session(['questionIds' => $questionIds->put($questionId, $actualDateTime)]);
        }

        return view('mobile.question')->with(
            [
                'studentLife' => $studentLife,
                'playerTypeId' => Student::getType($studentId),
                'questionText' => $questionText,
                'questionId' => $questionId,
                'answers' => $answers,
                'studentId' => $studentId,
                'imagePath' => $imagePath,
                'numberCorrectAnswers' => $numberCorrectAnswers,
                'isTraining' => $isTraining,
                'showStory' => $showStory,
                'isGame' => $isGame,
                'gameModeType' => GameSession::getSessionGameModeType($sessionId),
                'showNumberCorrectAnswers' => GameSession::getSessionOptionNumberCorrectAnswers($sessionId),
            ]);
    }

    /**
     * Send answers into the DB, check if the answers are correct.
     *
	 * @return { \ArrayObject [
	 * 					 [string FLAG_REDIRECT", <string redirect>, <int/string data>],
	 * 					<[int (boolean 1/0) correct, \Collection "correctAnswers" [int answer_id, string answer_text, int (boolean 1/0) correct]]>,
	 * 					<[int actualStreak, int studentLife, int studentId]>,
	 * 					<[string questionText, int questionId, \Collection "nextAnswers" [int answer_id, string answer_text], string imagePath]>  ]}
	 *
     */
    public function sendAnswers(Request $request){
        // no debug´s allowed here, caused of json decode on client side !!
        $studentId = session('studentId');
        $sessionId = session('sessionId');

        /******************************************************************************************************************************
         * Possible attack surface: The user can fake ajax calls.                                                                     *
         *                                                                                                                            *
         * How to: Use chrome, send an answer and directly after that stop js via "dev-tools -> source".                              *
         * Now go to network, find the sendAnswers ajax call and right click on it -> Copy -> copy as cURL.                           *
         * Paste that into a linux terminal. Now you are able to modify the request.                                                  *
         *                                                                                                                            *
         * Now because we use the larvel session you can only send requests for your user and you can not impersonate another player! *
         * However you can now resend a request for your user:                                                                        *
         *                                                                                                                            *
         *  - with known answers to the attacker: will get an sql Integrity error.                                                    *
         *  - with not yet answered question: will return error                                                                       *
         *  - join a lot of users at the same time: we can not protect against this currently                                         *
         *                                                                                                                            *
         * We will do our best here to restrict such cheating behavior,                                                               *
         * but it will need *a lot* more work to make it completely secure, if that is even possible and worth the work:)             *
         ******************************************************************************************************************************/

        if ($studentId == Null || $sessionId == Null || Student::getStudentLife($studentId) < 0) {
            Log::notice("Student tried to send an answer, but $studentId == Null || $sessionId == Null || Student::getStudentLife($studentId) < 0");
			return json_encode([["FLAG_REDIRECT", "Error", "/Error/Data"]]);
        }

        $questionId = $request->input("questionId");
        $givenAnswers = $request->input("answers");
        $actualStreak = 0;
        $correctAnswers = Answer::getAnswers($questionId, 0);
        if (GameSession::isTraining($sessionId) == 1) $playerDamage = 1;
        else $playerDamage = Student::getPlayerDamage($studentId);

        // check if the given answers are correct
        $correct = 1;
        $count = 0;
        foreach ($correctAnswers as $correctAnswer){
            if($correctAnswer->correct != $givenAnswers[$count])
            	$correct = 0;
            $count++;
        }

        // prepare to be able to end the game
        $isActive = GameSession::isSessionActive($sessionId);
        $endGameTime = GameSession::getEndDateTime($sessionId);

        // We have a $endGameTime so the session is just over
        if ($isActive == 0 && $endGameTime){
			$studentLife = Student::getStudentLife($studentId);
			return json_encode([["FLAG_REDIRECT", "GameFinish"], [$correct, $correctAnswers], [$actualStreak, $studentLife, $studentId]]);
		} elseif ($isActive == 0){
            Log::notice("Student " . $studentId . " tried to send an answer, but " . $sessionId . " is not active");
            return json_encode([["FLAG_REDIRECT", "Error", "/Error/Inactive"]]);
		}

        // insert infos to db, if the user cheats will return 0
        $correct = Answer::insertAnswerInformation($studentId, $questionId, $correct);

        if ($correct == -1){
            // this error is logged already
            return json_encode([["FLAG_REDIRECT", "Error", "/question"]]);
        }

        // decrement boss or student life
        if ($correct == 1){
            Boss::decrementBossLife($sessionId, $playerDamage);
            $actualStreak = Student::incrementStudentStreak($studentId);
        } else{
            if (GameSession::isTraining($sessionId) == 0) Student::decrementStudentLife();
            Student::stopStudentStreak($studentId);
        }
		$studentLife = Student::getStudentLife($studentId);

		$question = Question::getNewQuestion($studentId, $sessionId);
		if ($question == null || $studentLife <= 0){
			return json_encode([["FLAG_REDIRECT", "GameOver"], [$correct, $correctAnswers], [$actualStreak, $studentLife, $studentId]]);
		}

		$questionId = $question->question_id;
		$questionText = $question->question_text;
		$imagePath = $question->image_path;
        $numberCorrectAnswers = $question->number_correct_answers;
		$nextAnswers = Answer::getAnswers($questionId, 1);

        // we must save the question id to our session when loading the next question to be able to check the response
        date_default_timezone_set('Europe/Berlin');
        $actualDateTime = date('Y-m-d H:i:s');
        $questionIds = session('questionIds');

        if (is_null($questionIds) || $questionIds->isEmpty()){
            session(['questionIds' => collect([$questionId => $actualDateTime])]);
        }else{
            session(['questionIds' => $questionIds->put($questionId, $actualDateTime)]);
        }

        return json_encode([["FLAG_ANSWER"], [$correct, $correctAnswers], [$actualStreak, $studentLife, $studentId], [$questionText, $questionId, $nextAnswers, $imagePath, $numberCorrectAnswers]]);
    }

    /**
     * Check if Game is over.
     *
	 * @return \ArrayObject associative array [boolean gameFinished <0|1>, int bossHp <0-1>, int characterHP <0-1>]
     */
    public function checkGameStatus(){
        $sessionId = session('sessionId');
        $response = [];

		$bossData = GameSession::getBossData($sessionId);
		$bossLifeStart = $bossData['bossLifeStart'];

        if($bossLifeStart <= 0){
            if ($bossData['bossLife'] <= 0){
                // This happens when only the game has not jet started.
                // $response['bossHp'] will be 0 in this case as $bossData['bossLife'] is 0 and therefor 0/1 = 0.
                // We need this as we otherwise get divion by zero errors.
                Log::notice("checkGameStatus called, but we have no bossLife > 0 and no bossLifeStart > 0. SessionID = ". $sessionId);
                $bossLifeStart = 1;
            }else{
                $bossLifeStart = $bossData['bossLife'];
            }
        }

		$response['gameFinished'] = (GameSession::isSessionActive($sessionId) == 0);
		$response['bossHp'] = $bossData['bossLife'] / $bossLifeStart;
		$response['characterCountGold'] = $bossData['studentCountLeftGold'];
		$response['characterCountGray'] = $bossData['studentCountLeftGray'];

		return $response;
    }

    /**
     * Called by ajax at page leave to decremet the student life.
     *
     * Needs an Illuminate\Support\Facades\Input "questionId" int.
     *
     * @return void
     */
    public function leaveQuestionPage(Request $request){
        $studentId = session('studentId');
        $sessionId = session('sessionId');

        if (! is_numeric($sessionId) || ! is_numeric($sessionId) || GameSession::isSessionActive($sessionId) != 1){
           Log::notice("leaveQuestionPage called, but we have no studentId/sessionId or session not active. question_id = " . $request->input("questionId"));
           return "Error, we dont know who you are!";
       }

        $questionId = $request->input("questionId");
        Answer::insertAnswerInformation($studentId, $questionId, 0);
        if (GameSession::isTraining($sessionId) == 0) Student::decrementStudentLife();
    }
}
