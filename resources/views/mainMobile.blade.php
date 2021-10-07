@extends('mainParent')

@section('mainParentHead')
    @yield('mainMobileHead')
@endsection

@section('mainParentHeader')
    <!--parent view extending main view; used by all mobile pages except question view -->
    <div class="fillerTop"></div>
    <div class="headerColoring navbarMobile">
        <img class="topOfcImg" src="{{ URL::asset('images/logos/lowRes.png')}}" alt="logo" height="22px">
        @yield('mainMobileHeaderNavbar')
    </div>
    @yield('mainMobileHeader')
@endsection

@section('mainParentBody')
    <div class="mobileWrapper">
        @yield('mainMobileBody')
    </div>
@endsection

@section('mainParentFooter')
    @yield('mainMobileFooter')
@endsection
