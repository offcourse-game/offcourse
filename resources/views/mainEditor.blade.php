@extends('mainParent')
<!--parent view extending main view; used by all editor (desktop) pages -->

@section('title')
    Editor
@endsection

@section('mainParentHead')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/editor/Editor.css')}}">
    <script src="{{URL::asset('js/mainComponents.js')}}"></script>
    @yield('mainEditorHead')
@endsection

@section('mainParentHeader')
    <div class="editorLogoBox shaded" sideBarHidden="@yield('HideEditorSidenav')">
        <a href="/editorHome"><img src="{{ URL::asset('images/logos/white.png')}}" alt="logo" class="centerImage" height="22"></a>
    </div>
    <div class="editorHeaderBox shaded">
        <a @yield('Hidelogout') class="btn btn-outline float-right" style="color: var(--pf_primary)" href="/logout" role="button">
            <span class="iconify iconify_medium_text_ml" data-icon="mdi:account-arrow-left-outline"></span>
            Abmelden
        </a>
        @hasSection('HeaderBoxHeadline')
            <h4 style="color: var(--pf_primary); text-align: center; padding-top: 0.25em"> @yield('HeaderBoxHeadline') </h4>
        @endif
    </div>
    @yield('mainParentHeader')
@endsection

@section('mainParentBody')

    <div @yield('HideEditorSidenav') class="editorSidenavigation">

        @if(!View::hasSection('sessionId')) {{-- catches user input to not click buttons; we should only make the sidebar clickable if the name is already given --}}
            <div style="z-index: 10; position: absolute; width: 100%; height: 100%;"><br></div>
        @endif

        {{-- session name --}}
        <div class="editorSidenavigationItem" state="@yield('activeSideNavItem0') noBorder">
            <span class="iconify iconify_medium_text_mr" data-icon="mdi:textbox"></span>
            <div class="editorSidenavigationItemText">Sessionname</div>
        </div>
        <span class="iconify iconify_medium" data-icon="mdi-play" data-rotate="90deg" style="color: var(--pf_primary)"></span>

        {{-- add questions --}}
        <a class="editorSidenavigationItem" state="@yield('activeSideNavItem1')" href="/editorQuestion/@yield('sessionId')">
            <span class="iconify iconify_medium_text_mr" data-icon="mdi:playlist-plus"></span>
            <div class="editorSidenavigationItemText">Fragen hinzufügen</div>
        </a>
        <span class="iconify iconify_medium" data-icon="mdi-play" data-rotate="90deg" style="color: var(--pf_primary)"></span>

        {{-- question summary --}}
        <a class="editorSidenavigationItem" state="@yield('activeSideNavItem2')" href="/editorQuestionSummary/@yield('sessionId')">
            <span class="iconify iconify_medium_text_mr" data-icon="mdi:format-list-numbers"></span>
            <div class="editorSidenavigationItemText">Fragenpool</div>
        </a>
        <span class="iconify iconify_medium" data-icon="mdi-play" data-rotate="90deg" style="color: var(--pf_primary)"></span>

        {{-- options --}}
        <a class="editorSidenavigationItem" state="@yield('activeSideNavItem3')" href="/editorGameSettings/@yield('sessionId')">
            <span class="iconify iconify_medium_text_mr" data-icon="octicon:settings"></span>
            <div class="editorSidenavigationItemText">Sessionoptionen</div>
        </a>
        <span class="iconify iconify_medium" data-icon="mdi-play" data-rotate="90deg" style="color: var(--pf_primary)"></span>

        {{-- start--}}
        <a class="editorSidenavigationItem" state="@yield('activeSideNavItem4')" href="/editorGameStartup/@yield('sessionId')">
            <span class="iconify iconify_medium_text_mr" data-icon="mdi:play-protected-content"></span>
            <div class="editorSidenavigationItemText">Session starten</div>
        </a>

        {{-- exit button --}}
        @if(View::hasSection('sessionId')) {{-- exit button has higher z index than click-catcher to always be clickable --}}
            <a class="editorSidenavigationItem" state="highlight" style="z-index: 15; position: absolute; width: 13.8em; bottom: 0;" href="/editorHome">
                <span class="iconify iconify_medium_text_mr" data-icon="mdi:exit-to-app" data-rotate="180deg"></span>
                <div class="editorSidenavigationItemText">später fortfahren</div>
            </a>
        @else
            <a class="editorSidenavigationItem" state="highlight" style="z-index: 15; position: absolute; width: 13.8em; bottom: 0;" href="/editorHome">
                <span class="iconify iconify_medium_text_mr" data-icon="mdi:delete-forever-outline"></span>
                <div class="editorSidenavigationItemText">abbrechen</div>
            </a>
        @endif
    </div>

    <div class="editorSidenavigationFiller">
        @yield('mainEditorBody')
    </div>
@endsection

@section('mainParentFooter')
    @yield('mainEditorFooter')
@endsection
