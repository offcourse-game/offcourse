<?php

namespace App\models;
use Session;
use App\models\Achievement;
use Illuminate\Support\Facades\DB;

class Student{

    /**
     * @param  int $studentId
     * @return int student life
     */
    public static function getStudentLife($studentId){
        $studentLife = DB::table('student')
            ->where('student_id', $studentId)
            ->value('student_life');

        return $studentLife;
    }

    /**
     * @param  int $studentId
     * @param  int $viewTime sec
     * @return void
     */
    public static function saveViewTime($studentId, $viewTime){
        $isSaved = DB::table('student')
            ->where('student_id', $studentId)
            ->value('achievm_view_time');

        if ($isSaved == Null){
            DB::table('student')
                ->where('student_id', $studentId)
                ->update(['achievm_view_time' => $viewTime]);
        }else{
            DB::table('student')
                ->where('student_id', $studentId)
                ->update(['achievm_view_time' => ($viewTime + $isSaved)]);
        }
    }


    /**
     * Gets all user data from db and put them in an array to export it
     *
     * @param  int $sessionId
     * @return \ArrayObject associative array [int student_id, int student_life, int int session_id,
     *                                         int player_type_id, int best_streak, int actual_streak,
     *                                         questions [int question_id, int correct, int duration]]
     */
    public static function getAllStudentData($sessionId){
        $students = DB::table('student')
            ->where('session_id', $sessionId)
            ->get();

        foreach ($students as $student) {
            $student->questions = DB::table('student_question_statistic')
                ->select('question_id', 'correct', 'duration')
                ->where('student_id', $student->student_id)
                ->get();
            $student->answered_questions = sizeof($student->questions);

            $student->answered_correct = 0; //count the correct answers and the average duration
            $student->average_duration = 0;
            foreach ($student->questions as $question) {
                $student->average_duration += $question->duration;
                if ($question->correct){
                    $student->answered_correct++;
                }
            }

            if($student->average_duration != 0 && $student->answered_questions != 0){
                $student->average_duration = round($student->average_duration/$student->answered_questions);
            }else{
                $student->average_duration = Null;
            }


            $student->nickname = self::getPlayerNickname($student->student_id, $sessionId);
        }

        return $students;
    }

	/**
     * Decrement the life of a student.
     *
     * This function will also be called by ajax, when the user leaves the questions page.
     * That's why we must get the studentID again! There for we need to have a Illuminate\Session 'studentId' int.
     *
     * @return void
     */
    public static function decrementStudentLife(){
        $studentId = session('studentId');
        DB::table('student')
            ->where('student_id', $studentId)
            ->decrement('student_life');
    }

    /**
     * Decrement the life of a student.
     *
     * This function can be called by the anyone, so we can not rely on session data
     *
     * @param $studentId
     * @return void
     */
    public static function decrementStudentLifeById($studentId){
        DB::table('student')
            ->where('student_id', $studentId)
            ->decrement('student_life');
    }

	/**
     * How much damage produces a student?
     *
     * @param  int $studentId
     * @return int damage
     */
    public static function getPlayerDamage($studentId){
        $playerType = DB::table('student')
            ->where('student_id', $studentId)
            ->value('player_type_id');
        //by coincidence player_type_id is the same as the damage each player does, therefore we do not need any conversion
        return $playerType;
    }

	/**
     * Set the student type.
     *
     * @param int $studentType 1 = defender and 2 = attacker
     * @param int $studentId
     * @param int $sessionId
     * @return string "ok"
     */
    public static function setType($studentType, $studentId, $sessionId){
        // force set to type 1 if character selection is disabled to not allow cheating
        if(GameSession::getSessionOptionCharacterSelection($sessionId) == 0){
            $studentType = 1;
        }

        DB::table('student')
            ->where('student_id', $studentId)
            ->update(['player_type_id' => $studentType, 'student_life' => GameSession::getStudentStartLife($studentType, $sessionId)]);

        return "ok";
    }

    /**
	 * Gets player type id (1 or 2)
	 *
	 * @param  int $studentId
	 * @return int [1 or 2]
	 */
	public static function getType($studentId){
		$playerTypeId = DB::table('student')
			->where('student_id', $studentId)
			->value('player_type_id');

		return $playerTypeId;
	}

    /**
	 * returns true if player is a bot
	 *
	 * @param  int $studentId
	 * @return int boolean 1/0 (true / 1 if player is a bot)
	 */
	public static function isBot($studentId){
		$isBot = DB::table('student')
			->where('student_id', $studentId)
			->value('is_bot');

		return $isBot;
	}

    /**
     * @param  int $studentType
     * @param  int $sessionId
     * @param  int boolean $isBot defaults to false
     * @return int student id
     */
    public static function createUser($studentType, $sessionId, $isBot = false){
        // force set to type 1 if character selection is disabled to not allow cheating
        if(GameSession::getSessionOptionCharacterSelection($sessionId) == 0){
            $studentType = 1;
        }

        $studentLife = GameSession::getStudentStartLife($studentType, $sessionId);

        $studentId = DB::table('student')->insertGetId([
            'student_life' => $studentLife, 'session_id' => $sessionId,
            'player_type_id' => $studentType, 'best_streak' => 0,
            'is_bot' => $isBot, 'actual_streak' => 0
        ]);

        return $studentId;
    }

    /**
     * Get Nickname from Player according to language set in game session settings
     *
     * We can generate 1000 unique name combinations,
     * if there are more than 1000 players the names are used multiple times!
     *
     * @param  int $studentId
     * @param  int $sessionId
     * @return string player nickname
     */
    public static function getPlayerNickname($studentId, $sessionId){
        /**********************************************************************************************
         * These names are generated by using studentId with help of a Linear Congruential Generator, *
         * so we do not have to save them in the DB.                                                  *
         * See https://en.wikipedia.org/wiki/Linear_congruential_generator for reference.             *
         * A player name consists of hue + color + animal, suffixes for animals adjectives added.     *
         * We have 4 hues, 10 colors, 25 animals!                                                     *
         **********************************************************************************************/

        $lang = GameSession::getSessionOptionLanguage($sessionId);

        if($lang == "en") {
            /* longest en name: Slightly Turquoise Chameleon (longest overall: de)*/
            $randomHue = array("Bright ", "Dark ", "Deep ", "Slightly ");
            $randomColor = array("Red ", "Orange ", "Yellow ", "Green ", "Blue ", "Violet ", "Pink ", "Brown ", "Gray ", "Turquoise ");
            $randomAnimal = array("Squirrel", "Fox", "Bear", "Gazelle", "Lama", "Penguin", "Giraffe", "Kangaroo",
                "Frog", "Shark", "Peacock", "Leopard", "Rabbit", "Elephant", "Raccoon", "Whale", "Chameleon",
                "Alpaca", "Anteater", "Bee", "Flamingo", "Rhino", "Otter", "Bunny", "Seal");
        }else {
            /* longest de name: Dunkel Orangenes Eichhörnchen (longest overall: de)*/
            $randomHue = array("Hell", "Dunkel", "Tief ", "Leicht ");
            $randomColor = array("rot", "orangen", "gelb", "grün", "blau", "violett", "rosan", "braun", "grau", "türkis");
            $randomAnimal = array("es Eichhörnchen", "er Fuchs", "er Bär", "e Gazelle", "es Lama", "er Pinguin", "e Giraffe", "es Känguru",
                "er Frosch", "er Hai", "er Pfau", "er Leopard", "es Kaninchen", "er Elefant", "er Waschbär", "er Wal", "es Chamäleon",
                "es Alpaka", "er Ameisenbär", "e Biene", "er Flamingo", "es Nashorn", "er Otter", "er Hase", "e Robbe");
        }

        $randId = ($studentId - 1);

        //Linear Congruential Generator to order names deterministic and with random appearance
        //algorithm parameters for nice distribution are hardcoded
        $randId = (181 * $randId + 739) % 1000;

        $hueId = floor($randId / 250) % 4;
        $colorId = floor($randId / 25) % 10;
        $animalId = $randId % 25;

        $studentName = $randomHue[$hueId] . $randomColor[$colorId] . $randomAnimal[$animalId];

        return($studentName);
    }

    /**
     * Increment Student Streak in DB.
     *
     * @param  int $studentId
     * @return int actual_streak current streak the player has
     */
    public static function incrementStudentStreak($studentId){

        DB::table('student')
            ->where('student_id', $studentId)
            ->increment('actual_streak', 1);

        $streaks = DB::table('student')
            ->select('best_streak','actual_streak')
            ->where('student_id', $studentId)
            ->get();

        foreach($streaks as $streak){
            if($streak->actual_streak > $streak->best_streak){
                DB::table('student')
                    ->where('student_id', $studentId)
                    ->update(['best_streak' => $streak->actual_streak]);
            }
            return $streak->actual_streak;
        }

    }

    /**
     * @param  int $studentId
     * @return void
     */
    public static function stopStudentStreak($studentId){
        DB::table('student')
            ->where('student_id', $studentId)
            ->update(['actual_streak' => 0]);
    }

}
