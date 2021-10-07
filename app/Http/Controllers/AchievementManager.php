<?php
/* modularized manager for all achievements
*/

namespace App\Http\Controllers;


class AchievementManager {
	public $allAchievements;

	/**
	 * creates instances of all achievements
	 * @param float $pointMultiplier 			factor used to multiply point requirements with increasing time relative to a 3 minute game
	 * @return \void
	 */
	public function __construct($pointMultiplier) {
		$this->allAchievements = array();
		BaseAchievement::setPointMultiplier($pointMultiplier);

		//RANK ACHIEVEMENTS

		array_push($this->allAchievements, new AchievementRank(
			"Achievement.winner",
			"Achievement.winner_text",
			0,"sapphire", "medal", 5, 100007, 1, false));

		array_push($this->allAchievements, new AchievementRank(
			"Achievement.podium",
			"Achievement.podium_text",
			0,"gold", "podium", 3, 1007, 3, false));

		array_push($this->allAchievements, new AchievementRank(
			"Achievement.podium_view",
			"Achievement.podium_view_text",
			0, "silver", "podium", 2, 107, 0.1, true));

		array_push($this->allAchievements, new AchievementRank(
			"Achievement.podium_calling",
			"Achievement.podium_calling_text",
			0, "bronze", "podium", 1, 17, 0.25, true));


		//BULLSHIT ACHIEVEMENTS

		array_push($this->allAchievements, new AchievementRank(
			"Achievement.take_part",
			"Achievement.take_part_text",
			-1, "white", "controller", 0, 1, 1, true));

		array_push($this->allAchievements, new AchievementRank(
			"Achievement.certificate",
			"Achievement.certificate_text",
			-1, "white", "scroll", 0, 1, 1, true));


		//STREAK ACHIEVEMENTS

		array_push($this->allAchievements, new AchievementQuestionStreak(
			"Achievement.wisdom",
			"Achievement.wisdom_text",
			1, "emerald", "enlightenment", 4, 10003, 10));

		array_push($this->allAchievements, new AchievementQuestionStreak(
			"Achievement.hepta",
			"Achievement.hepta_text",
			1, "gold", "card", 3, 1003, 7));

		array_push($this->allAchievements, new AchievementQuestionStreak(
			"Achievement.penta",
			"Achievement.penta_text",
			1, "silver", "card", 2, 103, 5));

		array_push($this->allAchievements, new AchievementQuestionStreak(
			"Achievement.triple",
			"Achievement.triple_text",
			1, "bronze", "card", 1, 13, 3));


		//FAST QUESTION ACHIEVEMENTS

		array_push($this->allAchievements, new AchievementFastQuestions(
			"Achievement.legend",
			"Achievement.legend_text",
			2, "emerald", "wisdom", 4, 10002, 10));

		array_push($this->allAchievements, new AchievementFastQuestions(
			"Achievement.lightning",
			"Achievement.lightning_text",
			2, "gold", "click", 3, 1002, 7));

		array_push($this->allAchievements, new AchievementFastQuestions(
			"Achievement.weasel",
			"Achievement.weasel_text",
			2, "silver", "click", 2, 102, 5));

		array_push($this->allAchievements, new AchievementQuestionStreak(
			"Achievement.hand",
			"Achievement.hand_text",
			2, "bronze", "click", 1, 12, 3));


		//SCORE ACHIEVEMENTS

		array_push($this->allAchievements, new AchievementPoints(
			"Achievement.meticulously",
			"Achievement.meticulously_text",
			3, "gold", "coins", 3, 1004, 50));

		array_push($this->allAchievements, new AchievementPoints(
			"Achievement.diligent",
			"Achievement.diligent_text",
			3, "silver", "coins", 2, 104, 35));

		array_push($this->allAchievements, new AchievementPoints(
			"Achievement.talented",
			"Achievement.talented_text",
			3, "bronze", "coins", 1, 14, 20));


		//SURVIVOR AND MIN LIFES ACHIEVEMENTS

		array_push($this->allAchievements, new AchievementSurvivor(
			"Achievement.good_defence",
			"Achievement.good_defence_text",
			4, "bronze", "shield", 1, 10, 1, -1));

		array_push($this->allAchievements, new AchievementSurvivor(
			"Achievement.good_attack",
			"Achievement.good_attack_text",
			4, "bronze", "spear", 1, 10, 2, -1));

		array_push($this->allAchievements, new AchievementSurvivor(
			"Achievement.perfectionist",
			"Achievement.perfectionist_text_1",
			5, "gold", "bullseye", 3, 1000, 1, 0));

		array_push($this->allAchievements, new AchievementSurvivor(
			"Achievement.perfectionist",
			"Achievement.perfectionist_text_2",
			5, "gold", "bullseye", 3, 1000, 2, 0));

		array_push($this->allAchievements, new AchievementSurvivor(
			"Achievement.accurate",
			"Achievement.accurate_text_1",
			5, "silver", "bullseye", 2, 100, 1, 1));

		array_push($this->allAchievements, new AchievementSurvivor(
			"Achievement.accurate",
			"Achievement.accurate_text_2",
			5, "silver", "bullseye", 2, 100, 2, 1));


		//RANK BY PLAYER TYPE ACHIEVEMENT

		array_push($this->allAchievements, new AchievementRankPlayerType(
			"Achievement.good_defence",
			"Achievement.good_defence_text_2",
			6, "silver", "shield", 2, 101, 1, 0.1));

		array_push($this->allAchievements, new AchievementRankPlayerType(
			"Achievement.good_attack",
			"Achievement.good_attack_text_2",
			6, "silver", "spear", 2, 101, 2, 0.1));
	}

	/**
	 * returns only displayable achievements (max 3) ordered by display priority
	 *
	 * @return \ArrayObject [Achievement]
	 */
	public function getAchievements($studentId, $sessionId) {
		foreach ($this->allAchievements as $achievement) {
			$achievement->checkRequirement($studentId, $sessionId);
		}

		//put highest achievement of same category on top, rest at bottom
		foreach ($this->allAchievements as $a1) {
			if($a1->category == -1|| $a1->fulfilled == false)
				continue;

			foreach ($this->allAchievements as $a2) {
				if($a2->category == -1 || $a1 == $a2 || $a2->fulfilled == false)
					continue;

				if($a1->category == $a2->category) {
					if($a1->starCount < $a2->starCount)
						$a1->displayPriority = 0;
					else if($a1->starCount > $a2->starCount)
						$a2->displayPriority = 0;
				}
			}
		}

		// sort achievements by fulfillment and display priority
		usort($this->allAchievements, function ($a, $b) {
			return self::compareAchievementPriority($b, $a);	//order reversed!
		});


		// are at least 3 achievements fulfilled and displayable?
		$n = count($this->allAchievements); // n=3 (FOR DEBUG:  count($this->allAchievements); ) currently blade only displays 3 others are hidden
		$displayCount = 0;
		for($i = 0; $i < $n; $i++) {
			if($this->allAchievements[$i]->fulfilled == true) {
				$displayCount = $i;
			}
		}

		return array_slice($this->allAchievements, 0, $displayCount + 1);
	}

	/**
	 * returns all achievements, sorted by value mostly DEBUG FEATURE
	 *
	 * @return \ArrayObject [Achievement]
	 */
	public function debugAchievements($studentId, $sessionId, $checkRequirements) {
		if($checkRequirements) {
			foreach ($this->allAchievements as $achievement) {
				$achievement->checkRequirement($studentId, $sessionId);
			}
		}

		// sort achievements by fulfillment and display priority
		usort($this->allAchievements, function ($a, $b) {
			return self::compareAchievementPriority($b, $a);	//order reversed!
		});

		return $this->allAchievements;
	}

	/**
	 * comparable function for display priority of achievements
	 *
	 * @return \int
	 */
	public static function compareAchievementPriority($a, $b)
	{
		if($a->fulfilled == false && $b->fulfilled == true)
			return -1;

		if($b->fulfilled == false && $a->fulfilled == true)
			return 1;

		if($a->displayPriority < $b->displayPriority)
			return -1;
		else if($a->displayPriority > $b->displayPriority)
			return 1;
		else
			return 0;
	}
}
