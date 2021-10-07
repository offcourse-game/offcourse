/* ================== NOTIFICATION MANAGER BOSS BATTLE ================== */

class NotificationManagerBossBattle extends NotificationManager {

    constructor() {
        super();
    }

    /**
     * creates boss notifications at some intervals (not reliable)
     * @return {void}
     */
    displayNotification() {
        if (gameManager._bossLife / gameManager._bossLifeStartTotal <= 0.75) {
            if (this._notificationsAlreadyDisplayed['threeQuatersLife'] == undefined) {
                console.log("75% life Notification");
                NotificationManager.createPopup(document.getElementById('boss_threeQuatersLife').getAttribute('string'));
                this._notificationsAlreadyDisplayed['threeQuatersLife'] = true;
            }
        }
        if (gameManager._bossLife / gameManager._bossLifeStartTotal <= 0.5) {
            if (this._notificationsAlreadyDisplayed['halfLife'] == undefined) {
                console.log("50% life Notification");
                NotificationManager.createPopup(document.getElementById('boss_halfLife').getAttribute('string'));
                this._notificationsAlreadyDisplayed['halfLife'] = true;
            }
        }
        if (gameManager._bossLife / gameManager._bossLifeStartTotal <= 0.25) {
            if (this._notificationsAlreadyDisplayed['quarterLife'] == undefined) {
                console.log("25% life Notification");
                NotificationManager.createPopup(document.getElementById('boss_quarterLife').getAttribute('string'));
                this._notificationsAlreadyDisplayed['quarterLife'] = true;
            }
        }
        if (gameManager._studentsLeft == 5) {
            if (this._notificationsAlreadyDisplayed['fiveStudents'] == undefined) {
                console.log("5 students Notification");
                NotificationManager.createPopup(document.getElementById('fiveStudents').getAttribute('string'));
                this._notificationsAlreadyDisplayed['fiveStudents'] = true;
            }
        }
        if (gameManager._studentsLeft == 10) {
            if (this._notificationsAlreadyDisplayed['tenStudents'] == undefined) {
                console.log("10 students Notification");
                NotificationManager.createPopup(document.getElementById('tenStudents').getAttribute('string'));
                this._notificationsAlreadyDisplayed['tenStudents'] = true;
            }
        }
        if (gameManager._studentsLeft == 20) {
            if (this._notificationsAlreadyDisplayed['twentyStudents'] == undefined) {
                console.log("20 students Notification");
                NotificationManager.createPopup(document.getElementById('twentyStudents').getAttribute('string'));
                this._notificationsAlreadyDisplayed['twentyStudents'] = true;
            }
        }
        if (gameManager._studentsLeft == 50) {
            if (this._notificationsAlreadyDisplayed['fiftyStudents'] == undefined) {
                console.log("50 students Notification");
                NotificationManager.createPopup(document.getElementById('fiftyStudents').getAttribute('string'));
                this._notificationsAlreadyDisplayed['fiftyStudents'] = true;
            }
        }
        if (gameManager._studentsLeft == 100) {
            if (this._notificationsAlreadyDisplayed['hundredStudents'] == undefined) {
                console.log("100 students Notification");
                NotificationManager.createPopup(document.getElementById('hundredStudents').getAttribute('string'));
                this._notificationsAlreadyDisplayed['hundredStudents'] = true;
            }
        }
    }
}
