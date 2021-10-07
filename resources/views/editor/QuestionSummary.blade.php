@extends('mainEditor')

@section('title')
    Fragenpool
@endsection

@section('activeSideNavItem2') active @endsection
@section('sessionId'){{$sessionId}}@endsection {{-- yields the sessionId to the sidebar--}}
@section('HeaderBoxHeadline') {{$sessionName}} @endsection

@section('mainEditorBody')
    <div class="wrapperOuterMargin">
        <h3>Fragenpool</h3>
        <hr/>
        <!-- button panel at top, buttons at bottom showed if page is filled and user needs to scroll -->
        <a class="btn btn-info shaded bordered bg_pf_primary" href="/editorQuestion/{{$sessionId}}">
            <span class="iconify iconify_medium_text_mr" data-icon="mdi:playlist-plus"></span>
            Fragen hinzufügen
        </a>
        <button class="btn btn-info shaded bordered bg_pf_primary" data-toggle="modal" data-target=".bd-modal-lg">
            <span class="iconify iconify_medium_text_mr" data-icon="entypo:merge" data-rotate="180deg"></span>
            Fragen importieren
        </button>

        @if (count($answersQuestions) >= 12)
            <!-- continue button only shown when page is full -->
            <a class="btn btn-success float-right shaded bordered bg_pf_correct" href="/editorGameSettings/{{$sessionId}}">Sessionoptionen bearbeiten
                <span class="iconify iconify_medium_text_ml" data-icon="mdi:arrow-right-thick"></span>
            </a>
        @endif
        <br>
        <br>
        <div class="sessionItem">
            <div id="accordion">
                @foreach ($answersQuestions as $answersQuestion)
                    <div class='card'>
                        <div class='card-header editorQuestionSummaryCard' id='question{{$answersQuestion['question']->question_id}}'>
                            <h3 class='mb-0 overflowEllipsis'>
                                <button class='btn btn-link editorAccordionHeading overflowEllipsis' data-toggle='collapse' data-target='#answerCollapse{{$answersQuestion['question']->question_id}}' aria-expanded='true' aria-controls='answerCollapse{{$answersQuestion['question']->question_id}}'>
                                    {{$loop->iteration}}
                                    @if ($answersQuestion['question']->image_path != Null) (Bildfrage)@endif
                                    {{$answersQuestion['question']->question_text}}
                                </button>
                            </h3>
                            <div>
                                <a class="btn btn-danger shaded bordered bg_pf_wrong" data-balloon="Frage löschen" data-balloon-pos="up" href='/deleteQuestion/{{$sessionId}}/{{$answersQuestion['question']->question_id}}' role="button">
                                <span class="iconify iconify_medium" data-icon="mdi:delete-forever-outline"></span>
                                </a>
                                <a class="btn btn-primary shaded bordered bg_pf_primary" data-balloon="Frage bearbeiten" data-balloon-pos="up" href='/editorQuestion/{{$sessionId}}/{{$answersQuestion['question']->question_id}}' role="button">
                                    <span class="iconify iconify_medium" data-icon="mdi:square-edit-outline"></span>
                                </a>
                            </div>
                        </div>
                        <div id='answerCollapse{{$answersQuestion['question']->question_id}}' class='collapse' aria-labelledby='question{{$answersQuestion['question']->question_id}}' data-parent='#accordion'>
                            <div class='card-body'>
                                <div class='editorGridRight'>
                                    <div>
                                        <ol>
                                            {{-- connects every question to their answer--}}
                                            @foreach ($answersQuestion['answers'] as $answer)
                                                <li>
                                                @if($answer->correct == '1') <span style='color:var(--pf_correct); font-weight: bold;'>{{$answer->answer_text}}</span>
                                                @else <span style='color:var(--pf_wrong)'>{{$answer->answer_text}}</span>@endif
                                                </li>
                                                <br>
                                            @endforeach
                                        </ol>
                                    </div>
                                    @if ($answersQuestion['question']->image_path != Null)
                                        <div class='editorGridDescription'>
										<h5>Bild zur Frage:</h5>
                                        <img src='/storage/{{$answersQuestion['question']->image_path}}' id='imagePreview' class='editorPreviewImage'>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                @empty ($answersQuestions)
                    <div class='textCentered'>
					    <h5>keine Fragen vorhanden</h5>
                    </div>
                @endempty
            </div>
        </div>
        <hr/>
        @if (count($answersQuestions) >= 12)
            <!-- buttons only shown second time if screen is full with answers and user needs to scroll-->
            <a class="btn btn-info shaded bordered bg_pf_primary" href="/editorQuestion/{{$sessionId}}">
                <span class="iconify iconify_medium_text_mr" data-icon="mdi:playlist-plus"></span>
                Fragen hinzufügen
            </a>
            <button class="btn btn-info shaded bordered bg_pf_primary" data-toggle="modal" data-target=".bd-modal-lg">
                <span class="iconify iconify_medium_text_mr" data-icon="entypo:merge" data-rotate="180deg"></span>
                Fragen importieren
            </button>
        @endif
        <!-- continue button always shown at bottom -->
        <a class="btn btn-success float-right shaded bordered bg_pf_correct" href="/editorGameSettings/{{$sessionId}}">
            Sessionoptionen bearbeiten
            <span class="iconify iconify_medium_text_ml" data-icon="mdi:arrow-right-thick"></span>
        </a>
        <div class="fillerBottom"></div>
    </div>


    <!-- modal to import questions from other session -->
    <div class="modal fade bd-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="padding: 15px">
                @if(count($session_infos) > 1)
                    <h4>Fragen importieren aus ungespielter Session<hr></h4>
                    @foreach ($session_infos as $session_info)
                        <div class="sessionItem">
                            <div class ="editorImportSessionFrameUnplayed">
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
                                    @if ($session_info->session_id == $sessionId) <span style="color: var(--pf_wrong); font-size: 80%;">Gerade in Bearbeitung</span>@endif
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
                                <button class='btn btn-success float-right shaded bordered bg_pf_correct target'
                                    data-balloon="Fragen importieren" data-balloon-pos="up" id='import{{$session_info->session_id}}'
                                    onclick='importSession({{$sessionId}}, {{$session_info->session_id}})' role='button'>
                                    <span class="iconify iconify_medium" data-icon="entypo:merge" data-rotate="180deg"></span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif

                <h4>Fragen importieren aus gespielter Session<hr></h4>

                {{-- Displaying Multiple Sessions --}}
                @empty($played_session_infos->toArray())
                    Keine Session gespielt!
                @else
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

                                </h4>
                            </button>
                            <div class="innerCollapsible">
                        @endif

                        @php
                            $last_session_name = $played_session_info->session_name;
                        @endphp

                        <div class='sessionItem'>
                            <div class ="editorImportSessionFrame">
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
                                <button class='btn btn-success float-right shaded bordered bg_pf_correct target'
                                    data-balloon="Fragen importieren" data-balloon-pos="up" id='import{{$played_session_info->session_id}}'
                                    onclick='importSession({{$sessionId}}, {{$played_session_info->session_id}})' role='button'>
                                    <span class="iconify iconify_medium" data-icon="entypo:merge" data-rotate="180deg"></span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                    </div>
                @endempty
            <div class="modalItem">
                <button id="cancelButton" class="btn btn-danger float-right shaded bordered bg_pf_wrong" data-dismiss="modal">
                    <span class="iconify iconify_medium_text_mr" data-icon="mdi:cancel"></span>
                    Schließen
                </button>
            </div>
        </div>
    </div>

<script>
    // target and source are the sessionIds for the import
    function importSession(target, source) {
        console.log(target,source);

        // The user should not be able to import a session accidentally twice
        var buttons = document.getElementsByClassName("target");
        // use the call function because the return from documentGetElementByClassName is a HTML Collection.
        // A normal foreach function doesnt work out here
        Array.prototype.forEach.call(buttons, function(button) {
            button.className = "disabled btn btn-secondary float-right shaded bordered bg_pf_correct target";
            button.disabled = true;
        });

        var cancelButton = document.getElementById("cancelButton");
        cancelButton.className = "disabled btn btn-danger float-right shaded bordered bg_pf_wrong";
        cancelButton.disabled = true;

        $.ajax({
            type: 'get',
            url: '/importSession',
            data: {target: target, source: source},
            success: function (res) {
               console.log(res);
                window.location.href= '/editorQuestionSummary/'+target;
            }
        });
    }
</script>
@endsection
