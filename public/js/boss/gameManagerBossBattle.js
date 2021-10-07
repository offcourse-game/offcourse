/* ================== GAME MANAGER BOSS BATTLE ================== */

class GameManagerBossBattle extends GameManager {
    /* variables:
        {Animator} _animator            (handles fire, smoke, notifications, etc.)
        {NotificationManager} _notificationManager  (handles notifications)

        {Array of Character} _characterListGray;     (list of all gray characters)
        {Array of Character} _characterListGold;     (list of all gold characters)
        {int} _studentsLeftGray          (number of active gray students in game alive)
        {int} _studentsLeftGold          (number of active gold students in game alive)

        {int} _bossLife             (hp of boss)
        {int} _bossLifeStartTotal   (max hp of boss at beginning)
        {int} _lastUpdateBossLife   (used to determine change of bosslife in each update)
        {int} _studentsLeft         (total number of students in game)
        {int} _studentsStartTotal   (max count of students in beginning)

        {int} _endTimerDelay        (delay when game is over to switch view)
     */

    constructor() {
        super();

        this._animator = new AnimatorBossBattle();
        this._notificationManager = new NotificationManagerBossBattle();

        this._characterListGray = [];
        this._characterListGold = [];
        this._studentsDisplayedGray = 0;
        this._studentsDisplayedGold = 0;

        this._bossLife = -1;
        this._bossLifeStartTotal = -1;
        this._studentsLeft = -1;
        this._studentsLeftGray = -1;
        this._studentsLeftGold = -1;

        this._endTimerDelay = 10000;

        this.initGame();
    }

    /**
     * initializes game, creates characters etc
     *
     * @return {void}
     */
    initGame() {
        this._animator.setupSmokeParticles(10);
        this._animator.setupFireParticles(15);
        this._animator.setupBeautification(12);
        this._animator.setupBoss();
    }

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

        this._studentsLeftGray = studentsLeftGray;
        this._studentsLeftGold = studentsLeftGold;
        this._studentsLeft = studentsLeftGray + studentsLeftGold;
        this._studentsStartTotal = studentsStartTotal;
    }

    /**
     * main game loop, organizes updates of characters, hud etc
     * @return {void}
     */
    updateGame() {
        //update boss size and spawn spear if boss life changed
        if(this._bossLife < this._lastUpdateBossLife) {
            this.updateBossHealth();
            this.createSpearThrows();
        }

        //update character count
        this.updateCharacterDisplay(this._characterListGray, this._studentsLeftGray, "Gray", 25);
        this.updateCharacterDisplay(this._characterListGold, this._studentsLeftGold, "Gold", 25);
    }

    /**
     * refreshes all information displayed in HUD
     *
     * @return {void}
     */
    updateUI() {
        //set bosslife, studentsLeft and students number to blade
        $('#bossLife').html(this._bossLife);
        $('#bossLifeTotal').html(this._bossLifeStartTotal);
        $('#students').html(this._studentsStartTotal);
        $('#studentsLeft').html(this._studentsLeft);

        let characterHealthbar = document.getElementById("healthBarCharacter");
        characterHealthbar.style.width = String((this._studentsLeft / this._studentsStartTotal * 319) + "px");
        let characterHealthbarDelayed = document.getElementById("healthBarCharacterDelayed");
        characterHealthbarDelayed.style.width = String((this._studentsLeft / this._studentsStartTotal * 319) + "px");

        // display  notifications
        this._notificationManager.displayNotification();
    }

    /**
     * resizes boss and refreshes HUD for boss health
     *
     * @return {void}
     */
    updateBossHealth() {
        this.animateBossHealth(this._bossLife);

        //when smaller then 1/3 of the size particles glitch below the grass
        if(Math.sqrt(this._bossLife / this._bossLifeStartTotal) < 0.35){
            this._animator.deleteAllFireParticles();
        }
    }

    /**
     * used for delayed animation for the health bars (css transition-delay causes issues when updated multiple times within transition-delay time span)
     *
     * @param {int} bossLife            (current boss life when calling function)
     *
     * @return void
     */
    async animateBossHealth(bossLife) {
        await sleep(1500);
        this._animator.addBossFlash();

        await sleep(500);
        //relative height change of boss
        let bossImg = document.getElementById("bossImg");
        bossImg.style.height = String(Math.round(506 * Math.sqrt(bossLife / this._bossLifeStartTotal) + 50) ) + "px";
        bossImg.style.width = String(Math.round(445 * Math.sqrt(bossLife / this._bossLifeStartTotal) + 44) ) + "px";
        //boss health bar width change
        let bossHealthbar = document.getElementById("healthBarBoss");
        bossHealthbar.style.width = String((bossLife / this._bossLifeStartTotal * 319) + "px");

        await sleep(1000);
        //boss health bar delayed background change
        let bossHealthbarDelayed = document.getElementById("healthBarBossDelayed");
        bossHealthbarDelayed.style.width = String((bossLife / this._bossLifeStartTotal * 319) + "px");
    }

    /**
     * throws a spear with even random distribution of all characters
     *
     * @return {void}
     */
    createSpearThrows() {
        if(this._characterListGray.length > 0 && this._characterListGold.length > 0) {
            if(Math.random() * (this._characterListGray.length + this._characterListGold.length) < this._characterListGray.length) {
                this._characterListGray[Math.floor(Math.random() * this._characterListGray.length)].createSpear();
            } else {
                this._characterListGold[Math.floor(Math.random() * this._characterListGold.length)].createSpear();
            }
        } else if(this._characterListGray.length > 0) {
            this._characterListGray[Math.floor(Math.random() * this._characterListGray.length)].createSpear();
        } else if(this._characterListGold.length > 0) {
            this._characterListGold[Math.floor(Math.random() * this._characterListGold.length)].createSpear();
        }
    }

    /**
     * adds / removes characters if count changed
     * @param characterListType     (list of characters of type (gray / gold))
     * @param studentsLeftType      (number of students left (gray / gold))
     * @param characterType         ("Gray" or "Gold")
     * @param spawnLimit            (how many characters should be visible at once?)
     * @return {void}
     */
    updateCharacterDisplay(characterListType, studentsLeftType, characterType, spawnLimit) {
        while(characterListType.length != studentsLeftType) {
            if (characterListType.length < studentsLeftType) {
                let character = new CharacterBossBattle(characterType, characterListType.length, Math.min(studentsLeftType, spawnLimit), characterListType.length >= spawnLimit);
                characterListType.push(character);
            } else {
                //wake up next character, so number of visible characters stays the same if not all are displayed
                if(characterListType.length > 1 && characterListType[characterListType.length - 1]._isHidden == false) {
                    for (let i = 0; i < characterListType.length - 1; i++) {
                        if (characterListType[i]._isHidden == true) {
                            characterListType[i].activateCharacterImages(false);
                            break;
                        }
                    }
                }

                if(characterListType.length > 0) {
                    characterListType[characterListType.length - 1].initiateDeath();
                    characterListType.pop();
                }
            }
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
    }

    /**
     * checks for internal values if game is over, timer is checked in main
     *
     * @return {boolean}
     */
    isEndConditionTrue() {
        return (gameManager._bossLife <= 0 || gameManager._studentsLeft <= 0);
    }

    /**
     * stops game and triggers end animation, called by button and timer
     *
     * @return {void}
     */
    endGame() {
        if (this._bossLife <= 0) {
            this._animator.endAnimation(true);
        } else {
            this._animator.endAnimation(false);
        }
    }
}