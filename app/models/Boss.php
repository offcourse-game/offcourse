<?php

namespace App\models;
use Illuminate\Support\Facades\DB;

class Boss{
    /**
     * @param  int $sessionId
     * @return int boss life
     */
    public static function getBossLife($sessionId){
        $bossLife = DB::table('boss')
            ->where('session_id', $sessionId)
            ->value('boss_life');
        return $bossLife;
    }
	
	/**
	 * @param  int $sessionId
	 * @return int boss life at start
	 */
	public static function getBossMaxLife($sessionId){
		$bossLifeStart = DB::table('boss')
			->where('session_id', $sessionId)
			->value('boss_life_start');
		return $bossLifeStart;
	}
	
	/**
	 * @param  int $sessionId
	 * @return float boss health option (factor)
	 */
	public static function getBossHealthOption($sessionId){
		$bossHealthOption = DB::table('boss')
			->where('session_id', $sessionId)
			->value('boss_health_option');
		return $bossHealthOption;
	}

    /**
     * @param  int $sessionId
     * @param  int $damage how much damage
     * @return void
     */
    public static function decrementBossLife($sessionId, $damage){
        DB::table('boss')
            ->where('session_id', $sessionId)
            ->decrement('boss_life', $damage);
    }

    /**
     * Scale Boss and save to DB.
     *
     * @param  int $sessionId
     * @return void
     */
    public static function scaleBossLife($sessionId){
        $numberPlayers = DB::table('student')
            ->where('session_id', $sessionId)
            ->count();
        $bossHealthOption = DB::table('boss')
            ->where('session_id', $sessionId)
            ->value('boss_health_option');
        $time = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_duration');

        /****************************************************************************************************************************************
         * Scale the number of lifes of the Boss.                                                                                               *
         *                                                                                                                                      *
         * A student needs 20 second on average for a question, so we multiplicate the time with 3, 50% of them have to be answered correctly,  *
         * so we multiplicate the number with 0.5. Last, the boss life is multiplicated with the number of players and with the factor for      *
         * the difficulty. The logarithm function is only used if there are more than 40 players because the game would get too easy otherwise. *
         * We use the logarithm so the game gets easier with a growing number of players.                                                       *
         ****************************************************************************************************************************************/

        if($numberPlayers <= 40){
            $bossLife = round($time * 3 * $numberPlayers * 0.5 * $bossHealthOption);
        } else {
            $bossLife = round($time * 3 * $numberPlayers * 0.5 * $bossHealthOption / log10($numberPlayers) * 1.3);
        }

        DB::table('boss')
            ->where('session_id', $sessionId)
            ->update(['boss_life' => $bossLife, 'boss_life_start' => $bossLife]);
    }
}
