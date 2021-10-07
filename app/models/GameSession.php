<?php

namespace App\models;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class GameSession{
    /**
     * Returns the ids with the same session name, that are played already
     *
     * @param  int $sessionName
     * @return \Collection [SessionIds]
     */
    public static function getSessionIdsbyName($sessionName){
        $sessionIds = DB::table('session')
            ->where('session_name', $sessionName)
            ->whereNotNull('session_time')
            ->pluck('session_id');
        return $sessionIds;
    }

    /**
     * Returns the name of a session.
     *
     * @param  int $sessionId
     * @return string $session_name
     */
    public static function getSessionName($sessionId){
        $sessionName = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_name');
        return $sessionName;
    }

    /**
     * @param  int $sessionId
     * @return \DateTime when the session ends
     */
    public static function getEndDateTime($sessionId){
        $endDateTime = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_time');
        return $endDateTime;
    }

    /**
     * @param  int  $sessionId
     * @return int boolean 1/0
     */
    public static function isSessionActive($sessionId){
        $isActive = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('is_active');
        return $isActive;
    }

    /**
     * @param  int $sessionId
     * @return int number of players in a session including bots
     */
    public static function getNumberPlayers($sessionId){
        $numberPlayers = DB::table('student')
            ->where('session_id', $sessionId)
            ->count();
        return $numberPlayers;
    }

    /**
     * @param  int $sessionId
     * @return int number of bots in a session
     */
    public static function getNumberBots($sessionId){
        $numberPlayers = DB::table('student')
            ->where([['session_id', $sessionId],
                    ['is_bot', 1]])
            ->count();
        return $numberPlayers;
    }

    /**
     * Import a session into another session.
     *
     * @param  int $target where to import it
     * @param  int $source where to export it from
     * @return void
     */
    public static function importSession($target, $source){
        $rawData = DB::table('questions')
            ->join('answers', 'questions.question_id', '=', 'answers.question_id')
            ->select('questions.question_id', 'questions.question_text', 'answers.answer_text', 'questions.image_path', 'answers.correct')
            ->where('questions.session_id' , $source)
            ->get();

        // put the answers in the form for the addQuestion method we are using to create a new Question
        for($i = 0; $i < sizeof($rawData); $i = $i +4){
            $answers = [];
            for ($j = $i; $j < $i + 4; $j++){
                $answers['answer'.($j-$i)] = $rawData[$j]->answer_text;
                $answers['checkAnswerCorrect'.($j-$i)] = $rawData[$j]->correct;
            }
            Question::addQuestion($rawData[$i]->question_text, $answers, $target, $rawData[$i]->image_path);
        }
    }

    /**
     * Insert the current date/time + the game duration into the DB.
     *
     * @param  int $sessionId
     * @return void
     */
    public static function insertGameTime($sessionId){
        // We can not only use the time, as you should also be able to play the game near midnight.
        date_default_timezone_set('Europe/Berlin');
        $actualDateTime = date('Y-m-d H:i:s');

        $gameTime = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_duration');

        $endDateTime = date('Y-m-d H:i:s', strtotime("+$gameTime Minutes", strtotime($actualDateTime)));
        DB::table('session')
            ->where('session_id', $sessionId)
            ->update(['session_time' => $endDateTime]);
    }

    /**
     * @param  int $sessionId
     * @return void
     */
    public static function activateSession($sessionId){
        DB::table('session')
            ->where('session_id', $sessionId)
            ->update(['is_active' => 1]);
    }

    /**
     * @param  int $sessionId
     * @return boolean auth okay?
     */
    public static function checkAuthority($sessionId){
        $profId = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('prof_id');

        return $profId == Auth::id();
    }

    /**
     * @param  int $sessionId
     * @return void
     */
    public static function endGame($sessionId){
        DB::table('session')
            ->where('session_id', $sessionId)
            ->update(['is_active' => 0, 'session_end_date' => DB::raw('CURRENT_TIMESTAMP')]);
    }

    /**
     * Everything possible to know about the current status of the boss.
     *
     * @param  int $sessionId
     * @return \ArrayObject associative array [int bossLife, int studentCount, int gameTime,
     *                             int bossLifeStart, int studentCountLeftGray, int studentCountLeftGold]
     */
    public static function getBossData($sessionId){
        $bossLife = DB::table('boss')
            ->where('session_id', $sessionId)
            ->value('boss_life');

        $studentCountLeftGray = DB::table('student')->where([ // all defenders
                ['student_life', '>', 0],
                ['player_type_id', 1],
                ['session_id', $sessionId]
            ])->count();

        $studentCountLeftGold = DB::table('student')->where([ // all attackers
                ['student_life', '>', 0],
                ['player_type_id', 2],
                ['session_id', $sessionId]
            ])->count();

        $studentCount = DB::table('student')
            ->where('session_id', $sessionId)
            ->count();
        $endTime = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_time');

        date_default_timezone_set('Europe/Berlin');
        $actualDateTime = date('Y-m-d H:i:s');
        // calc seconds the game should last
        $gameTime = strtotime($endTime) - strtotime($actualDateTime);

        $bossLifeStart = DB::table('boss')
            ->where('session_id', $sessionId)
            ->value('boss_life_start');

        return array('bossLife' => $bossLife, 'studentCount' => $studentCount, 'gameTime' => $gameTime,
                     'bossLifeStart' => $bossLifeStart, 'studentCountLeftGray' => $studentCountLeftGray,
                     'studentCountLeftGold' => $studentCountLeftGold);
    }

    /**
     * Everything possible to know about the current status of the teams.
     * Called by the projector js in team modus.
     *
     * @param  int $sessionId
     * @return \ArrayObject associative array [int pointsBlue, int pointsRed, int gameTime, int studentCountBlue, int studentCountRed, int studentCountLeftBlue, int studentCountLeftRed]
     */
    public static function getTeamsData($sessionId){
        $entryTime = microtime(true);           //get time to log duration

        $endTime = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_time');

        date_default_timezone_set('Europe/Berlin');
        $actualDateTime = date('Y-m-d H:i:s');
        // calc seconds the game should last
        $gameTime = strtotime($endTime) - strtotime($actualDateTime);

        // We could also merge all the following sql calls into one
        // its just easier to do it in seperate queries.
        $studentCountBlue = DB::table('student')->where([
                ['player_type_id', 1],
                ['session_id', $sessionId]
            ])->count();

        $studentCountRed = DB::table('student')->where([
                ['player_type_id', 2],
                ['session_id', $sessionId]
            ])->count();

        //Count alive characters
        $studentCountLeftBlue = DB::table('student')->where([
                ['student_life', '>', 0],
                ['player_type_id', 1],
                ['session_id', $sessionId]
            ])->count();

        $studentCountLeftRed = DB::table('student')->where([
                ['student_life', '>', 0],
                ['player_type_id', 2],
                ['session_id', $sessionId]
            ])->count();

        $correctAnswersBlue = DB::table('student_question_statistic')
            ->where('correct', '1')
            ->whereExists(function ($query) use ($sessionId){
                $query->select('student_id')
                    ->from('student')
                    ->where([['player_type_id', 1],
                             ['session_id', $sessionId]])
                    ->whereRaw('student.student_id = student_question_statistic.student_id');
                })
            ->count();

       $correctAnswersRed = DB::table('student_question_statistic')
            ->where('correct', '1')
            ->whereExists(function ($query) use ($sessionId){
                $query->select('student_id')
                    ->from('student')
                    ->where([['player_type_id', 2],
                            ['session_id', $sessionId]])
                    ->whereRaw('student.student_id = student_question_statistic.student_id');
               })
            ->count();

        // calc points of teams: - use relative points depending on team in relation to overall session size
        //                       - multiplied by 10 to get bigger numbers
        if ($studentCountBlue != 0){
            $pointsBlue = $correctAnswersBlue * (1 - ($studentCountBlue / ($studentCountRed + $studentCountBlue))) * 10;
        }else{
            $pointsBlue = 0;
        }

        if ($studentCountRed != 0){
            $pointsRed = $correctAnswersRed * (1 - ($studentCountRed / ($studentCountRed + $studentCountBlue))) * 10;
        }else{
            $pointsRed = 0;
        }

        if((microtime(true) - $entryTime) > 0.25){
            Log::warning("GetTeamsData is slow, duration: " . (microtime(true) - $entryTime) .  "s sessionId: " . $sessionId);
        }

        return array('pointsBlue' => $pointsBlue, 'pointsRed' => $pointsRed, 'gameTime' => $gameTime, 
                'studentCountLeftBlue' => $studentCountLeftBlue, 'studentCountLeftRed' => $studentCountLeftRed,
                'studentCountBlue' => $studentCountBlue, 'studentCountRed' => $studentCountRed);
    }

    /**
     * Get session options.
     *
     * @param  int $sessionId
     * @return \Collection string session_name,
     *                     int (minutes) session_duration,
     *                     int (show rank: 0:nowhere, 1:only mobile, 2:only projector, 3:both) session_option_rank,
     *                     double boss_health_option (easy:0.8, normal:1, hard:1.2),
     *                     int boolean show_badges,
     *                     int boolean is_training,
     *                     int boolean use_points,
     *                     int boolean show_story,
     *                     int boolean is_game,
     *                     int boolean show_character_selection,
     *                     int boolean use_dynamic_start_life,
     *                     int start_life_player_type_1,
     *                     int start_life_player_type_2,
     *                     string lang en/de,
     *                     string evaluation url,
     *                     int boolean show_number_correct_answers,
     *                     int boolean use_bots,
     *                     int bot_difficulty
     */
    public static function getCurrentSessionSettings($sessionId){
        $currentSessionSettings = DB::table('session')
            ->join('boss', 'session.session_id', '=', 'boss.session_id')
            ->select('session_name', 'session_duration', 'session_option_rank', 'show_badges', 'is_training',
                     'use_points', 'show_story', 'show_character_selection', 'is_game', 'is_teams', 'show_number_correct_answers', 'use_bots', 'bot_difficulty',
                     'use_dynamic_start_life', 'start_life_player_type_1', 'start_life_player_type_2', 'lang', 'evaluation_url', 'boss.boss_health_option')
            ->where('session.session_id', $sessionId)
            ->get();
        return $currentSessionSettings;
    }

    /**
     * Returns game mode type of session
     *
     * @param int $sessionId
     * @return string gameModeType teams|classic|noGame|training
     */
    public static function getSessionGameModeType($sessionId){
        $isTraining = GameSession::isTraining($sessionId);
        $isGame = GameSession::getSessionOptionGame($sessionId);
        $isTeams = GameSession::getSessionOptionTeams($sessionId);

        if ($isTeams == 1){
            return 'teams';
        }elseif ($isGame == 0 && $isTraining == 1){
            return 'noGame';
        }elseif ($isTraining == 1){
            return 'training';
        }else{ //default
            return 'classic';
        }
    }

    /**
     * Export session data, for statistics.
     *
     * @param  int $sessionId
     * @return \Collection string session_name, int (minutes) session_duration,
     *                     int session_option_rank (show rank: 0:nowhere, 1:only mobile, 2:only projector, 3:both),
     *                     double boss_health_option (easy:0.8, normal:1, hard:1.2),
     *                     int boolean show_badges,
     *                     int is_training
     *                     ... (Just give all we have!)
     */
    public static function getSessionInfosExport($sessionId){
        $currentSessionInfos = DB::table('session')
            ->join('boss', 'session.session_id', '=', 'boss.session_id')
            ->where('session.session_id', $sessionId)
            ->get();
        return $currentSessionInfos;
    }

    /**
     *  Infos for the statistics tab.
     *
     * @return \Collection int session_id, string session_name, \DateTime session_time, int player_count, int boss_life,
     *              int boolean is_training, int boolean show_story, int boolean is_game, int boolean is_teams,
     *              int boolean use_dynamic_start_life, int start_life_player_type_1, int start_life_player_type_2, int string lang, int number_questions
     */
    public static function getStatisticSessionInfos($orderByName){
        $number_questions = DB::table('session')
            ->join('questions', 'questions.session_id', '=', 'session.session_id')
            ->selectRaw('session.session_id, count(questions.question_id) as number_questions')
            ->groupBy('session.session_id');

        $sessionInfos = DB::table('session')
			->join('student', 'student.session_id', '=', 'session.session_id')
			->join('boss', 'boss.session_id', '=', 'session.session_id')
			->selectRaw('session.session_id, session_name, session_time, count(session.session_id) AS player_count,
			            boss_life, is_training, show_story, is_game, is_teams,
			            use_dynamic_start_life, start_life_player_type_1, start_life_player_type_2, lang, number_questions')
            ->where('prof_id', Auth::id())
			->whereNotNull('session_time')
			->groupBy('session_id', 'session_name', 'session_time', 'boss_life')
            ->joinSub($number_questions, 'sub_query', function ($join) {
                $join->on('session.session_id', '=', 'sub_query.session_id');
            });

		if($orderByName == True) {
			$sessionInfos = $sessionInfos
				->orderBy('session_name')
				->orderByRaw('cast(session_time as datetime) desc')// normal order by session_time does not always work here
				->get();
		} else {
			$sessionInfos = $sessionInfos
			->orderByRaw('cast(session_time as datetime) desc')// normal order by session_time does not always work here
			->get();
		}

		return $sessionInfos;
    }

    /**
     * Delete a Session.
     *
     * We do not delete the images here.
     *
     * @param  int $sessionId
     * @return void
     */
    public static function deleteSession($sessionId){
        DB::table('session')->where('session_id', '=', $sessionId)->delete();
    }

    /**
     * Check if a session is existing.
     *
     * @param  int $sessionId
     * @return int boolean 1/0
     */
    public static function isSessionExisting($sessionId){
        $sessionId = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_id');
        if($sessionId != Null) {
            return 1;
        }
        return 0;
    }

    /**
     * Check if a session has not been played already and is therefor ready to be played.
     *
     * @param  int $sessionId
     * @return int boolean 1/0
     */
    public static function isSessionPlayable($sessionId){
        $sessionId = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_time');
        if($sessionId == Null) {
            return 1;
        }
        return 0;
    }

    /**
     * @param int $sessionId
     * @return int boolean 1/0
     */
    public static function isTraining($sessionId){
        return DB::table('session')
            ->where('session_id', $sessionId)
            ->value('is_training');
    }

    /**
     * @param string $sessionName
     * @param int $profId for which user
     * @return int session id
     */
    public static function addSession($sessionName, $profId){

        $sessionId = DB::table('session')->insertGetId(
            ['session_name' => $sessionName,
                'prof_id' => $profId
            ]
        );
        DB::table('boss')->insert(
            ['boss_id' => $sessionId,
                'boss_life' => 0,
                'boss_life_start' => 0,
                'session_id' => $sessionId
            ]
        );

        return $sessionId;
    }

    /**
     * @param  int $session_duration minutes
     * @param  int $sessionOptionRank show rank: 0:nowhere, 1:only mobile, 2:only projector, 3:both
     * @param  double $bossHealthOption easy:0.8, normal:1, hard:1.2
     * @param  int $showBadges boolean 1 = show, 0 = do not show
     * @param  int $isTraining boolean 1 = true, 0 = false
     * @param  int $usePoints boolean 1 = true, 0 = false
     * @param  int $showCharacterSelection boolean 1 = true, 0 = false
     * @param  int $showStory boolean 1 = true, 0 = false
     * @param  int $isGame boolean 1 = true, 0 = false
     * @param  int $isTeams boolean 1 = true, 0 = false
     * @param  int $showNumberCorrectAnswers boolean 1 = true, 0 = false
     * @param  int $useDynamicStartLife boolean 1 = player start life increases with longer duration, 0 = fixed default values
     * @param  string lang de/en
     * @param  string evaluation url
     * @param  int $sessionId
     * @return void
     */
    public static function saveSessionSettings($session_duration, $sessionOptionRank, $bossHealthOption, $showBadges, $isTraining, $usePoints, $showCharacterSelection, $showStory, $isGame, $isTeams, $showNumberCorrectAnswers, $useBots, $botDifficulty, $useDynamicStartLife, $lang, $evaluationUrl, $sessionId){
        DB::table('session')
            ->where('session_id', $sessionId)
            ->update(['session_duration' => $session_duration, 'session_option_rank' => $sessionOptionRank, 'show_badges' => $showBadges,
                       'is_training' => $isTraining, 'use_points' => $usePoints, 'show_character_selection' => $showCharacterSelection,
                       'show_story' => $showStory, 'is_game' => $isGame, 'is_teams' => $isTeams,
                       'show_number_correct_answers' => $showNumberCorrectAnswers, 'use_bots' => $useBots, 'bot_difficulty' => $botDifficulty,
                       'lang' => $lang, 'evaluation_url' => $evaluationUrl]);

        DB::table('boss')
            ->where('session_id', $sessionId)
            ->update(['boss_health_option' => $bossHealthOption]);

        GameSession::saveStudentStartLife($sessionId, $useDynamicStartLife);
    }

    /**
     * set new Session duration, but not more then 5 as that should remain the max default value
     *
     * @param  int $sessionId
     * @param  int $sessionDuration
     * @return void
     */
    public static function setSessionDurationMax5($sessionId, $sessionDuration){
        if ($sessionDuration > 5) $sessionDuration = 5;
        DB::table('session')
            -> where('session_id', $sessionId)
            -> update(['session_duration' => $sessionDuration]);

        GameSession::saveStudentStartLife($sessionId, GameSession::getSessionOptionDynamicStartLife($sessionId));
    }

    /**
     * Get session infos with auth prof id from future sessions.
     *
     * @return \Collection int session_id, string session_name, \DateTime session_creation_date, int boolean is_training, in boolean show_character_selection, int boolean is_game, int boolean is_teams, int number_questions
     */
    public static function getSessionInfos(){
        $number_questions = DB::table('session')
            ->join('questions', 'questions.session_id', '=', 'session.session_id')
            ->selectRaw('session.session_id, count(questions.question_id) as number_questions')
            ->groupBy('session.session_id');

        $sessionInfos = DB::table('session')
            ->select('session.session_id', 'session_name', 'session_creation_date', 'is_training',
                     'show_story','show_character_selection', 'is_game', 'is_teams',
                     DB::raw('COALESCE(number_questions, 0) as number_questions')) // number_questions is null if we have no questions yet
            ->where('prof_id', Auth::id())
            ->whereNull('session_time')
			->orderByRaw('cast(session_creation_date as datetime) desc') // normal order by session_creation_date does not always work here
            ->leftjoinSub($number_questions, 'sub_query', function ($join) { // we must do an left outer join, because otherwise we lose a session without questions
                $join->on('session.session_id', '=', 'sub_query.session_id');
            })
            ->get();
        return $sessionInfos;
    }

	/**
	 * Returns duration setting
	 *
     * @param  int $sessionId
	 * @return int $sessionDuration
	 */
	public static function getSessionDuration($sessionId) {
		$sessionDuration = DB::table('session')
			->where('session_id', $sessionId)
			->value('session_duration');
		return $sessionDuration;
	}

    /**
	 * Returns acutal played duration of a session
	 *
     * @param  int $sessionId
	 * @return string minutes and seconds of actual game duration | unknown
	 */
	public static function getActualSessionDuration($sessionId) {
		$sessionDates = DB::table('session')
            ->select('session_duration', 'session_time', 'session_end_date')
            ->where('session_id', $sessionId)
            ->first();

        if ($sessionDates->session_end_date == Null){
            return "unknown";
        }

        $startDate = strtotime($sessionDates->session_time) - (($sessionDates->session_duration * 60) - 3); // 3 sec delay
        $actualSessionDuration = ((int) date('i', strtotime($sessionDates->session_end_date) - $startDate)) //cast to int to remove leading zero
                                 . ":" . date('s', strtotime($sessionDates->session_end_date) - $startDate);
        return $actualSessionDuration;
	}

    /**
     * Translates int session_option_rank from DB into int boolean 0/1 whether to show ranking, on differnt devices.
     *
     * @param  int $sessionId
     * @param  int $mobile boolean 1:mobile view/0:projector view
     * @return int boolean when projector view: 0: only show best player, 1: show ranking
     *                     when mobile view: 0: do not show player rank 1: show rank
     */
    public static function getSessionOptionRank($sessionId, $mobile){
        $sessionOptionRank = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('session_option_rank');

        if (($sessionOptionRank == 1 || $sessionOptionRank == 3) && $mobile == 1) return 1;
        if (($sessionOptionRank == 2 || $sessionOptionRank == 3) && $mobile == 0) return 1;
        return 0;
    }

    /**
     * Translates int show_badges from DB into int boolean 0/1 whether to show badges
     *
     * @param  int $sessionId
     * @return int boolean 1 = show badges, 0 = do not show
     */
    public static function getSessionOptionBadges($sessionId){
        $showBadges = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('show_badges');

        return $showBadges;
    }

    /**
     * Translates int use_points from DB into int boolean 0/1 whether to show points
     *
     * @param  int $sessionId
     * @return int boolean 1 = show points, 0 = do not show
     */
    public static function getSessionOptionPoints($sessionId){
        $usePoints = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('use_points');

        return $usePoints;
    }

    /**
     * Translates int show_story from DB into int boolean 0/1 whether to show story
     *
     * @param  int $sessionId
     * @return int boolean 1 = show story, 0 = do not show
     */
    public static function getSessionOptionStory($sessionId){
        $showStory = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('show_story');

        return $showStory;
    }

    /**
     * Translates int is_game from DB into int boolean 0/1 whether we show a game
     *
     * @param  int $sessionId
     * @return int boolean 1 = have game elements, 0 = do not have any game elements
     */
    public static function getSessionOptionGame($sessionId){
        $isGame = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('is_game');

        return $isGame;
    }

    /**
     * Translates int is_teams from DB into int boolean 0/1 whether we use teams
     *
     * @param  int $sessionId
     * @return int boolean 1 = use teams, 0 = do not have teams
     */
    public static function getSessionOptionTeams($sessionId){
        $isTeams = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('is_teams');

        return $isTeams;
    }

    /**
     * Translates int show_character_selection from DB into int boolean 0/1 whether to show character selection
     *
     * @param  int $sessionId
     * @return int boolean 1 = show character selection, 0 = do not show
     */
    public static function getSessionOptionCharacterSelection($sessionId){
        $showCharacterSelection = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('show_character_selection');

        return $showCharacterSelection;
    }

    /**
     * Translates int show_number_correct_answers from DB into int boolean 0/1 whether to show number of correct answers
     *
     * @param  int $sessionId
     * @return int boolean 1 = show number of correct answers, 0 = do not show
     */
    public static function getSessionOptionNumberCorrectAnswers($sessionId){
        $showNumberCorrectAnswers = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('show_number_correct_answers');

        return $showNumberCorrectAnswers;
    }

    /**
     * Translates int use_dynamic_start_life from DB into int boolean 0/1 whether we have dynamically calculated start life
     *
     * @param  int $sessionId
     * @return int boolean 1 = calculates dynamic start life, 0 = default start life
     */
    public static function getSessionOptionDynamicStartLife($sessionId){
        $useDynamicStartLife = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('use_dynamic_start_life');

        return $useDynamicStartLife;
    }

    /**
     * Get Language
     *
     * @param  int $sessionId
     * @return string lang code (en/de)
     */
    public static function getSessionOptionLanguage($sessionId){
        $lang = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('lang');

        return $lang;
    }

    /**
     * Translates int use_bots from DB into int boolean 0/1 whether we use bots or not
     *
     * @param  int $sessionId
     * @return int boolean 1 = bots are enabled, 0 = bots are disabled
     */
    public static function getSessionOptionBots($sessionId){
        $bots = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('use_bots');

        return $bots;
    }

    /**
     * Sets use_bots in DB with int boolean 0/1 whether we use bots or not
     *
     * @param  int $sessionId
     * @param  int boolean $botsEnabled 1 = bots are enabled, 0 = bots are disabled
     * @return int boolean
     */
    public static function setSessionOptionBots($sessionId, $botsEnabled){
        DB::table('session')
            ->where('session_id', $sessionId)
            ->update(['use_bots' => $botsEnabled]);
    }

    /**
     * Returns bot_difficulty 1 = easy, 2 = medium, 3 = strong, 4 = madness
     *
     * @param  int $sessionId
     * @return int 1 = easy, 2 = medium, 3 = strong, 4 = madness
     */
    public static function getSessionOptionBotDifficulty($sessionId){
        $bots = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('bot_difficulty');

        return $bots;
    }

    /**
     * Sets bot_difficulty with 1 = easy, 2 = medium, 3 = strong, 4 = madness
     *
     * @param  int $sessionId
     * @param  int int $botDifficulty 1 = easy, 2 = medium, 3 = strong, 4 = madness
     * @return int boolean
     */
    public static function setSessionOptionBotDifficulty($sessionId, $botDifficulty){
        DB::table('session')
            ->where('session_id', $sessionId)
            ->update(['bot_difficulty' => $botDifficulty]);
    }

    /**
     * Returns bot manager status code (0 = default, 1 = bots generated)
     *
     * @param  int $sessionId
     * @return int bot_status
     */
    public static function getBotStatus($sessionId){
        $status = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('bot_status');

        return $status;
    }

    /**
     * Sets bot_status in DB (0 = default, 1 = bots generated)
     *
     * @param  int $sessionId
     * @param  int boolean $botsEnabled 1 = bots are enabled, 0 = bots are disabled
     */
    public static function setBotStatus($sessionId, $botStatus){
        DB::table('session')
            ->where('session_id', $sessionId)
            ->update(['bot_status' => $botStatus]);
    }

    /**
     * dynamically calculates starting life of player according to player type and session duration
     * @param int $studentType 1 = defender and 2 = attacker
     * @param int $sessionId
     * @param boolean $calculateDynamically if true then a longer session time is increasing student starting life
     *
     * @return int life
     */
    public static function calculateStudentStartLife($studentType, $sessionId, $calculateDynamically){
        if($studentType == 1) {
            $studentDefaultLife = 5;
            $studentDynamicLife = round(GameSession::getSessionDuration($sessionId) * 5 / 3);
        }else{
            $studentDefaultLife = 3;
            $studentDynamicLife = GameSession::getSessionDuration($sessionId);
        }

        if($calculateDynamically == true) {
            return max($studentDefaultLife, $studentDynamicLife);
        } else {
            return $studentDefaultLife;
        }
    }

    /**
     * save student start life
     * @param int $sessionId
     * @param int boolean $calculateDynamically
     */
    public static function saveStudentStartLife($sessionId, $calculateDynamically) {
        $startLife1 = GameSession::calculateStudentStartLife(1, $sessionId, $calculateDynamically);
        $startLife2 = GameSession::calculateStudentStartLife(2, $sessionId, $calculateDynamically);

        DB::table('session')
            -> where('session_id', $sessionId)
            -> update(['use_dynamic_start_life' => $calculateDynamically, 'start_life_player_type_1' => $startLife1, 'start_life_player_type_2' => $startLife2]);
    }

    /**
     * returns starting life
     * @param int $studentType 1 = defender and 2 = attacker
     * @param int $sessionId
     *
     * @return int life
     */
    public static function getStudentStartLife($studentType, $sessionId) {
        $studentLife = DB::table('session')
            ->where('session_id', $sessionId)
            ->value('start_life_player_type_' . $studentType);

        return $studentLife;
    }

    /**
     * Who won the game?
     *
     * @param  int $sessionId
     * @return int boolean 1=students/0=boss
     */
    public static function gameWin($sessionId){
        $bossLife = DB::table('boss')
            ->where('session_id', $sessionId)
            ->value('boss_life');

        if($bossLife <= 0) return 1;
        else return 0;
    }

    /**
     * @param  string $profUidOrEmail
     * @return int -1= not found, otherwise profId
     */
    public static function getProfIdbyUidorEmail($profUidOrEmail){
        $profId = DB::table('users')
            ->where('username', $profUidOrEmail)
            ->orWhere('email', $profUidOrEmail)
            ->value('id');

        if (is_numeric($profId)){
            return $profId;
        }else{
            return -1;
        }
    }
}
