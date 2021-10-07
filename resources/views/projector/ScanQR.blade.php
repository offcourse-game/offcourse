@extends('mainParent')

@section('title')
    Lobby ({{$sessionId}})
@endsection

@section('mainParentHead')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/HenrikButtons.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/editor/Editor.css')}}">
@endsection

@section('mainParentBody')
    <div class="wrapperOuterMargin">
        <img src="{{ URL::asset('images/logos/full.png')}}" class="headingOFClarge" alt="OFFCOURSE">
        <hr class="hrShort">
        <div class="text-center">
            <h4>{{__('projectorScanQR.session_number')}}</h4>
            <h1 class="emphasizeHeading yellow">{{$sessionId}}</h1>
        </div>

        <div class="flexCenter">
            @if ($lang == "de")
                <img src="{{ URL::asset('images/scanQRnote.png')}}" class="scanQRnote" alt="OFFCOURSE">
            @else
                <img src="{{ URL::asset('images/scanQRnote_en.png')}}" class="scanQRnote" alt="OFFCOURSE">
            @endif

            <img src="https://chart.apis.google.com/chart?chs=350x350&cht=qr&chld=L|1&chl=https://beta.gamify.uni-goettingen.de/mobileStart/{{$sessionId}}" class="centerQRcode" alt="qrCode">

            <div style="width: 334px;">
                <h4>{{__('projectorScanQR.question_count')}}:</h4>
                <h4 class="emphasizeHeading yellow">{{$questionCount}}</h4>
                <h4>{{__('projectorScanQR.duration')}}:</h4>
                <h4 class="emphasizeHeading yellow">{{$sessionDuration}} {{__('projectorScanQR.minutes')}}</h4>
                <h4>{{__('projectorScanQR.level')}}:</h4>
                <h4 class="emphasizeHeading yellow">
                    @if($bossHealthOption == 0.8) {{__('projectorScanQR.easy')}}
                    @elseif($bossHealthOption == 1) {{__('projectorScanQR.normal')}}
                    @elseif($bossHealthOption == 1.2) {{__('projectorScanQR.hard')}}
                    @else {{$bossHealthOption}}
                    @endif
                </h4>

                <div class="fillerBottom"></div>
                <h4>{{__('projectorScanQR.participants')}}:</h4>
                @if($gameModeType != 'teams')
                    <h1 class="emphasizeHeading yellow"><span id="students"> </span></h1>
                @else
                    <h1 class="emphasizeHeading"><span style="color: var(--pf_primary_dark)" id="studentsBlue"> </span>-<span style="color: var(--pf_wrong_dark)" id="studentsRed"> </span></h1>
                @endif

            </div>
        </div>

        <div class="flexMaximize">
            <div>
                <a href="/editorGameStartup/{{$sessionId}}" class="henrikButton wrong" role="button">
                    <span class="iconify iconify_medium_text_mr" data-icon="mdi:exit-to-app" data-rotate="180deg"></span>
                    {{__('projectorScanQR.cancel')}}
                </a>
            </div>
            <div class="centerHeadingWrapper">
                <h1><span style="color: var(--pf_gray_medium)">https://</span><span>tiny.cc/ofc/</span><b style="color: var(--pf_yellow);">{{$sessionId}}</b></h1>
                <br>
                {{--<span class="credits">{!! __('projectorScanQR.credits') !!}</span>--}}
            </div>
            <div id="startButtonTooltip">
                <a class="henrikButton correct disabled" role="button" href="/game/{{$sessionId}}" id="gameStart">
                {{__('projectorScanQR.start_game')}}
                <span class="iconify iconify_medium_text_ml" data-icon="mdi:play-circle-outline"></span>
                </a>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // We use the boss Blade ajax call here, to have less code.
        // The downside is that we get some data we not need, but that should not be a problem.
        function doAjaxCall() {
            if('{{$gameModeType}}' != 'teams'){
                doBossAjaxCall({{$sessionId}});
            }else{
                doTeamsAjaxCall({{$sessionId}});
            }
        }
        setTimeout(doAjaxCall, 0); // Do the first call immediately

        /**
         * Gets data for classic|noGame|training mode and updates UI
         *
         * @return {void}
         */
        function doBossAjaxCall(sessionId) {
            $.ajax({
                type: 'get',
                url: '/getBossData/' + sessionId,
                success: function (data) {
                    console.log(data);
                    studentCount = data['studentCountLeftGray'] + data['studentCountLeftGold']
                    document.getElementById('students').innerText = studentCount;

                    if(studentCount < 1){
                        document.getElementById('gameStart').classList.add("disabled");
                        setStartButtonTooltip("{{__('projectorScanQR.start_game_tooltip_boss')}}", false);
                    }else{
                        document.getElementById('gameStart').classList.remove("disabled");
                        setStartButtonTooltip("", true);
                    }
                    setTimeout(doAjaxCall, 2000); // We wait 2 Seconds before doing the next call
                }
            });
        }

        /**
         * Gets data for team mode and updates UI
         *
         * @return {void}
         */
        function doTeamsAjaxCall(sessionId) {
            $.ajax({
                type: 'get',
                url: '/getTeamsData/' + sessionId,
                success: function (data) {
                    console.log(data);
                    studentCountBlue = data['studentCountBlue'];
                    studentCountRed = data['studentCountRed'];
                    //document.getElementById('students').innerText = String(studentCountBlue + studentCountRed);
                    document.getElementById('studentsBlue').innerText = String(studentCountBlue);
                    document.getElementById('studentsRed').innerText = String(studentCountRed);

                    if(studentCountBlue < 1 || studentCountRed < 1){
                        document.getElementById('gameStart').classList.add("disabled");
                        setStartButtonTooltip("{{__('projectorScanQR.start_game_tooltip_teams')}}", false);
                    }else{
                        document.getElementById('gameStart').classList.remove("disabled");
                        setStartButtonTooltip("", true);
                    }
                    setTimeout(doAjaxCall, 2000); // We wait 2 Seconds before doing the next call
                }
            });
        }

        /**
         * Changes display of tooltip for start button
         * 
         * @param string text  text to display
         * @param boolean hidden  hide or unhide tooltip
         */
        function setStartButtonTooltip(text, hidden) {
            if(hidden == false){
                document.getElementById('startButtonTooltip').setAttribute("data-balloon", text);
                document.getElementById('startButtonTooltip').setAttribute("data-balloon-pos", "left");
            }else{
                document.getElementById('startButtonTooltip').removeAttribute("data-balloon");
                document.getElementById('startButtonTooltip').removeAttribute("data-balloon-pos");
            }
        }
    </script>

@endsection
