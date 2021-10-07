@extends('mainMobile')

@section('title')
    {{__('EnterId.enter_id')}}
@endsection

@section('mainMobileHead')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/mobile/Start.css')}}">
@endsection

@section('mainMobileHeaderNavbar')
    <a class="langChoose float-right @if(app()->getLocale() == "en") font-weight-bold @endif" href="{{ url('lang/en') }}">EN</a>
    <span class="langChoose float-right">/</span>
    <a class="langChoose float-right @if(app()->getLocale() == "de") font-weight-bold @endif" href="{{ url('lang/de') }}">DE</a>
@endsection

@section('mainMobileBody')
    <div class="wrapperOuter">
        <br>
        <div class="textCentered">
            <img src="/images/characterPair.png" class="backgroundCharacter" alt="character">
            <form id="form" action="javascript:check()">    {{--using forms enables to press ENTER too--}}
                <h4>{{__('EnterId.join')}}</h4>
                {{__('EnterId.join_text')}}<br><br>
                <input type="number" class="form-control bordered shaded" id="game-id" min="1" placeholder="{{__('EnterId.session_number')}}" required>
                <br>
                <button type="submit" class="btn btn-success float-center shaded bordered bg_pf_correct">{{__('Start.start')}}</button>
            </form>

            <script type="text/javascript">
                function check() {
                    let input = document.getElementById("game-id");

                    if ( input.checkValidity()){
                            location.href = "/mobileStart/" + input.value;
                    }
                }
            </script>
        </div>
    </div>


@endsection
