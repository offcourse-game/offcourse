{{-- does NOT derive from mainMobile parent, because special header design is required --}}
@extends('mainParent')

@section('title')
    {{__('question.question')}}
@endsection

@section('mainParentHead')
    <!-- This makes it possbile to use the vars in our js-->
    <div id="dataStorage" questionId="{{$questionId}}" gameModeType="{{$gameModeType}}"></div>
    <script src="{{URL::asset('js/question.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/mobile/Question.css')}}">
@endsection

@section('mainParentHeader')
    <div class="fillerTopEnlarged"></div>
    <div class="headerColoringTransparent navbarMobileEnlarged">
        <img src="{{ URL::asset('images/logos/lowRes.png')}}" class="centerImageHorizontal" alt="logo" height="22px">

        {{--circle timer--}}
        <div class="circleTimerOuter">
            <svg class="progress-ring" width="64" height="64">
                <circle class="progress-ring__circle" stroke="rgb(35, 35, 55)" stroke-width="2" fill="transparent" r="23" cx="32" cy="32"></circle>
                <circle class="progress-ring__circle" id="circleTimerMovable" stroke="rgb(175, 15, 74)" stroke-width="6" fill="rgb(245, 245, 235)" r="23" cx="32" cy="32"></circle>
                <circle class="progress-ring__circle" stroke="transparent" stroke-width="0" fill="rgb(245, 245, 235)" r="17" cx="32" cy="32"></circle>
            </svg>
            <div class="circleTimerNumber">
                <span id ="countdown">30</span>
            </div>
        </div>

        <div class="heartContainerWrapper">
            @if ($gameModeType == 'training')
                Training
            @elseif ($gameModeType == 'classic')
                <div class='fillQuestionHeartImage'>
                    <img src="{{asset('images/heart.png')}}" class='imageHeart' id='animatedBackgroundImage' alt='HEART'>
                    <div class='fillQuestionHeartImage animatedHeart' id='animatedHeartImage'>
                        <img src="{{asset('images/heart.png')}}" class='imageHeart' alt='HEART'>
                    </div>
                </div>
                <b id="heartCounter">x {{$studentLife}}</b>
            @endif

            <span class="imageSpearContainer" id="streakInfoOuter" style="visibility: hidden"><span id="streakInfo">15 x</span><img src="{{asset('images/flame.png')}}" class='imageSpear'></span>

            <div class="smallNotificationCornerPlaceholder" id="smallNotificationCornerPlaceholder" >
                <span id="actualStreakSpaner" style="display:none;"></span>
                <span id="actualStreakSpanerText" style="display:none;">{{__('question.row')}}</span>
                <span id="fastQuestionsSpanerText" style="display:none;">{{__('question.fast')}}</span>
                <span id="fasterQuestionsSpanerText" style="display:none;">{{__('question.faster')}}</span>
                <span id="fastestQuestionsSpanerText" style="display:none;">{{__('question.fastest')}}</span>
            </div>
        </div>
    </div>
@endsection

@section('mainParentBody')
    <div class="mobileWrapper">
        <div class="wrapperOuter">
            <div class="questionBox" id="questionBox">
                @if ($numberCorrectAnswers != -1)
                    <span class="badge badge-pill numberCorrectAnswers">
                        <span class="iconify" data-icon="ic:outline-check"></span>
                        <span id="numberCorrectAnswers">{{$numberCorrectAnswers}} {{__('question.correct')}}</span>
                    </span>
                @endif
                <span id="questionText">{{$questionText}}</span>
            </div>

            {{-- question image --}}
            <div id="imagespanhere">
                @if ($imagePath != Null)
                    <img src='storage/{{$imagePath}}' class='questionImage' id='questionImage'>
                @endif
            </div>

            {{-- answers --}}
            @foreach($answers as $answer)
                <div id='answerCheckbox'>
                    <input type='checkbox' id='{{$loop->index}}' name='rGroup' onclick='check({{$loop->index}})' value='1' autocomplete='off'/>
                    <label class='answerLabel' id='answerLabel{{$loop->index}}' animID='{{$loop->index}}' for='{{$loop->index}}'>
                        <div class='answerLabelAnimateFiller' id='animateFiller{{$loop->index}}'> {{$answer->answer_text}} </div>
                    </label>
                </div>
            @endforeach

            <div class="questionFooterFiller"></div>
        </div>
    </div>
@endsection

@section('mainParentFooter')
    <div class="mobileWrapper">
        <div class="questionFooter">
            @if ($showStory == 1 && $gameModeType == 'classic')
                <span id="bossHpDisplay">100%</span>
                <img src="{{URL::asset('images/boss_lowRes.png')}}" class="footerImages">
                <img src="{{URL::asset('images/characterGray_lowRes.png')}}" class="footerImages">
                <span id="characterHpDisplay">x 1</span>
            @elseif($gameModeType == 'teams')
                <!-- dummy spans to not break js if story is not shown-->
                <span id="bossHpDisplay" style="display: none;"></span>
                <span id="characterHpDisplay" style="display: none;"></span>
                @if($playerTypeId == 1)
                    <img src="{{URL::asset('images/characterTeamBlue.png')}}" class="footerImages" style="transform: scaleX(-1);">
                    <span style="color: var(--pf_primary_dark);"> {{__('question.team_blue')}}</span>
                    <img src="{{URL::asset('images/characterTeamBlue.png')}}" class="footerImages">
                @else
                    <img src="{{URL::asset('images/characterTeamRed.png')}}" class="footerImages">
                    <span style="color: var(--pf_wrong_dark)"> {{__('question.team_red')}}</span>
                    <img src="{{URL::asset('images/characterTeamRed.png')}}" class="footerImages" style="transform: scaleX(-1);">
                @endif
            @else
                <!-- dummy spans to not break js if story is not shown-->
                <span id="bossHpDisplay" style="display: none;"></span>
                <span id="characterHpDisplay" style="display: none;"></span>
            @endif
            <button onclick='sendAnswers("{{$questionId}}","{{$studentId}}")' type="button" id="sendAnswers" disabled="" class="btn btn-info float-right shaded pulseShadow bg_pf_primary nextQuestion">
                <img src="{{URL::asset('images/arrowRight.png')}}" class="nextQuestionIcon">
            </button>
        </div>
    </div>
@endsection
