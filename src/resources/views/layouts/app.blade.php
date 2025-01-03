<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rese</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&family=Zen+Kaku+Gothic+New&display=swap"
        rel="stylesheet">
    <link rel="icon" href="{{ asset('img/favicon.ico/favicon.ico.jpg') }}" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__img">
            <div id="drawer_toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <a class="header__logo" href="{{ route('restaurants.index') }}">
                Rese
            </a>
        </div>
        @yield('nav')
    </header>
    <nav class="nav__menu" id="menu">
        <span class="close-btn" id="closeMenu">&times;</span>
        <ul>
            @yield('nav-js')
        </ul>
    </nav>
    <main class="main">
        @yield('content')
    </main>
    <script src="{{ asset('js/menu.js') }}"></script>
    @yield('js')
</body>

</html>
