/* ================== ANIMATOR TRAINING ================== */

class AnimatorTraining extends Animator {

    constructor() {
        super();
    }

    /**
     * Displays the end animation.
     *
     * @param  {boolean} win (did student win?)
     * @return {void}
     */
    async endAnimation(win){
        if(win == true) {
            NotificationManager.createPopup(document.getElementById('win').getAttribute('string'));
        } else {
            NotificationManager.createPopup(document.getElementById('flower_lose').getAttribute('string'));
        }
    }
}
