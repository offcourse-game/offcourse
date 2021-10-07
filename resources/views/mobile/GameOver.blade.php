@extends('mainMobile')

@section('title')
    {{__('GameOver.end')}}
@endsection

@section('mainMobileHead')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/mobile/Achievement.css')}}">
@endsection

@section('mainMobileBody')

    <div class="centerHeadingWrapper">
        {{-- @extends('title') --}}
        @if($studentOut == 1) <h2 style="color: var(--pf_wrong);" align="center"> <b> {{__('GameOver.out')}}</b></h2>
        @else <h2 style="color: var(--pf_correct);" align="center"> <b> {{__('GameOver.all')}}</b></h2>
        @endif
    </div>

    <div class="wrapperOuter">

        <hr/>

        <div class="textCentered">
            <h4>{{__('GameOver.wait')}}</h4>
        </div>

        @if ($showStory == 1 && $gameModeType == "classic")
            <img src="/images/characterPair.png" class="imageEndScreenIdle" alt="character">
        @elseif ($gameModeType == "training")
            <img src="/images/training/characterPairBees.png" class="imageEndScreenIdle" alt="character">
        @elseif ($gameModeType == "teams")
            <img src="/images/characterPairTeams.png" class="imageEndScreenIdle" alt="character">
        @endif

        <div class="scoreFlagWrapper">
            <div class="scoreFlag" size="small"><b>?</b>/?<br><small>{{__('Achievement.correct_caps')}}</small></div>

            @if($showRank == 1)
                <div class="scoreFlag"><b>?</b>/?<br><small>{{__('Achievement.rank_caps')}}</small></div>
            @endif
            @if($usePoints == 1)
                <div class="scoreFlag" size="small"><b>?</b>pts<br><small>{{__('Achievement.points_caps')}}</small></div>
            @endif
        </div>

        @if ($showBadges == 1)
            @for($i = 0; $i < 3; $i++)
                @component('layouts.badge')
                    @slot('color') white @endslot
                    @slot('animId') {{$i}} @endslot
                    @slot('badgeImageClass') dice @endslot
                    @slot('title') ???????????? @endslot
                    @slot('formattedText') {!! __('GameOver.avm_after_game') !!} @endslot
                    @slot('starCount') 3 @endslot
                    @slot('hidden') @endslot
                @endcomponent
            @endfor
        @endif
    </div>

<script>

    window.onload = function() {

        // Check if the game is over
        function checkGameStatus() {
            $.ajax({
                type: 'get',
                url: '/checkGameStatus',
                success: function (response){
                    if(response['gameFinished'] == 1) {
                        console.log("Game Finished");
                        window.location.href = "/gameFinish";
                    }
                    setTimeout(checkGameStatus, 1000); // Wait 1 Seconds
                }
            });
        }
        setTimeout(checkGameStatus, 0); // Make the first call immediately
    };

</script>
@endsection
