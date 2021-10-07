@extends('mainParent')

@section('title')
    Off Course!
@endsection

@section('mainParentHead')
    <script src="{{URL::asset('js/boss/main.js')}}"></script>
    <script src="{{URL::asset('js/boss/gameManagerBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/gameManagerMinimal.js')}}"></script>
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
        @if($isGame == 1)
            <img src="{{ URL::asset('images/noStoryHUD.png')}}" class="noStoryHUDimage">
            <img src="{{ URL::asset('images/bossHUDBarBoss.png')}}" class="healthBarBoss noStory" id="healthBarBoss">
            <img src="{{ URL::asset('images/bossHUDBarBossLightIndicator.png')}}" class="healthBarBoss delayed noStory" id="healthBarBossDelayed">
            <img src="{{ URL::asset('images/bossHUDBarCharacter.png')}}" class="healthBarCharacter noStory" id="healthBarCharacter">
            <img src="{{ URL::asset('images/bossHUDBarCharacterLightIndicator.png')}}" class="healthBarCharacter delayed noStory" id="healthBarCharacterDelayed">
        @endif

        @if($isGame == 1)
            <div class="bossLifeDisplay noStory">
                {{__('projectorGameOver.questions')}}: <span id="bossLife"></span>/<span id="bossLifeTotal"></span>
            </div>
            <div class="characterLifeDisplay noStory">
                {{__('projectorGameOver.participants')}}: <span id="studentsLeft"> </span>/<span id="students"> </span>
            </div>
        @endif

        @if($isGame == 1)
            <div class="timerDisplay noStory" id="timeDisplay">
                <span id="end_time_min"> </span>:<span id="end_time_sec"> </span>
            </div>
        @else
            <div class="timerDisplay noStory big" id="timeDisplay">
                <span id="end_time_min"> </span>:<span id="end_time_sec"> </span>
            </div>
        @endif
    </div>

    <button class="close" data-balloon="{{__('projectorGameOver.cancel_session')}}" data-balloon-pos="left" onclick="stopGame(0)" style="z-index: 70; position: absolute; right: 0.25em; top: 0.05em; padding-top: 10px; font-size: 3rem;">
        <span aria-hidden="true">&times;</span>
    </button>

    <!-- This makes it possbile to use the vars in our js-->
    <div id="sessionId" data-field-id="{{$sessionId}}"></div>
    <div id="gameModeType" data-field-id="{{($isGame == false) ? "noGame" : "classic"}}"></div>
    <div id="showStory" data-field-id="false"></div>
    <div id="isGame" data-field-id="{{$isGame}}"></div>

@endsection

@section('mainParentSuffix')

@endsection
