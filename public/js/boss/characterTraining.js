/* ================== CHARACTER TRAINING ================== */

class CharacterTraining {
    /* variables:
        {int} _characterID    (internal id of character for individual div)
        {int} _positionX      (where is character horizontally positioned in scene?)
        {string} _type        ("HoneyBee" or "BumbleBee")
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
     * @param  {string} type "HoneyBee" or "BumbleBee"
     * @param  {int} characterID    (id of the character div of certain type)
     * @param  {int} maxCharacter   (total amount of characters to be displayed)
     * @param  {boolean} hideCharacter      (should images be created for character or should it remain hidden without divs?)
     * @return {void}
     */
    initCharacter(type, characterID, maxCharacter, hideCharacter) {
        this._type = type;
        this._characterID = characterID;
        this._isHidden = true;
        this._positionX = (Math.random() * 70) / maxCharacter + 70 * ((this._characterID % maxCharacter) / maxCharacter) + 13;  //random position in roughly centered and spread them equally with semi random even deviation

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
        this._image.src = "/images/training/character" + this._type + ".png";

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

        this._isHidden = false;
    }

    /**
     * Switches character fall animation to idle
     *
     * @param  {AnimationEvent} event
     * @return {void}
     */
    setCharacterAnimationIdle(event) {
        this.className = "imageCharacter imageCharacterIdle";
        let animationDelay = 0;
        let durationMultiplier = Math.random() * 0.5 + 0.75;
        let oszillationAmplitude = Math.random() * 50 + 75;
        let horizontalDistance = Math.random() * 75 + 75;

        this.style.animationDelay = animationDelay + "s";
        this.style.setProperty("--animationDurationMultiplier", durationMultiplier);
        this.style.setProperty("--oscillationAmplitude", oszillationAmplitude + "px");
        this.style.setProperty("--horizontalDistance", horizontalDistance + "px");
    }

    initiateDeath() {}

    present() {
        return 'CharacterTraining: Type(' + this._type + ") id: " + this._characterID;
    }
}