<?php

namespace App\Http\Controllers;
use DB;
use App\models\Question;
use App\models\GameSession;
use App\models\Boss;
use App\models\Achievement;
use App\models\Student;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

/**
 * Controls the statistics tab.
 */
class EditorStatisticController extends Controller{

    /**
     * @param  int $sessionId
     * @return \View editorStatistics
     */
    public function openEditorStatisticsView($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return redirect()->route('editorHome');

        $questions = Question::getQuestionInformation($sessionId);
        $questionsMergedWithStatistics = [];
        foreach($questions as $question){
            $percentage = Question::getPercentCorrect($question->question_id);
            if($percentage != 0) $percentage = round($percentage*100,2);
            array_push($questionsMergedWithStatistics,
                ['questionId' => $question->question_id,
				'answers' => Question::getAnswers($question->question_id),
                'text' => $question->question_text,
                'image' => $question->image_path,
                'percentage' => $percentage,
                'duration' => round(Question::getQuestionTimes($question->question_id), 2),
                'numberAnswered' => Question::numberAnswered($question->question_id)]);
        }

        $gameModeType = GameSession::getSessionGameModeType($sessionId);
        $teamsData = [];
        if($gameModeType == 'teams'){
            $teamsData = GameSession::getTeamsData($sessionId);
        }

        // sort array by percentage of correct answers, desc
        usort($questionsMergedWithStatistics, function ($item1, $item2){
            return $item2['percentage'] <=> $item1['percentage'];
        });
        return view('editor.Statistics')->with(
            [
                'hardestQuestions' => Question::getHardestOrEasiestQuestions($sessionId, 0),
                'easiestQuestions' => Question::getHardestOrEasiestQuestions($sessionId, 1),
                'percentCorrect' => Question::getAllPercentCorrect($sessionId),
                'sessionId' => $sessionId,
                'numberAnsweredQuestions' => Question::numberAnsweredQuestions($sessionId),
                'questions' => $questionsMergedWithStatistics,
                'sessionName' => GameSession::getSessionName($sessionId),
				'playerCount' => GameSession::getNumberPlayers($sessionId),
                'botCount' => GameSession::getNumberBots($sessionId),
				'bossHealthLeft' => Boss::getBossLife($sessionId),
				'bossHealthOption' => Boss::getBossHealthOption($sessionId),
				'survivingPlayers' => Achievement::getSurvivingPlayers($sessionId),
				'sessionDuration' => GameSession::getSessionDuration($sessionId),
                'actualSessionDuration' => GameSession::getActualSessionDuration($sessionId),
                'gameModeType' => $gameModeType,
                'teamsData' => $teamsData,
                'showStory' => GameSession::getSessionOptionStory($sessionId),
                'dynamicStartLife' => GameSession::getSessionOptionDynamicStartLife($sessionId),
                'startLifePlayerType1' => GameSession::getStudentStartLife(1, $sessionId),
                'startLifePlayerType2' => GameSession::getStudentStartLife(2, $sessionId),
                'useBots' => GameSession::getSessionOptionBots($sessionId),
                'botDifficulty' => GameSession::getSessionOptionBotDifficulty($sessionId)
            ]
        );
    }

    /**
     * Export the cumulated statistic from a session as a json file.
     *
     * @param  int $sessionId
     * @return jsonfile
     */
    public function exportStatistic($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return redirect()->route('editorHome');

        $sessionData = self::getExportStatistic($sessionId);

        // we generate the file and delete it after the user has downloaded it
        $exportName = 'exports/'.date("d-m-Y_h:i").'_'.GameSession::getSessionName($sessionId).'.json';
        Storage::put($exportName, json_encode($sessionData));

        return response()->download('storage/'.$exportName)->deleteFileAfterSend(true);
    }

    /**
     * Export the cumulated statistic from all sessions from a name as a json file.
     *
     * @param  int $sessionId
     * @return jsonfile
     */
    public function exportStatisticCum($sessionId){
        $sessionName = GameSession::getSessionName($sessionId);
        $sessionIds = GameSession::getSessionIdsbyName($sessionName);
        $exportData = new Collection();

        foreach($sessionIds as $sessionId){
            //probally dont need to check all, but just to be sure
            if(!GameSession::checkAuthority($sessionId)) return redirect()->route('editorHome');

            $exportData->push(self::getExportStatistic($sessionId));
        }

        // we generate the file and delete it after the user has downloaded it
        $exportName = 'exports/'.date("d-m-Y_h:i").'_'.$sessionName.'_cum.json';
        Storage::put($exportName, json_encode($exportData));

        return response()->download('storage/'.$exportName)->deleteFileAfterSend(true);
    }

    /**
     * Get the Export Data for the the cumulated statistic from a session.
     *
     * @param  int $sessionId
     * @return \ArrayObject associative array [all stuff we can get]
     */
    public function getExportStatistic($sessionId){

        $questions = Question::getQuestionInformation($sessionId);
        $questionsMergedWithStatistics = [];
        foreach($questions as $question){
            $percentage = Question::getPercentCorrect($question->question_id);
            if($percentage != 0) $percentage = round($percentage*100,2);
            array_push($questionsMergedWithStatistics,
                ['questionId' => $question->question_id,
                 'answers' => Question::getAnswers($question->question_id),
                 'text' => $question->question_text,
                 'image' => $question->image_path,
                 'percentage' => $percentage,
                 'duration' => round(Question::getQuestionTimes($question->question_id), 2),
                 'numberAnswered' => Question::numberAnswered($question->question_id)]);
        }

        $sessionData = GameSession::getSessionInfosExport($sessionId);
        $sessionData[0]->questions = $questionsMergedWithStatistics;

        return $sessionData;
    }

    /**
     * Export the user statistic from a session as a json file.
     *
     * @param  int $sessionId
     * @return jsonfile
     */
    public function exportStatisticUser($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return redirect()->route('editorHome');

        // we generate the file and delete it after the user has downloaded it
        $exportName = 'exports/'.date("d-m-Y_h:i").'_'.GameSession::getSessionName($sessionId).'_user'.'.json';
        Storage::put($exportName, json_encode(Student::getAllStudentData($sessionId)));

        return response()->download('storage/'.$exportName)->deleteFileAfterSend(true);
    }

    /**
     * Export the user statistic from all cumulated sessions as a json file.
     *
     * @param  int $sessionId
     * @return jsonfile
     */
    public function exportStatisticUserCum($sessionId){
        $sessionName = GameSession::getSessionName($sessionId);
        $sessionIds = GameSession::getSessionIdsbyName($sessionName);
        $exportData = new Collection();

        foreach($sessionIds as $sessionId){
            //probally dont need to check all, but just to be sure
            if(!GameSession::checkAuthority($sessionId)) return redirect()->route('editorHome');

            $exportData->push(Student::getAllStudentData($sessionId));
        }

        // we generate the file and delete it after the user has downloaded it
        $exportName = 'exports/'.date("d-m-Y_h:i").'_'.$sessionName.'_cum_user'.'.json';
        Storage::put($exportName, json_encode($exportData));

        return response()->download('storage/'.$exportName)->deleteFileAfterSend(true);
    }

    /**
     * Get Data for the Graph "Prozentzahl richtig beantworteter Fragen".
     *
     * @param  int $sessionId
     * @return \ArrayObject [int[] graphArray, int[] array_values(graph2Data), int[] array_keys(graph2Data)]
     */
    public function getGraphData($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return redirect()->route('editorHome');
        return Question::getGraphData($sessionId);
    }
}
