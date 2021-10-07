/* ================== ANIMATOR BOSS BATTLE ================== */

class AnimatorBossBattle extends  Animator {

    constructor() {
        super();
    }

    /**
     * creates n smoke particles on boss character
     *
     * @param  {int} particleCount  (number of smoke particles)
     * @return {void}
     */
    setupSmokeParticles(particleCount) {
        //creates n smoke particles with random animation

        for (let i = 0; i < particleCount; i++) {
            let imgId = i % 5; //gets ordered image id from 0 to 4 (without 5) all images will be equally often used
            let randDelay = Math.random() * -3; // get random animation delay (-3 to 0) -3 means 3 seconds into animation before starting
            let randDuration = Math.random() * 3 + 3; // get random animation duration (3 to 6)

            let smokeParticle = document.createElement('img');
            smokeParticle.src = "/images/smoke/smoke" + imgId + ".png";
            smokeParticle.classList.add("bossSmoke");     //adds class smoke to element
            document.body.appendChild(smokeParticle);
            document.getElementById('bossImg').appendChild(smokeParticle);

            smokeParticle.style.animationDelay = randDelay + "s";
            smokeParticle.style.animationDuration = randDuration + "s";
        }
    }

    /**
     * removes all smoke particles of boss
     *
     * @return {void}
     */
    deleteAllSmokeParticles() {
        let particles = document.getElementsByClassName("bossSmoke");

        while (particles[0]) {
            particles[0].parentNode.removeChild(particles[0]);
        }
    }

    /**
     * creates fire particles parented at boss
     *
     * @param  {int} particleCount      (number of fire particles)
     * @return {void}
     */
    setupFireParticles(particleCount) {
        for (let i = 0; i < particleCount; i++) {
            let delay = Math.floor(i / 3) * 0.5 + 5; //create little "bursts"
            let duration = 15;

            let fireParticle = document.createElement('img');
            fireParticle.src = "/images/fire/fire.png";
            fireParticle.classList.add("bossFire");     //adds class fire to element
            fireParticle.style.animationName = "bossFire" + (i % 3).toString();

            document.body.appendChild(fireParticle);
            document.getElementById('bossImg').appendChild(fireParticle);

            fireParticle.style.animationDelay = delay + "s";
            fireParticle.style.animationDuration = duration + "s";
        }
    }

    /**
     * removes all fire particles in boss
     *
     * @return {void}
     */
    deleteAllFireParticles() {
        let particles = document.getElementsByClassName("bossFire");

        while (particles[0]) {
            particles[0].parentNode.removeChild(particles[0]);
        }
    }

    /**
     * sets up boss animation fall and idle.
     * @return {void}
     */
    setupBoss() {
        let boss = document.getElementById('bossImg');

        boss.addEventListener("animationend", this.setBossAnimationIdle);
    }

    /**
     * Switches boss fall animation to idle
     *
     * @param  {AnimationEvent} event
     * @return {void}
     */
    setBossAnimationIdle(event) {
        this.classList.remove("imageBossFall");
        this.classList.add("imageBossIdle");
        this.style.animationDelay = "0s";
    }

    /**
     * Adds flash animation when boss takes damage
     *
     * @return {void}
     */
    addBossFlash() {
        let boss = document.getElementById('innerBossImg');

        boss.classList.add("bossFlashWhite");
        boss.addEventListener("animationend", this.deleteBossFlash);

        console.log("addBossFlash");
    }

    /**
     * Removes flash class if transition has finished
     *
     * @return {void}
     */
    deleteBossFlash() {
        this.classList.remove("bossFlashWhite");

        console.log("deleteBossFlash");
    }

    /**
     * Displays the end animation.
     *
     * @param  {boolean} win (student win?)
     * @return {void}
     */
    async endAnimation(win){
        let bossParent = document.getElementById("bossImg");
        let bossImg = document.getElementById("innerBossImg");
        let cloud = document.getElementById("cloud");
        let lightning = document.getElementById("lightning");
        let background = document.getElementById("blueSkyBackground");
        let tree = document.getElementById("tree");
        let foregroundFader = document.getElementById("foregroundFader");

        if(win == true){
            await sleep(4000);
            NotificationManager.createPopup(document.getElementById('win').getAttribute('string'));
            await sleep(1000);

            this.deleteAllFireParticles();
            this.deleteAllSmokeParticles();

            bossParent.classList.remove("imageBossTransitioner");
            bossParent.style.width = "351px";
            bossParent.style.height = "450px";
            bossParent.style.animation = "bossDie 1.5s ease-in 0s";
            bossParent.style.animationFillMode = "reverse";

            bossParent.style.opacity = "1";
            bossImg.style.opacity = "1";

            await sleep(250);

            let bone0 = document.getElementById("bonesImg0");
            bone0.style.animation = "bones0 1.5s ease-out 0s";
            bone0.style.animationFillMode = "forwards";

            let bone1 = document.getElementById("bonesImg1");
            bone1.style.animation = "bones1 1.5s ease-out 0s";
            bone1.style.animationFillMode = "forwards";

            let bone2 = document.getElementById("bonesImg2");
            bone2.style.animation = "bones2 1.5s ease-out 0s";
            bone2.style.animationFillMode = "forwards";

            await sleep(500);

            bossParent.style.opacity = "1";
            bossImg.style.opacity = "0";
        } else {
            await sleep(3000);
            NotificationManager.createPopup(document.getElementById('boss_lose').getAttribute('string'));
            bossParent.classList.remove("imageBossTransitioner");
            bossParent.classList.remove("imageBossTransitioner");
            background.classList.add("backgroundThunder");
            foregroundFader.classList.add("foregroundFader");

            await sleep(800);
            //createGhost("", -1, true);
            cloud.style.webkitFilter = "brightness(90%)";
            await sleep(800);
            //createGhost("", -1, true);
            //createGhost("", -1, true);
            cloud.style.webkitFilter = "brightness(70%)";
            await sleep(800);
            //createGhost("", -1, true);
            cloud.style.webkitFilter = "brightness(40%)";
            cloud.style.animationDuration = "2s";
            lightning.style.visibility = "visible";
            await sleep(800);
            //createGhost("", -1, true);
            //createGhost("", -1, true);
            cloud.style.webkitFilter = "brightness(20%)";

            //tree.style.webkitFilter = "brightness(20%)";
        }
    }
}
