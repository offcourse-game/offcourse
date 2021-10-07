/* ================== NOTIFICATION MANAGER TRAINING ================== */

class NotificationManagerTraining extends NotificationManager {

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
                NotificationManager.createPopup(document.getElementById('flower_threeQuatersLife').getAttribute('string'));
                this._notificationsAlreadyDisplayed['threeQuatersLife'] = true;
            }
        }
        if (gameManager._bossLife / gameManager._bossLifeStartTotal <= 0.5) {
            if (this._notificationsAlreadyDisplayed['halfLife'] == undefined) {
                console.log("50% life Notification");
                NotificationManager.createPopup(document.getElementById('flower_halfLife').getAttribute('string'));
                this._notificationsAlreadyDisplayed['halfLife'] = true;
            }
        }
        if (gameManager._bossLife / gameManager._bossLifeStartTotal <= 0.25) {
            if (this._notificationsAlreadyDisplayed['quarterLife'] == undefined) {
                console.log("25% life Notification");
                NotificationManager.createPopup(document.getElementById('flower_quarterLife').getAttribute('string'));
                this._notificationsAlreadyDisplayed['quarterLife'] = true;
            }
        }
        if (gameManager._timeLeft.getTime() <= 32 * 1000 && gameManager._timeLeft.getTime() > 0) {
            if (this._notificationsAlreadyDisplayed['30seconds'] == undefined) {
                console.log("30 seconds Notification");
                NotificationManager.createPopup(document.getElementById('30seconds').getAttribute('string'));
                this._notificationsAlreadyDisplayed['30seconds'] = true;
            }
        }
        if (gameManager._timeLeft.getTime() <= 62 * 1000 && gameManager._timeLeft.getTime() > 0) {
            if (this._notificationsAlreadyDisplayed['60seconds'] == undefined) {
                console.log("60 seconds Notification");
                NotificationManager.createPopup(document.getElementById('60seconds').getAttribute('string'));
                this._notificationsAlreadyDisplayed['60seconds'] = true;
            }
        }
        if (gameManager._timeLeft.getTime() <= 122 * 1000 && gameManager._timeLeft.getTime() > 0) {
            if (this._notificationsAlreadyDisplayed['120seconds'] == undefined) {
                console.log("120 seconds Notification");
                NotificationManager.createPopup(document.getElementById('120seconds').getAttribute('string'));
                this._notificationsAlreadyDisplayed['120seconds'] = true;
            }
        }
    }
}
