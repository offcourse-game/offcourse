@extends('mainEditor')

@section('title')
    Sessionstart
@endsection

@section('activeSideNavItem4') active @endsection
@section('sessionId'){{$sessionId}}@endsection {{-- yields the sessionId to the sidebar--}}
@section('HeaderBoxHeadline') {{$sessionName}} @endsection

@section('mainEditorBody')
    <div class="wrapperOuterMargin">
        <h3>Sessionstart</h3>
        <hr/>
        <div class="sessionItem">
            <div class="editorGridHalf">
                <h4>Zusammenfassung der Einstellungen:</h4><br>
                @if($gameModeType == 'classic')
                    <div>
                        <h4><span class="iconify" data-icon="mdi:sword-cross" data-inline="true"></span> Boss Battle</h4>
                        @if($current_session_infos->show_story == 1)
                            <span>Ein interaktiver, animierter Bossfight wird am Projektor angezeigt. Einzelne Elemente können deaktiviert werden.</span>
                            <div>
                                <img src="{{URL::asset('images/exampleScenes/bossBattle.png')}}" class="editorPreviewImage large">
                            </div>
                        @else
                            <span>Zwei Fortschrittsleisten werden angezeigt. Einzelne Elemente können deaktiviert werden.</span>
                            <div>
                                <img src="{{URL::asset('images/exampleScenes/noStory.png')}}" class="editorPreviewImage large">
                            </div>
                        @endif
                    </div>
                @elseif ($gameModeType == 'noGame')
                    <div>
                        <h4><span class="iconify" data-icon="zmdi:eye-off" data-inline="true"></span> Time Attack</h4>
                        <span>Nur ein ablaufender Timer wird am Projektor angezeigt. Alle Spielelemente werden deaktiviert.</span>
                        <div>
                            <img src="{{URL::asset('images/exampleScenes/timeAttack.png')}}" class="editorPreviewImage large">
                        </div>
                    </div>
                @elseif ($gameModeType == 'training')
                    <div>
                        <h4><span class="iconify" data-icon="fa-solid:running" data-inline="true"></span> Training</h4>
                        <span>In diesem Modus können Studierende nicht ausscheiden. Eine animierte Blume wird angezeigt. </span>
                        <div>
                            <img src="{{URL::asset('images/exampleScenes/training.png')}}" class="editorPreviewImage large">
                        </div>
                    </div>
                @elseif ($gameModeType == 'teams')
                    <div>
                        <h4><span class="iconify" data-icon="ant-design:team-outlined" data-inline="true"></span> Teams</h4>
                        <span>Wettkampf zwischen zwei Teams. Spielelemente nicht steuerbar.</span>
                        <div>
                            <img src="{{URL::asset('images/exampleScenes/teams.png')}}" class="editorPreviewImage large">
                        </div>
                    </div>
                @endif
                <div class="editorGridContent">
                    <h4>Optionen:</h4>
                    <h5><span class="iconify" data-icon="mdi:checkbox-multiple-marked-outline"></span> Anzahl Fragen: {{$questionCount}}<br></h5>
                    <h5><span class="iconify" data-icon="mdi-timer-outline"></span> Spielzeit: {{$current_session_infos->session_duration}} Minuten</h5>
                    <h5><span class="iconify" data-icon="ic-outline-language"></span> Anzeigesprache:
                        @if ($lang == "de") Deutsch
                        @elseif ($lang == "en") Englisch @endif
                    </h5>
                    <h5><span class="iconify" data-icon="carbon-chart-evaluation"></span> Evaluations-Link:
                        @if ($current_session_infos->evaluation_url) aktiviert
                        @else deaktiviert @endif
                    </h5>
                    <h5>
                        <span class="iconify" data-icon="mdi-crosshairs-question"></span> Anzahl korrekter Antworten:
                        @if($current_session_infos->show_number_correct_answers == 0)
                            nicht angezeigt
                        @else
                            angezeigt
                        @endif
                    </h5>
                    <h5>
                        <span class="iconify" data-icon="bx:bx-bot"></span> Spielerbots:
                        @if($current_session_infos->use_bots == 1)
                            @if($current_session_infos->bot_difficulty == 1)
                                schwach
                            @elseif($current_session_infos->bot_difficulty == 2)
                                normal
                            @elseif($current_session_infos->bot_difficulty == 3)
                                stark
                            @elseif($current_session_infos->bot_difficulty == 4)
                                superstark
                            @endif
                            (für bis zu 10 Spielende)
                        @else
                            deaktiviert
                        @endif
                    </h5>

                    @if($gameModeType == 'classic')
                        <div>
                            <h5>
                                <span class="iconify" data-icon="emojione-monotone:level-slider" data-inline="false"></span> Schwierigkeitsgrad:
                                @if($current_session_infos->boss_health_option == 0.8) Leicht<br>
                                @elseif($current_session_infos->boss_health_option == 1) Normal<br>
                                @elseif($current_session_infos->boss_health_option == 1.2) Schwer<br>
                                @elseif($current_session_infos->boss_health_option == 3) Superschwer<br>
                                @else {{$current_session_infos->boss_health_option}}<br>
                                @endif
                            </h5>
                            <div>
                                @if($current_session_infos->show_character_selection == 0)
                                    <h5><span class="iconify" data-icon="bx:bx-donate-heart" data-inline="false"></span> Startleben: {{$current_session_infos->start_life_player_type_1}} </h5>
                                @else
                                    <h5><span class="iconify" data-icon="bx:bx-donate-heart" data-inline="false"></span> Startleben: Verteidiger {{$current_session_infos->start_life_player_type_1}}, Angreifer {{$current_session_infos->start_life_player_type_2}}</h5>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if($gameModeType == 'classic')
                        <br>
                        @if($current_session_infos->session_option_rank == 0)
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:cross-mark-button" data-inline="false"></span>
                                Es werden keine Ranglisten angezeigt.
                            </div>
                        @elseif($current_session_infos->session_option_rank == 1)
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:white-heavy-check-mark" data-inline="false"></span>
                                Der Spielerrang wird auf den Mobilgeräten angezeigt.
                            </div>
                        @elseif($current_session_infos->session_option_rank == 2)
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:white-heavy-check-mark" data-inline="false"></span>
                                Eine Rangliste wird auf dem Projektor angezeigt.
                            </div>
                        @else
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:white-heavy-check-mark" data-inline="false"></span>
                                Eine Rangliste wird auf dem Projektor angezeigt.
                            </div>
                        @endif
                        @if($current_session_infos->show_badges == 0)
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:cross-mark-button" data-inline="false"></span>
                                Auf dem mobilen Endgerät werden keine Auszeichnungen angezeigt.
                            </div>
                        @else
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:white-heavy-check-mark" data-inline="false"></span>
                                Auf dem mobilen Endgerät werden die jeweiligen Auszeichnungen angezeigt.
                            </div>
                        @endif
                        @if($current_session_infos->use_points == 0)
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:cross-mark-button" data-inline="false"></span>
                                Den Studierenden werden keine Punkte berechnet.
                            </div>
                        @else
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:white-heavy-check-mark" data-inline="false"></span>
                                Den Studierenden werden Punkte berechnet.
                            </div>
                        @endif
                        @if($current_session_infos->show_story == 0)
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:cross-mark-button" data-inline="false"></span>
                                Es gibt keine Story.
                            </div>
                        @else
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:white-heavy-check-mark" data-inline="false"></span>
                                Es gibt eine Story, das Spiel wird am Projektor durch eine animierte Szene visualisiert.
                            </div>
                        @endif
                        @if($current_session_infos->show_character_selection == 0)
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:cross-mark-button" data-inline="false"></span>
                                Die Charakterauswahl wird nicht angezeigt.
                            </div>
                        @else
                            <div class="iconizedItem">
                                <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:white-heavy-check-mark" data-inline="false"></span>
                                Die Studierenden können zu Beginn einen Charakter auswählen.
                            </div>
                        @endif

                        {{--TODO REMOVE THIS BLOCK AND REPLACE WITH PROPER BACKEND LOGIC--}}
                        @if($current_session_infos->show_character_selection == 1 && $current_session_infos->show_story == 0)
                            <br>
                            <h5>Eine unzulässige Spieloption liegt vor: Die Charakterauswahl sollte bei deaktivierter Stroy auch deaktiviert werden, da ein Avatar angezeigt wird!</h5>
                        @endif
                        {{-- END REMOVE --}}
                    @else
                        @if($gameModeType == 'training')
                            <div>
                                <h5>
                                    <span class="iconify" data-icon="emojione-monotone:level-slider" data-inline="false"></span> Schwierigkeitsgrad:
                                    @if($current_session_infos->boss_health_option == 0.8) Leicht<br>
                                    @elseif($current_session_infos->boss_health_option == 1) Normal<br>
                                    @elseif($current_session_infos->boss_health_option == 1.2) Schwer<br>
                                    @elseif($current_session_infos->boss_health_option == 3) Superschwer<br>
                                    @else {{$current_session_infos->boss_health_option}}<br>
                                    @endif
                                </h5>
                            </div>
                            <br>
                            <div>
                                @if($current_session_infos->show_character_selection == 0)
                                    <div class="iconizedItem">
                                        <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:cross-mark-button" data-inline="false"></span>
                                        Die Charakterauswahl wird nicht angezeigt.
                                    </div>
                                @else
                                    <div class="iconizedItem">
                                        <span class="iconify iconify_small_text_mr" data-icon="emojione-monotone:white-heavy-check-mark" data-inline="false"></span>
                                        Die Studierenden können zu Beginn einen Charakter auswählen.
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <hr/>
        @if ($questionCount == 0)
            <a type='submit' class='btn btn-secondary disabled float-right shaded bordered bg_pf_correct' href='/projectorScanQR/{{$sessionId}}'>Session starten</a>
            <small class='form-text float-right marginRightSmall smallAlert'>Kann Spiel nicht starten, da keine Fragen vorhanden sind!</small>
        @else
            <a type='submit' class='btn btn-success float-right shaded bordered bg_pf_correct' href='/projectorScanQR/{{$sessionId}}' data-balloon="Erstellt eine Lobby, damit die Studierenden der Session beitreten können." data-balloon-pos="up" data-balloon-length='large'>
                <span class="iconify iconify_medium_text_mr" data-icon="mdi:qrcode-scan"></span>
                Session starten
            </a>
        @endif
        <br>
    </div>
@endsection
