<?php

namespace App\Http\Controllers;
use App\models\Achievement;
use DB;
use App\models\GameSession;
use App\models\Boss;
use App\models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * Responsible for the end screen (which shows the achievements) on the mobile devices of the students.
 */
class StudentGameFinishController extends Controller{


	/**
	 * displays all achievements for debugging
	 *
	 * @return \View badgeTest
	 */
	public function openAchievementDebugView() {
		include 'Achievement.php'; //ugly, but necessary

		$achievements = [];
		$achievementManager = new AchievementManager(1);
		$achievements = $achievementManager->debugAchievements(0, 0, false);

		return view('badgeTest')->with(['achievements' => $achievements]);
	}

    /**
     * Displays the achievements on the student devices.
     *
     * @return \View mobileAchievement
     */
    public function openGameFinishView() {
		include 'Achievement.php'; //ugly, but necessary

		$studentId = session('studentId');
        $sessionId = session('sessionId');

        //for testing / debugging the rank list just set sessionId and studentId to fixed values here
        //$sessionId = 0;
        //$studentId = 0;

        $gameModeType = GameSession::getSessionGameModeType($sessionId);

        if($studentId == Null || $sessionId == Null) {
			Log::notice("Student tried to reaload the achievements view :/");
			return view('mobile.Error')->with(['error' => 'Error.achievements_seen']);
		}

        if(GameSession::isSessionExisting($sessionId) == 0) {
            Log::notice("Student tried to join a not existing session :/");
            return view('mobile.Error')->with(['error' => 'Error.deleted_session']);
        }

		if(GameSession::isSessionActive($sessionId) == 1) {
			Log::notice("Student tried to reload the achievements view :/");
			return view('mobile.Error')->with(['error' => 'Error.achievements_seen_game_open']);
		}

        $pointsBlue = 0;    //global vars to avoid two returns
        $pointsRed = 0;
        $gameWin = 0;
        if($gameModeType == "teams") {
            $teamData = GameSession::getTeamsData($sessionId);
            $pointsBlue = $teamData['pointsBlue'];
            $pointsRed = $teamData['pointsRed'];
        } else {
            $bossLife = Boss::getBossLife($sessionId);
            $gameWin = $bossLife <= 0 ? 1 : 0;
        }

        //generate, check & save achievements
        $achievements = [];
		$achievementManager = new AchievementManager(GameSession::getSessionDuration($sessionId) / 3);
		$achievements = $achievementManager->getAchievements($studentId, $sessionId);
		Achievement::saveAchievements($studentId, $achievements);


        $studentName = Student::getPlayerNickname($studentId, $sessionId);
        $projectorOptionRank = GameSession::getSessionOptionRank($sessionId, 1);
        $numberPlayers = GameSession::getNumberPlayers($sessionId);
        $showBadges = GameSession::getSessionOptionBadges($sessionId);

		$bestPlayers = "";
        if ($projectorOptionRank == 1){
            $studentRank = Achievement::getPlayerQuestionRank($studentId, $sessionId, false);
			$bestPlayers = Achievement::getBestPlayers($sessionId, -1);
			foreach($bestPlayers as $bestPlayer){
				$bestPlayer->student_name = Student::getPlayerNickname($bestPlayer->student_id, $sessionId);
			}
		}
        else {
        	$studentRank = -1;
		}

        $player_type_id = Student::getType($studentId);
		$studentLife = Student::getStudentLife($studentId);
        $studentStartLife = GameSession::getStudentStartLife($player_type_id, $sessionId);
        $numberAnswered = Achievement::getAnsweredQuestions($studentId);
        $correctAnswered = Achievement::getCorrectAnsweredQuestions($studentId);
        $score = Achievement::getStudentScore($studentId);
		$answersQuestions = Achievement::getQuestionsByStudent($studentId);
		$usePoints = GameSession::getSessionOptionPoints($sessionId);
		$showStory = GameSession::getSessionOptionStory($sessionId);
		$sessionSettings = GameSession::getCurrentSessionSettings($sessionId)[0];

        return view('mobile.Achievement')->with(
            [
                'numberPlayers' => $numberPlayers,
                'gameWin' => $gameWin,
                'studentId' => $studentId,
                'achievements' => $achievements,
                'studentName' => $studentName,
                'studentRank' => $studentRank,
                'player_type_id' => $player_type_id,
                'studentLife' => $studentLife,
                'studentStartLife' => $studentStartLife,
                'numberAnswered' => $numberAnswered,
                'correctAnswered' => $correctAnswered,
                'score' => $score,
                'showBadges' => $showBadges,
                'showStory' => $showStory,
                'studentID' => $studentId,
				'answersQuestions' => $answersQuestions,
				'bestPlayers' => $bestPlayers,
				'gameModeType' => $gameModeType,
				'sessionSettings' => $sessionSettings,
                'pointsBlue' => $pointsBlue,
                'pointsRed' => $pointsRed,
            ]
        );
    }

    /**
     * Save the achievement view time to db.
     *
     * Needs an Illuminate\Support\Facades\Input "viewTime" int in ms as input via ajax
     *
     * @return void
     */
    public function saveViewTime(Request $request){
        $viewTime = $request->input("viewTime");
        $studentId = session('studentId');
        $sessionId = session('sessionId');

        if ( $studentId && $viewTime != 0){
            // only save the the time if we have not replaced this student_already already!
            if (GameSession::isSessionActive($sessionId) == 0 && GameSession::getEndDateTime($sessionId) != Null){
                Student::saveViewTime($studentId, round($viewTime/1000));
            }
        }
    }

    /**
     * Delete a student in a session. This function gets called by ajax.
     *
     * @return void
     */
    public function deleteStudent(){
        $studentId = session('studentId');
        $sessionId = session('sessionId');

        if ( $studentId ){
            // only forget the ids if we have not replaced this student already!
            if (GameSession::isSessionActive($sessionId) == 0 && GameSession::getEndDateTime($sessionId) != Null){
                session()->forget('studentId');
                session()->forget('sessionId');
            }
        }
    }

	/**
     * Displays the waiting page on the students devices.
     *
     * @return \View gameover
     */
    public function openGameOverView(){
		$studentId = session('studentId');
        $sessionId = session('sessionId');

		if (Student::getStudentLife($studentId) > 0){
			$studentOut = 0; // no more questions left
		}else{
			$studentOut = 1; // no more lives left
		}

		return view('mobile.GameOver')->with([
			'studentOut' => $studentOut,
			'gameModeType' => GameSession::getSessionGameModeType($sessionId),
			'showStory' => GameSession::getSessionOptionStory($sessionId),
			'usePoints' => GameSession::getSessionOptionPoints($sessionId),
			'showBadges' => GameSession::getSessionOptionBadges($sessionId),
			'showRank' => GameSession::getSessionOptionRank($sessionId, 1),
		]);
	}
}
