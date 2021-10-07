<!DOCTYPE html>
<!--parent view used by all mobile and desktop pages; mainly used to import scripts -->

<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ URL::asset('images/logos/Icon.png')}}">

    <!-- import Bootstrap and jquery (both are MIT licensed) -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <script src="//code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <!-- import icons by iconify.design: dual-licensed under Apache 2.0 and GPL 2.0 licence -->
    <script src="//code.iconify.design/1/1.0.7/iconify.min.js"></script>

    <script src="{{ URL::asset('js/swiper.min.js')}}"></script>

    <!-- import local css files-->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/Checkboxes.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/LoadingSpinner.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/Tooltip.css')}}">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/Constants.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/GlobalComponents.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/StructureComponents.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/CommonAttributes.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/Keyframes.css')}}">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/mobile/swiper.min.css')}}">

    @yield('mainParentHead')
</head>

<body>
    <header>
        @yield('mainParentHeader')
    </header>

    @yield('mainParentBody')

    <footer>
        @yield('mainParentFooter')
    </footer>
</body>

@yield('mainParentSuffix')
