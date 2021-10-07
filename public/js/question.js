let answer_array = [0,0,0,0];      // array to evaluate the given answers
let localTimer;                    // variable for the localTimer
let timeleft = 30;                 // time value, 30 seconds for each question
let answersWereSend = 0;           // variable that saves whether the answers have been send to Server already
let gameModeType = document.getElementById("dataStorage").getAttribute("gameModeType");


//-------------------------- first the standard functions

/**
 * OnLoad function, runs one time in the beginning when window is load.
 * Here we set interval functions that runs the whole time
 *
 * @return {void}
 */
window.onload = function(){

    // Run local question time countdown, every second
    localTimer = setInterval(localTimerHandler, 1000);
    initCircleTimer();

    /**
     * Check if the game is over every second.
     *
     * @return {void}
     */
    function checkGameStatus(){
        $.ajax({
            type: 'get',
            url: '/checkGameStatus',
            success: function (response){
                if(response['gameFinished'] == 1){

                    console.log("Game Finished");
                    answersWereSend = 1;
                    window.location.href = "/gameFinish";
                }

                refreshBossFooter(response['bossHp'], response['characterCountGold'] + response['characterCountGray']);

                setTimeout(checkGameStatus, 1000); // Wait 1 Seconds
            }
        });
    }
    setTimeout(checkGameStatus, 0); // Make the first call immediately
};

/**
 * If student refresh or leaves the page, the number of lives need to be decrement and the question has to be deleted.
 * Using syncron call therefore first the ajax call will be proceeded and then the page will unload.
 *
 * @return {null}
 */
window.onbeforeunload = function(){
    let questionId = document.getElementById("dataStorage").getAttribute("questionId");
    if(answersWereSend == 0) {
        $.ajax({
            type: 'get',
            async: false,
            data: {questionId: questionId},
            url: '/leaveQuestionPage',
            success: function (res) {
                console.log(res);
            }
        });
    }
    return null;
};

//----------------------------------------------------------------functions when the user interacts with the game

/**
 * Function for clicking on answers in question.blade, save the actual selected answers.
 *
 * @param  {int} id
 * @return {void}
 */
function check(id){
    if(document.getElementById(id).checked) {
        answer_array[id] = 1;
    } else {
        answer_array[id] = 0;
    }

    let blocked = true;
    for(let i = 0; i < 4; i++) {
        if(document.getElementById(i).checked)
            blocked = false;
    }

    document.getElementById("sendAnswers").disabled = blocked;

    console.log(answer_array);
}

/**
 * Execute when next button was clicked, send data to db and open new Question.
 *
 * @param  {int} questionId
 * @return {void}
 */
function sendAnswers(questionId) {

    let redirectInfo;
    let currentAnswerInfo;
    let playerInfo;
    let nextAnswerInfo;

    if (answersWereSend == 1){
        console.log("Answers already send");
        return;
    }
    //disable the submit button
    document.getElementById("sendAnswers").disabled = true;

    //disable all checkboxes
    for (i = 0; i < 4; i++)
        document.getElementById(i).disabled = true;

    clearInterval(localTimer);    //immediately stop timer (before ajax transmission)

    $.ajax({
        type: 'get',
        data: {questionId: questionId, answers: answer_array},
        url: '/sendAnswers',
        success: function (result) {

            console.log("sendAnswers, Ajax Response:" + result);
            result = JSON.parse(result);

            answersWereSend = 1;     // save that the answers were send already

            if(result == null || result.length == 0) {
                window.location.href = "/Error/Data";
            }
            if(result.length > 0 && result[0] != null) {
                redirectInfo = result[0];       //[string FLAG_REDIRECT", <string redirect>, <int/string data>

                redirectFlag = redirectInfo[0];
                redirectLocation = redirectInfo[1];
            }
            if(result.length > 1 && result[1] != null) {
                currentAnswerInfo = result[1];  //<[int (boolean 1/0) correct, \Collection "correctAnswers" [int answer_id, string answer_text, int (boolean 1/0) correct]]>

                correct = currentAnswerInfo[0];
                correctAnswers = currentAnswerInfo[1];
            }
            if(result.length > 2 && result[2] != null) {
                playerInfo = result[2];         //<[int actualStreak, int studentLife, int studentId]>

                actualStreak = playerInfo[0];   //length of actual Streak of correct answers
                studentLife = playerInfo[1];    //hp of student
                studentId = playerInfo[2];      //id of student
            }
            if(result.length > 3 && result[3] != null) {
                nextAnswerInfo = result[3];     //<[string questionText, int questionId, \Collection "nextAnswers" [int answer_id, string answer_text], string imagePath]>

                nextQuestionText = nextAnswerInfo[0]; //text of next question
                nextQuestionId = nextAnswerInfo[1];   //id of next question
                nextAnswers = nextAnswerInfo[2];      //next answer array text
                nextImagePath = nextAnswerInfo[3];    //url of next image
                nextNumberCorrectAnswers = nextAnswerInfo[4];
            }

            if(redirectFlag == "FLAG_REDIRECT") {
                switch (redirectLocation) {
                    case "Error":
                        //data error or session inactive
                        window.location.href = redirectInfo[2];
                        break;
                    case "GameFinish":

                        // visual effect whether the single answers were correct & display achievements / heart animation
                        flashCorrectAnswers(correctAnswers, correct);
                        if (gameModeType != 'noGame'){
                            displayRunTimeAchievements(actualStreak, correct);
                        }

                        if (correct == false) {
                            if (gameModeType == 'classic') refreshHeartDisplay(studentLife);
                        }

                        setTimeout(function () {
                            window.location.href = '/gameFinish';
                        }, 2500);
                        break;
                    case "GameOver":

                        // visual effect whether the single answers were correct & display achievements / heart animation
                        flashCorrectAnswers(correctAnswers, correct);
                        if (gameModeType != 'noGame'){
                            displayRunTimeAchievements(actualStreak, correct);
                        }
                        if (correct == false) {
                            if (gameModeType == 'classic') refreshHeartDisplay(studentLife);
                        }

                        setTimeout(function () {
                            window.location.href = '/gameOver';
                        }, 2500);
                        break;
                }
            } else if(redirectFlag == "FLAG_ANSWER") {
                // visual effect whether the single answers were correct & display achievements / heart animation
                flashCorrectAnswers(correctAnswers, correct);
                if (gameModeType != 'noGame'){
                    displayRunTimeAchievements(actualStreak, correct);
                }
                if (correct == false) {
                    if (gameModeType == 'classic') refreshHeartDisplay(studentLife);
                }

                //next questions in 2.5 seconds
                setTimeout(function () {
                    /*window.location.href = '/question';*/
                    handleNextQuestion(studentLife, nextQuestionText, nextQuestionId, nextAnswers, studentId, nextImagePath, nextNumberCorrectAnswers);
                    unflashAnswers();
                }, 2500);
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            console.log("Error in question.js, sendAnswers(), ajax call" + xhr.status + " " + thrownError);
        }
    });
}

/**
 * Loads new questions, resets timers and replaces the text in answer / question boxes
 *
 * @param Array [ {int} studentLife, {string} questionText, {int} questionId, {collection} answers, {int} studentId, {string} imagePath, {int} numberCorrectAnswers]
 */
function handleNextQuestion(studentLife, questionText, questionId, answers, studentId, imagePath, numberCorrectAnswers) {

    console.log("handleNextQuestion: " + studentLife + " " + questionText + " " + questionId + " " + imagePath + numberCorrectAnswers);

    answersWereSend = 0;
    answer_array = [0,0,0,0];
    timeleft = 30;

    //set attribute of "submit" button to new question ID
    document.getElementById("sendAnswers").setAttribute("onclick", "sendAnswers(" + questionId + ", " + studentId + ")");

    //set attribute of data tag
    document.getElementById("dataStorage").setAttribute("questionId", questionId);

    // remove visual effect
    unflashAnswers();

    //reset timer
    updateTimer(1);

    // in case we added a notification class remove it or set display none
    let notif = document.getElementById('smallNotificationCornerPlaceholder');
    let notifFast = document.getElementById('fastQuestionsSpanerText');
    let notifFaster =document.getElementById('fasterQuestionsSpanerText');
    let notifFastest =document.getElementById('fastestQuestionsSpanerText');
    let notifStreak = document.getElementById('actualStreakSpaner');
    let notifStreakText = document.getElementById('actualStreakSpanerText');

    if (notif != null && notifFast != null && notifFaster != null && notifFastest != null && notifStreak != null && notifStreakText != null) {
        notif.classList.remove('smallNotificationCorner');
        notifFast.style.display = 'none';
        notifFaster.style.display = 'none';
        notifFastest.style.display = 'none';
        notifStreak.style.display = 'none';
        notifStreakText.style.display = 'none';
    }

    //restarts timer
    localTimer = setInterval(localTimerHandler, 1000);

    refreshText(questionText, answers);

    //remove an existing image first
    try{
        document.getElementById("questionImage").remove();
    }catch(e){
        console.log("No image to remove: " + e);
    }

    // readd the image if we need to
    if(imagePath){
        let imagedom = document.createElement("img");
        document.getElementById("imagespanhere").appendChild(imagedom);
        imagedom.id = 'questionImage';
        imagedom.classList.add('questionImage');
        imagedom.src = "storage/" + imagePath;
    }

    //update numberCorrectAnswers
    if (numberCorrectAnswers != -1){
        let numberCorrectAnswersSpan = document.getElementById('numberCorrectAnswers');
        if (typeof numberCorrectAnswersSpan !== "undefined" && numberCorrectAnswersSpan !== null) {
            document.getElementById('numberCorrectAnswers').innerText = numberCorrectAnswers;
        }
    }

    //reenable all checkboxes
    for (i = 0; i < 4; i++)
        document.getElementById(i).disabled = false;
}

//-------------------------------- Help functions

/**
 * manages local time and changes display
 *
 */
function localTimerHandler(){
    timeleft--;
    document.getElementById("countdown").textContent = timeleft;

    updateTimer(timeleft / 30);

    if (timeleft <= 0) {

        // Delete the time
        document.getElementById("countdown").textContent = "0";

        // Stop this counter
        clearInterval(localTimer);

        //reset visualization
        updateTimer(0);

        // get vars from a dummy div in our question blade and open sendAnswers function
        sendAnswers(document.getElementById("dataStorage").getAttribute("questionId"));
    }
}

/**
 * manages remaining time visualization (circle)
 */
function updateTimer(ratio) {
    let circleTimer = document.getElementById('circleTimerMovable');
    let circumference = circleTimer.r.baseVal.value * 2 * Math.PI;

    circleTimer.style.strokeDashoffset = circumference - ratio * circumference;

    if(ratio <= 0.17) {
        circleTimer.style.stroke = "rgb(175, 15, 74)";
    } else if(ratio <= 0.4) {
        circleTimer.style.stroke = "rgb(252, 165, 46)";
    } else {
        circleTimer.style.stroke = "rgb(30, 160, 14)";
    }
}

/**
 * inits circle timer
 */
function initCircleTimer() {
    let circleTimer = document.getElementById('circleTimerMovable');
    let circumference = circleTimer.r.baseVal.value * 2 * Math.PI;

    circleTimer.style.strokeDasharray = `${circumference} ${circumference}`;
    circleTimer.style.strokeDashoffset = `${circumference}`;

    updateTimer(1);
}


/**
 * Displays the in game achievements.
 *
 * Save the number of fastQuestions at all locally, because it decrease the sql queries.
 * But we do not send the local data to the DB, so the user cannot manipulate the statistics on the server.
 *
 * @param  {int} actualStreak
 * @return {void}
 */
function displayRunTimeAchievements(actualStreak, correct){
    console.log("the actual Streak send by the server is: " + actualStreak);

    //display spears for streaks
    if(actualStreak >= 3) {
        let streakInfoOuter = document.getElementById('streakInfoOuter');
        let streakInfo = document.getElementById('streakInfo');

        streakInfoOuter.style.visibility = "visible";
        streakInfo.innerText = actualStreak + " x";
    } else {
        let streakInfoOuter = document.getElementById('streakInfoOuter');
        streakInfoOuter.style.visibility = "hidden";
    }

    if(correct == true) {
        fastQuestions = localStorage.getItem('fastQuestions');
        if (timeleft >= 18) {
            if (fastQuestions == null || fastQuestions == "NaN" || fastQuestions == "null") {
                localStorage.setItem('fastQuestions', 1);
                console.log("fastQuestions: " + fastQuestions);
            }
            else {
                fastQuestions = parseInt(fastQuestions) + 1;
                localStorage.setItem('fastQuestions', fastQuestions);
                console.log("fastQuestions +1 : " + fastQuestions);
            }
        }

        // Pop Ups while playing if student is good
        // first looking for streaks of correct answers
        if (actualStreak >= 3 && (actualStreak % 2) == 1) {      // beginning at 3, give a notification at every odd-numbered count
            let notif = document.getElementById('smallNotificationCornerPlaceholder');
            let notifStreak = document.getElementById('actualStreakSpaner');
            let notifStreakText = document.getElementById('actualStreakSpanerText');
            if (notif != null && notifStreak != null && notifStreakText != null) {
                notifStreak.innerHTML = actualStreak;
                notifStreak.style.display = 'unset';
                notifStreakText.style.display = 'unset';
                notif.classList.add('smallNotificationCorner');
            }
        }
        // if there is no Streak, look for fastQuestions
        // here we need static notifications, because we need customized texts
        else if (fastQuestions >= 2) {
            let notif = document.getElementById('smallNotificationCornerPlaceholder');

            let notifFast = document.getElementById('fastQuestionsSpanerText');
            let notifFaster =document.getElementById('fasterQuestionsSpanerText');
            let notifFastest =document.getElementById('fastestQuestionsSpanerText');

            if (notif != null) {
                switch (fastQuestions) {
                    case 2:
                            if (notifFast != null){
                                notifFast.style.display = 'unset';
                                notif.classList.add('smallNotificationCorner');
                            }
                        break;
                    case 4:
                            if (notifFaster != null){
                                notifFaster.style.display = 'unset';
                                notif.classList.add('smallNotificationCorner');
                            }
                        break;
                    case 6:
                            if (notifFastest != null){
                                notifFastest.style.display = 'unset';
                                notif.classList.add('smallNotificationCorner');
                            }
                        break;
                }
            }
        }
    }
}

/**
 * Visual effect whether the individual answers were correct.
 *
 * @param  {Array} answers
 * @param  {boolean} allCorrect
 * @return {void}
 */
function flashCorrectAnswers(answers, allCorrect){
    let counter = 0;

    // flash the single answer divs green or red
    answers.forEach(function (item){
        let filler = document.getElementById('animateFiller' + counter);
        if (item['correct']) {
            filler.classList.add('answerLabelAnimateCorrect');
        } else {
            filler.classList.add('answerLabelAnimateWrong');
        }

        filler.style.padding = "0";
        counter++;
    });

    // flash the questionBox green or red
    let questionBox = document.getElementById('questionBox');
    if (allCorrect) {
        questionBox.classList.add('questionBoxAnimateCorrect');
    } else {
        questionBox.classList.add('questionBoxAnimateWrong');
    }
}

/**
 * remove visual effect for answers
 *
 * @return {void}
 */
function unflashAnswers(){
    let counter = 0;

    // remove single answer flash div
    for(let counter = 0; counter < 4; counter ++) {
        let filler = document.getElementById('animateFiller' + counter);
        filler.classList.remove('answerLabelAnimateWrong');
        filler.classList.remove('answerLabelAnimateCorrect');
        filler.style.padding = "0em 0.5em 0em 0.5em";
    }

    // remove questionBox flash div
    let questionBox = document.getElementById('questionBox');
    questionBox.classList.remove('questionBoxAnimateCorrect');
    questionBox.classList.remove('questionBoxAnimateWrong');
}

/**
 * Visual effect to delete heart.
 * @param {int} imageId id of heart to delete (current health points)
 * @return {void}
 */
function refreshHeartDisplay(studentLife){
    let heart = document.getElementById('animatedHeartImage');
    let heartCounter = document.getElementById('heartCounter');

    if(heart != null) {
        heart.classList.add('questionHeartBoxAnimate');
        heart.addEventListener('animationend', function e(ev) {
            this.classList.remove('questionHeartBoxAnimate');
        });
    }

    if(studentLife >= 1) {
        heartCounter.innerText = "x " + studentLife;
    } else {
        heartCounter.innerText = "";

        let backgroundHeart = document.getElementById('animatedBackgroundImage');
        backgroundHeart.style.display = "none";
    }
}

/**
 * refreshes text in footer for displaying boss hp and characterHp
 *
 * @param {float} bossHealth <0-1>
 * @param {float} characterHealth <0-1>
 * @return {void}
 */
function refreshBossFooter(bossHealth, characterHealth) {
    let bossHpDisplay = document.getElementById('bossHpDisplay');
    let characterHpDisplay = document.getElementById('characterHpDisplay');

    bossHpDisplay.innerText = Math.round(bossHealth * 100) + "%";
    characterHpDisplay.innerText = "x "  + Math.round(characterHealth);
}

/**
 * replaces text in textboxes when (re-) loading new questions
 *
 * @param  {string} questionText
 * @param {Array} answers
 * @return {void}
 */
function refreshText(questionText, answers) {
    document.getElementById('questionText').innerHTML = questionText;

    let counter = 0;
    answers.forEach(function (item){
        //set text in text box
        let answerLabelAnimateFiller = document.getElementById('animateFiller' + counter);
        answerLabelAnimateFiller.innerHTML = "" + item['answer_text'];

        //restart rotate in animation
        let answerLabel = document.getElementById('answerLabel' + counter);
        answerLabel.style.animation = 'none';
        answerLabel.offsetHeight = answerLabel.offsetHeight; /* trigger reflow and restart anim*/
        answerLabel.style.animation = null; /* inherit anim again */

        //reset checkbox state
        let checkbox = document.getElementById(counter);
        checkbox.checked = false;

        counter++;
    });

    let sendAnswersButton = document.getElementById("sendAnswers");
    sendAnswersButton.style.animation = 'none';
    sendAnswersButton.offsetHeight = sendAnswersButton.offsetHeight; /* trigger reflow and restart anim*/
    sendAnswersButton.style.animation = null; /* inherit anim again */

    document.getElementById("countdown").textContent = 30;
}
