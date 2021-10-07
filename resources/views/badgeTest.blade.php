@extends('mainMobile')

@section('title')
    DEBUG BADGE TESTER
@endsection

@section('mainMobileHead')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/mobile/Achievement.css')}}">
@endsection

@section('mainMobileHeaderNavbar')
    <a class="langChoose float-right @if(app()->getLocale() == "en") font-weight-bold @endif" href="{{ url('lang/en') }}">EN</a>
    <span class="langChoose float-right">/</span>
    <a class="langChoose float-right @if(app()->getLocale() == "de") font-weight-bold @endif" href="{{ url('lang/de') }}">DE</a>
@endsection

@section('mainMobileBody')
    <div class="centerHeadingWrapper">
        <h2 style="color: var(--pf_correct);" align="center"> <b> DEBUG TEST PAGE FOR ALL BADGES</b></h2>
    </div>


    @component('layouts.flower') @endcomponent



    <div class="heartContainer">
        @for($i = 0; $i < 7; $i ++)
            @if(5 > $i)
                <img src="/images/heart.png" class="heartImg" alt="heart">
            @else
                <img src="/images/heart.png" class="heartImg" alt="heart" gray>
            @endif
        @endfor
    </div>

    <img id="secondimg" src="/images/characterGray.png" class="imageEndScreenIdle" first="false" alt="character">

    <div class="playerDescriptor">
        Spielername:
        <h3>KEVIN PROPELLER</h3>
    </div>

    <div class="scoreFlagWrapper">
        <div class="scoreFlag" size="small"><b>50</b>/61<br><small>RICHTIG</small></div>
        <div class="scoreFlag"><b>524</b>/524<br><small>RANG</small></div>
        <div class="scoreFlag" size="small"><b>420</b>pts<br><small>PUNKTE</small></div>
    </div>

    <div class="wrapperOuter">
        <br>
        <h4>Deine Auszeichnungen: (min res 350px equivalent horizontal)</h4>

        @foreach($achievements as $achievement)
            @component('layouts.badge')
                @slot('color') {{$achievement->color}} @endslot
                @slot('animId') 0 @endslot
                @slot('badgeImageClass') {{$achievement->badgeImageClass}} @endslot
                @slot('title') {{ __($achievement->title)}} @endslot
                @slot('formattedText') {!! __($achievement->text, ['text_display_parameter' => $achievement->textDisplayParameter]) !!} @endslot
                @slot('starCount') {{$achievement->starCount}} @endslot
                @slot('hidden') @endslot
            @endcomponent
        @endforeach
    </div>

    <div class="wrapperOuter">
        <hr/>
        <a class="btn btn-success shaded bordered bg_pf_correct" href="https://survey3.gwdg.de/index.php?r=survey/index&sid=67537&lang=de&pid=0" role="button">
            Evaluation starten
        </a>
        <a class="btn btn-primary shaded bordered bg_pf_primary" href="/mobileStart" role="button" style="float: right">
            Neustart
        </a>
        <div class="fillerBottom"></div>
    </div>

    <script src='//cdnjs.cloudflare.com/ajax/libs/gsap/3.2.4/gsap.min.js'></script>
    <script src="{{URL::asset('js/boss/flowerTimeline.js')}}"></script>
    <script>
        //sunflowerTL.pause();
        //sunflowerTL.timeScale(1);
        sunflowerTL.play();
    </script>

@endsection
