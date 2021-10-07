/* logic for disabling checkboxes in no game mode */

window.onload = function (){
    // hide settings if necessary
    toggleGameSelection(document.getElementById("input_gameModeType").getAttribute("value"));
};

function toggleBotsEnabled() {
    //disable bots in noGame mode
    if(document.getElementById("gameSelect_noGame").classList.contains('active') == false){
        document.getElementById("botDifficulty").hidden = !document.getElementById("checkbox_use_bots").checked;
    }
}

function toggleGameSelection(gameType) {
    document.getElementById("input_gameModeType").setAttribute("value", gameType);
    document.getElementById("gameSelect_classic").classList.remove("active");
    document.getElementById("gameSelect_noGame").classList.remove("active");
    document.getElementById("gameSelect_training").classList.remove("active");
    document.getElementById("gameSelect_teams").classList.remove("active");

    if(gameType == 'classic') {     //classic full game Mode
        document.getElementById("gameSelect_classic").classList.add("active");

        disableCheckbox("checkbox_session_option_private_rank", false);
        disableCheckbox("checkbox_session_option_public_rank", false);
        disableCheckbox("checkbox_show_story", false);
        disableCheckbox("checkbox_show_badges", false);
        disableCheckbox("checkbox_use_points", false);
        disableCheckbox("checkbox_show_character_selection", ! document.getElementById("checkbox_show_story").checked);
        disableCheckbox("checkbox_use_bots", false);
        document.getElementById("botDifficulty").hidden = !document.getElementById("checkbox_use_bots").checked;

        disableCheckbox("radio_boss_health_option_0", false); // users can choose a difficulty level
        disableCheckbox("radio_boss_health_option_1", false);
        disableCheckbox("radio_boss_health_option_2", false);
        disableCheckbox("radio_boss_health_option_3", false);
    }else if(gameType == 'training') {
        document.getElementById("gameSelect_training").classList.add("active");

        disableCheckbox("checkbox_session_option_private_rank", true);
        disableCheckbox("checkbox_session_option_public_rank", true);
        disableCheckbox("checkbox_show_story", true);
        disableCheckbox("checkbox_show_badges", true);
        disableCheckbox("checkbox_use_points", true);
        disableCheckbox("checkbox_show_character_selection", false);
        disableCheckbox("checkbox_use_bots", false);
        document.getElementById("botDifficulty").hidden = !document.getElementById("checkbox_use_bots").checked;

        disableCheckbox("radio_boss_health_option_0", false); // users can choose a difficulty level
        disableCheckbox("radio_boss_health_option_1", false);
        disableCheckbox("radio_boss_health_option_2", false);
        disableCheckbox("radio_boss_health_option_3", false);
    }else if(gameType == 'noGame') {
        document.getElementById("gameSelect_noGame").classList.add("active");

        disableCheckbox("checkbox_session_option_private_rank", true);
        disableCheckbox("checkbox_session_option_public_rank", true);
        disableCheckbox("checkbox_show_story", true);
        disableCheckbox("checkbox_show_badges", true);
        disableCheckbox("checkbox_use_points", true);
        disableCheckbox("checkbox_show_character_selection", true);
        disableCheckbox("checkbox_use_bots", true);
        document.getElementById("botDifficulty").hidden = true;

        disableCheckbox("radio_boss_health_option_0", true); // users dont need to choose a difficulty level
        disableCheckbox("radio_boss_health_option_1", true);
        disableCheckbox("radio_boss_health_option_2", true);
        disableCheckbox("radio_boss_health_option_3", true);
    }else if(gameType == 'teams') {
        document.getElementById("gameSelect_teams").classList.add("active");

        disableCheckbox("checkbox_session_option_private_rank", true);
        disableCheckbox("checkbox_session_option_public_rank", true);
        disableCheckbox("checkbox_show_story", true);
        disableCheckbox("checkbox_show_badges", true);
        disableCheckbox("checkbox_use_points", true);
        disableCheckbox("checkbox_show_character_selection", true);
        disableCheckbox("checkbox_use_bots", false);
        document.getElementById("botDifficulty").hidden = !document.getElementById("checkbox_use_bots").checked;

        disableCheckbox("radio_boss_health_option_0", true); // users dont need to choose a difficulty level
        disableCheckbox("radio_boss_health_option_1", true);
        disableCheckbox("radio_boss_health_option_2", true);
        disableCheckbox("radio_boss_health_option_3", true);
    }
}

function disableCheckbox(name, disabled) {
    document.getElementById(name).disabled = disabled;
}

// if story is unchecked, we must disable character selection
function clickOptionStory(clickevent){
    if (clickevent.checked){
        document.getElementById("checkbox_show_character_selection").disabled = false;
    }else{
        document.getElementById("checkbox_show_character_selection").disabled = true;
        document.getElementById("checkbox_show_character_selection").checked = false;
    }
}
