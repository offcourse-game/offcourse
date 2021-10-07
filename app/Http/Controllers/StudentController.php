<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;
use App\models\GameSession;
use App\models\Student;

/**
 * Joins the students in the game and handles the excreted students.
 */
class StudentController extends Controller{

    /**
     * The students should choose a character.
     *
     * @param  int $sessionId
     * @return \View mobileStart|mobileError
     */
    public function openMobileStartView($sessionId){
        if (! is_numeric($sessionId)){
            Log::notice("Route mobileStart called with no int");
            return view('mobile.Error')->with(['error' => 'Error.no_game']);
        }

        // Cannot play a session that is not existing or is already played
        if(GameSession::isSessionExisting($sessionId) == 0 || GameSession::getEndDateTime($sessionId) != Null){
            Log::notice("Student tried to join game " . $sessionId . ", but it does not exist or is already played");
            return view('mobile.Error')->with(['error' => 'Error.no_game']);
        }
        // can not access if session is already active
        if(GameSession::isSessionActive($sessionId) == 1){
            Log::notice("Student tried to join game " . $sessionId . ", but it already started");
            return view('mobile.Error')->with(['error' => 'Error.game_started']);
        }

        $gameModeType = GameSession::getSessionGameModeType($sessionId);
        $studentType = 1;
        if($gameModeType == 'teams'){
            $studentType = rand(1, 2);
        }

        if(session('studentId') == Null){
            $studentId = Student::createUser(1, $sessionId);
            session(['sessionId' => $sessionId]); // Save this in the laravel session
            session(['studentId' => $studentId]);
        }else if(session('sessionId') != $sessionId){

            /**************************************************************************************************
                * This is the case, when the student has played before and joins a new game.                     *
                *                                                                                                *
                * If a student whats to join two different game-session at the same time,                        *
                * it is not possible as we will get confused with the variable $sessionId in the session storage.*
                * There for we just kick the player out and create a new one for the newer game,                 *
                * if the old game is not active!                                                                 *
                **************************************************************************************************/
            if (GameSession::isSessionActive(session('sessionId'))){
                Log::notice("Student tried to join game " . $sessionId . ", but is already playing in game ". session('sessionId'));
                return view('mobile.Error')->with(['error' => 'Error.other_game']);
            }else{
                $studentId = Student::createUser($studentType, $sessionId);
                session(['sessionId' => $sessionId]); // Save this in the laravel session
                session(['studentId' => $studentId]);

            }
        }

        return view('mobile.Start')->with(
            [   'sessionId' => $sessionId,
                'gameModeType' => GameSession::getSessionGameModeType($sessionId),
                'currentSessionSettings' => GameSession::getCurrentSessionSettings($sessionId)[0],
            ]);
    }

    /**
     * Check if a session is active.
     *
     * mobileStart view uses this function to join the players.
     *
     * @return int boolean 1/0
     */
    public function isSessionActive(){
        $sessionId = session('sessionId');
        return GameSession::isSessionActive($sessionId);
    }

    /**
     * Sets the student type via ajax call.
     *
     * Needs an Illuminate\Http\Request "studentType" int: 1 = defender and 2 = attacker
     *
     * @return void / string
     */
    public function setStudentType(Request $request){
        $studentType = $request->input("studentType");

        $sessionId = session('sessionId');
        $studentId = session('studentId');

        // If we dont know the sessionId and studentId, we can not set a user type
        if (! is_numeric($sessionId) || ! is_numeric($studentId) || ! is_numeric($studentType)){
            Log::notice("SetStudentType called, but no id or wrong input: sessionId: " . $sessionId . ", studentId: " .
                        $studentId . ", studentType: " . $studentType);
            return "Error, no id or wrong studentType!";
        }

        // check if the session has not started
        if (GameSession::isSessionPlayable($sessionId) == 0){
            Log::notice("Student tried to set the studentType, after the game was stared. StudentId: " . $studentId);
            return "Dont cheat!";
        }

        Student::setType($studentType, $studentId, $sessionId);
    }
}
