<?php
//class for session bots which can be added to sessions to act like real players

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use App\models\Boss;
use App\models\GameSession;
use App\models\Question;
use App\models\Student;

class Bot {
	public $sessionId;              //sessionId for current session
    public $studentId;              //studentId for bot
    public $characterType;          //type of character of bot
    public $playerDamage;           //amount of damage dealing
    public $hibernating;            //true if the bot has died or answered all questions

    public $lastAnswerTime;         //date when last question was answered
    public $nextAnswerTime;         //date when question will be answered
    public $answerTimeDurationMin;  //randomly modified lower bound of random interval for next question time in seconds
    public $answerTimeDurationMax;  //randomly modified upper bound of random interval for next question time in seconds
    public $answerCorrectChance;    //chance to answer question correctly [0.00 ... 1.00]

    /**
     * Construct bot
     *
     * @param int $sessionId
     * @param int $characterType
     * @param int $botDifficulty 1 = easy, 2 = medium, 3 = strong, 4 = madness
     * @return void
     */
	public function __construct($sessionId, $currentTime, $botDifficulty, $isTeams) {
        $this->sessionId = $sessionId;
        if($isTeams == 0){
            $this->characterType = rand(0, 100) <= 70 ? 1 : 2;
        }else{
            //random equal distribution for teams mode
            $this->characterType = rand(0, 100) <= 50 ? 1 : 2;
        }

        $this->studentId = Student::createUser($this->characterType, $this->sessionId, true);
        $this->lastAnswerTime = $currentTime;

        //Setting random interval for random answer duration when answering quesitons so we have different strenght of bots rather than just a big average
        //NOTE: The balancing of the bots might be heavily dependent on your system environment. 
        //      If the bot manager is not processed fully parallel, the bots might appear as too weak..
        
        if($botDifficulty == 1) {
            //weak bots for hard sessions with harder to read and difficult questions
            //(avg min 16, avg max 26 slightly left shifted, total avg 22)
            $this->answerTimeDurationMin = rand(120, 200) / 10;
            $this->answerTimeDurationMax = rand($this->answerTimeDurationMin * 10 + 50, 300) / 10;
            //randomly setting interval for chances when answering questions, this will allow for some stronger and some weaker bots (avg: 40)
            $this->answerCorrectChance = rand(30, 50) / 100;
        }else if($botDifficulty == 2) {
            //normal bots for normal sessions with medium hard to read and medium difficult questions
            //(avg min 12, avg max 19 slightly left shifted, total avg 16)
            $this->answerTimeDurationMin = rand(80, 150) / 10;
            $this->answerTimeDurationMax = rand($this->answerTimeDurationMin * 10 + 50, 300) / 10;
            //randomly setting interval for chances when answering questions, this will allow for some stronger and some weaker bots (avg: 45)
            $this->answerCorrectChance = rand(35, 55) / 100;
        }else if($botDifficulty == 3) {
            //strong bots for easy sessions with short and simple questions
            //(avg min 8, avg max 19 slightly left shifted, total avg 14)
            $this->answerTimeDurationMin = rand(50, 100) / 10;
            $this->answerTimeDurationMax = rand($this->answerTimeDurationMin * 10 + 50, 250) / 10;
            //randomly setting interval for chances when answering questions, this will allow for some stronger and some weaker bots (avg: 55)
            $this->answerCorrectChance = rand(45, 65) / 100;
        }else if($botDifficulty == 4) {
            //super strong bots for super easy sessions with short and simple questions
            //(avg min 5, avg max 14 slightly left shifted, total avg 9)
            $this->answerTimeDurationMin = rand(35, 60) / 10;
            $this->answerTimeDurationMax = rand($this->answerTimeDurationMin * 10 + 40, 190) / 10;
            //randomly setting interval for chances when answering questions, this will allow for some stronger and some weaker bots (avg: 75)
            $this->answerCorrectChance = rand(60, 90) / 100;
        }

        //starting delay for some seconds before bots can answer first questions
        $this->nextAnswerTime = date('Y-m-d H:i:s', strtotime($currentTime) + 5);
        $this->refreshAnswerTime();

        if (GameSession::isTraining($this->sessionId) == 1) {
            $this->playerDamage = 1;
        } else {
            $this->playerDamage = Student::getPlayerDamage($this->studentId);
        }

        $this->hibernating = false;

        Log::debug("Bot created! (sessionId" . $this->sessionId . " studentId: " . $this->studentId . " characterType: " . $this->characterType . " difficulty " . $botDifficulty);
	}

    /**
     * Update bot, add answers, refresh timers
     *
     * @return void
     */
	public function update($currentTime) {
        if($this->hibernating == true) {
            return;
        }

        if(strtotime($currentTime) > strtotime($this->nextAnswerTime)) {
            $correct = (rand(0, 100) / 100 < $this->answerCorrectChance) ? 1 : 0;
            $answerDuration = strtotime($this->nextAnswerTime) - strtotime($this->lastAnswerTime);
            $this->refreshAnswerTime();
            $this->insertAnswer($correct, $answerDuration);
        }
	}

    /**
     * Inserts answer and updates life
     *
     * @param int (boolean 0/1) $correct
     * @param int $answerTime
     *
     */
    public function insertAnswer($correct, $answerTime) {

   // Log::debug("Bot tried to inserted answer! (studentId: " . $this->studentId . " correct: " . $correct . " answerTime: " . $answerTime . ")");

        if(Question::addAnswerLightweight($this->studentId, $this->sessionId, $correct, $answerTime) == false) {
            $this->hibernate();
            return Null;
        }

        if ($correct == 1){
            Boss::decrementBossLife($this->sessionId, $this->playerDamage);
        }else{
            if (GameSession::isTraining($this->sessionId) == 0) {
                Student::decrementStudentLifeById($this->studentId);
            }
            Student::stopStudentStreak($this->studentId);
        }

		$life = Student::getStudentLife($this->studentId);

        if($life <= 0) {
            $this->hibernate();
        }
    }

    /**
     * Recalculates the timings for the next question
     *
     * @return void
     */
    public function refreshAnswerTime() {
        $this->lastAnswerTime = $this->nextAnswerTime;
        $this->nextAnswerTime = date('Y-m-d H:i:s', strtotime($this->nextAnswerTime) + rand($this->answerTimeDurationMin, $this->answerTimeDurationMax));

        //Log::debug("nextAnswerTime for studentId " . $this->studentId . " " . $this->lastAnswerTime . " " . $this->nextAnswerTime);
    }

    /**
     * Set bot to sleep; used when no life remaining or answered all questions
     *
     * @return void
     */
	public function hibernate() {
        $this->hibernating = true;
    }
}
