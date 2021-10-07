@extends('mainEditor')

@section('title')
    Statistiken
@endsection

@section('HideEditorSidenav')hidden @endsection
@section('HeaderBoxHeadline') {{$sessionName}} @endsection

@section('mainEditorHead')
    <!-- Plotly is licenced under MIT license --->
    <script src="//cdn.plot.ly/plotly-1.57.1.min.js"></script>
@endsection

@section('mainEditorBody')
    <div class="wrapperOuterMargin">
        <h3 style="display: inline-block">Statistiken</h3>
        <div class="float-right">
            <a class='btn btn-info shaded bordered bg_pf_primary' data-balloon="Session erneut spielen" data-balloon-pos="up" href='/editorCopySession/{{$sessionId}}' role='button'>
                <span class="iconify iconify_medium" data-icon="mdi:replay"></span>
            </a>
            <a class="btn btn-danger shaded bordered bg_pf_wrong" data-balloon="Kehrt zur Startseite zurück" data-balloon-pos="up" href="{{ route('editorHome')}}" role="button">
                <span class="iconify iconify_medium_text_mr" data-icon="mdi:exit-to-app" data-rotate="180deg"></span>
                Beenden
            </a>
        </div>
        <hr/>
        <br>
        <div class="statisticsGrid">
            <div class="statisticsCardWrapperLarge">
                <div class="overflowEllipsis">
                    <h4>Allgemeine Informationen</h4>

                    <div class="editorGridHalf">
                        <div>
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:account-group"></span>
                            Anzahl der Teilnehmenden: {{$playerCount}}
                            @if($gameModeType == 'teams')
                                (Blau: {{$teamsData['studentCountBlue']}}, Rot: {{$teamsData['studentCountRed']}})
                            @endif
                            <br>
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:account-multiple-check"></span>
                            Ausgeschieden: @if ($gameModeType == 'classic') {{$playerCount - $survivingPlayers}}
                                            @else niemand konnte ausscheiden
                                            @endif
                            <br>
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:checkbox-multiple-marked-outline"></span>
                            Beantwortete Fragen: {{$numberAnsweredQuestions}}
                            <br>
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi:dice-multiple-outline"></span> Spielmodus:
                            @if ($gameModeType == 'teams') Teams
                            @elseif ($gameModeType == 'noGame') Time Attack
                            @elseif ($gameModeType == 'training') Training
                            @else Boss Battle @if ($showStory == 0) (Keine Story)@endif
                            @endif
                            <br>
                            <span class="iconify iconify_medium_text_mr" data-icon="bx:bx-bot"></span> Spielerbots:
                            @if($useBots == 1)
                                @if($botDifficulty == 1)
                                    schwach
                                @elseif($botDifficulty == 2)
                                    normal
                                @elseif($botDifficulty == 3)
                                    stark
                                @elseif($botDifficulty == 4)
                                    superstark
                                @endif
                                ({{(($botCount) != 1 ? $botCount . " Bots" : $botCount . " Bot")}})
                            @else
                                deaktiviert
                            @endif
                        </div>
                        <div>
                            @if($gameModeType == 'classic')
                                @if($bossHealthLeft <= 0)
                                    <span class="iconify iconify_medium_text_mr" data-icon="mdi:bookmark-check"></span>
                                    Ergebnis: Bossgegner besiegt!
                                @else
                                    <span class="iconify iconify_medium_text_mr" data-icon="mdi:bookmark-remove"></span>
                                    Ergebnis: Bossgegner nicht besiegt.
                                    <br>
                                    <span class="iconify iconify_medium_text_mr" data-icon="mdi:heart-multiple-outline"></span>
                                    Verbleibende Bosslebenspunkte: {{$bossHealthLeft}}
                                @endif
                                <br>
                                <span class="iconify iconify_medium_text_mr" data-icon="emojione-monotone:level-slider" data-inline="false"></span>
                                Schwierigkeitsgrad:
                                @if($bossHealthOption == 0.8) Leicht
                                @elseif($bossHealthOption == 1) Normal
                                @elseif($bossHealthOption == 1.2) Schwer
                                @else {{$bossHealthOption}}
                                @endif
                                <br>
                                <span class="iconify iconify_medium_text_mr" data-icon="bx:bx-donate-heart" data-inline="false"></span> Startleben: Verteidiger {{$startLifePlayerType1}}, Angreifer {{$startLifePlayerType2}}
                                <br>
                            @elseif($gameModeType == 'teams')
                                <span class="iconify iconify_medium_text_mr" data-icon="mdi:podium-gold"></span>
                                Gewinner:
                                @if(round($teamsData['pointsBlue']) > round($teamsData['pointsRed']))
                                    Team Blau
                                @elseif(round($teamsData['pointsBlue']) < round($teamsData['pointsRed']))
                                    Team Rot
                                @else
                                    Gleichstand
                                @endif
                                (Blau: {{round($teamsData['pointsBlue'])}}, Rot: {{round($teamsData['pointsRed'])}})
                                <br>
                            @endif
                            <span class="iconify iconify_medium_text_mr" data-icon="mdi-timer-outline"></span>
                            Eingestellte Spieldauer: {{$sessionDuration}}:00 Minuten <br>
                            @if ($actualSessionDuration != "unknown")
                                <span class="iconify iconify_medium_text_mr" data-icon="bx-bx-timer"></span>
                                Tatsächliche Spieldauer: {{$actualSessionDuration}} Minuten
                            @endif
                        </div>
                        <br>
                    </div>
                    <h4 style="color: var(--pf_wrong_dark);">Schwierigste Fragen:</h4> 1. {{$hardestQuestions[0]}} <br> 2. {{$hardestQuestions[1]}}<br><br>
                    <h4 style="color: var(--pf_correct_dark);">Leichteste Fragen:</h4> 1. {{$easiestQuestions[0]}} <br> 2. {{$easiestQuestions[1]}}<br><br>
                </div>
            </div>

            <div class="statisticsCardWrapperSmall" id="plot0"></div>
            <div class="statisticsCardWrapperSmall" id="plot2"></div>
            <div class="statisticsCardWrapperLarge" id="plot1" @if ($gameModeType == 'training' || $gameModeType == 'nogame') style="display: none;" @endif></div>
        </div>
        <!-- This makes it possible to use the vars in our js-->
        <div id="sessionId" data-field-id="{{$sessionId}}" ></div>

        <div id="percentCorrect" data-field-id="{{$percentCorrect}}" ></div>
        <div class="sessionItem">
                <h4>Fragen sortiert nach Erfolgsquote</h4>
                @foreach ($questions as $question)
                    <div class="statisticQuestionOuter collapsible">
                        <div class="statisticQuestion">
                            <div>
                                @if ($question['image'] != Null)
                                    (Bildfrage)
                                @endif
                                {{$question['text']}}
                                @if ($question['image'] != Null)
                                    <div class='editorGridDescription'>
                                        <img src='/storage/{{$question['image']}}' id='imagePreview' class='editorPreviewImage'>
                                    </div>
                                @endif
                            </div>
                            <div>
                                @if($question['percentage'] == -1)
                                    Diese Frage wurde nicht beantwortet
                                @else
                                    <span class="iconify iconify_medium_text_mr" data-icon="vaadin:pie-chart"></span>
                                    Erfolgsquote: {{$question['percentage']}}% <br>
                                    <span class="iconify iconify_medium_text_mr" data-icon="mdi:checkbox-multiple-marked-outline"></span>
                                    Frage ausgeteilt: {{$question['numberAnswered']}}x <br>

                                    @empty($question['duration'])
                                        <span class="iconify iconify_medium_text_mr" data-icon="mdi:timer-outline"></span>
                                        Zu dieser Frage existiert keine Zeitangabe.
                                    @else
                                        <span class="iconify iconify_medium_text_mr" data-icon="mdi:timer-outline"></span>
                                        Durchschnittliche Antwortzeit: {{$question['duration']}}s
                                    @endempty
                                @endif
                            </div>
                        </div>
                        {{-- bar of barchart at bottom (-1 is the value for questions that weren´t answered, so add 0.1 to fit range from 0 to 10) --}}
                        <div class="statisticQuestionBar" percentage="{{$question['percentage'] / 10 + 0.1}}" style="width:calc({{$question['percentage']}}% * 0.99 + 1%)"></div>
                    </div>
                    <div class="innerCollapsible">
                        <div class="statisticAnswer bordered shaded">
                            @foreach($question['answers'] as $answer)
                                <div>
                                    @if($answer->correct == 0)
                                        <h5 style="color:var(--pf_wrong_dark)">Antwort {{$loop->index + 1}}:</h5>
                                    @else
                                        <h5 style="color:var(--pf_correct_dark); font-weight: bold;">Antwort {{$loop->index + 1}}:</h5>
                                    @endif
                                    {{$answer->answer_text}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                @empty ($questions)
                    <div class='textCentered'>
                        <h5>keine Fragen vorhanden</h5>
                    </div>
                @endempty
            </div>
            <hr/>
            <a data-balloon="Lädt kumulierte Sessiondaten herunter" data-balloon-pos="up" class="btn btn-info shaded bordered bg_pf_primary" href="/exportStatistic/{{$sessionId}}" role="button">
                <span class="iconify iconify_medium_text_mr" data-icon="mdi:download" data-inline="false"></span>
                Sessiondaten
            </a>
            <a data-balloon="Lädt kumulierte Statistiken für jede Einzelperson herunter" data-balloon-pos="up" class="btn btn-info shaded bordered bg_pf_primary" href="/exportStatisticUser/{{$sessionId}}" role="button">
                <span class="iconify iconify_medium_text_mr" data-icon="mdi:download" data-inline="false"></span>
                Nutzerdaten
            </a>
            <div class="float-right">
                <a class='btn btn-info shaded bordered bg_pf_primary' data-balloon="Session erneut spielen" data-balloon-pos="up" href='/editorCopySession/{{$sessionId}}' role='button'>
                    <span class="iconify iconify_medium" data-icon="mdi:replay"></span>
                </a>
                <a class="btn btn-danger shaded bordered bg_pf_wrong" data-balloon="Kehrt zur Startseite zurück" data-balloon-pos="up" href="{{ route('editorHome')}}" role="button">
                    <span class="iconify iconify_medium_text_mr" data-icon="mdi:exit-to-app" data-rotate="180deg"></span>
                    Beenden
                </a>
            </div>
        </div>
        <div class="fillerBottom"></div>
    </div>
    <br>

    <script>
        let percentCorrect = $('#percentCorrect').data("field-id");
        let layout2 = {
            title: 'Verhältnis korrekter zu <br> falschen Antworten',
            autosize: false,
            width: 400,
            height: 400,
            margin: {
                l: 50,
                r: 50,
                b: 0,
                t: 50,
                pad: 20
            },
        };
        var data2 = [{
            values: [100-percentCorrect, percentCorrect],
            labels: ['Falsch beantwortet', 'Richtig beantwortet'],
            marker: {colors: ['rgb(175, 15, 74)', 'rgb(30, 160, 14)'], line: ['width:', '1']},
            type: 'pie',
            textposition: 'outside'
        }];
        Plotly.newPlot('plot2', data2, layout2, {displayModeBar: false});
        sessionId = $('#sessionId').data("field-id");
        $.ajax({
            type: 'get',
            url: '/getGraphData/'+sessionId,
            success: function (data) {
                console.log(data);
                let data0 = [
                    {
                        values: data[0],
                        labels: ['0-25%','25-50%','50-75%','75-100%'],
                        type: 'pie',
                        name: ' ',
                        textposition: 'outside'
                    }
                ];
                let layout0 = {
                    autosize: false,
                    width: 400,
                    height: 400,
                    margin: {
                        l: 100,
                        r: 50,
                        b: 0,
                        t: 50,
                        pad: 20
                    },
                    title: 'Verteilung der Teilnehmenden <br> nach Erfolgsquote',
                    yaxis: {
                        title: 'Anzahl richtig beantworteter Fragen je Student',
                        fixedrange: true
                    }
                };
                let layout1 = {
                    autosize: false,
                    widht: 800,
                    height: 400,
                    title: 'Anzahl ausgeschiedener Spieler nach Anzahl beantworteter Fragen',
                    xaxis: {
                        title: 'Anzahl Fragen',
                        fixedrange: true
                    },
                    yaxis: {
                        title: 'Anzahl ausgeschiedener Spieler',
                        fixedrange: true
                    }
                };
                Plotly.newPlot('plot0', data0, layout0, {displayModeBar: false});
                let data1 = [
                    {
                        x: data[2],
                        y: data[1],
                        mode: 'lines+markers',
                        name: 'spline',
                        //text: ['tweak line smoothness<br>with "smoothing" in line object', 'tweak line smoothness<br>with "smoothing" in line object', 'tweak line smoothness<br>with "smoothing" in line object', 'tweak line smoothness<br>with "smoothing" in line object', 'tweak line smoothness<br>with "smoothing" in line object', 'tweak line smoothness<br>with "smoothing" in line object'],
                        //line: {shape: 'spline'},
                        type: 'scatter',
                    }
                ];
                Plotly.newPlot('plot1', data1, layout1, {displayModeBar: false});

                if ( ! data[1].length && ! data[2].length){ // if we dont have data here, we should not display this plot
                    document.getElementById("plot1").style.display = "none";
                }
            }
        });
    </script>

@endsection
