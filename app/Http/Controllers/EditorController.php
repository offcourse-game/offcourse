<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\models\Question;
use App\models\GameSession;

/**
 * Controls all editor tasks.
 */
class EditorController extends Controller{

    /**
     * This is the editor starting page.
     *
     * @return \View editorHome
     */
    function editorHome(){
        $sessionInfos = GameSession::getSessionInfos();
        $playedSessionInfos = GameSession::getStatisticSessionInfos(True);
        return view('editor.Home')->with([
                'session_infos' => $sessionInfos,
				'played_session_infos' => $playedSessionInfos,
        ]);
    }

    /**
     * View to add a question to a Session.
     *
     * @param  int $sessionId
     * @return \View editorQuestion
     */
    function addQuestion($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();
        return view('editor.Question')->with([
                'sessionId' => $sessionId,
                'sessionName' => GameSession::getSessionName($sessionId)
        ]);
    }

    /**
     * Used for a Get Request when the user wants to edit an question.
     *
     * @param  int $sessionId
     * @param  int $questionId
     * @return \View editorQuestion
     */
    function editQuestion($sessionId, $questionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();
        $answersQuestion = Question::getQuestionAnswersById($questionId);

        return view('editor.Question')->with([
            'sessionId' => $sessionId,
            'answersQuestion' => $answersQuestion,
            'sessionName' => GameSession::getSessionName($sessionId)
        ]);
    }

    /**
     * Update a Question already in DB.
     *
     * @param  Request $request
     * @param  int  $sessionId
     * @param  int  $questionId
     * @return $this editorQuestionSummary
     */
    function updateQuestion(Request $request, $sessionId, $questionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();

        $imagePath = $this->saveImage($request);

        // Get question and answers
        $question = $request->input('question');
        $answers = $request->only(['checkAnswerCorrect0', 'checkAnswerCorrect1', 'checkAnswerCorrect2', 'checkAnswerCorrect3',
                                   'answer0', 'answer1', 'answer2', 'answer3']);

        Question::updateQuestion($questionId, $question, $answers, $sessionId, $imagePath);

        return $this->editorQuestionSummary($sessionId);
    }

    /**
     * Save image from Request to storage/*path*.
     *
     * @param  Request $request should contain an image to store it
     * @return string|Null the path to the image or null if there is no image given
     */
    function saveImage(Request $request){
        // Get image
        if ($request->hasFile('image')){
            // This $imagePath must be saved to the db for the right question!
            // the image can be accessed via "storage/$imagePath" if you have run php artisan storage:link
            return $request->image->store('uploads');

        }else{
            return Null; //We don't have an image
        }
    }

    /**
     * Post request to add a question.
     *
     * @param  Request $request
     * @param  int  $sessionId
     * @return \View editorQuestion
     */
    function editorQuestion(Request $request, $sessionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();

        $imagePath = $this->saveImage($request);

        // Get question and answers
        $question = $request->input('question');
        $answers = $request->only(['checkAnswerCorrect0', 'checkAnswerCorrect1', 'checkAnswerCorrect2', 'checkAnswerCorrect3',
                                   'answer0', 'answer1', 'answer2', 'answer3']);

        Question::addquestion($question, $answers, $sessionId, $imagePath);
        GameSession::setSessionDurationMax5($sessionId, $this->max_duration($sessionId)); //Update session duration with best value possible to account for change in the number of questions

        return view('editor.Question')->with([
                'sessionId' => $sessionId,
                'sessionName' => GameSession::getSessionName($sessionId)
        ]);
    }

    /**
     * Post request to add a new session.
     *
     * @param  Request $request
     * @return \View editorQuestion
     */
    function editorSessionSetup(Request $request){
        $sessionId = GameSession::addsession($request->input('nameInput'), Auth::id());
        return view('editor.Question')->with([
                'sessionId' => $sessionId,
                'sessionName' => GameSession::getSessionName($sessionId)
        ]);
    }

    /**
     * Post request to save different session settings.
     *
     * @param  Request $request
     * @param  int  $sessionId
     * @return $this editorGameStartup
     */
    function editorGameSettings(Request $request, $sessionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();

        $bossHealthOption = 1;
        if ($request->has('radio_boss_health_option')){ // key does not exists in nogame/teams mode
            if ($request->input('radio_boss_health_option') == 'option0') $bossHealthOption = 0.8;
            else if ($request->input('radio_boss_health_option') == 'option1') $bossHealthOption = 1;
            else if ($request->input('radio_boss_health_option') == 'option2') $bossHealthOption = 1.2;
            else if ($request->input('radio_boss_health_option') == 'option3') $bossHealthOption = 3;
        }

        $lang = $request->input('lang');
        $evaluationUrl = $request->input('evaluation_url');

        $mobileRankSetting = (int) $request->boolean('checkbox_session_option_private_rank');
        $projectorRankSetting = (int) $request->boolean('checkbox_session_option_public_rank');
        // we get 4 different numbers for 4 different options, show rank: 0:nowhere, 1:only mobile, 2:only projector, 3:both
        $sessionOptionRank = 2 * $projectorRankSetting + $mobileRankSetting;

        $showStory = $request->boolean('checkbox_show_story');
        $showBadges = $request->boolean('checkbox_show_badges');
        $usePoints = $request->boolean('checkbox_use_points');
        $showCharacterSelection = $request->boolean('checkbox_show_character_selection');
        $showNumberCorrectAnswers = $request->boolean('checkbox_show_number_correct_answers');
        $useBots = $request->boolean('checkbox_use_bots');
        $botDifficulty = 2;
        if($request->has('radio_bot_difficulty')){
            $botDifficulty = $request->input('radio_bot_difficulty');
        }

        //default values
        $isTraining = 0;
        $isGame = 1;
        $isTeams = 0;
        $useDynamicStartLife = 1;

        //override settings depending on game mode
        if($request->input('input_gameModeType') == 'classic') {
            $isGame = 1;
            $isTraining = 0;
        }  else if($request->input('input_gameModeType') == 'noGame') {
            $isGame = 0;
            $isTraining = 1;

            $showStory = 0;
            $showCharacterSelection = 0;
            $usePoints = 0;
            $showBadges = 0;
            $mobileRankSetting = 0;
            $projectorRankSetting = 0;
            $sessionOptionRank = 2 * $projectorRankSetting + $mobileRankSetting;
            $useBots = 0;
        } else if($request->input('input_gameModeType') == 'training') {
            $isGame = 1;
            $isTraining = 1;

            $showStory = 0;
            $usePoints = 1;
            $showBadges = 1;
            $mobileRankSetting = 1;
            $projectorRankSetting = 1;
            $sessionOptionRank = 2 * $projectorRankSetting + $mobileRankSetting;
        } else if($request->input('input_gameModeType') == 'teams') {
            $isGame = 0;
            $isTeams = 1;
            $isTraining = 1;

            $showCharacterSelection = 1;
            $showStory = 1;
            $usePoints = 1;
            $showBadges = 1;
            $mobileRankSetting = 1;
            $projectorRankSetting = 1;
            $sessionOptionRank = 2 * $projectorRankSetting + $mobileRankSetting;
        }

        GameSession::saveSessionSettings($request->input('session_duration'), $sessionOptionRank, $bossHealthOption, $showBadges,
            $isTraining, $usePoints, $showCharacterSelection, $showStory, $isGame, $isTeams, $showNumberCorrectAnswers, $useBots, $botDifficulty,
            $useDynamicStartLife, $lang, $evaluationUrl, $sessionId);
        return $this->editorGameStartup($sessionId);
    }

    /**
     * Get request to editorGameSettings.
     *
     * @param  int $sessionId
     * @return \View editorGameSettings
     */
    function getEditorGameSettings($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();

        $max_duration = $this->max_duration($sessionId); // get
        $currentSessionSettings = GameSession::getCurrentSessionSettings($sessionId);
        $gameModeType = GameSession::getSessionGameModeType($sessionId);
        $lang = GameSession::getSessionOptionLanguage($sessionId);

        return view('editor.GameSettings')->with([
                'sessionId' => $sessionId,
                'max_duration' => $max_duration,
                'gameModeType' => $gameModeType,
                'lang' => $lang,
                'sessionSettings' => $currentSessionSettings[0]
        ]);
    }

    /**
     * Get question summary.
     *
     * @param  int $sessionId
     * @return \View editorQuestionSummary
     */
    function editorQuestionSummary($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();

        $answersQuestions = Question::getAllAnswersQuestions($sessionId);
        $sessionInfos = GameSession::getSessionInfos();
        $playedSessionInfos = GameSession::getStatisticSessionInfos(True);

        return view('editor.QuestionSummary')->with([
                'sessionId' => $sessionId,
                'answersQuestions' => $answersQuestions,
                'session_infos' => $sessionInfos,
                'played_session_infos' => $playedSessionInfos,
                'sessionName' => GameSession::getSessionName($sessionId)
        ]);
    }

    /**
     * @param  int $sessionId
     * @param  int $questionId
     * @return $this editorQuestionSummary
     */
    function deleteQuestion($sessionId, $questionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();
        Question::deleteQuestion($questionId);
        GameSession::setSessionDurationMax5($sessionId, $this->max_duration($sessionId)); //Update session duration with best value possible
        return $this->editorQuestionSummary($sessionId);
    }

    /**
     * @param  int $sessionId
     * @return $this editorHome
     */
    function deleteSession($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();
        GameSession::deleteSession($sessionId);
        return $this->editorHome();
	}

    /**
     * Get the game startup page, which shows a summary of the session.
     *
     * @param  int $sessionId
     * @return \View editorGameStartup
     */
    function editorGameStartup($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();

        $currentSessionSettings = GameSession::getCurrentSessionSettings($sessionId);
        $questionCount = Question::getQuestionCount($sessionId);
        $gameModeType = GameSession::getSessionGameModeType($sessionId);
        $lang = GameSession::getSessionOptionLanguage($sessionId);

        return view('editor.GameStartup')->with([
                'sessionId' => $sessionId,
                'current_session_infos' => $currentSessionSettings[0],
                'gameModeType' => $gameModeType,
                'questionCount' => $questionCount,
                'lang' => $lang,
                'sessionName' => GameSession::getSessionName($sessionId)
        ]);
    }

    /**
     * How long can the game be, so that we have enough questions? Between 1..59
     *
     * @param  int $sessionId
     * @return int in minutes
     */
    function max_duration($sessionId){
        $number_questions = Question::getQuestionCount($sessionId);
        // the average user needs 20 Sec per Question
        $max_duration = floor($number_questions *0.333);

        if ($max_duration < 1){
            $max_duration = 1;
        } elseif ($max_duration > 59) {
            $max_duration = 59;
        }
        return $max_duration;
    }

    /**
     * Import Questions from a an old session to a new one.
     *
     * We need to have an Illuminate\Http\Request with the name 'target' and 'source'.
     * These ints controls the copy operation in the DB.
     *
     * @return void
     */
    function importSession(Request $request){
        $target = $request->input("target");
        $source = $request->input("source");

        GameSession::importSession($target, $source);
        GameSession::setSessionDurationMax5($target, $this->max_duration($target)); //Update session duration with best value possible
    }

    /**
     * Copy a session to another prof
     *
     * @param  int $SessionId to be copied
     * @param  Request $request to get profUidOrEmail to whom to copy
     * @return $this editorGameStartup($sessionId);
     */
    function shareSession($sessionId, Request $request){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();
        $profUidOrEmail = $request->input("profUidOrEmail");

        $profId = GameSession::getProfIdbyUidorEmail($profUidOrEmail);

        if ($profId == -1){
            return -1;
        }

        $this->copySession($sessionId, $profId, False);
        return 1;

    }

    /**
     * Copy a session into a new session!
     *
     * @param  int $SessionId to be copied
     * @param  int $profId [default = calling user] to whom the session should be copied
     * @param  boolean $redirect [default = True] if we redirect to editorGameStartup
     * @return $this editorGameStartup($sessionId) || Void;
     */
    function copySession($sessionId, $profId = -1, $redirect = True){
        if(!GameSession::checkAuthority($sessionId)) return $this->editorHome();

        if ($profId == -1){
            $profId = Auth::id();
        }

        $oldSessionSettings = GameSession::getCurrentSessionSettings($sessionId);

        // create new session for $profId, with the same name
        $newSessionId = GameSession::addsession($oldSessionSettings[0]->session_name, $profId);
        GameSession::importSession($newSessionId, $sessionId);

        // copy old settings
        GameSession::saveSessionSettings($oldSessionSettings[0]->session_duration, $oldSessionSettings[0]->session_option_rank,
            $oldSessionSettings[0]->boss_health_option, $oldSessionSettings[0]->show_badges, $oldSessionSettings[0]->is_training,
            $oldSessionSettings[0]->use_points, $oldSessionSettings[0]->show_character_selection, $oldSessionSettings[0]->show_story,
            $oldSessionSettings[0]->is_game, $oldSessionSettings[0]->is_teams, $oldSessionSettings[0]->show_number_correct_answers,
            $oldSessionSettings[0]->use_bots, $oldSessionSettings[0]->bot_difficulty, $oldSessionSettings[0]->use_dynamic_start_life, $oldSessionSettings[0]->lang,
            $oldSessionSettings[0]->evaluation_url, $newSessionId);

        if ($redirect){
            return redirect()->route('editorGameStartup', ['sessionId' => $newSessionId]);
        }
    }
}
