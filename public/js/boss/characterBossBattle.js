/* ================== CHARACTER BOSS BATTLE ================== */

class CharacterBossBattle {
    /* variables:
        {int} _characterID    (internal id of character for individual div)
        {int} _positionX      (where is character horizontally positioned in scene?)
        {string} _type        ("Gold" or "Gray")
        {boolean} _isHidden      (does character have an image or is it silent / hidden?)

        {div} _animTransform  (parent transform of character for animation)
        {div} _image          (child transform of character image)
     */

    constructor(type, characterID, maxCharacter, hideCharacter) {
        this.initCharacter(type, characterID, maxCharacter, hideCharacter);
    }

    /**
     * initializes characters div and places character randomly
     *
     * @param  {string} type "Gold" or "Gray"
     * @param  {int} characterID    (id of the character div of certain type)
     * @param  {int} maxCharacter   (total amount of characters to be displayed)
     * @param  {boolean} hideCharacter      (should images be created for character or should it remain hidden without divs?)
     * @return {void}
     */
    initCharacter(type, characterID, maxCharacter, hideCharacter) {
        this._characterID = characterID;
        this._type = type;
        this._isHidden = true;
        this._positionX = (Math.random() * 50) / maxCharacter + 50 * ((this._characterID % maxCharacter) / maxCharacter);  //random position in right half and spread them equally with semi random even deviation

        if(hideCharacter == false) {
            this.activateCharacterImages(true);
        }
    }

    /**
     * creates divs and images for character
     *
     * @param {boolean} animateFall (create an animation for falling character?)
     * @return {void}
     */
    activateCharacterImages(animateFall) {
        let randDelay = Math.random() * 1.5 + 6; // get random animation delay (6 to 7.5)
        this._animTransform = document.createElement('div');
        this._animTransform.setAttribute("id", this._characterID + this._type);

        document.body.appendChild(this._animTransform);
        document.getElementById('characterParent').appendChild(this._animTransform);

        this._image = document.createElement('img');
        this._image.setAttribute("id", this._characterID + this._type + "image");
        this._image.src = "/images/character" + this._type + ".png";

        this._image.className = "imageCharacter imageCharacterFall";

        this._image.style = "right:" + this._positionX + "%";
        this._image.style.animationDelay = randDelay + "s";
        this._image.style.zIndex = Math.floor(Math.random() * 8 + 2);       //sets z index to min 2 and max 9, so gold and gray characters are distributed randomly in z axis
        this._image.alt = "character";

        this._image.addEventListener("animationend", this.setCharacterAnimationIdle);

        if(animateFall == false) {
            setCharacterAnimationIdle(null);
        }

        document.body.appendChild(this._image);
        document.getElementById(this._characterID + this._type).appendChild(this._image);

        if (this._type == "Gold") {
            gameManager._studentsDisplayedGold++;
        } else {
            gameManager._studentsDisplayedGray++;
        }

        this._isHidden = false;
    }

    /**
     * Switches character fall animation to idle
     *
     * @param  {AnimationEvent} event
     * @return {void}
     */
    setCharacterAnimationIdle(event) {
        let randDelay = Math.random() * -3; // get random animation delay (-3 to 0); starts 3 sec inside animation

        this.className = "imageCharacter imageCharacterIdle";
        this.style.animationDelay = randDelay + "s";
    }

    /**
     * initiates death animation and places ghost
     *
     * @return {void}
     */
    initiateDeath() {
        if(this._isHidden == false) {
            this._animTransform.classList.remove("imageCharacterIdle");
            this._animTransform.classList.add("imageCharacterDie");

            if (this._type == "Gold") {
                gameManager._studentsDisplayedGold--;
            } else {
                gameManager._studentsDisplayedGray--;
            }
        }

        this.createGhost();
    }

    /**
     * Adds a ghost to dying players.
     *
     * @return {void}
     */
    createGhost() {
        let ghost = document.createElement('div');
        ghost.innerHTML = "<img src='/images/ghost.png' class='imageGhost' style='right:" + (this._positionX + 2) + "%;' alt='ghost'>";
        document.body.appendChild(ghost);
        document.getElementById('characterParent').appendChild(ghost);
        ghost.addEventListener("animationend", this.deleteGhost);

        if(this._characterID >= 0) {
            ghost.addEventListener("animationend", function () {
                //TODO fix: passing var does not really work somehow
                deleteCharacter(this._animTransform);
            });
        }
    }

    /**
     * @param  {AnimationEvent} event
     * @return {void}
     */
    deleteGhost(event){
        this.parentNode.removeChild(this);
        console.log("deleteGhost" + event);
    }

    /**
     * creates a spear at characters position
     *
     * @return {void}
     */
    createSpear(){
        let spear = document.createElement('div');
        spear.innerHTML = "<img src='/images/spear" + this._type + ".png' class='imageSpear' style='right:" + (this._positionX + 4) + "%;' alt='spear'>";

        document.body.appendChild(spear);
        document.getElementById('spearParent').appendChild(spear);

        spear.addEventListener("animationend", this.deleteSpear);
    }

    /**
     * Delete thrown spear.
     *
     * @param  {AnimationEvent} event
     * @return {void}
     */
    deleteSpear(event) {
        this.parentNode.removeChild(this);
        console.log("spear" + event);
    }

    present() {
        return 'CharacterBossBattle: Type(' + this._type + ") pos: " + this._positionX;
    }
}