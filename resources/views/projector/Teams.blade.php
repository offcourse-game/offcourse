@extends('mainParent')

@section('title')
    Off Course!
@endsection

@section('mainParentHead')
    <script src="{{URL::asset('js/boss/main.js')}}"></script>
    <script src="{{URL::asset('js/boss/gameManagerBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/gameManagerTeams.js')}}"></script>
    <script src="{{URL::asset('js/boss/characterBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/animatorBase.js')}}"></script>

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/projector/Boss.css')}}">
@endsection

@section('mainParentBody')
    <div class="" id="foregroundFader"></div>
    <div class="slider noStory" side="left">
        <img src="{{ URL::asset('images/logos/HalfLeft.png')}}" class="sliderLogo" side="left" alt="logo left">
    </div>
    <div class="slider noStory" side="right">
        <img src="{{ URL::asset('images/logos/HalfRight.png')}}" class="sliderLogo" side="right" alt="logo right">
    </div>

    <div class="">
        <img src="{{ URL::asset('images/logos/full.png')}}" class="mainLogo">
    </div>

    <div class="noStoryHUDwrapper">
        <img src="{{ URL::asset('images/teamsHUD.png')}}" class="noStoryHUDimage">

        {{--total bar chart width: 890px + 12px white space in middle--}}
        <img src="{{ URL::asset('images/teamsHUDBarBlue.png')}}" class="healthBarTeams blue" id="healthBarBlue">
        <img src="{{ URL::asset('images/teamsHUDBarRed.png')}}" class="healthBarTeams red" id="healthBarRed">

        <span class="teamNameDisplay red">{{__('projectorGameOver.teamRed')}}</span>
        <span class="teamNameDisplay blue">{{__('projectorGameOver.teamBlue')}}</span>

        <div class="teamScoreDisplay blue">
            <span id="teamScoreBlue"></span>
        </div>
        <div class="teamScoreDisplay red">
            <span id="teamScoreRed"></span>
        </div>

        <div class="timerDisplay teams" id="timeDisplay">
            <span id="end_time_min"> </span>:<span id="end_time_sec"> </span>
        </div>
    </div>

    <img src="{{ URL::asset('images/characterPairTeams.png')}}" class="teamStoryImage">

    <button class="close" data-balloon="{{__('projectorGameOver.cancel_session')}}" data-balloon-pos="left" onclick="stopGame(0)" style="z-index: 70; position: absolute; right: 0.25em; top: 0.05em; padding-top: 10px; font-size: 3rem;">
        <span aria-hidden="true">&times;</span>
    </button>

    <!-- This makes it possbile to use the vars in our js-->
    <div id="sessionId" data-field-id="{{$sessionId}}" ></div>
    <div id="gameModeType" data-field-id="teams"></div>
    <div id="showStory" data-field-id="false"></div>
    <div id="isGame" data-field-id="true"></div>

@endsection

@section('mainParentSuffix')

@endsection
