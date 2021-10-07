@extends('mainMobile')

@section('title')
    {{__('Achievement.end')}}
@endsection

@section('mainMobileHead')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/mobile/Achievement.css')}}">
@endsection

@section('mainMobileBody')
    <!-- Slider main container -->
    <div class="swiper-container">
        <!-- Additional required wrapper -->
        <div class="swiper-wrapper">

            @if($gameModeType != 'noGame')
                @if (! empty($bestPlayers))
                    <!-- =============== leaderboard slide =============== -->
                    <div class="swiper-slide">
                        <div class="fullScreenExpander">
                            <div style="padding: 15px">
                                <h4>{{__('Achievement.ranking')}} <div class="swipe-right-arrow float-right">
                                    <span class="iconify" data-icon="dashicons:arrow-right-alt2"></span></div>
                                    <hr>
                                </h4>
                                @isset ($bestPlayers)
                                    <img class="imageCrown" src="/images/crown_gold.png">
                                @endisset
                                <ul class="list-group">
                                    @foreach ($bestPlayers as $bestPlayer)
                                        @if($studentId == $bestPlayer->student_id)
                                            <li class="leaderboardItemWrapper playerHighlight" gameModeType="{{$gameModeType}}" playerTypeId="{{$bestPlayer->player_type_id}}">
                                        @elseif($loop->iteration == 1)
                                            <li class="leaderboardItemWrapper gold" gameModeType="{{$gameModeType}}" playerTypeId="{{$bestPlayer->player_type_id}}">
                                        @elseif($loop->iteration == 2)
                                            <li class="leaderboardItemWrapper silver" gameModeType="{{$gameModeType}}" playerTypeId="{{$bestPlayer->player_type_id}}">
                                        @elseif($loop->iteration == 3)
                                            <li class="leaderboardItemWrapper bronze" gameModeType="{{$gameModeType}}" playerTypeId="{{$bestPlayer->player_type_id}}">
                                        @else
                                            <li class="leaderboardItemWrapper" gameModeType="{{$gameModeType}}" playerTypeId="{{$bestPlayer->player_type_id}}">
                                        @endif
                                            <div class="leaderboardItem rank">
                                                {{$loop->iteration}}.
                                            </div>
                                            <div class="leaderboardItem">
                                                {{$bestPlayer->student_name}}
                                                @if($studentId == $bestPlayer->student_id)
                                                    ({{__('Achievement.you')}})
                                                @endif
                                                @if($sessionSettings->use_points)
                                                    <span>{{$bestPlayer->score}} {{__('Achievement.pts')}}</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="wrapperOuter">
                                <hr/>
                                @if ($sessionSettings->evaluation_url)
                                    <a class="btn btn-success shaded bordered bg_pf_correct" href="{{$sessionSettings->evaluation_url}}&pid={{$studentId}}" role="button">
                                    <span class="iconify" data-icon="ic:outline-feedback"></span> {{__('Achievement.eval')}}</a>
                                @endif
                                <a class="btn btn-primary shaded bordered bg_pf_primary" href="/mobileStart" role="button" style="float: right">
                                    <span class="iconify" data-icon="mdi:replay"></span> {{__('Achievement.new_game')}}
                                </a>
                                <div class="fillerBottom"></div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- =============== main slide =============== -->
                <div class="swiper-slide">
                    <div class="fullScreenExpander">
                        <div class="centerHeadingWrapper">
                            @if($gameModeType == 'teams')
                                @if(round($pointsBlue) > round($pointsRed))   {{--do comparison also with rounded values to match projector info display--}}
                                    <h2 style="color: var(--pf_primary_dark);" align="center"> <b> {{__('Achievement.win_blue')}}</b></h2>
                                @elseif(round($pointsBlue) < round($pointsRed))
                                    <h2 style="color: var(--pf_wrong_dark);" align="center"> <b> {{__('Achievement.win_red')}}</b></h2>
                                @else
                                    <h2 align="center"> <b> {{__('Achievement.tie')}}</b></h2>
                                @endif
                            @else
                                @if($gameWin == 1)
                                    <h2 style="color: var(--pf_correct);" align="center"> <b> {{__('Achievement.win')}}</b></h2>
                                @else
                                    <h2 style="color: var(--pf_wrong);" align="center"> <b> {{__('Achievement.lose')}}</b></h2>
                                @endif
                            @endif
                        </div>

                        {{--display up to seven hearts, fill gray if players lost heart or or use compact display mode for more than 7 start lifes--}}
                        @if($gameModeType == 'classic')
                            @if($studentStartLife <= 7)
                                <div class="heartContainer">
                                    @for($i = 0; $i < $studentStartLife; $i ++)
                                        @if($studentLife > $i)
                                            <img src="/images/heart.png" class="heartImg" alt="heart">
                                        @else
                                            <img src="/images/heart.png" class="heartImg" alt="heart" gray>
                                        @endif
                                    @endfor
                                </div>
                            @else
                                <div class="heartContainer compact">
                                    <span><img src="/images/heart.png" class="heartImg" alt="heart"> x {{$studentLife}}</span>
                                    /
                                    <span><img src="/images/heart.png" class="heartImg" alt="heart" gray> x {{$studentStartLife}}</span>
                                </div>
                            @endif
                        @endif

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div class="swipe-left-arrow">
                                @if(! empty($bestPlayers))
                                    <span class="iconify" data-height="2em" data-icon="dashicons:arrow-left-alt2" data-inline="false"></span>
                                @else
                                    <span class="iconify" data-height="2em" data-icon="dashicons:arrow-left-alt2" data-inline="false" visibility="hidden"></span>
                                @endif
                            </div>

                            @if ($gameModeType == 'classic' && $sessionSettings->show_story == 1)
                                @if($player_type_id == 1)
                                    <img src="/images/characterGray.png" class="imageEndScreenIdle" alt="character">
                                @else
                                    <img src="/images/characterGold.png" class="imageEndScreenIdle" alt="character">
                                @endif
                            @elseif ($gameModeType == 'training')
                                @if($player_type_id == 1)
                                    <img src="/images/training/characterHoneyBee.png" class="imageEndScreenIdle" alt="character">
                                @else
                                    <img src="/images/training/characterBumbleBee.png" class="imageEndScreenIdle" alt="character">
                                @endif
                            @elseif ($gameModeType == 'teams')
                                @if($player_type_id == 1)
                                    <img src="/images/characterTeamBlue.png" class="imageEndScreenIdle" alt="character">
                                @else
                                    <img src="/images/characterTeamRed.png" class="imageEndScreenIdle" alt="character">
                                @endif
                            @endif

                            <div class="swipe-right-arrow">
                                <span class="iconify" data-height="2em" data-icon="dashicons:arrow-right-alt2"></span>
                            </div>
                        </div>

                        <div class="playerDescriptor">
                            {{__('Achievement.playername')}}
                            @if ($gameModeType == 'teams')
                                @if($player_type_id == 1)
                                    <h3 style="color: var(--pf_primary_dark)">{{ $studentName }}</h3>
                                @else
                                    <h3 style="color: var(--pf_wrong_dark)">{{ $studentName }}</h3>
                                @endif
                            @else
                                <h3>{{ $studentName }}</h3>
                            @endif
                        </div>

                        <div class="scoreFlagWrapper">
                            <div class="scoreFlag" size="small"><b>{{$correctAnswered}}</b>/{{$numberAnswered}}<br><small>{{__('Achievement.correct_caps')}}</small></div>

                            @isset($studentRank)
                                @if($studentRank != -1)        {{--  -1 => setting disabled display nothing--}}
                                    <div class="scoreFlag"><b>{{$studentRank}}</b>/{{$numberPlayers}}<br><small>{{__('Achievement.rank_caps')}}</small></div>
                                @endif
                            @endisset

                            @if($score != -1 && $sessionSettings->use_points == 1)
                                <div class="scoreFlag" size="small"><b>{{$score}}</b>pts<br><small>{{__('Achievement.points_caps')}}</small></div>
                            @endif
                        </div>

                        @if ($showBadges == 1)
                            <div class="wrapperOuter">
                                @foreach($achievements as $achievement)
                                    @component('layouts.badge')
                                        @slot('color') {{$achievement->color}} @endslot
                                        @slot('animId') {{$loop->index}} @endslot
                                        @slot('badgeImageClass') {{$achievement->badgeImageClass}} @endslot
                                        @slot('title') {{__($achievement->title)}} @endslot
                                        @slot('formattedText') {!! __($achievement->text, ['text_display_parameter' => $achievement->textDisplayParameter]) !!} @endslot
                                        @slot('starCount') {{$achievement->starCount}} @endslot

                                        {{-- only hide rest to be able to see when debugging --}}
                                        @if($loop->index >= 3)
                                            @slot('hidden') hidden @endslot
                                        @else
                                            @slot('hidden') @endslot
                                        @endif
                                    @endcomponent
                                @endforeach

                            </div>
                        @endif
                        <div class="wrapperOuter">
                            <hr/>
                            @if ($sessionSettings->evaluation_url)
                                <a class="btn btn-success shaded bordered bg_pf_correct" href="{{$sessionSettings->evaluation_url}}&pid={{$studentId}}" role="button">
                                <span class="iconify" data-icon="ic:outline-feedback"></span> {{__('Achievement.eval')}}</a>
                            @endif
                            <a class="btn btn-primary shaded bordered bg_pf_primary" href="/mobileStart" role="button" style="float: right">
                                <span class="iconify" data-icon="mdi:replay"></span> {{__('Achievement.new_game')}}
                            </a>
                            <div class="fillerBottom"></div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- =============== question summary slide =============== -->
            <div class="swiper-slide">
                <div class="fullScreenExpander">
                    <div style="padding: 15px">
                        <h4><div class="swipe-left-arrow float-left">
                                @if($gameModeType != 'noGame')<span class="iconify" data-icon="dashicons:arrow-left-alt2"></span> @endif
                            </div>
                            {{__('Achievement.correct_answers')}}<hr>
                        </h4>
                        <div>
                            <div id="accordion">
                                @foreach ($answersQuestions as $answersQuestion)
                                    <div class='accordionHeader iconizedItem center noBalancer bordered shaded' id='question{{$answersQuestion->question_id}}' data-toggle='collapse' data-target='#answerCollapse{{$answersQuestion->question_id}}' aria-expanded='false' aria-controls='answerCollapse{{$answersQuestion->question_id}}'>
                                        <div class='accordionHeaderIcon' data-toggle='collapse' data-target='#answerCollapse{{$answersQuestion->question_id}}' aria-expanded='false' aria-controls='answerCollapse{{$answersQuestion->question_id}}'>
                                            <span class="iconify" data-icon="dashicons:arrow-right-alt2" data-inline="false"></span>
                                        </div>
                                        <h6 class='mb-0 overflowEllipsis'>
                                            @if ($answersQuestion->image_path != Null) {{__('Achievement.image_question')}}@endif
                                            {{$answersQuestion->question_text}}
                                        </h6>
                                        @if ($answersQuestion->correct ==1) <span class="unicodeIcon" style="color: var(--pf_correct)">✓</span>
                                        @else <span class="unicodeIcon" style="color: var(--pf_wrong); font-weight: bold;">✘</span>
                                        @endif
                                    </div>
                                    <div id='answerCollapse{{$answersQuestion->question_id}}' class='accordionCollapse collapse' aria-labelledby='question{{$answersQuestion->question_id}}' data-parent='#accordion'>
                                        <div class="accordionCollapse innerCollapse bordered shaded">
                                            <div class='editorGridRight'>
                                                @if ($answersQuestion->image_path != Null)
                                                    <div class='editorGridDescription' style="margin-bottom: 1em;">
                                                        <img src='/storage/{{$answersQuestion->image_path}}' id='imagePreview' class='editorPreviewImage'>
                                                    </div>
                                                @endif
                                                <div>
                                                    <ol style="margin:0; padding-left: 1.25em;">
                                                        {{-- connects every question to their answer--}}
                                                        @foreach ($answersQuestion->answers as $answer)
                                                            <li style="margin-bottom: 0.25em;">
                                                            @if($answer->correct == '1') <span style='color:var(--pf_correct)'><b>{{$answer->answer_text}}</b></span>
                                                            @else <span style='color:var(--pf_wrong)'>{{$answer->answer_text}}</span>@endif
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @empty ($answersQuestions)
                                    <div class='textCentered'>
                                        <h5>{{__('Achievement.no_question')}}</h5>
                                    </div>
                                @endempty
                            </div>
                        </div>
                    </div>
                    <div class="wrapperOuter"> <!-- Possible hack to have a better transition: style="margin-bottom: 13em;"-->
                        <hr/>
                        @if ($sessionSettings->evaluation_url)
                            <a class="btn btn-success shaded bordered bg_pf_correct" href="{{$sessionSettings->evaluation_url}}&pid={{$studentId}}" role="button">
                            <span class="iconify" data-icon="ic:outline-feedback"></span> {{__('Achievement.eval')}}</a>
                        @endif
                        <a class="btn btn-primary shaded bordered bg_pf_primary" href="/mobileStart" role="button" style="float: right">
                            <span class="iconify" data-icon="mdi:replay"></span> {{__('Achievement.new_game')}}
                        </a>
                        <div class="fillerBottom"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<script>
    // save the time when the user sees the page
    let opendAt;

    //delete the local storage
    localStorage.setItem('fastQuestions', null);

    if (document.hasFocus()) {
        // sometimes the first onfocus does not work
        console.log("focusclick");
        opendAt =  new Date();
    }

    window.onfocus = function() {
        console.log("focus");
        opendAt =  new Date();
    };

    // send the view time
    window.onblur = function(){
        console.log("blur");
        sendTime();
    };

    function sendTime (){
        let now = new Date();
        $.ajax({
            type: 'get',
            data: {viewTime: now - opendAt}, // viewTime in ms
            url: '/saveViewTime',
            success: function (res) {
                console.log("viewTime send");
            }
        });
    }

    window.onbeforeunload = function(){
        sendTime();
        $.ajax({
            type: 'get',
            async: false, // do we need that??
            url: '/deleteStudent',
            success: function (res) {
                console.log(res);
            }
        });
    }

    let mySwiper = new Swiper ('.swiper-container', {
        // Optional parameters
        //loop: true,
        @empty($bestPlayers)
            initialSlide: 0,
        @else
            initialSlide: 1,
        @endempty

        autoHeight: true,
        threshold: 10,      //swipes smaller than 10px will not swipe, so you can scroll down wihtout changing swipers

        // Navigation arrows
        navigation: {
            nextEl: '.swipe-right-arrow',
            prevEl: '.swipe-left-arrow',
        },

        keyboard: {
            enabled: true,
            onlyInViewport: true,
        },

  });

  // updates swiper height, when opening the accordion
  $('#accordion').on('shown.bs.collapse', function () {
      mySwiper.updateAutoHeight();

      console.log("shown: updated");
  })

  // updates swiper height, when closing the accordion
  $('#accordion').on('hidden.bs.collapse', function () {
      mySwiper.updateAutoHeight(300);

      console.log("hidden: updated");
  })
</script>
@endsection
