/* ================== CHARACTER ABSTRACT BASE CLASS ================== */

class Character {
    //abstract class
    constructor() {
        if (new.target === Character) {
            throw new TypeError("Cannot construct Abstract instances directly");
        }
    }

    initCharacter(type, characterID, maxCharacter, hideCharacter) {}
    activateCharacterImages(animateFall) {}
    setCharacterAnimationIdle(event) {}
    initiateDeath() {}
}

/* ================== CHARACTER STATIC FUNCTIONS ================== */

/**
 * Delete a type character, should be killed first to create ghost and fade out.
 *
 * @param {div} animTransform
 * @return {void}
 */
function deleteCharacter(animTransform) {
    console.log("deleteCharacter " + animTransform);
    //TODO fix this function!
    //animTransform.parentNode.removeChild(animTransform);
}