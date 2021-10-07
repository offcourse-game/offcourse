@extends('mainParent')

@section('title')
    Off Course!
@endsection

@section('mainParentHead')
    <script src="{{URL::asset('js/boss/main.js')}}"></script>
    <script src="{{URL::asset('js/boss/gameManagerBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/gameManagerBossBattle.js')}}"></script>
    <script src="{{URL::asset('js/boss/characterBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/characterBossBattle.js')}}"></script>
    <script src="{{URL::asset('js/boss/animatorBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/animatorBossBattle.js')}}"></script>
    <script src="{{URL::asset('js/boss/notificationManagerBase.js')}}"></script>
    <script src="{{URL::asset('js/boss/notificationManagerBossBattle.js')}}"></script>

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/projector/Boss.css')}}">
@endsection

@section('mainParentBody')
    <div class="" id="foregroundFader"></div>
    <div class="slider" side="left">
        <img src="{{ URL::asset('images/logos/HalfLeft.png')}}" class="sliderLogo" side="left" alt="logo left">
    </div>
    <div class="slider" side="right">
        <img src="{{ URL::asset('images/logos/HalfRight.png')}}" class="sliderLogo" side="right" alt="logo right">
    </div>

    <div class="gameSceneWrapper" >
        <div class="blueSkyBackground" id="blueSkyBackground">
            <img src="{{ URL::asset('images/backgroundMountains.png')}}" class="imageMountainsBackground" id="" alt="mountains">
            <br>
            <img src="{{ URL::asset('images/treeFlat.png')}}" class="imageTree" id="tree" alt="tree">
            <div class="imageBoss imageBossFall imageBossTransitioner" id="bossImg">
                <img src="{{ URL::asset('images/boss.png')}}" id="innerBossImg" class="fullSize" alt="boss">
                <div class="bossTopAnchor">
                    <img src="{{ URL::asset('images/bones0.png')}}" class="imageBones" posID="0" id="bonesImg0" alt="boss" style="opacity: 0">
                    <img src="{{ URL::asset('images/bones1.png')}}" class="imageBones" posID="1" id="bonesImg1" alt="boss" style="opacity: 0">
                    <img src="{{ URL::asset('images/bones2.png')}}" class="imageBones" posID="2" id="bonesImg2" alt="boss" style="opacity: 0">
                </div>
            </div>
            <div class="floatRight animCloud">
                <img src="{{ URL::asset('images/lightning.png')}}" class="imageLightning" style="visibility: hidden" id="lightning" alt="lightning">
                <img src="{{ URL::asset('images/cloud.png')}}" class="imageCloud" id="cloud" alt="cloud">
            </div>

            <div id="characterParent"></div> <!-- the character will spawn here -->
            <div id="spearParent"></div> <!-- the spears will spawn here -->
            <div id="beautificationParent"></div> <!-- flowers will spawn here -->
            <div id="bossNotificationParent" class="bossNotificationListWrapper"></div>
        </div>

        <div class="imageGrassBackground" style="background-image: url( {{ URL::asset('images/grassTile.png')}} )" alt="grass"></div>

        <div class="gameSceneInfo">
            <div class="bossInfoBack">
                <img src="{{ URL::asset('images/bossHUD.png')}}">
                <img src="{{ URL::asset('images/bossHUDBarBoss.png')}}" class="healthBarBoss" id="healthBarBoss">
                <img src="{{ URL::asset('images/bossHUDBarBossLightIndicator.png')}}" class="healthBarBoss delayed" id="healthBarBossDelayed">
                <img src="{{ URL::asset('images/bossHUDBarCharacter.png')}}" class="healthBarCharacter" id="healthBarCharacter">
                <img src="{{ URL::asset('images/bossHUDBarCharacterLightIndicator.png')}}" class="healthBarCharacter delayed" id="healthBarCharacterDelayed">

                <div class="timerDisplay" id="timeDisplay">
                    <span style="display: inline-block; text-align: right;" id="end_time_min"> </span>:<span style="width: 40px; display: inline-block;" id="end_time_sec"> </span>
                </div>

                <div class="bossLifeDisplay">
                    <span id="bossLife"></span>/<span id="bossLifeTotal"></span>
                </div>
                <div class="characterLifeDisplay">
                    <span id="studentsLeft"> </span>/<span id="students"> </span>
                </div>
            </div>
        </div>

        <button class="close" data-balloon="{{__('projectorGameOver.cancel_session')}}" data-balloon-pos="left" onclick="stopGame(0)" style="z-index: 70; position: absolute; right: 0.25em; top: 0.05em; padding-top: 10px; font-size: 3rem;">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <!-- This makes it possbile to use the vars in our js-->
    <div id="sessionId" data-field-id="{{$sessionId}}"></div>
    <div id="gameModeType" data-field-id="classic"></div>
    <div id="showStory" data-field-id="true"></div>
    <div id="isGame" data-field-id="true"></div>
    <div id="isTraining" data-field-id="false"></div>

    <!-- Translation strings for js-->
    <div id="boss_threeQuatersLife" string="{{__('projectorGameNotification.boss_threeQuatersLife')}}"></div>
    <div id="boss_halfLife" string="{{__('projectorGameNotification.boss_halfLife')}}"></div>
    <div id="boss_quarterLife" string="{{__('projectorGameNotification.boss_quarterLife')}}"></div>
    <div id="fiveStudents" string="{{__('projectorGameNotification.fiveStudents')}}"></div>
    <div id="tenStudents" string="{{__('projectorGameNotification.tenStudents')}}"></div>
    <div id="twentyStudents" string="{{__('projectorGameNotification.twentyStudents')}}"></div>
    <div id="fiftyStudents" string="{{__('projectorGameNotification.fiftyStudents')}}"></div>
    <div id="hundredStudents" string="{{__('projectorGameNotification.hundredStudents')}}"></div>
    <div id="win" string="{{__('Achievement.win')}}"></div>
    <div id="boss_lose" string="{{__('projectorGameNotification.boss_lose')}}"></div>
@endsection

@section('mainParentSuffix')

@endsection
