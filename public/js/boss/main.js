//main script for boss view organizing ajax calls etc

// our global variables
let firstRun = true;
let end; // this is the date the time is up

let gameManager;

window.onload = function (){

     // Get settings from controller via blade
    let gameModeType = $('#gameModeType').data("field-id");
    let isGame  = $('#isGame').data("field-id");
    let showStory = $('#showStory').data("field-id");
    let isTraining = $('#isTraining').data("field-id");
    let isTeams = false;

    console.log(isTraining);

    if(gameModeType == "classic" && showStory == true) {
        gameManager = new GameManagerBossBattle();
    }else if(gameModeType == "training") {
        gameManager = new GameManagerTraining();
    }else if(gameModeType == "teams"){
        gameManager = new GameManagerTeams();
        isTeams = true;
    }else{
        gameManager = new GameManagerMinimal(isGame);
    }

    createTimer();
    doAjaxCall(isTeams); // Do the first call immediately
};

// -------------------------------------- start of logic functions

/**
 * Main function to initialize central update
 *
 * boolean isTeams      use special ajax call for teams
 *
 * @return {void}
 */
function doAjaxCall(isTeams){
    sessionId = $('#sessionId').data("field-id"); // Get sessionId from controller, via blade
    if(isTeams == false){
        doBossAjaxCall(sessionId);
        setInterval(function(){doBossAjaxCall(sessionId);}, 1000);
    }else{
        doTeamsAjaxCall(sessionId);
        setInterval(function(){doTeamsAjaxCall(sessionId);}, 1000);
    }
}

/**
 * Main function to handle central update every second for all game modes except teams 
 *
 * @param int sessionId
 *
 * @return {void}
 */
function doBossAjaxCall(sessionId) {
    $.ajax({
        type: 'get',
        url: '/getBossData/' + sessionId,
        success: function (data){
            if (firstRun){
                nowStart = new Date();
                end = nowStart.setSeconds(nowStart.getSeconds() + data['gameTime'] + 3); // add delay

                console.log("firstRunReached");
                firstRun = false;
            }

            bossLife = data['bossLife'] >= 0 ? data['bossLife'] : 0;
            bossLifeTotal = data['bossLifeStart'];
            studentsStartTotal = data['studentCount'];
            studentsLeftGray = data['studentCountLeftGray'];
            studentsLeftGold = data['studentCountLeftGold'];

            gameManager.updateVariables(bossLife, bossLifeTotal, studentsStartTotal, studentsLeftGray, studentsLeftGold);
            gameManager.updateGame();
            gameManager.updateUI();
        },
        error: function(data){
            var errors = data.responseJSON;
            console.log(errors);
        }
    });
}

/**
 * Main function to handle central update every second for teams game mode
 *
 * @param int sessionId
 *
 * @return {void}
 */
function doTeamsAjaxCall(sessionId) {
    $.ajax({
        type: 'get',
        url: '/getTeamsData/' + sessionId,
        success: function (data){
            if (firstRun){
                nowStart = new Date();
                end = nowStart.setSeconds(nowStart.getSeconds() + data['gameTime'] + 3); // add delay

                console.log("firstRunReached");
                firstRun = false;
            }

            pointsBlue = data['pointsBlue'];
            pointsRed = data['pointsRed'];

            studentCountBlue = data['studentCountBlue'];
            studentCountRed = data['studentCountRed'];

            studentCountLeftBlue = data['studentCountLeftBlue'];
            studentCountLeftRed = data['studentCountLeftRed'];

            gameManager.updateVariables(pointsBlue, pointsRed, studentCountBlue, studentCountRed, studentCountLeftBlue, studentCountLeftRed);
            gameManager.updateGame();
            gameManager.updateUI();
        },
        error: function(data){
            var errors = data.responseJSON;
            console.log(errors);
        }
    });
}

/**
 * Controls our display of the timer in HUD.
 *
 * DateTime end should be already given.
 *
 * @return {void}
 */
function createTimer(){
    let timer = setInterval(function(){

        if (end != undefined){ // only start to calculate the time if we know our end date
            let now = new Date();
            let time_left = new Date(end - now);

            gameManager.updateTimer(time_left);

            if (now >= end || gameManager.isEndConditionTrue()) {
                gameManager.updateTimer(new Date(0));

                clearInterval(timer);
                stopGame(gameManager._endTimerDelay);
            }
        }
    }, 250); // Update our Timer every 250 ms, this should be enough to have a smooth clock
}

/**
 * Ajax call to stop the game.
 *
 * Will be called by the "stop game" button and by createTimer.
 * The "Spiel abbrechen"-Button calls with 0 so we directly end.
 * The timer uses the _endTimerDelay from the gameManager, since isGame needs a different delay if it is animated or not
 *
 * @param  {Number} [timeout=1000] in ms
 * @return {void}
 */
function stopGame(timeout=1000){
    // stop session
    $.ajax({
        type: 'get',
        url: '/stopGame/' + sessionId,
        success: function (data){
            console.log(data);
        }
    });

    gameManager.endGame();

    //waits n seconds and loads new site afterwards; time used for animating boss when it dies
    setTimeout(function (){
        window.location.href = "/projector/" + sessionId;
    }, timeout);
}

/**
 * Function to have an async time out.
 *
 * @param  {int} ms
 * @return {void} returns after the timout given by parameter
 */
function sleep(ms){
    return new Promise(resolve => setTimeout(resolve, ms));
}
