/* ================== GAME MANAGER BASE  ================== */

class GameManager {
    //abstract class
    constructor() {
        if (new.target === GameManager) {
            throw new TypeError("Cannot construct Abstract instances directly");
        }
    }

    initGame() {}
    updateVariables(bossLife, bossLifeStartTotal, studentsStartTotal, studentsLeftGray, studentsLeftGold) {}
    updateGame() {}
    updateUI() {}
    updateTimer(timeLeft) {}
    updateBossHealth() {}
    endGame() {}
    isEndConditionTrue() {}
}