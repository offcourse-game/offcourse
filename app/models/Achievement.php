<?php

namespace App\models;
use Illuminate\Support\Facades\DB;

class Achievement{
    /**
     * Save the achievement to the db.
     *
     * @param int studentId
     * @param  \ArrayObject [Achievement]
     */
    public static function saveAchievements($studentId, $achievements){

    	$update = [];

    	for($i = 0; $i < min(count($achievements), 3); $i++ ) {
			$update['achievm_' .  ($i + 1)] = $achievements[$i]->title;
			$update['achievm_' . ($i + 1) . '_color'] = $achievements[$i]->color;
		}

    	DB::table('student')
			->where('student_id', $studentId)
			->update($update);
    }

    /**
     * Gives us the rank of the player.
     *
     * @param  int $studentId
     * @param  int $sessionId
     * @param  int $relativeRank boolean true => returns relative rank (0..1) / false => returns absolute rank
     * @return int | float ( absolute 0 is top OR relative rank (0..1) ) | NULL (If we have no rank)
     */
    public static function getPlayerQuestionRank($studentId, $sessionId, $relativeRank)
	{
		$bestPlayers = Achievement::getBestPlayers($sessionId, -1);

        if (isset($bestPlayers->firstwhere('student_id', $studentId)->rank)){
            $rank = $bestPlayers->firstwhere('student_id', $studentId)->rank;
        }else{ // happens when rank in the db is null
            return Null;
        }


		if ($relativeRank == false) {
			return $rank;
		}

        $numberPlayers = Achievement::numberPlayers($sessionId);

        $percentRank = $rank / $numberPlayers;
        return $percentRank;
    }

    /**
     * Get number players from session.
     *
     * @param  int $sessionId
     * @return int count
     */
    public static function numberPlayers($sessionId){
        $numberPlayers = DB::table('student')
            ->where('session_id', $sessionId)
            ->count();
        return $numberPlayers;
    }

    /**
     * returns calculated amount of total damage a player dealt during the whole game
     *
     * @param  int $studentId
     * @return int damage made by the specified player
     */
    public static function getPlayerDamage($studentId){
        $numberCorrect = DB::table('student_question_statistic')
            ->where('student_id', $studentId)
            ->sum('correct');

        $damage = Student::getPlayerDamage($studentId);

        return $numberCorrect * $damage;
    }

    /**
     * returns the length of a players max streak
     *
     * @param  int $studentId
     * @param  int $sessionId
     * @return \int
     */
    public static function getStreakAchievement($studentId, $sessionId){
        $streak = DB::table('student')
            ->where('session_id', $sessionId)
			->where('student_id', $studentId)
            ->value('best_streak');

        return $streak;
    }

    /**
     * Checks if a player is in the top X% of player type.
     *
     * @param  int $studentId
     * @param  int $sessionId
	 * @param  int $playerType
     * @return \float [0 .. 1]
     */
    public static function getTopPlayerType($studentId, $sessionId, $playerType){
        $pType = DB::table('student')
            ->where('student_id', $studentId)
            ->value('player_type_id');

        if ($pType == $playerType){
            $studentIds = DB::table('student')
                ->select('student_id', 'student_life')
                ->where('session_id', $sessionId)
                ->where('player_type_id', $playerType)
                ->orderBy('score', 'desc')
                ->get();

            $rank = 0;
            foreach ($studentIds as $i){
                $rank++;
                if ($i->student_id == $studentId){
                    break;
                }
            }
            $numberPlayers = Achievement::numberPlayers($sessionId);

            $percentRank = $rank / $numberPlayers;
            return $percentRank;
        }
        return 1;
    }

    /**
     * Checks if a player has a streak.
     *
     * @param  int $studentId
     * @return \ArrayObject|0 [1, string]
     */
    public static function getStreak($studentId){
        $streak = DB::table('student')
            ->where('student_id', $studentId)
            ->value('best_streak');

        if($streak >= 3){
            return [1,"Mindestens 3 Fragen hintereinander richtig beantwortet!"];
        }
        return 0;
    }

    /**
	 * counts all correct answered questions below 12 sec.
	 *
	 * @param  int $studentId
	 * @param  int $sessionId
	 * @return \int
	 */
	public static function getFastQuestionCount($studentId, $sessionId){
		$fastQuestion = DB::table('student_question_statistic')
			->join('student', 'student_question_statistic.student_id', '=', 'student.student_id')
			->where('session_id', $sessionId)
			->where('student_question_statistic.student_id', $studentId)
			->where('correct', 1)
			->where('duration', '<=', 12)
			->count();

		return $fastQuestion;
	}


	/**
	 * Checks if a player has a fast question streak.
	 * used for ingame notifications too..			TODO THIS MIGHT BE BROKEN
	 *
	 * @param  int $studentId
	 * @param  int $sessionId
	 * @return \ArrayObject|0 [1, string]
	 */
	public static function getFastQuestions($studentId, $sessionId){
		$fastQuestion = DB::table('student_question_statistic')
			->join('student', 'student_question_statistic.student_id', '=', 'student.student_id')
			->where('session_id', $sessionId)
			->where('student_question_statistic.student_id', $studentId)
			->where('correct', 1)
			->whereRaw('duration <= 12')
			->count();

		if($fastQuestion >= 3){
			return [1,"Mindestens 3 Fragen sehr schnell beantwortet!"];
		}
		return 0;
	}

    /**
     * Calculates and saves all points for each player; generates and saves rank foreach player
	 * Player who have answered no question are not included!
     *
     * @param  int $sessionId
	 *
     * @return \ArrayObject|0 [student_id, sum]
     */
     public static function savePlayerScore($sessionId){

         if (GameSession::getSessionOptionPoints($sessionId)){
            $gameModeType = GameSession::getSessionGameModeType($sessionId);
            $characterMultiplierSQL = '';
            if($gameModeType == 'classic') {
                $characterMultiplierSQL = 'if ( player_type_id = 1, 1, 1.3)';
            }else{
                $characterMultiplierSQL = '1';
            }
            $answersCorrectSQL = '(count(case correct when "0" then null else 1 end))';

            // the points are calculated by:(AnzKorrekte*5 + BestStreak*2 + AnzKorrekte*(30-AvgDauer)/10)
            //                               * WENN(Spielertyp=1;1;1,3) * WENN(AnzKorrekte/AnzFragen>0,75;AnzKorrekte/AnzFragen;0,75)
            // Note: as we use coalesce(*,0) if we get any null we will always return 0 for this case
            $bestPlayers = DB::table('student_question_statistic')
                ->selectRaw('student_id, coalesce (round(
                            ' . $answersCorrectSQL . ' * 5 + best_streak * 2
                            + ' . $answersCorrectSQL . '
                            * ( (30 - ( sum(duration)/(count(questions)) ))/10 )
                            * ' . $characterMultiplierSQL
                            . ' * if ( ( ' . $answersCorrectSQL . '/(count(questions))) > 0.75 , ' . $answersCorrectSQL . '/(count(questions)), 0.75),
                            2 ), 0 ) as points')
                    ->fromSub(function ($query) use ($sessionId) {
                        // First group the answers by each player
                        $query->select('stud.best_streak', 'stud.player_type_id', 'stat.*', 'correct as questions')

                        ->from(DB::raw('student_question_statistic as stat , questions as q, student as stud'))
                        ->whereRaw('stat.question_id = q.question_id and stud.student_id=stat.student_id')
                        ->where('q.session_id', $sessionId)
                        ->orderBy('student_id');
                    }, '_')
            ->groupBy('student_id')
            ->orderBy('points', 'desc')
            ->get();
        }else{ // use the sum of all correct questions as score
            $bestPlayers = DB::table('student_question_statistic')
                ->join('questions', 'student_question_statistic.question_id', '=', 'questions.question_id')
                ->select(DB::raw('student_id, SUM(correct) AS points'))
                ->where('questions.session_id', $sessionId)
                ->groupBy('student_id')
                ->orderBy('points', 'desc')
                ->get();

        }
         //save individual score $ rank
		$rank = 1;
		foreach ($bestPlayers as $player) {
			DB::table('student')
				->where('student_id', $player->student_id)
				->update(['score' => $player->points,
					      'rank' => $rank]);
			$rank++;
		}
    }


    /*
     * Returns the best players by rank saved in db
     *
     * @param  $sessionId
     * @param  $number      use -1 to return all
     *
     * @return \ArrayObject|0 [student_id, sum]
     */
    public static function getBestPlayers($sessionId, $number) {
    	$bestPlayers = DB::table('student')
			->where('session_id', $sessionId)
            ->where('rank', '!=', 'NULL')
			->orderBy('rank', 'asc')
			->get();

    	if($number > 0)
		      $bestPlayers = $bestPlayers->slice(0, $number);

		return $bestPlayers;
	}

	/*
	 * get the score one player gets
	 *
	 * @param  int $studentId
	 * @param  int $sessionId
	 * @return int score, may be null
	 */
	public static function getStudentScore($studentId) {
		$score = DB::table('student')
			->where('student_id', $studentId)
			->value('score');

		if(empty($score))
			return 0;

		return $score;
	}

    /**
     * Get all data for one Student we need to display in the GameOver Screen.
     *
     * @param  int $sessionId
	 * @param  int $studentId
     * @return \ArrayObject|0 [avg_time, best_streak, student_life]
     */
    public static function getStudentGameOverData($studentId){
        $playerStat = DB::table('student_question_statistic')
            ->selectRaw('round( IFNULL(avg(duration), 0), 2) as avg_time, best_streak, student_life')
            ->fromRaw('student, student_question_statistic')
            ->whereRaw('student.student_id = student_question_statistic.student_id')
            ->where('student.student_id', $studentId)
            ->groupBy('student.student_id')
            ->first();

        return $playerStat;
    }

    /**
	   * NOTE: Not in use currently may use in the future.
	   *
     * How many questions were answerd per student in average?
     *
     * @param  int $sessionId
     * @return double if there a no players then return 0
     */
    public static function getAverageQuestions($sessionId){
        $numberQuestions = DB::table('student_question_statistic')
            ->join('questions', 'student_question_statistic.question_id', '=', 'questions.question_id')
            ->where('questions.session_id', $sessionId)
            ->count();

        $numberPlayers = Achievement::numberPlayers($sessionId);
        if ($numberQuestions != 0){
            return $numberQuestions / $numberPlayers;
        }
        return 0;
    }

    /**
     * How many questions has one student answered?
     *
     * @param $studentId
     * @return int answerd
     */
    public static function getAnsweredQuestions($studentId){
        $answered = DB::table('student_question_statistic')
            ->where('student_id', $studentId)
            ->count();
        return $answered;
    }

    /**
     * Which question has a student answered?
     *
     * @param $studentId
     * @return \Collection [question_id, question_text, image_path, duration, correct [answer_id, answer_text, correct]]
     */
    public static function getQuestionsByStudent($studentId){
        $questions = DB::table('questions')
            ->join('student_question_statistic', 'student_question_statistic.question_id', '=', 'questions.question_id')
            ->select('questions.question_id', 'question_text', 'image_path' ,'student_question_statistic.duration', 'student_question_statistic.correct')
            ->where('student_id', $studentId)
            ->oldest('student_question_statistic.answer_date_time')
            ->get();

        $answers = DB::table('answers')
            ->whereIn('question_id', $questions->pluck('question_id'))
            ->get();

        foreach ($questions as $question) {
            $question->answers = $answers->where('question_id', $question->question_id);
        }

        return $questions;
    }

    /**
     * How many questions has one student answered correctly?
     *
     * @param $studentId
     * @return int count
     */
    public static function getCorrectAnsweredQuestions($studentId){
        $correct = DB::table('student_question_statistic')
            ->where('student_id', $studentId)
            ->where('correct', 1)
            ->count();
        return $correct;
    }

    /**
     * @param  int $sessionId
     * @return int surviving players
     */
    public static function getSurvivingPlayers($sessionId){
        $survivingPlayers = DB::table('student')->where([
                ['student_life', '>', 0],
                ['session_id', $sessionId]
        ])->count();
        return $survivingPlayers;
    }
}
