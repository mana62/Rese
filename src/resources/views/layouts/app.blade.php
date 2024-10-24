<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rese</title>
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__img">
            <img src="/src/public/img/menu_icon.png" alt="" id="menuIcon">
        </div>
        <a class="header__logo" href="">
            Rese
        </a>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <small class="copyright">&copy; Rese,inc</small>
    </footer>

    @yield('js')
</body>

</html>
</body>

</html>
