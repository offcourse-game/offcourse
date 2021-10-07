/* ================== ANIMATOR ABSTRACT BASE ================== */
//abstract base animator class for inheriting to manage fire, smoke, flowers, etc.

class Animator {

    constructor() {
        if (new.target === Animator) {
            throw new TypeError("Cannot construct Abstract instances directly");
        }
    }

    /**
     * create beautifying items (flowers)
     *
     * @param  {int} flowerCount
     * @return {void}
     */
    setupBeautification(flowerCount) {
        for (let i = 0; i < flowerCount; i++) {
            let flowerType = Math.floor(Math.random() * 3); //select one of 3 flower types
            let randPos = (Math.random() * 90) / flowerCount + 90 * (i / flowerCount);  //random position in right half and spread them equally with random deviation
            let height = Math.floor(Math.random() * 20) + 40; //random heigth
            let zIndex = Math.floor(Math.random() * 3 + 1); //random z index in front or behind character

            let flower = document.createElement('img');
            flower.src = "/images/flower" + flowerType + ".png";  //get corresponding image
            flower.classList.add("imageFlower");
            flower.style.height = height + "px";
            flower.style.right = randPos + "%";
            flower.style.zIndex = zIndex;

            document.body.appendChild(flower);
            document.getElementById('beautificationParent').appendChild(flower);
        }
    }

    /**
     * Displays the end animation.
     *
     * @param  {boolean} win (did student win?)
     * @return {void}
     */
    async endAnimation(win){}
}