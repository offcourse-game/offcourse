<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use DB;

use App\models\GameSession;
use App\Http\Controllers\Bot;

class BotManager{

    //instance variables for bot manager
    private $sessionId;
    private $botList;

    /**
     * construct bot manager
     *
     * @param $sessionId
     * @return void
     */
    public function __construct($sessionId){
        $this->sessionId = $sessionId;
    }

    /**
     * This func handles all required actions for bots
     * Start this before game starts
     * Closes automatically after game has finished
     *
     * @return void
     */
    public function handle(){
        $botCount = 10 - GameSession::getNumberPlayers($this->sessionId);
        $botDifficulty = GameSession::getSessionOptionBotDifficulty($this->sessionId);
        $isTeams = GameSession::getSessionOptionTeams($this->sessionId);
        if($botCount <= 0) {
            GameSession::setBotStatus($this->sessionId, 1);
            Log::debug("Bot manager exits due to sufficient player count for session " . $this->sessionId);
            return;
        }

        Log::debug("Bot manager started for session " . $this->sessionId);

        $this->generateBots($botCount, $botDifficulty, $isTeams);
        GameSession::setBotStatus($this->sessionId, 1);

        // wait until the session has started
        while(GameSession::isSessionActive($this->sessionId) == 0) {
            sleep(1);
        }

        // update bots while the session runs
        while(GameSession::isSessionActive($this->sessionId) == 1) {
            $this->updateBots();
            sleep(1);
        }

        $this->destroyBots();

        Log::debug("Bot manager finished for session " . $this->sessionId);
    }

    /**
     * Create bots for game sessions
     *
     * @param  int $botCount
     * @param  int $botDifficulty 1 = easy, 2 = medium, 3 = strong, 4 = madness
     * @param  int $isTeams 0 = noTeams, 1 = teams mode
     * @return void
     */
    protected function generateBots($botCount, $botDifficulty, $isTeams){
        date_default_timezone_set('Europe/Berlin');
        $currentTime = date('Y-m-d H:i:s');

        $this->botList = array();

        for($i = 0; $i < $botCount; $i++) {
            array_push($this->botList, new Bot($this->sessionId, $currentTime, $botDifficulty, $isTeams));
        }
    }

    /**
     * Update bots and add answers
     *
     * @return void
     */
    protected function updateBots(){
        date_default_timezone_set('Europe/Berlin');
        $currentTime = date('Y-m-d H:i:s');

        foreach ($this->botList as $bot) {
            $bot->update($currentTime);
        }
    }

    /**
     * Destroy all bots after use
     *
     * @return void
     */
    protected function destroyBots(){
        try {
            foreach ($this->botList as $bot) {
                unset($bot);
            }
        } catch (\Exception $e) {
            Log::info("Bot manager job could not destroy bots for session " . $this->sessionId . "!" .  $e->getMessage());
        }
    }
}
