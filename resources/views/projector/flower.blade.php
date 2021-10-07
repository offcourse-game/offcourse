 @extends('mainParent')

@section('title')
    Off Course!
@endsection

@section('mainParentHead')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/projector/Boss.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/projector/Flower.css')}}">
@endsection

@section('mainParentBody')
    <div id="foregroundFader"></div>
    <div class="slider" side="left">
        <img src="{{ URL::asset('images/logos/HalfLeft.png')}}" class="sliderLogo" side="left" alt="logo left">
    </div>
    <div class="slider" side="right">
        <img src="{{ URL::asset('images/logos/HalfRight.png')}}" class="sliderLogo" side="right" alt="logo right">
    </div>

    <div class="gameSceneWrapper" >
        <div class="blueSkyBackground" id="blueSkyBackground">
            <img src="{{ URL::asset('images/backgroundHills.png')}}" class="imageMountainsBackground" id="" alt="hills">
            @component('layouts.flower') @endcomponent

            <div id="characterParent" style="position: relative; top: -40%"></div> <!-- the character will spawn here -->
            <div id="beautificationParent"></div> <!-- flowers will spawn here -->
            <div id="bossNotificationParent" class="bossNotificationListWrapper"></div>
        </div>

        <div class="imageGrassBackground" style="background-image: url( {{ URL::asset('images/grassTile.png')}} )" alt="grass"></div>

        <div class="gameSceneInfo">
            <div class="bossInfoBack">
                <img src="{{ URL::asset('images/trainingHUD.png')}}">
                <img src="{{ URL::asset('images/bossHUDBarCharacter.png')}}" class="healthBarBoss" id="healthBarBoss">
                <img src="{{ URL::asset('images/bossHUDBarCharacterLightIndicator.png')}}" class="healthBarBoss delayed" id="healthBarBossDelayed">

                <div class="timerDisplay" id="timeDisplay">
                    <span style="display: inline-block; text-align: right;" id="end_time_min"> </span>:<span style="width: 40px; display: inline-block;" id="end_time_sec"> </span>
                </div>

                <div class="bossLifeDisplay">
                    {{__('projectorGameOver.progress')}} <span id="bossLife"></span>/<span id="bossLifeTotal"></span>
                </div>
            </div>
        </div>

        <button class="close" data-balloon="{{__('projectorGameOver.cancel_session')}}" data-balloon-pos="left" onclick="stopGame(0)" style="z-index: 70; position: absolute; right: 0.25em; top: 0.05em; padding-top: 10px; font-size: 3rem;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- This makes it possbile to use the vars in our js-->
    <div id="sessionId" data-field-id="{{$sessionId}}"></div>
    <div id="gameModeType" data-field-id="training"></div>
    <div id="showStory" data-field-id="true"></div>
    <div id="isGame" data-field-id="true"></div>
    <div id="isTraining" data-field-id="true"></div>

    <!-- Translation strings for js-->
    <div id="flower_threeQuatersLife" string="{{__('projectorGameNotification.flower_threeQuatersLife')}}"></div>
    <div id="flower_halfLife" string="{{__('projectorGameNotification.flower_halfLife')}}"></div>
    <div id="flower_quarterLife" string="{{__('projectorGameNotification.flower_quarterLife')}}"></div>
    <div id="30seconds" string="{{__('projectorGameNotification.30seconds')}}"></div>
    <div id="60seconds" string="{{__('projectorGameNotification.60seconds')}}"></div>
    <div id="120seconds" string="{{__('projectorGameNotification.120seconds')}}"></div>
    <div id="win" string="{{__('Achievement.win')}}"></div>
    <div id="flower_lose" string="{{__('projectorGameNotification.flower_lose')}}"></div>
@endsection

@section('mainParentSuffix')
    <script src='//cdnjs.cloudflare.com/ajax/libs/gsap/3.5.1/gsap.min.js'></script>

    <script src="{{URL::asset('js/boss/main.js')}}"></script>
    <script src="{{URL::asset('js/boss/gameManagerBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/gameManagerTraining.js')}}"></script>
    <script src="{{URL::asset('js/boss/characterBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/characterTraining.js')}}"></script>
    <script src="{{URL::asset('js/boss/animatorBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/animatorTraining.js')}}"></script>
    <script src="{{URL::asset('js/boss/notificationManagerBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/notificationManagerTraining.js')}}"></script>
    <script src="{{URL::asset('js/boss/flowerTimeline.js')}}"></script>

@endsection
