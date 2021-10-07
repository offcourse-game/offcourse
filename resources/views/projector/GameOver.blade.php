@extends('mainParent')

@section('title')
    {{__('projectorGameOver.session_over')}}
@endsection

@section('mainParentHead')
<!-- confetti.js -->
<script src="{{URL::asset('js/confetti.min.js')}}"></script>

<!--editor stylesheet-->
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/editor/Editor.css')}}">

@endsection

@section('mainParentBody')
<canvas id="confetti" style="z-index: -1;"></canvas> <!--This will span the confetti -->
    <!-- have this ugly styling here to make the confetti work -->
    <div class="wrapperOuterMarginGameOver">
        <div class="centerHeadingWrapper">
            {{-- @extends('title') --}}
            @if($gameModeType == 'teams')
                @if(round($pointsBlue) > round($pointsRed))     {{--do comparison also with rounded values to match info display--}}
                    <h2 align="center">
                        <b style="color: var(--pf_primary_dark);"> {{__('Achievement.win_blue')}}</b>
                        <b>(</b><b style="color: var(--pf_primary_dark);">{{round($pointsBlue)}}</b><b>-</b><b style="color: var(--pf_wrong_dark);">{{round($pointsRed)}}</b><b>)</b>
                    </h2>
                @elseif (round($pointsBlue) < round($pointsRed))
                    <h2 align="center">
                        <b style="color: var(--pf_wrong_dark);"> {{__('Achievement.win_red')}}</b>
                        <b>(</b><b style="color: var(--pf_primary_dark);">{{round($pointsBlue)}}</b><b>-</b><b style="color: var(--pf_wrong_dark);">{{round($pointsRed)}}</b><b>)</b>
                    </h2>
                @else
                    <h2 align="center">
                        <b> {{__('Achievement.tie')}}</b>
                        <b>(</b><b style="color: var(--pf_primary_dark);">{{round($pointsBlue)}}</b><b>-</b><b style="color: var(--pf_wrong_dark);">{{round($pointsRed)}}</b><b>)</b>
                    </h2>
                @endif
            @elseif($gameWin == 1)
                <h2 style="color: var(--pf_correct);" align="center"> <b> {{__('Achievement.win')}}</b></h2>
            @else
                <h2 style="color: var(--pf_wrong);" align="center"> <b> {{__('Achievement.lose')}}</b></h2>
            @endif
        </div>

        <div class="centerBigWrapper">
            <div class="projectorGrid">
                <div class="secondthirdplace">
                    @isset($bestPlayers[1])
                        <div style="height: 3em;">
                            2. {{$bestPlayers[1]->student_name}}
                            {{($bestPlayers[1]->is_bot == true ? "🤖" : "")}}
                        </div>
                        @if ($showStory == 1 || $gameModeType == 'training')
                            <img id="secondimg" src="/images/{{$bestPlayers[1]->player_type_image}}.png" class="imageEndScreenIdle" first="false" alt="character">
                        @endif
                        @if($usePoints)
                            <div style="font-weight: bold;"> {{$bestPlayers[1]->score}} {{__('projectorGameOver.points')}}<br></div>
                        @endif
                        <hr>
                        <span style="color: var(--pf_correct);">✔</span> {{__('projectorGameOver.correct')}} {{$bestPlayers[1]->correctAnsweredQuestions}}/{{$bestPlayers[1]->answeredQuestions}}<br>

                        @isset($bestPlayers[1]->stat_data)
                            <span class="iconify" data-icon="octicon:flame" data-inline="false" style="color: #fb3600"></span> {{__('projectorGameOver.longest_streak')}} {{$bestPlayers[1]->stat_data->best_streak}} <br>
                            <span class="iconify" data-icon="ic:round-flash-on" data-inline="false" style="color: #f5a100ff;"></span> {{__('projectorGameOver.answer_time')}} {{$bestPlayers[1]->stat_data->avg_time}}s <br>
                            @if($gameModeType == 'classic')
                                <span style="color: var(--pf_wrong);">❤</span> {{__('projectorGameOver.life_left')}} {{$bestPlayers[1]->stat_data->student_life}}
                            @endif
                        @endisset
                    @endisset
                </div>

                <div class="firstplace">
                    @isset($bestPlayers[0])
                        <div style="height: 3em; font-weight: bold;">
                            1. {{$bestPlayers[0]->student_name}}
                            {{($bestPlayers[0]->is_bot == true ? "🤖" : "")}}
                        </div>
                        @if ($showStory == 1 || $gameModeType == 'training')
                            <img class="imageCrown" src="/images/crown_gold.png">
                            <img id="firstimg" src="/images/{{$bestPlayers[0]->player_type_image}}.png" class="imageEndScreenIdle" alt="character">
                        @endif

                        @if($usePoints)
                            <div style="font-weight: bold;">{{$bestPlayers[0]->score}} {{__('projectorGameOver.points')}}<br></div>
                        @endif
                        <hr>
                        <span style="color: var(--pf_correct);">✔</span> {{__('projectorGameOver.correct')}} {{$bestPlayers[0]->correctAnsweredQuestions}}/{{$bestPlayers[0]->answeredQuestions}}<br>

                        @isset($bestPlayers[0]->stat_data)
                            <span class="iconify" data-icon="octicon:flame" data-inline="false" style="color: #fb3600"></span> {{__('projectorGameOver.longest_streak')}} {{$bestPlayers[0]->stat_data->best_streak}} <br>
                            <span class="iconify" data-icon="ic:round-flash-on" data-inline="false" style="color: #f5a100ff;"></span> {{__('projectorGameOver.answer_time')}} {{$bestPlayers[0]->stat_data->avg_time}}s <br>
                            @if($gameModeType == 'classic')
                                <span style="color: var(--pf_wrong);">❤</span> {{__('projectorGameOver.life_left')}} {{$bestPlayers[0]->stat_data->student_life}}
                            @endif
                        @endisset
                    @endisset
                </div>

                <div class="secondthirdplace">
                    @isset($bestPlayers[2])
                        <div style="height: 3em;">
                            3. {{$bestPlayers[2]->student_name}}
                            {{($bestPlayers[2]->is_bot == true ? "🤖" : "")}}
                        </div>
                        @if ($showStory == 1 || $gameModeType == 'training')
                            <img id="secondimg" src="/images/{{$bestPlayers[2]->player_type_image}}.png" class="imageEndScreenIdle" first="false" alt="character">
                        @endif

                        @if($usePoints)
                            <div style="font-weight: bold;"> {{$bestPlayers[2]->score}} {{__('projectorGameOver.points')}}<br></div>
                        @endif
                        <hr>
                        <span style="color: green;">✔</span> {{__('projectorGameOver.correct')}} {{$bestPlayers[2]->correctAnsweredQuestions}}/{{$bestPlayers[2]->answeredQuestions}}<br>

                        @isset($bestPlayers[2]->stat_data)
                            <span class="iconify" data-icon="octicon:flame" data-inline="false" style="color: #fb3600"></span> {{__('projectorGameOver.longest_streak')}} {{$bestPlayers[2]->stat_data->best_streak}} <br>
                            <span class="iconify" data-icon="ic:round-flash-on" data-inline="false" style="color: #f5a100ff;"></span> {{__('projectorGameOver.answer_time')}} {{$bestPlayers[2]->stat_data->avg_time}}s <br>
                            @if($gameModeType == 'classic')
                                <span style="color: var(--pf_wrong);">❤</span> {{__('projectorGameOver.life_left')}} {{$bestPlayers[2]->stat_data->student_life}}
                            @endif
                        @endisset
                    @endisset
                </div>
            </div>

            @isset($bestPlayers[3])
                <div class="marquee" id="marquee">
                    {{__('projectorGameOver.more_places')}} 4. <b>{{$bestPlayers[3]->student_name}}</b>@if($usePoints) {{__('projectorGameOver.with')}} {{$bestPlayers[3]->score}} {{__('projectorGameOver.points_german_dative')}}; @else; @endif
                    @for ($i = 4; isset($bestPlayers[$i]); $i++)
                        {{ $i + 1 }}. <b>{{$bestPlayers[$i]->student_name}}</b>@if($usePoints) {{__('projectorGameOver.with')}} {{$bestPlayers[$i]->score}} {{__('projectorGameOver.points_german_dative')}}; @else; @endif
                    @endfor
                </div>
            @endisset
        </div>
        <br>

        <a class="btn btn-danger float-right shaded bordered bg_pf_wrong"  href="{{route('editorHome')}}" role="button">
            <span class="iconify iconify_medium_text_mr" data-icon="mdi:exit-to-app" data-rotate="180deg"></span>
            {{__('projectorGameOver.finish')}}
        </a>
        <a class="btn btn-primary float-right shaded bordered bg_pf_primary" style="margin-right: 0.5em;" href="/editorStatistic/{{$sessionId}}" role="button">
            <span class="iconify iconify_medium_text_mr" data-icon="mdi:book-open-outline"></span>
            {{__('projectorGameOver.statistics')}}
        </a>
        <a class="btn btn-primary float-right shaded bordered bg_pf_primary" style="margin-right: 0.5em;" href="/editorCopySession/{{$sessionId}}" role="button">
            <span class="iconify iconify_medium_text_mr" data-icon="mdi:replay"></span>
            {{__('projectorGameOver.play_again')}}
        </a>
        <div class="fillerBottom" style="width: 0.5em"></div>
    </div>

    <!-- render confettif if game has been won -->
    @if($gameModeType == 'teams')
        @if ($pointsBlue > $pointsRed)
            <script>
                var confettiSettings = {"target":"confetti","max":"500","size":"1","animate":true,"props":["circle","square","triangle","line"],"colors":[[31,63,103]],"clock":"30"};
                var confetti = new ConfettiGenerator(confettiSettings);
                confetti.render();
            </script>
        @elseif($pointsBlue < $pointsRed)
            <script>
                var confettiSettings = {"target":"confetti","max":"500","size":"1","animate":true,"props":["circle","square","triangle","line"],"colors":[[175,0,60]],"clock":"30"};
                var confetti = new ConfettiGenerator(confettiSettings);
                confetti.render();
            </script>
        @else
            <script>
                var confettiSettings = {"target":"confetti","max":"500","size":"1","animate":true,"props":["circle","square","triangle","line"],"colors":[[175,0,60], [31,63,103]],"clock":"30"};
                var confetti = new ConfettiGenerator(confettiSettings);
                confetti.render();
            </script>
        @endif
    @elseif($gameWin == 1)
        <script>
            var confettiSettings = {"target":"confetti","max":"500","size":"1","animate":true,"props":["circle","square","triangle","line"],"colors":[[165,104,246],[230,61,135],[0,199,228],[253,214,126]],"clock":"30"};
            var confetti = new ConfettiGenerator(confettiSettings);
            confetti.render();
        </script>
    @endif

    <!-- makes random jumping of characters possbile -->
    <script>
        let places = ["firstimg", "secondimg", "thirdimg"];

        for (let place of places){
            let randDelay = Math.random(); // get random animation
            try{
                document.getElementById(place).style.animationDelay = randDelay + "s";
            }catch(e){} // we dont care if it fails
        }

        let marquee = document.getElementById("marquee");
        if(marquee != null) {
            marquee.scrollLeft = 0;
            let timer = setInterval(function () {
                marquee.scrollLeft += marquee.offsetWidth;
                if (marquee.scrollLeft > marquee.scrollWidth - marquee.offsetWidth)
                    marquee.scrollLeft = 0;
            }, 5000);
        }
    </script>
@endsection
