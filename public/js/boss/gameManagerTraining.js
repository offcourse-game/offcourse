/* ================== GAME MANAGER TRAINING ================== */

class GameManagerTraining extends GameManager {
    /* variables:
        {Animator} _animator                        (handles animations, etc.)
        {NotificationManager} _notificationManager  (handles notifications)

        {Array of Character} _characterListGray;     (list of all gray characters)
        {Array of Character} _characterListGold;     (list of all gold characters)
        {int} _studentsLeftHoneyBee          (number of active gray students in game alive)
        {int} _studentsLeftBumbleBee          (number of active gold students in game alive)

        {int} _bossLife             (hp of boss)
        {int} _bossLifeStartTotal   (max hp of boss at beginning)
        {int} _lastUpdateBossLife   (used to determine change of bosslife in each update)
        {int} _studentsStartTotal   (max count of students in beginning)
        
        {int} _lastTweenPause       (saves time where tweenmax pause was set)
        {int} _endTimerDelay        (delay when game is over to switch view)
        {Date} _timeLeft            (how much time in ms is left until game ends)
     */

    constructor() {
        super();

        this._animator = new AnimatorTraining();
        this._notificationManager = new NotificationManagerTraining();

        this._characterListHoneyBee = [];
        this._characterListBumbleBee = [];
        this._studentsLeftHoneyBee = -1;
        this._studentsLeftBumbleBee = -1;

        this._bossLife = -1;
        this._bossLifeStartTotal = -1;

        this._lastTweenPause = 0;
        this._endTimerDelay = 7000;
        this._timeLeft = new Date(Infinity);

        this.initGame();
    }

    /**
     * initializes game, creates characters etc
     *
     * @return {void}
     */
    initGame() {
        sunflowerTL.pause();
        this._animator.setupBeautification(12);
    }

    /**
     * updates variables to data received by ajax call in main
     *
     * @param {int} bossLife            (current boss life)
     * @param {int} bossLifeStartTotal  (boss life at start / max boss life)
     * @param {int} studentsStartTotal  (student count at start of game)
     * @param {int} studentsLeftHoneyBee    (amount of students alive of type honeybee)
     * @param {int} studentsLeftBumbleBee  (amount of students alive of type bumblebee)
     * @return {void}
     */
    updateVariables(bossLife, bossLifeStartTotal, studentsStartTotal, studentsLeftHoneyBee, studentsLeftBumbleBee) {
        this._lastUpdateBossLife = this._bossLife;
        this._bossLife = bossLife;
        this._bossLifeStartTotal = bossLifeStartTotal;

        this._studentsLeftHoneyBee = studentsLeftHoneyBee;
        this._studentsLeftBumbleBee = studentsLeftBumbleBee;
        this._studentsStartTotal = studentsStartTotal;
    }

    /**
     * main game loop, organizes updates of characters, hud etc
     * @return {void}
     */
    updateGame() {
        if(this._bossLife < this._lastUpdateBossLife) {
            this.updateBossHealth();
        }

        //enable this for testing to display many characters
        /*this._studentsLeftHoneyBee = 50;
        this._studentsLeftBumbleBee = 50;*/

        this.updateCharacterDisplay(this._characterListHoneyBee, this._studentsLeftHoneyBee, "HoneyBee", 25);
        this.updateCharacterDisplay(this._characterListBumbleBee, this._studentsLeftBumbleBee, "BumbleBee", 25);
    }

    /**
     * refreshes all information displayed in HUD
     *
     * @return {void}
     */
    updateUI() {
        //set bosslife, studentsLeft and students number to blade
        $('#bossLife').html(this._bossLifeStartTotal - this._bossLife);
        $('#bossLifeTotal').html(this._bossLifeStartTotal);
        $('#students').html(this._studentsStartTotal);
        $('#studentsLeft').html(this._studentsLeft);

        // display  notifications
        this._notificationManager.displayNotification();
    }

    /**
     * adds / removes characters if count changed
     * @param characterListType     (list of characters of type (honeyBee / bumbleBee))
     * @param studentsLeftType      (number of students left (honeyBee / bumbleBee))
     * @param characterType         ("HoneyBee" or "BumbleBee")
     * @param spawnLimit            (how many characters should be visible at once?)
     * @return {void}
     */
    updateCharacterDisplay(characterListType, studentsLeftType, characterType, spawnLimit) {
        while(characterListType.length < studentsLeftType && characterListType.length < spawnLimit) {
            let character = new CharacterTraining(characterType, characterListType.length, Math.min(studentsLeftType, spawnLimit), false);
            characterListType.push(character);
        }
    }

    /**
     * updates the timer display
     *
     * @param timeLeft {Date}       (how much time is the game still lasting?)
     * @return {void}
     */
    updateTimer(timeLeft) {
        this._timeLeft = timeLeft;
        $('#end_time_min').html(this._timeLeft.getMinutes());
        $('#end_time_sec').html(this._timeLeft.getSeconds().toString().padStart(2, '0')); // add zero if needed
    }

    /**
     * plays flower grow animation and updates progress bar
     *
     * @return {void}
     */
    updateBossHealth() {
        if(this._bossLife == this._bossLifeStartTotal)
            return;

        sunflowerTL.tweenTo(4.7 * (this._bossLifeStartTotal - this._bossLife) / this._bossLifeStartTotal);
        this.animateBossHealth(this._bossLife);
    }

    /**
     * used for delayed animation for the progress bars (css transition-delay causes issues when updated multiple times within transition-delay time span)
     *
     * @param {int} bossLife            (current boss life when calling function)
     *
     * @return void
     */
    async animateBossHealth(bossLife) {
        let bossHealthbarDelayed = document.getElementById("healthBarBossDelayed");
        bossHealthbarDelayed.style.width = String(((this._bossLifeStartTotal - this._bossLife) / this._bossLifeStartTotal * 404) + "px");

        await sleep(1000);
        let bossHealthbar = document.getElementById("healthBarBoss");
        bossHealthbar.style.width = String(((this._bossLifeStartTotal - this._bossLife) / this._bossLifeStartTotal * 404) + "px");
    }

    /**
     * checks for internal values if game is over, timer is checked in main
     *
     * @return {boolean}
     */
    isEndConditionTrue() {
        return (this._bossLife <= 0)
    }

    /**
     * stops game and triggers end animation, called by button and timer
     *
     * @return {void}
     */
    endGame() {
        if (this._bossLife <= 0) {
            /*sunflowerTL.tweenTo(sunflowerTL.duration());*/
            sunflowerTL.play();
            this._animator.endAnimation(true);
        } else {
            this._animator.endAnimation(false);
        }
    }
}