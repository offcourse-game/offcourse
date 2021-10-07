<?php

namespace App\models;
use Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Question{

    /**
     * Get a new not answered question for a student.
     *
     * @param  int $studentId
     * @param  int $sessionId
     * @return string one not answered question
     */
    public static function getNewQuestion($studentId, $sessionId){

        $usedQuestions = DB::table('student_question_statistic')
            ->where('student_id', $studentId)
            ->distinct()->pluck('question_id');

        $availableQuestions = DB::table('questions')
            ->where('session_id', $sessionId)
            ->pluck('question_id');

        // get not yet answered questions
        $possibleQuestionIds = $availableQuestions->diff($usedQuestions);

        if ($possibleQuestionIds->isEmpty()){
            return Null;
        }else{
            $randomQuestionId = $possibleQuestionIds->random();
        }

        $question = DB::table('questions')
            ->join('answers', 'questions.question_id', '=', 'answers.question_id')
            ->select('questions.question_id', 'question_text', 'image_path', DB::raw('count(correct) as number_correct_answers'))
            ->where([['questions.question_id', '=', $randomQuestionId], ['correct', '=', '1'],])
            ->groupBy('question_id') // needed because sql_mode=only_full_group_by
            ->first();

        return $question;
    }

    /**
     * How many questions we have in a session.
     *
     * @param  int $sessionId
     * @return int question count
     */
    public static function getQuestionCount($sessionId){
        $questionCount = DB::table('questions')
            ->where('session_id', $sessionId)
            ->count();

        return $questionCount;
    }

    /**
     * Delete a question, as well as the corresponding answers and image (if not used anywhere else) from a session.
     *
     * @param  int $questionId
     * @return void
     */
    public static function deleteQuestion($questionId){
        //delete an image first if possible
        $imagePath = DB::table('questions')
            ->where('question_id', $questionId)
            ->value('image_path');

        if ($imagePath){
            //only delete the image if we don't use it in another question
            $questionsImageUsed = DB::table('questions')
                ->where('image_path', $imagePath)
                ->pluck('question_id');

            if ( count($questionsImageUsed) == 1){
                Storage::delete($imagePath);
            }
        }

        DB::table('questions')
            ->where('question_id', $questionId)
            ->delete();
    }

    /**
     * Get one question with the corresponding answers and image-path.
     *
     * @param  int $questionId
     * @return \ArrayObject [question: Collection: string question_text, string image_path, int question_id,
     *                       answers: Collection: string answer_text, int (boolean 1/0) correct]
     */
    public static function getQuestionAnswersById($questionId){
        $question = DB::table('questions')
            ->select('question_text', 'image_path', 'question_id')
            ->where('question_id', $questionId)
            ->first();

        $answers = DB::table('answers')
            ->select('answer_text', 'correct')
            ->where('question_id', $questionId)
            ->get();

        return array('question' => $question, 'answers' => $answers);
    }

    /**
     * Get for an question the corresponding answers, used in stat export.
     *
     * @param  int $questionId
     * @return \ArrayObject [answers: Collection: int answer_id, string answer_text, int (boolean 1/0) correct]
     */
    public static function getAnswers($questionId){

        $answers = DB::table('answers')
            ->select('answer_id', 'answer_text', 'correct')
            ->where('question_id', $questionId)
            ->get();

        return $answers;
    }

    /**
     * @param  int $questionId
     * @return double when the question is not in the statistics table then 0
     */
    public static function getPercentCorrect($questionId){
        $correct = DB::table('student_question_statistic')
            ->where('correct', 1)
            ->where('question_id', $questionId)
            ->count();

        $all = DB::table('student_question_statistic')
            ->where('question_id', $questionId)
            ->count();

        if ($all != 0){
            $percentCorrect = $correct / $all;
            return $percentCorrect;
        }
        return 0;
    }

    /**
     * Get data for all statistics graphs.
     *
     * @param  int $sessionId
     * @return \ArrayObject [int[] graphArray, int[] array_values(graph2Data), int[] array_keys(graph2Data)]
     */
    public static function getGraphData($sessionId){
        if(!GameSession::checkAuthority($sessionId)) return redirect()->route('editorHome');
        // get the percent correct value for each student as an array, the function provides the data for the second pie chart
        $graph1Data = DB::table('student_question_statistic')
            ->join('questions', 'student_question_statistic.question_id', '=', 'questions.question_id')
            ->selectRaw('(sum(correct)/count(student_id)) as percent')
            ->where('questions.session_id', $sessionId)
            ->orderBy('percent')
            ->groupBy('student_id')
            ->pluck('percent');

        $students = 0;
        $graphArray = [];
        $percent = 0.25;

        // returns an array containing relative numbers of correct answered questions split in 4 areas
        if(!empty($graph1Data)){
            for ($i = 0; $i < sizeof($graph1Data); $i++){
                if ($graph1Data[$i] <= $percent){
                    $students++; // count number of students until the certain value is exceeded
                } else { // if the value is exceeded the absolute number gets pushed to the array and the percent value is incremented
                    array_push($graphArray, $students);
                    $percent += 0.25;
                    $i--;
                    $students = 0;
                }
            }
            array_push($graphArray, $students); // the last value is inserted
            $size = sizeof($graphArray);
            for ($i = 0; $i < (4 - $size); $i++){ // the array is filled up to size 4 if it did not happen already
                array_push($graphArray, 0);
            }
        }

        // get the numbers of answered questions if the studentLife is zero, this function provides the data for the line graph
        $graph2Data = DB::table('student_question_statistic')
            ->join('student', 'student_question_statistic.student_id', '=', 'student.student_id')
            ->selectRaw('count(student_question_statistic.question_id) as count')
            ->where('student.session_id', $sessionId)
            ->where('student_life', 0)
            ->orderBy('count')
            ->groupBy('student_question_statistic.student_id')
            ->pluck('count')->toArray();

        if(!empty($graph2Data)) {
            $graph2Data = array_count_values($graph2Data);
            $counter = 1;
            $end = max(array_keys($graph2Data));

            /* This fills up the missing keys and adds the value 0 to it, because sometimes nobody has answered
            e.g. 4 questions and then the graph would not be drawn correctly */
            for ($i = 1; $i < $end; $i++) {
                if (!array_key_exists("$counter", $graph2Data)) {
                    $graph2Data = $graph2Data + array("$counter" => 0);
                }
                $counter++;
            }
            ksort($graph2Data);
        }
        // If no data is provided, return [] for each array because the array [0,0,0,0] for the first graph is not meaningful
        if ($graphArray == [0,0,0,0] && empty($graph2Data)){
            return [[],[],[]];
        }
        return [$graphArray, array_values($graph2Data), array_keys($graph2Data)];
    }

    /**
     * What percentage of the answers is correct?
     *
     * @param  int $sessionId
     * @return double When there are no answered questions then 0
     */
    public static function getAllPercentCorrect($sessionId){
        $numberCorrect = DB::table('student_question_statistic')
            ->join('questions', 'student_question_statistic.question_id', '=', 'questions.question_id')
            ->where('correct', 1)
            ->where('questions.session_id', $sessionId)
            ->count('correct');

        $numberAnsweredQuestions = Question::numberAnsweredQuestions($sessionId);

        if ($numberAnsweredQuestions != 0){
            $percentCorrect = $numberCorrect / $numberAnsweredQuestions * 100;
            return $percentCorrect;
        }
        return 0;
    }

    /**
     * Number of answered questions from a session.
     *
     * @param  int $sessionId
     * @return int count
     */
    public static function numberAnsweredQuestions($sessionId){
        // get the number of how many questions have been answered
        $number = DB::table('student_question_statistic')
            ->join('questions', 'student_question_statistic.question_id', '=', 'questions.question_id')
            ->where('questions.session_id', $sessionId)
            ->count();
        return $number;
    }

    /**
     * Get the hardest or the easiest two questions.
     * @param  int $sessionId
     * @param  int $hard boolean 1=hardest/0=easiest
     * @return \ArrayObject [string questions]
     */
    public static function getHardestOrEasiestQuestions($sessionId, $hard){
        if ($hard == 1){
            $questionIds = DB::table('student_question_statistic')
                ->join('questions', 'student_question_statistic.question_id', '=', 'questions.question_id')
                ->selectRaw('student_question_statistic.question_id, (sum(correct)/count(student_question_statistic.question_id)) as count')
                ->where('questions.session_id', $sessionId)
                ->groupBy('question_id')
                ->orderByRaw('count desc')
                ->limit(2)
                ->pluck('question_id');
        }
        else{
            $questionIds = DB::table('student_question_statistic')
                ->join('questions', 'student_question_statistic.question_id', '=', 'questions.question_id')
                ->selectRaw('student_question_statistic.question_id, (sum(correct)/count(student_question_statistic.question_id)) as count')
                ->where('questions.session_id', $sessionId)
                ->groupBy('question_id')
                ->orderByRaw('count asc')
                ->limit(2)
                ->pluck('question_id');
        }
        $questionTexts = DB::table('questions')
            ->whereIn('question_id', $questionIds)
            ->pluck('question_text')->toArray();

        // ensure we have a complete array, add error string if needed
        $textArray = [];
        for ($i=0;$i<sizeof($questionTexts);$i++){
                array_push($textArray, $questionTexts[$i]);
        }
        for ($i=0;$i<2-sizeof($questionTexts);$i++){
            array_push($textArray, "Es existieren keine Einträge für diese Information");
        }

        return $textArray;
    }

    /**
     * Get the number how often one question has been answered.
     *
     * @param  int $questionId
     * @return int count
     */
    public static function numberAnswered($questionId){
        $number = DB::table('student_question_statistic')
            ->where('question_id', $questionId)
            ->count();
        return $number;
    }

    /**
     * Get average time spend on this question.
     *
     * @param  int $questionId
     * @return double Minutes
     */
    public static function getQuestionTimes($questionId){
        $questionTimes = DB::table('student_question_statistic')
            ->selectRaw('sum(duration)/count(question_id) as average')
            ->where('question_id', $questionId)
            ->groupBy('question_id')
            ->value('average');
        return $questionTimes;
    }

    /**
     * Get all questions from a session.
     *
     * @param  int $sessionId
     * @return \Collection int question_id, string question_text, string image_path
     */
    public static function getQuestionInformation($sessionId){
        $questionTexts = DB::table('questions')
            ->select('question_id', 'question_text', 'image_path')
            ->where('session_id', $sessionId)
            ->get();
        return $questionTexts;
    }

    /**
     * Get all answers and questions in a session.
     *
     * @param  int $sessionId
     * @return \ArrayObject [\ArrayObject [question: Collection: string question_text, string image_path, int question_id,
     *                                     answers: Collection: string answer_text, int (boolean 1/0) correct]]
     */
    public static function getAllAnswersQuestions($sessionId){
        $questionIds = DB::table('questions')
            ->where('session_id', $sessionId)
            ->pluck('question_id');

        $answersQuestions = array();
        foreach ($questionIds as $questionId){
            array_push($answersQuestions, self::getQuestionAnswersById($questionId));
        }
        return $answersQuestions;
    }

    /**
     * Update a question, used when the user edits a question.
     *
     * @param  int $questionId
     * @param  string $question
     * @param  \ArrayObject $answers [\ArrayObject [string answer$i, int (boolean 1/0) checkAnswerCorrect$i]]
     * @param  int $sessionId
     * @param  string $imagePath
     * @return void
     */
    public static function updateQuestion($questionId, $question, $answers, $sessionId, $imagePath){
        $oldImagePath = DB::table('questions')
            ->where('question_id', $questionId)
            ->value('image_path');

        // $imagePath is Null if we dont have an new image or dont update it
        // currently we can not delete an image and dont give a new one at the same time!
        if ($imagePath && $oldImagePath){
                Storage::delete($oldImagePath);
        }elseif(! $imagePath){ // use the old image if dont have a new one
            $imagePath = $oldImagePath;
        }

        DB::table('questions')
            ->where('question_id', $questionId)
            ->update(['question_text' => $question, 'image_path' => $imagePath]);

        $answerIds = DB::table('answers')
            ->where('question_id', $questionId)
            ->pluck('answer_id');

        $i=0;
        foreach ($answerIds as $answerId){
            DB::table('answers')
                ->where('answer_id', $answerId)
                ->update(['answer_text' => $answers["answer$i"], 'correct' => $answers["checkAnswerCorrect$i"]]);
            $i++;
        }
    }

    /**
     * Add a new question to the DB.
     *
     * @param string $question
     * @param \ArrayObject $answers
     * @param int $sessionId
     * @param string $imagePath
     */
    public static function addQuestion($question, $answers, $sessionId, $imagePath){
        // insert question and path, getting the new id
        $questionId = DB::table('questions')->insertGetId(
            ['question_text' => $question, 'session_id' => $sessionId, 'image_path' => $imagePath]
        );

        // insert answers
        $answerIds = [];

        for ($i=0; $i<4; $i++){ // We always have 4 answers
            $answerIds["answer$i"] = DB::table('answers')->insertGetId(
                ['answer_text' => $answers["answer$i"], 'correct' => $answers["checkAnswerCorrect$i"], 'question_id' => $questionId]
            );
        }
    }

    /**
     * Add a correct or incorrect answers for bots without unnecessary overhead
     *
     * @param  int $studentId
     * @param  int $sessionId
     * @param  int (boolean 1/0) $correct
     * @return int (boolean 1/0) successful
     */
    public static function addAnswerLightweight($studentId, $sessionId, $correct, $duration){

        $answeredQuestions = DB::table('student_question_statistic')
            ->where('student_id', $studentId)
            ->select('question_id');

        $nextQuestion = DB::table('questions')
            ->where('session_id', $sessionId)
            ->whereNotIn('question_id', $answeredQuestions)
            ->value('question_id');

        if($nextQuestion == Null){
            return false;
        }

        DB::table('student_question_statistic')->insert(
            ['student_id' => $studentId, 'question_id' => $nextQuestion, 'correct' => $correct, 'duration' => $duration]
        );

        if($correct == true){
            Student::incrementStudentStreak($studentId);
        }else{
            Student::stopStudentStreak($studentId);
        }

        //Log::debug("Question inserted lightweight answer: " . 'student_id ' . $studentId . ' question_id ' . $nextQuestion . ' correct ' . $correct . ' duration ' . $duration);

        return true;
    }
}
