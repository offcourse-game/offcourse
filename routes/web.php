<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/


// --------------- General Routes --------------------

//call /log-viewer to enter log viewer for debugging disable for production

Auth::routes();
Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/', function () {
    return redirect()->route('info');
})->name('welcome');

//debug to display all achievements
Route::get('/badgeTest', 'StudentGameFinishController@openAchievementDebugView')->name('badgeTest')->middleware('auth');

// change lang
Route::get('lang/{lang}', function ($lang){
    Session::put('lang', $lang);
    return redirect()->back();
});

Route::get('/mobileStart', function () {
    return view('mobile.EnterId');
})->name('mobileEnterId');

Route::get('/m', function () {
	return view('mobile.EnterId');
})->name('mobileEnterIdShort');

Route::get('/mobileStart/{sessionId}', 'StudentController@openMobileStartView')->name('mobileStart')->middleware('clearLang'); // force the session language
Route::get('/m/{sessionId}', 'StudentController@openMobileStartView')->name('mobileStart')->middleware('clearLang');

Route::get('/projectorScanQR/{sessionId}', 'ProjectorController@projectorScanQR')->name('projectorScanQR')->middleware('auth', 'clearLang');

// Error messages (mainly used by question.js)

Route::get('/Error/Data', function () {
	return view('mobile.Error')->with(['error' => 'Übertragene Daten fehlerhaft!']);
})->name('ErrorData');

Route::get('/Error/Inactive', function () {
	return view('mobile.Error')->with(['error' => 'Die Session ist nicht aktiv!']);
})->name('ErrorInactive');

// Editor

Route::get('/editorQuestion/{sessionId}', 'EditorController@addQuestion')->name('editorQuestion')->middleware('auth');

// Called when the user wants to update a question
Route::get('/editorQuestion/{sessionId}/{question_id}', 'EditorController@editQuestion')->name('editQuestion')->middleware('auth');

Route::get('/importSession', 'EditorController@importSession')->name('import')->middleware('auth');

Route::get('/editorStatistic/{sessionId}', 'EditorStatisticController@openEditorStatisticsView')->name('editorStatistic')->middleware('auth');

Route::get('/editorQuestionSummary/{sessionId}', 'EditorController@editorQuestionSummary')->name('editorQuestionSummary')->middleware('auth');

Route::get('/deleteQuestion/{sessionId}/{question_id}', 'EditorController@deleteQuestion')->name('deleteQuestion')->middleware('auth');

Route::get('/deleteSession/{sessionId}', 'EditorController@deleteSession')->name('deleteSession')->middleware('auth');

Route::get('/editorHome', 'EditorController@editorHome')->name('editorHome')->middleware('auth');

Route::get('/editorGameStartup/{sessionId}', 'EditorController@editorGameStartup')->name('editorGameStartup')->middleware('auth');

Route::get('/getGraphData/{sessionId}', 'EditorStatisticController@getGraphData')->middleware('auth');

Route::get('/exportStatistic/{sessionId}', 'EditorStatisticController@exportStatistic')->middleware('auth');

Route::get('/exportStatisticUser/{sessionId}', 'EditorStatisticController@exportStatisticUser')->middleware('auth');

Route::get('/exportStatisticCum/{sessionName}', 'EditorStatisticController@exportStatisticCum')->middleware('auth');

Route::get('/exportStatisticUserCum/{sessionName}', 'EditorStatisticController@exportStatisticUserCum')->middleware('auth');

Route::get('/editorSessionSetup', function () {
    return view('editor.SessionSetup');
})->name('editorSessionSetup')->middleware('auth');

Route::get('/editorGameSettings/{sessionId}', 'EditorController@getEditorGameSettings')->name('editorGameSettings')->middleware('auth');

Route::get('/editorCopySession/{sessionId}', 'EditorController@copySession')->name('editorCopySession')->middleware('auth');

Route::get('/shareSession/{sessionId}', 'EditorController@shareSession')->name('shareSession')->middleware('auth');

// All post routes to save data from the editor
Route::post('/editorQuestion/{sessionId}', 'EditorController@editorQuestion')->name('editorQuestion')->middleware('auth');

Route::post('/editorSessionSetup', 'EditorController@editorSessionSetup')->name('editorSessionSetup')->middleware('auth');

Route::post('/editorGameSettings/{sessionId}', 'EditorController@editorGameSettings')->name('editorGameSettings')->middleware('auth');

// called when an updated question should be saved
Route::post('/updateQuestion/{sessionId}/{questionId}', 'EditorController@updateQuestion')->name('updateQuestion')->middleware('auth');

// BOSS

Route::get('/game/{sessionId}', 'ProjectorController@startGame')->name('startGame')->middleware('auth');

Route::get('/getBossData/{sessionId}', 'ProjectorController@getBossData')->name('getBossData'); // Do not need auth, everyone can see this data

Route::get('/getTeamsData/{sessionId}', 'ProjectorController@getTeamsData')->name('getTeamsData'); // Do not need auth, everyone can see this data

// QUESTION

Route::get('/question', 'QuestionController@openQuestionView')->name('question');

Route::get('/sendAnswers', 'QuestionController@sendAnswers')->name('sendAnswers');

Route::get('/checkGameStatus', 'QuestionController@checkGameStatus')->name('checkGameStatus');

Route::get('/leaveQuestionPage', 'QuestionController@leaveQuestionPage')->name('leaveQuestionPage');


// GAME FINISHED, Student Side

Route::get('/gameFinish', 'StudentGameFinishController@openGameFinishView')->name('gameFinish');

Route::get('/saveViewTime', 'StudentGameFinishController@saveViewTime')->name('saveViewTime');

Route::get('/deleteStudent', 'StudentGameFinishController@deleteStudent')->name('deleteStudent');

//used by question.js and question controller
Route::get('/gameOver', 'StudentGameFinishController@openGameOverView')->name('openGameOverView');

// STUDENT

Route::get('/isSessionActive', 'StudentController@isSessionActive')->name('isSessionActive');

Route::get('/setStudentType', 'StudentController@setStudentType')->name('setStudentType');

// Projector, after Game is finished

Route::get('/projector/{sessionId}', 'ProjectorGameFinishController@openProjectorView')->name('projector')->middleware('auth');

Route::get('/stopGame/{sessionId}', 'ProjectorController@stopGame')->name('stopGame')->middleware('auth');

// Info page

Route::get('/info', function () {
	return view('info');
})->name('info');
