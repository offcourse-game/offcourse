<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 08.06.2019
 * Time: 13:25
 */

namespace App\Http\Controllers;
use App\models\Achievement;
use DB;
use App\models\GameSession;
use App\models\Boss;
use App\models\Student;
use Illuminate\Support\Facades\Input;

/* base class for all avievements */
abstract class BaseAchievement {
	protected static $pointMultiplier;					//factor used to multiply point requirements with increasing time => 1 equals a 3 minute game, factor is scaled in relation to a 3 minute game

	public $title = 'Missing Title';					//title
	public $text = 'missing text';						//formatted text, can use <b></b>
	public $textDisplayParameter = '<naN>';				//optional parameter that is used inside localization string of achievement
	public $color = 'white';							//color of achievement: sapphire, emerald, gold, silver, bronze, white
	public $badgeImageClass = 'star';					//determines what the prefix of the image name is called
	public $starCount = 0;								//determines how many stars are displayed
	public $displayPriority = -1;						//determines the display priority, the higher the further on top
	public $category = -1;								//category of achievement, highest star count of image will be on top; -1 will be ignored
	public $fulfilled = false;							//is the condition of the achievement true?

	public static function setPointMultiplier($multiplier) {
		BaseAchievement::$pointMultiplier = $multiplier;
	}

	public function __construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority) {
		$this->title = $title;
		$this->text = $text;
		$this->color = $color;
		$this->badgeImageClass = $badgeImageClass;
		$this->starCount = $starCount;
		$this->displayPriority = $displayPriority;
		$this->category = $category;

		$this->fulfilled = false;
	}

	abstract public function checkRequirement($studentId, $sessionId);
}

/* achievements if player is in
	1. useRelativeRank == true top §minRank (0..1) percent of all players (best X% of player)
	2. useRelativeRank == false in top §minRank (0,1,2,3,..) of all players */
class AchievementRank extends BaseAchievement {
	public $minRank = 0;
	public $useRelativeRank = false;

	public function __construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority, $minRank, $useRelativeRank) {
		parent::__construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority);

		$this->minRank = $minRank;
		$this->useRelativeRank = $useRelativeRank;
	}

	public function checkRequirement($studentId, $sessionId) {
		$rank = Achievement::getPlayerQuestionRank($studentId, $sessionId, $this->useRelativeRank);

		if(empty($rank) == true || $rank > $this->minRank) {
			$this->fulfilled = false;
		} else {
			$this->fulfilled = true;
		}
	}
}

/* how many Questions were answered in a row */
class AchievementQuestionStreak extends BaseAchievement {
	public $minStreak;

	public function __construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority, $minStreak) {
		parent::__construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority);

		$this->minStreak = $minStreak;
	}

	public function checkRequirement($studentId, $sessionId) {
		$this->fulfilled = (Achievement::getStreakAchievement($studentId, $sessionId) >= $this->minStreak);
	}
}

/* how many Questions were answered below 12 sec? */
class AchievementFastQuestions extends BaseAchievement {
	public $minCount;

	public function __construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority, $minCount) {
		parent::__construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority);

		$this->minCount = $minCount;
	}

	public function checkRequirement($studentId, $sessionId) {
		$this->fulfilled = (Achievement::getFastQuestionCount($studentId, $sessionId) >= $this->minCount);
	}
}

/* did they do less or equal maxMistakes mistakes? which player type are they?
	if maxMistakes is -1 see if they survived */
class AchievementSurvivor extends BaseAchievement {
	public $playerType;
	public $maxMistakes;

	public function __construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority, $playerType, $maxMistakes) {
		parent::__construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority);

		$this->playerType = $playerType;
		$this->maxMistakes = $maxMistakes;
	}

	public function checkRequirement($studentId, $sessionId) {
		//disable this archivement, when there is no story, thus no player type
		if (GameSession::getSessionOptionStory($sessionId) && GameSession::getSessionGameModeType($sessionId) == "classic"){
			$playerType = Student::getType($studentId);
			if($this->playerType != $playerType) {
				$this->fulfilled = False;
				return;
			}

			if($this->maxMistakes >= 0){
				$mistakes = (GameSession::getStudentStartLife($playerType, $sessionId) - Student::getStudentLife($studentId));
				$this->fulfilled = ($mistakes <= $this->maxMistakes);
			}else{
				$this->fulfilled = (Student::getStudentLife($studentId) > 0);
			}
		}else{
			$this->fulfilled = False;
		}
	}
}

/* is the player in top X% (point / score based rank) of player type X? */
class AchievementRankPlayerType extends BaseAchievement {
	public $playerType;
	public $minRank;

	public function __construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority, $playerType, $minRank) {
		parent::__construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority);

		$this->playerType = $playerType;
		$this->minRank = $minRank;
	}

	public function checkRequirement($studentId, $sessionId) {
		//disable this archivement, when there is no story or no classic mode, thus no player type
		if (GameSession::getSessionOptionStory($sessionId) && GameSession::getSessionGameModeType($sessionId) == "classic"){
			$this->fulfilled = (Achievement::getTopPlayerType($studentId, $sessionId, $this->playerType) <= $this->minRank);
		}else{
			$this->fulfilled = False;
		}
	}
}

/* achievements if player reached greater or equal than $minPoints as a score */
class AchievementPoints extends BaseAchievement {
	public $minPoints;

	public function __construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority, $minPoints) {
		parent::__construct($title, $text, $category, $color, $badgeImageClass, $starCount, $displayPriority);

		$this->minPoints = $minPoints;
		$this->textDisplayParameter = floor($minPoints * BaseAchievement::$pointMultiplier);
	}

	public function checkRequirement($studentId, $sessionId) {
		// If we dont want to show points, it will be always false
		if (GameSession::getSessionOptionPoints($sessionId)){
			$this->fulfilled = (Achievement::getStudentScore($studentId) >= floor($this->minPoints * BaseAchievement::$pointMultiplier));
		}else{
			$this->fulfilled = False;
		}
	}
}
