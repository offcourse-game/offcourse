<?php

namespace App\Http\Controllers;
use DB;
use App\models\GameSession;
use App\models\Student;
use App\models\Achievement;

/**
 * Controls the end screen on the projector.
 */
class ProjectorGameFinishController extends Controller{

    /**
     * Opens the last screen on the projector, which displays the winner.
     *
     * @param  int $sessionId
     * @return \View projectorGameOver
     */
    public function openProjectorView($sessionId)
	{

		// check authority
		if (!GameSession::checkAuthority($sessionId)) return redirect()->route('editorHome');

        if (GameSession::isSessionActive($sessionId)){
            // should be done already save the players score & rank here, some points might get lost in some cases (scores received between save and end game)
            Achievement::savePlayerScore($sessionId);

            // let the game end in the db
            // This should be done already, but if we call the url directly it should still work,
            // so better do it twice
            GameSession::endGame($sessionId);
        }

		//What name has the best player
		$projectorOptionRank = GameSession::getSessionOptionRank($sessionId, 0);
		if ($projectorOptionRank == 0) {
			$bestPlayers = Achievement::getBestPlayers($sessionId, 1);
		} else {
			$bestPlayers = Achievement::getBestPlayers($sessionId, -1);
		}

        $gameModeType = GameSession::getSessionGameModeType($sessionId);
        foreach($bestPlayers as $bestPlayer){
                $bestPlayer->student_name = Student::getPlayerNickname($bestPlayer->student_id, $sessionId);
                $bestPlayer->is_bot = Student::isBot($bestPlayer->student_id);
                // correctAnsweredQuestions and answeredQuestions is not in stat_data as we have already have a func for this
                $bestPlayer->correctAnsweredQuestions = Achievement::getCorrectAnsweredQuestions($bestPlayer->student_id);
                $bestPlayer->answeredQuestions = Achievement::getAnsweredQuestions($bestPlayer->student_id);
                $bestPlayer->stat_data = Achievement::getStudentGameOverData($bestPlayer->student_id);

                if ($gameModeType == "training"){
                    if ($bestPlayer->player_type_id == 1){
                        $bestPlayer->player_type_image = "training/characterHoneyBee";
                    }else{
                        $bestPlayer->player_type_image = "training/characterBumbleBee";
                    }
                }else if ($gameModeType == "teams"){
                    if ($bestPlayer->player_type_id == 1){
                        $bestPlayer->player_type_image = "characterTeamBlue";
                    }else{
                        $bestPlayer->player_type_image = "characterTeamRed";
                    }
                }else{
                    if ($bestPlayer->player_type_id == 1){
                        $bestPlayer->player_type_image = "characterGray";
                    }else{
                        $bestPlayer->player_type_image = "characterGold";
                    }
                }
        }

        if (GameSession::getSessionGameModeType($sessionId) == 'noGame'){
            return redirect()->route('editorStatistic', ['sessionId' => $sessionId]);
        }

        $pointsBlue = 0; // gobal var to avoid two returns
        $pointsRed = 0;
        if (GameSession::getSessionGameModeType($sessionId) == 'teams'){
            $teamData = GameSession::getTeamsData($sessionId);
            $pointsBlue = $teamData['pointsBlue'];
            $pointsRed = $teamData['pointsRed'];
        }

        return view('projector.GameOver')->with(
            [
                'gameWin' => GameSession::gameWin($sessionId),
                'bestPlayers' => $bestPlayers,
                'sessionId' => $sessionId,
                'usePoints' => GameSession::getSessionOptionPoints($sessionId),
                'showStory' => GameSession::getSessionOptionStory($sessionId),
                'gameModeType' => GameSession::getSessionGameModeType($sessionId),
                'pointsBlue' => $pointsBlue,
                'pointsRed' => $pointsRed,
            ]
        );
    }
}
