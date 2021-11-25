@extends('mainEditor')

@section('title')
    Sessionoptionen
@endsection

@section('activeSideNavItem3') active @endsection
@section('sessionId'){{$sessionId}}@endsection {{-- yields the sessionId to the sidebar--}}
@section('HeaderBoxHeadline') {{$sessionSettings->session_name}} @endsection

@section('mainEditorHead')
    <script src="{{URL::asset('js/editorSettings.js')}}"></script>
@endsection

@section('mainEditorBody')
    <div class="wrapperOuterMargin">
        <h3>Sessionoptionen bearbeiten</h3>
        <hr/>
        <form action="/editorGameSettings/{{$sessionId}}" method="post">
            <div class="sessionItem">
                <div class="editorGridTriple">
                    <div class="editorGridContent">
                        <h5><span class="iconify" data-icon="emojione-monotone:level-slider" data-inline="false"></span> Schwierigkeitsgrad:</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radio_boss_health_option" id="radio_boss_health_option_0" value="option0" {{$sessionSettings->boss_health_option == 0.8 ? "checked" : ""}}>
                            <label class="form-check-label" for="radio_boss_health_option_0"><span data-balloon="20 % weniger Antworten müssen richtig sein" data-balloon-pos="right">Leicht</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radio_boss_health_option" id="radio_boss_health_option_1" value="option1" {{$sessionSettings->boss_health_option == 1.0 ? "checked" : ""}}>
                            <label class="form-check-label" for="radio_boss_health_option_1">Normal</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radio_boss_health_option" id="radio_boss_health_option_2" value="option2" {{$sessionSettings->boss_health_option == 1.2 ? "checked" : ""}}>
                            <label class="form-check-label" for="radio_boss_health_option_2"><span data-balloon="20 % mehr Antworten müssen richtig sein" data-balloon-pos="right">Schwer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radio_boss_health_option" id="radio_boss_health_option_3" value="option3" {{$sessionSettings->boss_health_option == 3 ? "checked" : ""}}>
                            <label class="form-check-label" for="radio_boss_health_option_3"><span data-balloon="300 % mehr Antworten müssen richtig sein, optimiert für super einfache Fragen und den Einsatz von superstarken Bots" data-balloon-pos="right">Superschwer</label>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="timeInput"><h5> <span class="iconify" data-icon="mdi-timer-outline"></span> Spielzeit:</h5></label>
                            <input type="number" class="form-control" aria-describedby="timeInput" value="{{$sessionSettings->session_duration}}" min="1" max="59" name="session_duration" required>
                            <small class="form-text text-muted">Wählen Sie eine maximale Spieldauer in Minuten. Die Session sollte nicht länger als {{$max_duration}} Minuten dauern, da sonst nicht genügend Fragen vorhanden sind!</small>
                        </div>
                    </div>
                    <div class="editorGridContent">
                        <div class="custom-checkbox">
                            <label><h5> <span class="iconify" data-icon="mdi-crosshairs-question"></span> Anzahl korrekter Antworten:</h5></label>
                            <input type="checkbox" class="form-check-input" name="checkbox_show_number_correct_answers" id="checkbox_show_number_correct_answers" {{$sessionSettings->show_number_correct_answers == 1 ? "checked" : ""}}>
                            <label class="form-check-label" for="checkbox_show_number_correct_answers"><small class="form-text text-muted labelsmallMargin form-check-label">Den Studierenden wird die richtige Anzahl an Antworten angezeigt.</small></label><br>
                        </div>
                        <br>
                        <div class="custom-checkbox" onclick="toggleBotsEnabled()">
                            <label><h5> <span class="iconify" data-icon="bx:bx-bot"></span> Spielerbots:</h5></label>
                            <input type="checkbox" class="form-check-input" name="checkbox_use_bots" id="checkbox_use_bots" {{$sessionSettings->use_bots == 1 ? "checked" : ""}}>
                            <label class="form-check-label" for="checkbox_use_bots"><small class="form-text text-muted labelsmallMargin form-check-label">Bei weniger als 10 Teilnehmenden werden Bots zur Session hinzugefügt, die menschliche Spieler simulieren.</small></label><br>
                        </div>
                        <br>
                        <div id="botDifficulty" {{$sessionSettings->use_bots == 1 ? "" : "hidden"}}>
                            <h5><span class="iconify" data-icon="emojione-monotone:level-slider" data-inline="false"></span> Stärke der Spielerbots:</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radio_bot_difficulty" id="radio_bot_difficulty_option_0" value="1" {{$sessionSettings->bot_difficulty == 1 ? "checked" : ""}}>
                                <label class="form-check-label" for="radio_bot_difficulty_option_0"><span data-balloon="Die Spielerbots beantworten im Durchschnitt alle 22s eine Frage zu 40% richtig." data-balloon-pos="right">Schwach</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radio_bot_difficulty" id="radio_bot_difficulty_option_1" value="2" {{$sessionSettings->bot_difficulty == 2 ? "checked" : ""}}>
                                <label class="form-check-label" for="radio_bot_difficulty_option_1"><span data-balloon="Die Spielerbots beantworten im Durchschnitt alle 16s eine Frage zu 45% richtig." data-balloon-pos="right">Normal</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radio_bot_difficulty" id="radio_bot_difficulty_option_2" value="3" {{$sessionSettings->bot_difficulty == 3 ? "checked" : ""}}>
                                <label class="form-check-label" for="radio_bot_difficulty_option_2"><span data-balloon="Die Spielerbots beantworten im Durchschnitt alle 14s eine Frage zu 55% richtig." data-balloon-pos="right">Stark</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="radio_bot_difficulty" id="radio_bot_difficulty_option_3" value="4" {{$sessionSettings->bot_difficulty == 4 ? "checked" : ""}}>
                                <label class="form-check-label" for="radio_bot_difficulty_option_3"><span data-balloon="Die Spielerbots beantworten im Durchschnitt alle 9s eine Frage zu 75% richtig." data-balloon-pos="right">Superstark</label>
                            </div>
                        </div>
                    </div>
                    <div class="editorGridContent">
                        <label for="lang">
                            <h5><span class="iconify" data-icon="ic-outline-language" data-inline="false"></span> Anzeigesprache:</h5>
                        </label>
                        <select name="lang">
                            <option value="de" @if($lang == "de")selected="selected" @endif>Deutsch</option>
                            <option value="en" @if($lang == "en")selected="selected" @endif>Englisch</option>
                        </select>
                        <small class="form-text text-muted">Sämtliche Textelemente aller Teilnehmenden werden auf Deutsch / Englisch angezeigt.</small>
                        <br>
                        <div class="form-group">
                            <label for="urlInput"><h5> <span class="iconify" data-icon="carbon-chart-evaluation"></span> Evaluations-Link:</h5></label>
                            <input type="url" class="form-control" aria-describedby="urlInput" value="{{$sessionSettings->evaluation_url}}" name="evaluation_url">
                            <small class="form-text text-muted">Den Teilnehmenden wird nachdem Spiel ein zusätzlicher Button anzeigt, welcher zur angegebenen URL weiterleitet. Bitte geben Sie die Adresse mit dem Präfix (z.B. https:\\) an. Dem Link wird wird ein Parameter (pid) mit der entsprechenden Player-Id übergeben, um eine Zuordung zu ermöglichen.</small>
                        </div>
                    </div>
                </div>

                <br>
                <h5><span class="iconify" data-icon="mdi:dice-multiple-outline" data-inline="true"></span> Spielmodus auswählen:</h5>
                <div class="gameSelectionCardWrapper">
                    <div class="gameSelectionCard {{$gameModeType == 'classic' ? 'active' : ''}}" id="gameSelect_classic" onclick="toggleGameSelection('classic')">
                        <h1><span class="iconify" data-icon="mdi:sword-cross" data-inline="true"></span></h1>
                        <br>
                        <h4>Boss Battle</h4>
                        <small>Ein interaktiver Bossfight wird am Projektor angezeigt. Einzelne Elemente können deaktiviert werden.</small>
                    </div>
                    <!-- use style="display: none;" here to hide training mode for prod-->

                    {{--unsupported mode due to copyright issues--}}
                    <div class="gameSelectionCard {{$gameModeType == 'training' ? 'active' : ''}}" style="display: none;" id="gameSelect_training" onclick="toggleGameSelection('training')">
                        <h1><span class="iconify" data-icon="fa-solid:running" data-inline="true"></span></h1>
                        <br>
                        <h4>Training</h4>
                        <small>In diesem Modus können Studierende nicht ausscheiden. Eine animierte Blume wird angezeigt. </small>
                    </div>

                    <div class="gameSelectionCard {{$gameModeType == 'noGame' ? 'active' : ''}}" id="gameSelect_noGame" onclick="toggleGameSelection('noGame')">
                        <h1><span class="iconify" data-icon="zmdi:eye-off" data-inline="true"></span></h1>
                        <br>
                        <h4>Time Attack</h4>
                        <small>Nur ein ablaufender Timer wird am Projektor angezeigt. Alle Spielelemente werden deaktiviert.</small>
                    </div>
                    <div class="gameSelectionCard {{$gameModeType == 'teams' ? 'active' : ''}}" id="gameSelect_teams" onclick="toggleGameSelection('teams')">
                        <h1><span class="iconify" data-icon="ant-design:team-outlined" data-inline="true"></span></h1>
                        <br>
                        <h4>Teams</h4>
                        <small>Zwei Teams treten gegeneinander an, das Team mit der höheren Punktzahl (relativ zur Teamgröße) gewinnt.</small>
                    </div>
                </div>

                <h5><span class="iconify" data-icon="ic:settings" data-inline="false"></span>
                    sonstige Optionen:</h5>
                <div class="editorGridHalf">
                    <div class="editorGridContent">
                        <div class="custom-checkbox">
                            <input type="checkbox" class="form-check-input" name="checkbox_session_option_private_rank" id="checkbox_session_option_private_rank" {{($sessionSettings->session_option_rank == 1 || $sessionSettings->session_option_rank == 3) ? "checked" : ""}}>

                            <label class="form-check-label" for="checkbox_session_option_private_rank">persönlicher Rang</label>
                            <small class="form-text text-muted labelsmallMargin">Studierende sehen ihre individuelle Platzierung nach der Session auf ihrem Smartphone.</small><br>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" class="form-check-input" name="checkbox_session_option_public_rank" id="checkbox_session_option_public_rank" {{($sessionSettings->session_option_rank == 2 || $sessionSettings->session_option_rank == 3) ? "checked" : ""}}>

                            <label class="form-check-label" for="checkbox_session_option_public_rank">öffentliche Rangliste</label>
                            <small class="form-text text-muted labelsmallMargin">Studierende sehen die komplette Rangliste nach der Session auf dem Projektor.</small><br>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" class="form-check-input" onclick='clickOptionStory(this);' name="checkbox_show_story" id="checkbox_show_story" {{$sessionSettings->show_story == 1 ? "checked" : ""}}>

                            <label class="form-check-label" for="checkbox_show_story">Story</label>
                            <small class="form-text text-muted labelsmallMargin">Eine Story mit einem Drachen wird am Projektor angezeigt.</small><br>
                        </div>
                    </div>
                    <div class="editorGridContent">
                        <div class="custom-checkbox">
                            <input type="checkbox" class="form-check-input" name="checkbox_show_badges" id="checkbox_show_badges" {{$sessionSettings->show_badges == 1 ? "checked" : ""}}>

                            <label class="form-check-label" for="checkbox_show_badges">Auszeichnungen</label>
                            <small class="form-text text-muted labelsmallMargin">Studierende sehen nach der Session Auszeichnungen auf ihrem Smartphone.</small><br>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" class="form-check-input" name="checkbox_use_points" id="checkbox_use_points" {{$sessionSettings->use_points == 1 ? "checked" : ""}}>

                            <label class="form-check-label" for="checkbox_use_points">Punkte</label>
                            <small class="form-text text-muted labelsmallMargin">An die Studierenden werden Punkte verteilt.</small><br>
                        </div>
                        <div class="custom-checkbox">
                            <input type="checkbox" class="form-check-input" name="checkbox_show_character_selection" id="checkbox_show_character_selection" {{$sessionSettings->show_character_selection == 1 ? "checked" : ""}}>

                            <label class="form-check-label" for="checkbox_show_character_selection">Charakterauswahl</label>
                            <small class="form-text text-muted labelsmallMargin">Die Studierenden können zu Beginn einen von zwei unterschiedlichen Charakteren auswählen.</small><br>
                        </div>
                    </div>

                </div>
            </div>
            <hr/>
            <button type="submit" class="btn btn-success float-right shaded bordered bg_pf_correct">
                Weiter
                <span class="iconify iconify_medium_text_ml" data-icon="mdi:arrow-right-thick"></span>
            </button>
            <div class="fillerBottom"></div>

            <input type="hidden" name="input_gameModeType" id="input_gameModeType" value="{{$gameModeType}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
    </div>
@endsection
