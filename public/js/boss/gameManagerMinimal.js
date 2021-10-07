/* ================== GAME MANAGER MINIMAL ================== */

class GameManagerMinimal extends GameManager {
    /* variables:
    {boolean} _showStory        (hp bar will be still displayed)
    {int} _bossLife             (hp of boss)
    {int} _bossLifeStartTotal   (max hp of boss at beginning)
    {int} _lastUpdateBossLife   (used to determine change of bosslife in each update)
    {int} _studentsLeft         (total number of students in game)
    {int} _studentsStartTotal   (max count of students in beginning)
    {int} _endTimerDelay        (delay when game is over to switch view)
    */

    constructor(showStory) {
        super();

        this._showStory = showStory;
        this._bossLife = -1;
        this._bossLifeStartTotal = -1;
        this._studentsLeft = -1;
        this._bossLifeStartTotal = -1;
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
     * @param {int} bossLife            (current boss life)
     * @param {int} bossLifeStartTotal  (boss life at start / max boss life)
     * @param {int} studentsStartTotal  (student count at start of game)
     * @param {int} studentsLeftGray    (amount of students alive in gray)
     * @param {int} studentsLeftGold    (amount of students alive in gold)
     * @return {void}
     */
    updateVariables(bossLife, bossLifeStartTotal, studentsStartTotal, studentsLeftGray, studentsLeftGold) {
        this._lastUpdateBossLife = this._bossLife;
        this._bossLife = bossLife;
        this._bossLifeStartTotal = bossLifeStartTotal;

        this._studentsLeft = studentsLeftGray + studentsLeftGold;
        this._studentsStartTotal = studentsStartTotal;
    }

    /**
     * main game loop, organizes updates of characters, hud etc
     * @return {void}
     */
    updateGame() {
        if (this._bossLife < this._lastUpdateBossLife) {
            this.updateBossHealth();
        }
    }

    /**
     * refreshes all information displayed in HUD
     *
     * @return {void}
     */
    updateUI() {
        if(this._showStory == false) {
            return;
        }

        //set bosslife, studentsLeft and students number to blade
        $('#bossLife').html(this._bossLife);
        $('#bossLifeTotal').html(this._bossLifeStartTotal);
        $('#students').html(this._studentsStartTotal);
        $('#studentsLeft').html(this._studentsLeft);

        let characterHealthbar = document.getElementById("healthBarCharacter");
        characterHealthbar.style.width = String((this._studentsLeft / this._studentsStartTotal * 319) + "px");
        let characterHealthbarDelayed = document.getElementById("healthBarCharacterDelayed");
        characterHealthbarDelayed.style.width = String((this._studentsLeft / this._studentsStartTotal * 319) + "px");
    }

    /**
     * resizes boss and refreshes HUD for boss health
     *
     * @return {void}
     */
    updateBossHealth() {
        if(this._showStory == false) {
            return;
        }

        let bossHealthbar = document.getElementById("healthBarBoss");
        bossHealthbar.style.width = String((this._bossLife / this._bossLifeStartTotal * 319) + "px");
        let bossHealthbarDelayed = document.getElementById("healthBarBossDelayed");
        bossHealthbarDelayed.style.width = String((this._bossLife / this._bossLifeStartTotal * 319) + "px");
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
     * checks for internal values if game is over, timer is checked in main
     *
     * @return {boolean}
     */
    isEndConditionTrue() {
        if(this._showStory == false) {
            return false;
        }
        return (gameManager._bossLife <= 0 || gameManager._studentsLeft <= 0);
    }

    endGame() {}
}