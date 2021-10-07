@extends('mainMobile')

@section('title')
    {{__('GameOver.error')}}
@endsection

@section('mainMobileHeaderNavbar')
    <a class="langChoose float-right @if(app()->getLocale() == "en") font-weight-bold @endif" href="{{ url('lang/en') }}">EN</a>
    <span class="langChoose float-right">/</span>
    <a class="langChoose float-right @if(app()->getLocale() == "de") font-weight-bold @endif" href="{{ url('lang/de') }}">DE</a>
@endsection

@section('mainMobileBody')

    <br>
    <div class="wrapperOuter">
        <div class="alert alert-danger">
            <strong>{{__('Error.error')}}</strong> <h4>{{__($error)}}</h4>
        </div>
        <a class="btn btn-primary shaded bordered bg_pf_primary" href="/mobileStart" role="button" style="float: right">
            <span class="iconify" data-icon="mdi:replay"></span> {{__('Achievement.new_game')}}
        </a>
    </div>

@endsection
