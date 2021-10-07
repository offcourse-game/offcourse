hasTypeSet = false;

/**
 * Checks if the session starts and set the new location.
 *
 * @return {void}
 */
function doAjaxCall(){
    $.ajax({
        type: 'get',
        url: '/isSessionActive',
        success: function (isSessionActive){
            console.log(isSessionActive);
            if (isSessionActive == 1){
                // sleep 3 sec so the students can see the animation and dont start playing
                setTimeout(function(){
                    window.location.href = "/question";
                }, 3000);

            }
            setTimeout(doAjaxCall, 1000); // We wait 1 Seconds before doing the next call
        }
    });
}
setTimeout(doAjaxCall, 0); // Do the first call immediately

/**
 * Add a background to the character which is selected.
 *
 * @param  {int} characterId type of selected character 1 or 2
 * @return {void}
 */
function selectCharacter(characterId){
    let characterSelector1 = document.getElementById("characterSelector1");
    let characterSelector2 = document.getElementById("characterSelector2");

    if (characterSelector1.classList.contains("active") == true){
        characterSelector1.classList.remove("active");
    }

    if (characterSelector2 != null && characterSelector2.classList.contains("active") == true){
        characterSelector2.classList.remove("active");
    }

    if(characterId == 1) {
        characterSelector1.classList.add("active");
    }

    if(characterId == 2 && characterSelector2 != null) {
        characterSelector2.classList.add("active");
    }

    sendStudentType(characterId);
}

/**
 * Sends the student type to the controller.
 *
 * @param  {int} characterId type of character 1 or 2
 * @return {void}
 */
function sendStudentType(characterId){
    $.ajax({
        type : 'get',
        data: {studentType: characterId},
        url : '/setStudentType',
        success:function(result){
            hasTypeSet =true;
        }
    });
}
