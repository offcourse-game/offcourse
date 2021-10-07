<?php

namespace App\Http\Controllers;
use App\models\Question;
use DB;
use Illuminate\Support\Facades\Log;
use App\models\Boss;
use App\models\GameSession;
use App\models\Achievement;
use App\Jobs\BotManagerJob;

/**
 * Responsible for the projector during the game (contains the QR-Code view).
 */
class ProjectorController extends Controller{

    /**
     * This starts the game.
     *
     * @param  int $sessionId
     * @return \View boss
     */
    public function startGame($sessionId){
        // If the prof reloads the boss page the game starts again, with a reset time and boss life.
        if (GameSession::checkAuthority($sessionId) == 0){
            return redirect()->route('welcome');
        }

        //return to scanQR if not enough participants (edge case)
        $gameModeType = GameSession::getSessionGameModeType($sessionId);
        if ($gameModeType != 'teams'){
            $bossData = GameSession::getBossData($sessionId);
            if($bossData['studentCount'] < 1){
                Log::warning("Start Game was aborted due to insufficient player count for session " . $sessionId);
                return ProjectorController::projectorScanQR($sessionId);
            }
        }else{
            $teamsData = GameSession::getTeamsData($sessionId);
            if($teamsData['studentCountBlue'] < 1 || $teamsData['studentCountRed'] < 1){
                Log::warning("Start Game was aborted due to insufficient player count for session " . $sessionId);
                return ProjectorController::projectorScanQR($sessionId);
            }
        }

        if(GameSession::getSessionOptionBots($sessionId) == 1) {
            //execute script to launch new laravel instance for session bots in background
            if(env('SHELL') == '/bin/bash') { // linux
                exec('bash -c \'exec nohup setsid php -f ../app/Http/Controllers/BotManagerLauncher.php ' . $sessionId . ' > /dev/null 2>&1 &\'');
            }elseif (env('SHELL') == 'powershell'){ // windows
                //requires php_com_dotnet.dll
                $handle = new \COM('WScript.Shell');
                $handle->Run("php -f ../app/Http/Controllers/BotManagerLauncher.php " . $sessionId, 0, false);
            }

            //wait max $botTimeout seconds until bots have been created
            $botTimeout = 7;
            for($i = 0; $i < $botTimeout && GameSession::getBotStatus($sessionId) != 1; $i++) {
                sleep(1);
                if($i == $botTimeout -1) {
                    Log::warning("Bot creation exceeded timeout of " . $botTimeout . " seconds. Proceeding without bots for session " . $sessionId);
                }
            }
        }

        GameSession::insertGameTime($sessionId);
        GameSession::activateSession($sessionId);
        Boss::scaleBossLife($sessionId);

        Log::info("Started Game " . $sessionId);

        if ($gameModeType == 'noGame' || 
                (GameSession::getSessionOptionStory($sessionId) == 0 && $gameModeType == 'classic')){ //noStory
            //no Story, with or without game
            return view('projector.MinimalGame')->with([
                'sessionId' => $sessionId,
                'isGame' => GameSession::getSessionOptionGame($sessionId),
            ]);
        }elseif($gameModeType == 'training'){
            return view('projector.flower')->with([
                'sessionId' => $sessionId
            ]);
        }elseif ($gameModeType == 'teams'){
            return view('projector.Teams')->with([
                'sessionId' => $sessionId
            ]);
        }else{
            return view('projector.boss')->with([
                'sessionId' => $sessionId
            ]);
        }
    }

    /**
     * @param  int $sessionId
     * @return void
     */
    public function endGame($sessionId){
        GameSession::endGame($sessionId);
    }

    /**
     * Sends all boss data.
     *
     * @param  int $sessionId
     * @return \ArrayObject associative array [int bossLife, int studentCount, \DateTime endTime,
     *                             int bossLifeStart, int studentCountLeftGray, int studentCountLeftGold]
     */
    public function getBossData($sessionId){
        return GameSession::getBossData($sessionId);
    }

    /**
     * Sends all teams data.
     *
     * @param  int $sessionId
     * @return \ArrayObject associative array [int pointsBlue, int pointsRed, int gameTime, int studentCountBlue, int studentCountRed, int studentCountLeftBlue, int studentCountLeftRed]
     */
    public function getTeamsData($sessionId){
        return GameSession::getTeamsData($sessionId);
    }

    /**
     * Gets called when the user displays the QR-Code.
     *
     * @param  int $sessionId
     * @return \View projectorScanQR
     */
    public function projectorScanQR($sessionId){
        // Have this as a function here to check the Authority
        if (GameSession::checkAuthority($sessionId) == 0) return redirect()->route('welcome');

        // check if the game has not been played yet
        if (GameSession::isSessionPlayable($sessionId) == 0) return redirect()->route('welcome');

        return view('projector.ScanQR')->with([
            'sessionId' => $sessionId,
            'gameModeType' => GameSession::getSessionGameModeType($sessionId),
            'sessionDuration' => GameSession::getSessionDuration($sessionId),
            'bossHealthOption' => Boss::getBossHealthOption($sessionId),
            'questionCount' => Question::getQuestionCount($sessionId),
            'lang' => GameSession::getSessionOptionLanguage($sessionId),
        ]);
    }

    public static function stopGame($sessionId){

        // check if the user has permission. User has to be logged in to our system AND has to be right user id
        if (GameSession::checkAuthority($sessionId) == 0)
        	return 0;

		// save the players score & rank here, some points might get lost in some cases (scores received between save and end game)
		Achievement::savePlayerScore($sessionId);
        Log::info("Ended Game " . $sessionId);

        GameSession::endGame($sessionId);
    }
}
