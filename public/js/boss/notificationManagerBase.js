/* ================== NOTIFICATION MANAGER ABSTRACT BASE ================== */
/* manages the notifications, inherits for boss and flower view */

class NotificationManager {
    /* variables:
        {MappedArray} _notificationsAlreadyDisplayed (name and boolean value of notification is stored in list)
     */

    constructor() {
        if (new.target === NotificationManager) {
            throw new TypeError("Cannot construct Abstract instances directly");
        }

        this._notificationsAlreadyDisplayed = {};
    }

    /**
     * Creates a pop up in the main animation screen.
     *
     * @param  {string} text
     * @return {void}
     */
    static createPopup(text){
        let notification = document.createElement('div');
        notification.setAttribute('class', 'bossNotification');
        document.body.appendChild(notification);
        document.getElementById("bossNotificationParent").appendChild(notification);

        text = text.replace(/ /g, "\u00A0");        //replace all spaces with special character; with normal spaces no span can be created
        for(let i = 0; i < text.length; i++) {
            let innerChar = document.createElement('span');
            innerChar.setAttribute('class', 'char');
            innerChar.innerText = text[i].toString();
            innerChar.style.animationDelay = (Math.random() * -0.5) + "s";
            notification.append(innerChar);
        }

        notification.addEventListener("animationend", this.deletePopup);
    }

    /**
     * Deletes notification after fadeout.
     *
     * @param  {AnimationEvent} event
     * @return {void}
     */
    deletePopup(event){
        this.parentNode.removeChild(this);
    }

    /**
     * creates boss notifications at some intervals (not reliable)
     * @return {void}
     */
    displayNotification() {}
}