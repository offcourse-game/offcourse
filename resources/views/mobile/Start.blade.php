@extends('mainMobile')

@section('title')
    {{__('Start.start') . " (" . $sessionId . ")"}}
@endsection

@section('mainMobileHead')
    <script src="{{URL::asset('js/mobileStart.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/mobile/Start.css')}}">
@endsection

@section('mainMobileHeaderNavbar')
@endsection

@section('mainMobileBody')
    <div class="wrapperOuter">
        <br>
        <div class="textCentered">
            <h3>
                @if ($gameModeType == 'classic')
                    {{__('Start.game_mode_boss_battle')}}
                @elseif ($gameModeType == 'training')
                    {{__('Start.game_mode_training')}}
                @elseif ($gameModeType == 'noGame')
                    {{__('Start.game_mode_noGame')}}
                @endif

                @if($currentSessionSettings->show_character_selection == 1)
                    @if ($gameModeType == 'teams')
                        {{__('Start.team_choose')}}
                    @else
                        - {{__('Start.character_choose')}}
                    @endif
                @endif
            </h3>

            {{__('Start.explaination_general')}}
            {{__('Start.explaination_teams')}}
            <br><br>

            <div id="accordion">
                @if ($currentSessionSettings->use_points == true || $currentSessionSettings->show_badges == true || $currentSessionSettings->session_option_rank != 0)
                    <div class='accordionHeader iconizedItem center bordered shaded' id='accordionHeaderElements' data-toggle='collapse' data-target='#accordionTextElements' aria-expanded='false' aria-controls='accordionTextElements'>
                        <div class='accordionHeaderIcon' data-toggle='collapse' data-target='#accordionTextElements' aria-expanded='false' aria-controls='accordionTextElements'>
                            <span class="iconify" data-icon="dashicons:arrow-right-alt2" data-inline="false"></span>
                        </div>
                        <h5 class='mb-0 overflowEllipsis'>
                            {{__('Start.game_elements_title')}}
                        </h5>
                    </div>
                    <div id='accordionTextElements' class='accordionCollapse collapse' aria-labelledby='accordionHeaderElements' data-parent='#accordion'>
                        <div class="accordionCollapse innerCollapse bordered shaded">
                            @if ($currentSessionSettings->use_points == true)
                                <div class="iconizedItem">
                                    <span class="iconify iconify_medium_text_mr" data-icon="ri:hand-coin-line" data-inline="false"></span>
                                    <span class="textLeft">{{__('Start.explaination_points')}}</span>
                                </div>
                            @endif

                            @if ($currentSessionSettings->show_badges == true)
                                <div class="iconizedItem">
                                    <span class="iconify iconify_medium_text_mr" data-icon="cil:badge" data-inline="false"></span>
                                    <span class="textLeft">{{__('Start.explaination_badges')}}</span>
                                </div>
                            @endif

                            @if ($currentSessionSettings->session_option_rank == 1 || $currentSessionSettings->session_option_rank == 3)
                                <div class="iconizedItem">
                                    <span class="iconify iconify_medium_text_mr" data-icon="gridicons:list-ordered" data-inline="false"></span>
                                    <span class="textLeft">{{__('Start.explaination_rank_mobile')}}</span>
                                </div>
                            @endif

                            @if ($currentSessionSettings->session_option_rank == 2 || $currentSessionSettings->session_option_rank == 3)
                                <div class="iconizedItem">
                                    <span class="iconify iconify_medium_text_mr" data-icon="ant-design:trophy-outlined" data-inline="false"></span>
                                    <span class="textLeft">{{__('Start.explaination_rank_projector')}}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($gameModeType == 'classic' || $gameModeType == 'training')
                    <div class='accordionHeader iconizedItem center bordered shaded' id='accordionHeaderGame' data-toggle='collapse' data-target='#accordionTextGame' aria-expanded='false' aria-controls='accordionTextGame'>
                        <div class='accordionHeaderIcon' data-toggle='collapse' data-target='#accordionTextGame' aria-expanded='false' aria-controls='accordionTextGame'>
                            <span class="iconify" data-icon="dashicons:arrow-right-alt2" data-inline="false"></span>
                        </div>
                        <h5 class='mb-0 overflowEllipsis'>
                            {{__('Start.further_explaination_title')}}
                        </h5>
                    </div>
                    <div id='accordionTextGame' class='collapse' aria-labelledby='accordionHeaderGame' data-parent='#accordion'>
                        <div class="accordionCollapse innerCollapse bordered shaded">
                            @if ($gameModeType == 'classic')
                                @if ($currentSessionSettings->show_story == true)
                                    {{__('Start.explaination_boss')}}
                                    <br><br>
                                @endif
                                {{__('Start.losing_points')}}
                            @elseif ($gameModeType == 'training')
                                {{__('Start.explaination_training')}}
                            @endif

                            @if ($currentSessionSettings->show_character_selection == true)
                                <br><br>
                                {{__('Start.choose_character')}}
                            @endif
                        </div>
                    </div>
                @elseif ($gameModeType == 'noGame')
                    {{__('Start.explaination_noGame')}}
                @endif
            </div>
            <br>

            <div class="flexCenter" style="margin: 0 -0.5em 0 -0.5em;">
                @if ($gameModeType == 'teams')
                    {{-- Team 1--}}
                    <div class="boxhalfBordered" id="characterSelector1" onclick="selectCharacter(1)" style="cursor: pointer;">
                        <div class="textCentered">
                            <h1 style="color: var(--pf_primary_dark);"> {{__('Start.team_blue')}} </h1>
                            <img src="{{ URL::asset('images/characterTeamBlue.png')}}" class="backgroundCharacter horizontal">
                        </div>
                        <div class="fillerBottom"></div>
                        <div class="wrapperInner"></div>
                        <button class="btn btn-info btn-block bg_pf_primary floatBottom shaded bordered" id="select1" role="button" aria-pressed="true">{{__('Start.select')}}</button>
                    </div>

                    {{-- Team 2--}}
                    <div class="boxhalfBordered" id="characterSelector2" onclick="selectCharacter(2)" style="cursor: pointer;">
                        <div class="textCentered">
                            <h1 style="color: var(--pf_wrong_dark);"> {{__('Start.team_red')}} </h1>
                            <img src="{{ URL::asset('images/characterTeamRed.png')}}" class="backgroundCharacter horizontal">
                        </div>
                        <div class="fillerBottom"></div>
                        <div class="wrapperInner"></div>
                        <button class="btn btn-info btn-block bg_pf_primary floatBottom shaded bordered" role="button" aria-pressed="true">{{__('Start.select')}}</button>
                    </div>
                @else
                    {{--Character 1--}}
                    @if ($currentSessionSettings->show_character_selection == 1 || $currentSessionSettings->show_story == 1) {{-- show single character in story mode without character selection--}}
                        <div class="boxhalfBordered" id="characterSelector1" onclick="selectCharacter(1)" style="cursor: pointer;">
                            <div class="textCentered">
                                @if ($currentSessionSettings->is_training == 1)
                                    <h5>{{__('Start.honeybee')}}</h5>
                                @else
                                    <h5>{{__('Start.defender')}}</h5>
                                @endif
                            </div>
                            <div>
                                @if ($currentSessionSettings->is_training == 1)
                                    <img src="{{ URL::asset('images/training/characterHoneyBee.png')}}" class="backgroundCharacter horizontal" alt="imageCharacterGray">
                                @else
                                    <img src="{{ URL::asset('images/characterGray.png')}}" class="backgroundCharacter" alt="imageCharacterGray">
                                @endif

                                @if ($currentSessionSettings->is_training == 0)
                                    <div class="wrapperInner">
                                        <hr/>
                                        <div class='fillQuestionHeartImage'>
                                            <img src="{{asset('images/heart.png')}}" class='imageHeart' id='animatedBackgroundImage' alt='HEART'>
                                            <b>x {{$currentSessionSettings->start_life_player_type_1}}</b>

                                            <b style="float: right" >x 1</b>
                                            <img class="statItemSpear" src="{{ URL::asset('images/spearGrayCrossed.png')}}" alt="spearCrossed">
                                        </div>
                                    </div>
                                @endif

                                <div class="fillerBottom"></div>
                            </div>
                            <div class="wrapperInner">
                                {{-- The player should find out, what this character does :)
                                Du startest mit fünf Leben, machst aber nur sehr wenig Schaden gegen den Bossgegner.
                                --}}
                            </div>
                            <button class="btn btn-info btn-block bg_pf_primary floatBottom shaded bordered" id="select1" role="button" aria-pressed="true">{{__('Start.select')}}</button>
                        </div>
                    @endif

                    {{--Character 2--}}
                    @if ($currentSessionSettings->show_character_selection == 1) {{-- only show 2nd character if character selection is enabled --}}
                        <div class="boxhalfBordered" id="characterSelector2" onclick="selectCharacter(2)" style="cursor: pointer;">
                            <div class="textCentered">
                                @if ($currentSessionSettings->is_training == 1)
                                    <h5>{{__('Start.bumblebee')}}</h5>
                                @else
                                    <h5>{{__('Start.attacker')}}</h5>
                                @endif
                            </div>
                            <div>
                                @if ($currentSessionSettings->is_training == 1)
                                    <img src="{{ URL::asset('images/training/characterBumbleBee.png')}}" class="backgroundCharacter horizontal" alt="imageCharacterGold">
                                @else
                                    <img src="{{ URL::asset('images/characterGold.png')}}" class="backgroundCharacter" alt="imageCharacterGold">
                                @endif

                                @if ($currentSessionSettings->is_training == 0)
                                    <div class="wrapperInner">
                                        <hr/>
                                        <div class='fillQuestionHeartImage'>
                                            <img src="{{asset('images/heart.png')}}" class='imageHeart' id='animatedBackgroundImage' alt='HEART'>
                                            <b>x {{$currentSessionSettings->start_life_player_type_2}}</b>

                                            <b style="float: right" >x 2</b>
                                            <img class="statItemSpear" src="{{ URL::asset('images/spearGrayCrossed.png')}}" alt="spearCrossed">
                                        </div>
                                    </div>
                                @endif

                                <div class="fillerBottom"></div>
                            </div>
                            <div class="wrapperInner">
                                {{-- The player should find out, what this character does :)
                                Du machst deutlich mehr Schaden gegen den Bossgegner, hast aber nur drei Leben!
                                --}}
                            </div>
                            <button class="btn btn-info btn-block bg_pf_primary floatBottom shaded bordered" role="button" aria-pressed="true">{{__('Start.select')}}</button>
                        </div>
                    @endif

                    @if($gameModeType == 'noGame')
                        <img src="{{asset('images/clock.png')}}" style="width: 250px;">
                    @endif
                @endif
            </div>
            <br>
            <hr/>
            <br>

            {{__('Start.waiting')}}
            <div class="spinner"> <!-- loading spinner -->
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
                <div class="bounce4"></div>
                <div class="bounce5"></div>
            </div>
            <br>
        </div>
    </div>

@endsection
