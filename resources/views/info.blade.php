@extends('mainParent')

@section('title')
    Off Course!
@endsection

@section('mainParentHead')
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/HenrikButtons.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/mobile/Achievement.css')}}">
    {{-- include after achievement & henrik buttons; overrides media queries --}}
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/Info.css')}}">
@endsection

@section('mainParentBody')
    <div class="" style="width: calc(100% + 1px); overflow-x: hidden;">

        <div class="mainLogoFiller">
            <div>
                <img class="fullWidthImage" src="{{ URL::asset('images/ZHG.png')}}" alt="" style="width: 100%; overflow-x:hidden;">
            </div>
            <div class="mainLogoWrapper">
                <img class="mainLogoImage" part="left" src="{{ URL::asset('images/logos/offLeft.png')}}" alt="logoL">
                <img class="mainLogoImage" part="right" src="{{ URL::asset('images/logos/courseRight.png')}}" alt="logoR">
            </div>
            <div class="subHeader">
                <div class="subHeaderUpper shaded">
                    BOSSFIGHTS IM HÖRSAAL
                </div>
                <div class="subHeaderLower shaded">
                    EINE GAMIFIZIERTE APP FÜR DIE WISSENSABFRAGE
                </div>
            </div>
        </div>

        <div class="introField">
            @if (Auth::check())
                <a href="/editorHome"> <button class="henrikButton yellow large viewFadeIn" anim="right" ><b>LOGIN</b> FÜR DOZIERENDE</button></a>
            @else
                <button class="henrikButton yellow large viewFadeIn" data-toggle="modal" anim="right" data-target="#login-modal"><b>LOGIN</b> FÜR DOZIERENDE</button>
            @endif

            <div class="screenExampleWrapper">
                <img class="screenExample" elem="left" src="{{ URL::asset('images/infoImage1.png')}}" alt="img1">
                <img class="screenExample" elem="center" src="{{ URL::asset('images/infoImage0.png')}}" alt="img0">
                <img class="screenExample" elem="right" src="{{ URL::asset('images/infoImage2.png')}}" alt="img2">
            </div>
            <button class="henrikButton yellow large viewFadeIn" anim="left" data-toggle="modal" data-target="#enterId-modal"> SESSION <b>BEITRETEN</b></button>
        </div>

        <div class="textHeaderSkewed shaded waitingForScroll" anim="leftSkewed">
            ÜBER DAS PROJEKT
        </div>

        <div class="smartphoneCardScroller" id="smartphoneScroller">

            <div class="smartphoneCardScrollerButton">
                <button class="henrikButton yellow circle" onclick="document.getElementById('smartphoneScroller').scrollBy(-300, 0);"><b> < </b></button>
            </div>

            <div class="smartphoneCardScrollerMargin"></div>

            <div class="smartphoneCard waitingForScroll" anim="right">
                <div class="smartphoneCardInner">
                    <img class="smartphoneImage" src="{{ URL::asset('images/logos/Icon.png')}}" alt="">
                    <h3><b style="color: var(--pf_yellow)">OFF</b>COURSE</h3>
                    <hr>
                    OffCourse ist eine gamifizierte Anwendung zur Wissensabfrage von Studierenden unter Umsetzung der Bossfight-Mechanik.
                </div>
            </div>

            <div class="smartphoneCard waitingForScroll" anim="up">
                <div class="smartphoneCardInner">
                    <img class="smartphoneImage" src="{{ URL::asset('images/boss_lowRes.png')}}" alt="">
                    <h3><b style="color: var(--pf_wrong_dark)">DOZIERENDE</b></h3>
                    <hr>
                    <ul class="triangle">
                        <li class="triangle">bereiten Fragen über Editor vor</li>
                        <li class="triangle">legen Spieloptionen fest</li>
                        <li class="triangle">wählen Form des Feedbacks aus</li>
                        <li class="triangle">werten Wissensstand aus</li>
                    </ul>
                </div>
            </div>

            <div class="smartphoneCard waitingForScroll" anim="up">
                <div class="smartphoneCardInner">
                    <img class="smartphoneImage" src="{{ URL::asset('images/characterPair_lowRes.png')}}" alt="">
                    <h3><b style="color: var(--pf_gray_dark)">STUDIERENDE</b></h3>
                    <hr>
                    <ul class="triangle">
                        <li class="triangle">quizzen, um Spiel zu gewinnen</li>
                        <li class="triangle">testen ihr aktuelles Wissen</li>
                        <li class="triangle">sammeln Auszeichnungen</li>
                        <li class="triangle">erhalten individuelles Feedback</li>
                    </ul>
                </div>
            </div>

            <div class="smartphoneCard waitingForScroll" anim="left">
                <div class="smartphoneCardInner">
                    <img class="smartphoneImage" style="margin-top: 32px; margin-bottom: 32px;" src="{{ URL::asset('images/crown_gold.png')}}" alt="">
                    <h3><b style="color: var(--pf_primary)">ZIELE</b></h3>
                    <hr>
                    <ul class="triangle">
                        <li class="triangle">Interaktivität fördern</li>
                        <li class="triangle">Lernmotivation steigern</li>
                        <li class="triangle">Wissensstand effizient messen</li>
                    </ul>
                </div>
            </div>

            <div class="smartphoneCardScrollerMargin"></div>

            <div class="smartphoneCardScrollerButton">
                <button class="henrikButton yellow circle" onclick="document.getElementById('smartphoneScroller').scrollBy(300, 0);"><b> > </b></button>
            </div>
        </div>

        <div class="sleaze">
            <div class="textHeaderSkewed shaded waitingForScroll" anim="leftSkewed">
                DAS KANN DIE ANWENDUNG
            </div>

            <div class="badgeHiglight">
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') wisdom @endslot
                        @slot('title') Autorentool @endslot
                        @slot('formattedText') Übersichtlich und leicht zu bedienen @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') click @endslot
                        @slot('title') Single- & Multiple Choice @endslot
                        @slot('formattedText') Randomisiert mit sofortigem Feedback @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') glasses @endslot
                        @slot('title') Web-basiert @endslot
                        @slot('formattedText') Keine Installation, responsiv, und endgeräte-unabhängig @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                {{--second row --}}
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') podium @endslot
                        @slot('title') Rangliste @endslot
                        @slot('formattedText') Pseudonymisiert, aber transparent und kompetitiv @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') medal @endslot
                        @slot('title') Badges @endslot
                        @slot('formattedText') Zugeschnittenes Feedback für Studierende @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') coins @endslot
                        @slot('title') Over 900(0) @endslot
                        @slot('formattedText') Quizzen mit dem ganzen Hörsaal @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                {{--third row --}}
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') bullseye @endslot
                        @slot('title') Customizing @endslot
                        @slot('formattedText') Abfragesession anpassen über Schwierigkeitsgrad, Feedback und Zeit @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') enlightenment @endslot
                        @slot('title') Statistiken @endslot
                        @slot('formattedText') Den Wissensstand leicht verständlich sichtbar machen @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') shield @endslot
                        @slot('title') Trainingsmodus @endslot
                        @slot('formattedText') Denn Übung macht den Meister @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                {{--fourth row --}}
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') controller @endslot
                        @slot('title') Charaktere @endslot
                        @slot('formattedText') Zur Einschätzung des eigenen Wissensstands @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') spear @endslot
                        @slot('title') Bossfight @endslot
                        @slot('formattedText') Echte Herausforderung durch das Ausscheiden bei falschen Antworten @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
                <div class="waitingForScroll" anim="up">
                    @component('layouts.badge')
                        @slot('color') gold @endslot
                        @slot('animId') 1 @endslot
                        @slot('badgeImageClass') scroll @endslot
                        @slot('title') Export und Import @endslot
                        @slot('formattedText') Wissensstand erneut abfragen oder näher analysieren? Kein Problem! @endslot
                        @slot('starCount') 3 @endslot
                        @slot('hidden') @endslot
                    @endcomponent
                </div>
            </div>
        </div>


        {{--
        <img class="sceneImages" src="{{ URL::asset('images/scene1.png')}}" alt="img scene1">
        <img class="sceneImages" src="{{ URL::asset('images/scene0.png')}}" alt="img scene0">
        --}}

        <div class="textHeaderSkewed shaded waitingForScroll" anim="leftSkewed">
            WAS MACHEN DOZIERENDE?
        </div>

        <div class="stepDisplay rightwing">
            <div class="stepImage rightwing waitingForScroll" anim="right"></div>

            <div class="henrikButton white larger collapseMargin static waitingForScroll" anim="right"> <b>LOGIN</b> FÜR DOZIERENDE</div>
            <div class="henrikButton white larger collapseMargin static waitingForScroll" anim="up"> <b>SESSION</b> ERSTELLEN</div>
            <div class="stepTextItem waitingForScroll" anim="left">Legen Sie individuell Dauer, Spielelemente, Schwierigkeitsgrad und Modus Ihrer Session fest.</div>
            <div class="henrikButton white larger collapseMargin static arrow waitingForScroll" anim="up"> <b>FRAGEN</b> ANLEGEN / IMPORTIEREN</div>
            <div class="stepTextItem waitingForScroll" anim="left">Bereiten Sie Single- und Multiple-Choice Fragen vor.</div>
            <div class="henrikButton white larger collapseMargin static arrow waitingForScroll" anim="up"> <b>SESSION</b> AUFRUFEN & STARTEN</div>
            <div class="stepTextItem waitingForScroll" anim="left">Warten Sie bis die Studierenden der Session beigetreten sind und starten Sie die spielerische Wissensabfrage.</div>
            <div class="henrikButton white larger collapseMargin static arrow waitingForScroll" anim="up"> <b>STATISTIKEN</b> AUSWERTEN</div>
            <div class="stepTextItem waitingForScroll" anim="left">Verschaffen Sie sich einen sofortigen Überblick über den Wissensstand der Studierenden.</div>
            <div class="henrikButton correct larger collapseMargin static arrow waitingForScroll" anim="up"> <b>ERNEUT</b> SPIELEN?</div>
            <div class="stepTextItem waitingForScroll" anim="left">Nutzen Sie in künftigen Veranstaltungen erstellte Sessions und spielen Sie erneut mit den Studierenden.</div>
        </div>

        <div class="textHeaderSkewed shaded waitingForScroll" anim="leftSkewed">
            WAS MACHEN STUDIERENDE?
        </div>

        <div class="stepDisplay leftwing">
            <div class="stepImage leftwing waitingForScroll" anim="left"></div>

            <div class="henrikButton white larger collapseMargin static waitingForScroll" anim="left"> <b>SESSION</b> BEITRETEN</div>
            <div class="henrikButton white larger collapseMargin static waitingForScroll" anim="up"> <b>QR-CODE</b> SCANNEN</div>
            <div class="stepTextItem waitingForScroll" anim="right">Nutzen Sie Ihr Smartphone, Tablet oder Laptop um teilzunehmen.</div>
            <div class="henrikButton white larger collapseMargin static arrow waitingForScroll" anim="up"> <b>CHARAKTER</b> AUSWÄHLEN</div>
            <div class="stepTextItem waitingForScroll" anim="right">Wählen Sie auf Basis Ihres derzeitigen Wissensstands einen Charakter aus.</div>
            <div class="henrikButton white larger collapseMargin static arrow waitingForScroll" anim="up"> <b>FRAGEN</b> BEANTWORTEN</div>
            <div class="stepTextItem waitingForScroll" anim="right">Quizzen Sie schnell und richtig, um Errungenschaften zu sammeln.</div>
            <div class="henrikButton white larger collapseMargin static arrow waitingForScroll" anim="up"> <b>FEEDBACK</b> EINSEHEN</div>
            <div class="stepTextItem waitingForScroll" anim="right">Verschaffen Sie sich einen Überblick über Ihren individuellen Wissensstand.</div>
            <div class="henrikButton correct larger collapseMargin static arrow waitingForScroll" anim="up"> <b>EVALUATION</b> AUSFÜLLEN</div>
            <div class="stepTextItem waitingForScroll" anim="right">Unterstützen Sie das Projekt indem Sie den Fragebogen ausfüllen.</div>
        </div>

        <div class="footerFat">
            <img class="footerPinImg waitingForScroll" anim="up" src="{{ URL::asset('images/pin.png')}}" alt="pin">
            <div class="footerGrid waitingForScroll" anim="up">
                <div class="waitingForScroll" anim="up">
                    Georg-August-Universität Göttingen
                    <br><h4>Professur für Anwendungssysteme und E-Business</h4>
                </div>
                <div class="waitingForScroll" anim="left">
                    <h4>Projektleitung</h4>
                    <b>Dr. Henrik Wesseloh</b>
                    <br>Platz der Göttinger Sieben
                    <br>37073 Göttingen<br>
                    <a href="https://www.uni-goettingen.de/de/sh/43876.html">www.as.wiwi.uni-goettingen.de</a>
                </div>
                <div class="waitingForScroll" anim="right">
                    <h4>Programmierung</h4>
                    <p><b>Felix Stein</b></p>
                    <p><b>Phillip Szelat</b></p>
                    <img src="{{ URL::asset('images/logos/UgoeLogoPrimaryLight.png')}}" style="max-width: 350px; height: auto" alt="UGOE sponsor">
                </div>
            </div>
        </div>
    </div>

    <!-- modal to open enter Id dialog -->
    <div class="modal fade bd-modal-lg shaded bordered" id="enterId-modal" tabindex="-1" role="dialog" aria-labelledby="enterId-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content" style="padding: 15px;">
                <div style="display: inline-block;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.75rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="mx-auto" display="block">
                    <img src="/images/characterPair.png" class="backgroundCharacter" style="max-width: 25%; padding: 0.5em;" alt="character">
                    <form id="form" action="javascript:checkSession()">    {{--using forms enables to press ENTER too--}}
                        <h4>Session beitreten<hr></h4>
                        Gib die am Projektor angezeigte Sessionnummer ein,<br> um teilzunehmen: <br>
                        <div class="px-5 py-2">
                            <input type="number" class="form-control bordered shaded" id="game-id" min="1" placeholder="Sessionnummer" required>
                        </div>
                        <button type="submit" class="henrikButton yellow small">Start</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- modal to open login dialog -->
    <div class="modal fade bd-modal-lg shaded bordered" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content" style="padding: 15px;">
                <div style="display: inline-block;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.75rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="mx-auto" display="block">
                    <img style="max-width: 20%; padding: 0.5em;" src="{{ URL::asset('images/boss_lowRes.png')}}" alt="">
                    <h4>Login für Dozierende<hr></h4>
                    <p>Bitte melden Sie sich mit Ihrer GWDG-Kennung an <br>(z. B. mmuster).</p>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                <div class="px-5">
                                    <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="GWDG-Kennung" required autofocus>

                                    @if ($errors->has('username'))
                                        <!-- on error we reload the page, so we must show the modal again-->
                                        <script>$('#login-modal').modal('show');</script>
                                        <span class="help-block">
                                            <strong>{{ $errors->first('username') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <div class="px-5">
                                    <input id="password" type="password" class="form-control" name="password" placeholder="GWDG-Passwort" required>

                                    @if ($errors->has('password'))
                                        <!-- on error we reload the page, so we must show the modal again-->
                                        <script>$('#login-modal').modal('show');</script>
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox custom-checkbox">
                                    <input type="checkbox" id="check1" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="check1"> Angemeldet bleiben</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="p-0 text-center">
                                    <button type="submit" class="henrikButton yellow small">
                                        <span class="iconify" data-icon="fe:login" data-inline="false"></span> Anmelden
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function checkSession() {
            let input = document.getElementById("game-id");

            if (input.checkValidity()) {
                location.href = "/mobileStart/" + input.value;
            }
        }

        // provided by https://eddyerburgh.me/animate-elements-scrolled-view-vanilla-js
        let animateHTML = function() {
            let elem;
            let windowHeight;
            let startUp = true;
            function init() {
                elems = document.querySelectorAll('.waitingForScroll');
                windowHeight = window.innerHeight;
                addEventHandlers();
                checkPosition();
                startUp = false;
            }
            function addEventHandlers() {
                window.addEventListener('scroll', checkPosition);
                window.addEventListener('resize', init);
            }
            function checkPosition() {
                for (let i = 0; i < elems.length; i++) {
                    let positionFromTop = elems[i].getBoundingClientRect().top;
                    if (positionFromTop - windowHeight <= -75) {      //scrolled past object? 75px inside viewport
                        if(startUp == false) {
                            elems[i].className = elems[i].className.replace('waitingForScroll', 'viewFadeIn');
                        } else {
                            elems[i].classList.remove('waitingForScroll');
                        }
                    }
                }
            }
            return {
                init: init
            };
        };
        animateHTML().init();

        document.getElementById('smartphoneScroller').scrollBy(-200, 0);
    </script>

@endsection
