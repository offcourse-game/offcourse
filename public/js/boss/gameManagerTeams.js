/* ================== GAME MANAGER TEAMS  ================== */

class GameManagerTeams extends GameManager {
    /* variables:
    {int} _pointsBlue             (points for blue team)
    {int} _pointsRed              (points for red team)
    {int} _studentCountBlue       (max count of students in blue team in beginning)
    {int} _studentCountRed        (max count of students in red team in beginning)
    {int} _studentCountLeftBlue   (total number of students in blue team alive in game)
    {int} _studentCountLeftRed    (total number of students in red team alive in game)

    {int} _endTimerDelay      (delay when game is over to switch view)
     */

    constructor() {
        super();

        this._endTimerDelay = 3000;
        this.initGame();
    }

    /**
     * initializes game, sets up graphics etc
     *
     * @return {void}
     */
    initGame() {}

    /**
     * updates variables to data received by ajax call in main
     * 
     * @param {int} pointsBlue              (points for blue team)
     * @param {int} pointsRed               (points for red team)
     * @param {int} studentCountBlue        (max count of students in blue team in beginning)
     * @param {int} studentCountRed         (max count of students in red team in beginning)
     * @param {int} studentCountLeftBlue    (total number of students in blue team alive in game)
     * @param {int} studentCountLeftRed     (total number of students in red team alive in game)
     */
    updateVariables(pointsBlue, pointsRed, studentCountBlue, studentCountRed, studentCountLeftBlue, studentCountLeftRed) {
        this._pointsBlue = pointsBlue;
        this._pointsRed = pointsRed;
        this._studentCountBlue = studentCountBlue;
        this._studentCountRed = studentCountRed;
        this._studentCountLeftBlue = studentCountLeftBlue;
        this._studentCountLeftRed = studentCountLeftRed
    }

    updateGame() {}

    /**
     * refreshes all information displayed in HUD
     *
     * @return {void}
     */
    updateUI() {
        let healthBarMaxWidth = 890;
        //set team score to blade
        $('#teamScoreBlue').html(Math.round(this._pointsBlue));
        $('#teamScoreRed').html(Math.round(this._pointsRed));

        let healthBarBlue = document.getElementById("healthBarBlue");
        let healthBarRed = document.getElementById("healthBarRed");

        if(this._pointsBlue + this._pointsRed != 0){
            let relativeWidthBlue = healthBarMaxWidth * (this._pointsBlue / (this._pointsBlue + this._pointsRed));
            let relativeWidthRed = healthBarMaxWidth * (this._pointsRed / (this._pointsBlue + this._pointsRed));
            healthBarBlue.style.width = String(relativeWidthBlue + "px");
            healthBarRed.style.width = String(relativeWidthRed + "px");
        }else{
            healthBarBlue.style.width = String((healthBarMaxWidth * 0.5) + "px");
            healthBarRed.style.width = String((healthBarMaxWidth * 0.5) + "px");
        }
    }

    /**
     * updates the timer display
     *
     * @param timeLeft {Date}       (how much time is the game still lasting?)
     * @return {void}
     */
    updateTimer(timeLeft) {
        $('#end_time_min').html(timeLeft.getMinutes());
        $('#end_time_sec').html(timeLeft.getSeconds().toString().padStart(2, '0')); // add zero if needed

        if(timeLeft.getTime() < 30000) {
            let timeDisplay = document.getElementById("timeDisplay");
            if(timeDisplay.classList.contains("alertedTimer") == false) {
                timeDisplay.classList.add("alertedTimer");
            }
        }
    }

    /**
     * checks for internal values if game is over, timer is checked in main (game can not end early in teams mode)
     *
     * @return {boolean}
     */
    isEndConditionTrue() {
        return false;
    }

    updateBossHealth() {}
    endGame() {}
}