@extends('mainEditor')

@section('title')
    Sessionverwaltung
@endsection

@section('HideEditorSidenav')hidden @endsection

@section('mainEditorBody')
    <div class="wrapperOuterMargin">
        <h3 style="display: inline-block">Ungespielte Sessions</h3>
        <a class="btn btn-info float-right shaded bordered bg_pf_primary" href="/editorSessionSetup" role="button">
           <span class="iconify iconify_medium_text_mr" data-icon="mdi:note-plus-outline"></span> Session erstellen
        </a>
        <hr/>
        <br>

        @empty($session_infos->toArray())
            Keine Session angelegt, oder bereits alle Sessions gespielt! <br><br>
            Erstelle eine neue Session und importiere in der Fragenzusammenfassung Fragen einer bereits gespielte Session oder gib neue Fragen ein.
        @endempty

        {{-- Displaying Multiple Sessions --}}
        @foreach ($session_infos as $session_info)
            <div class="sessionItem">
                <div class ="editorHomeSessionFrameUnplayed">
                    <h4 style='display: inline-block'>
                        @if ($session_info->is_teams == 1)<span data-balloon="Teams" data-balloon-pos="left">
                        <span class="iconify" data-icon="ant-design:team-outlined" data-inline="false"></span>
                        @elseif ($session_info->is_game == 0 && $session_info->is_training == 1)<span data-balloon="Time Attack" data-balloon-pos="left">
                        <span class="iconify" data-icon="zmdi:eye-off" data-inline="false"></span>
                        @elseif ($session_info->is_training == 1)<span data-balloon="Training" data-balloon-pos="left">
                        <span class="iconify" data-icon="fa-solid:running" data-inline="false"></span>
                        @else<span data-balloon="Boss Battle" data-balloon-pos="left">
                        <span class="iconify" data-icon="mdi:sword-cross" data-inline="false"></span>@endif
                        {{$session_info->session_name}}
                    </h4>
                    <span>
                        <span data-balloon="erstellt am: {{explode(' ',$session_info->session_creation_date)[0]}}" data-balloon-pos="up">
                            <span class="iconify iconify_medium" data-icon="mdi:square-edit-outline"></span> {{explode(' ',$session_info->session_creation_date)[0]}}
                            <small>{{explode(' ',$session_info->session_creation_date)[1]}}</small>
                        </span>
                    </span>
                    <span>
                        <span data-balloon="Anzahl Fragen: {{$session_info->number_questions}}" data-balloon-pos="up">
                            <span class="iconify iconify_medium" data-icon="mdi:checkbox-multiple-marked-outline"></span> {{$session_info->number_questions}}
                        </span>
                    </span>
                    <div>
                        <a class="btn btn-danger shaded bordered bg_pf_wrong" data-balloon="Session löschen" data-balloon-pos="up" href='/deleteSession/{{$session_info->session_id}}' role="button">
                            <span class="iconify iconify_medium" data-icon="mdi:delete-forever-outline"></span>
                        </a>
                        <a class="btn btn-info shaded bordered bg_pf_primary" data-toggle="modal" data-target=".modal" href='#' data-balloon="Session teilen" data-balloon-pos="up" role="button" onclick='getSessionId({{$session_info->session_id}})'>
                            <span class="iconify iconify_medium" data-icon="mdi:share"></span>
                        </a>
                        <a class="btn btn-info shaded bordered bg_pf_primary" data-balloon="Session bearbeiten" data-balloon-pos="up" href='/editorQuestionSummary/{{$session_info->session_id}}' role='button'>
                            <span class="iconify iconify_medium" data-icon="mdi:square-edit-outline"></span>
                        </a>
                        <a class="btn btn-success shaded bordered bg_pf_correct" data-balloon="Session starten" data-balloon-pos="up" href='/editorGameStartup/{{$session_info->session_id}}' role='button'>
                            <span class="iconify iconify_medium" data-icon="mdi:play-circle-outline"></span>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach

        <br><br><br>
        <h3 style="display: inline-block">Gespielte Sessions</h3>
        <hr/>
        <br>

        @empty($played_session_infos->toArray())
            Keine Session gespielt!
        @endempty

        {{-- Displaying Multiple Sessions --}}

        @php
            $last_session_name = "";
        @endphp
        <div>
        @foreach ($played_session_infos as $played_session_info)

            @if($last_session_name != $played_session_info->session_name)
                </div>
                <button class="collapsible sessionItem">

                    <h4>
                        <span class="iconify iconify_small" data-icon="mdi:folder-multiple-outline"></span>
                        {{$played_session_info->session_name}}

                        <a onclick="event.stopPropagation();" data-balloon="Lädt kumulierte Statistiken für jede Einzelperson herunter" data-balloon-pos="up" data-balloon-length='large' class="btn btn-info shaded bordered bg_pf_primary float-right" href="/exportStatisticUserCum/{{$played_session_info->session_id}}" role="button">
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:download" data-inline="false"></span>
                            Nutzerdaten
                        </a>
                        <a onclick="event.stopPropagation();" data-balloon="Lädt kumulierte Sessiondaten herunter" data-balloon-pos="up" class="btn btn-info shaded mr-1 bordered bg_pf_primary float-right" href="/exportStatisticCum/{{$played_session_info->session_id}}" role="button">
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:download" data-inline="false"></span>
                            Sessiondaten
                        </a>
                    </h4>
                </button>
                <div class="innerCollapsible">
            @endif

            @php
                $last_session_name = $played_session_info->session_name;
            @endphp

            <div class='sessionItem'>
                <div class ="editorHomeSessionFrame">
                    <h4 style='display: inline-block'>
                        @if ($played_session_info->is_teams == 1)<span data-balloon="Teams" data-balloon-pos="left">
                        <span class="iconify" data-icon="ant-design:team-outlined" data-inline="false"></span>
                        @elseif ($played_session_info->is_game == 0 && $played_session_info->is_training == 1)<span data-balloon="Time Attack" data-balloon-pos="left">
                        <span class="iconify" data-icon="zmdi:eye-off" data-inline="false"></span>
                        @elseif ($played_session_info->is_training == 1)<span data-balloon="Training" data-balloon-pos="left">
                        <span class="iconify" data-icon="fa-solid:running" data-inline="false"></span>
                        @else<span data-balloon="Boss Battle" data-balloon-pos="left">
                        <span class="iconify" data-icon="mdi:sword-cross" data-inline="false"></span>@endif
                        {{$played_session_info->session_name}}
                    </h4>
                    <span>
                        <span data-balloon="gespielt am: {{explode(" ",$played_session_info->session_time)[0]}}" data-balloon-pos="up">
                            <span class="iconify iconify_medium" data-icon="mdi:presentation-play"></span> {{explode(" ",$played_session_info->session_time)[0]}}
                            <small>{{explode(' ',$played_session_info->session_time)[1]}}</small>
                        </span>
                    </span>
                    <span>
                        @if($played_session_info->boss_life <= 0)
                            <span data-balloon="Bossgegner wurde besiegt" data-balloon-pos="up">
                                <span class="iconify iconify_medium" style="color: var(--pf_correct)" data-icon="mdi:bookmark-check"></span>
                            </span>
                        @else
                            <span data-balloon="Bossgegner wurde nicht besiegt" data-balloon-pos="up">
                                <span class="iconify iconify_medium" style="color: var(--pf_wrong)" data-icon="mdi:bookmark-remove"></span>
                            </span>
                        @endif

                        <span data-balloon="Anzahl Teilnehmer: {{$played_session_info->player_count}}" data-balloon-pos="up">
                            <span class="iconify iconify_medium" data-icon="mdi:account-group"></span> {{$played_session_info->player_count}}
                        </span>
                        <span data-balloon="Anzahl Fragen: {{$played_session_info->number_questions}}" data-balloon-pos="up">
                            <span class="iconify iconify_medium" data-icon="mdi:checkbox-multiple-marked-outline"></span> {{$played_session_info->number_questions}}
                        </span>
                    </span>
                    <div>
                        <a class='btn btn-danger shaded bordered bg_pf_wrong' data-balloon="Session löschen" data-balloon-pos="up" href='/deleteSession/{{$played_session_info->session_id}}' role='button'>
                            <span class="iconify iconify_medium" data-icon="mdi:delete-forever-outline"></span>
                        </a>
                        <a class='btn btn-info shaded bordered bg_pf_primary' data-balloon="Session erneut spielen" data-balloon-pos="up" href='/editorCopySession/{{$played_session_info->session_id}}' role='button'>
                            <span class="iconify iconify_medium" data-icon="mdi:replay"></span>
                        </a>
                        <a class='btn btn-success shaded bordered bg_pf_correct' data-balloon="Statistiken anzeigen" data-balloon-pos="up" href='/editorStatistic/{{$played_session_info->session_id}}' role='button'>
                            <span class="iconify iconify_medium" data-icon="mdi:book-open-outline"></span>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>

    <!-- modal to share session -->
    <div class="modal fade bd-modal-lg shaded bordered" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content" style="padding: 15px;">
                <div style="display: inline-block;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.75rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="mx-auto" display="block">
                    <img style="max-width: 20%; padding: 0.5em;" src="{{ URL::asset('images/characterPair_lowRes.png')}}" alt="">
                    <h4>Session teilen<hr></h4>
                    <p> Der aktuelle Stand der Fragen und Einstellungen einer Session werden einer anderer Person zur Verfügung gestellt.</p>
                    <p>Geben Sie hierzu die GWDG-Kennung oder die E-Mail-Adresse an:</p>
                    <input type="text" id="profUidOrEmail" placeholder="GWDG-Kennung" style="width: 18em;">
                    <br>
                    <button class="btn btn-info shaded bordered bg_pf_primary" style="margin-top: 1em;" onclick='shareSession()'>
                        <span class="iconify iconify_medium_text_mr" data-icon="mdi:share"></span>
                        Session teilen
                    </button>
                    <small class="form-text smallAlert" style="visibility: hidden;" id="smallCheckAlert">Die Person konnte nicht gefunden werden!</small>
                </div>
            </div>
        </div>
    </div>

<script>
    let sessionId;

    // make sure that the error is hidden after we close a modal
    $(".modal").on("hidden.bs.modal", function () {
        document.getElementById("smallCheckAlert").style.visibility = "hidden";
    });

    // save the sessionId for the ajax call later
    function getSessionId(getSessionId){
        sessionId = getSessionId;
    }

    // make the ajax call
    function shareSession() {
        let profUidOrEmail = document.getElementById("profUidOrEmail").value;

        $.ajax({
            type: 'get',
            url: '/shareSession/' + sessionId,
            data: {"profUidOrEmail": profUidOrEmail},
            success: function (res) {
               if (res == "-1"){
                   document.getElementById("smallCheckAlert").style.visibility = "visible";
               }else{
                   $('.modal').modal('toggle'); // hide the modal, we are successful
               }
            }
        });
    }
</script>
@endsection
