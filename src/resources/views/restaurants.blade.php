@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/restaurants.css') }}">
@endsection

@section('nav')
    <div class="nav">
        <!-- 検索フォーム -->
        <form action="{{ route('restaurants.index') }}" method="GET" class="nav-search">
            <!-- エリア選択 -->
            <div class="nav-search__area">
                <select class="nav-search__area-select" name="area" onchange="this.form.submit()">
                    <option class="nav-search__area-option" value="">All area</option>
                    @foreach ($areas as $area)
                        <option class="nav-search__area-option" value="{{ $area->id }}"
                            {{ request('area') == $area->id ? 'selected' : '' }}>
                            {{ $area->area_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- ジャンル選択 -->
            <div class="nav-search__genre">
                <select class="nav-search__genre-select" name="genre" onchange="this.form.submit()">
                    <option class="nav-search__genre-option" value="">All genre</option>
                    @foreach ($genres as $genre)
                        <option class="nav-search__genre-option" value="{{ $genre->id }}"
                            {{ request('genre') == $genre->id ? 'selected' : '' }}>
                            {{ $genre->genre_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- キーワード検索 -->
            <div class="search">
                <img class="search__icon" src="{{ asset('img/icon/search-icon.png') }}" alt="search icon"
                    class="search__icon">
                <input class="search__input" type="text" name="input" placeholder="search..."
                    value="{{ request('input') }}">
                <button class="search__submit" type="submit">検索</button>
            </div>
        </form>
    </div>
@endsection


@section('nav-js')
    @guest
        <li><a href="/restaurants">HOME</a></li>
        <li><a href="/register">REGISTRATION</a></li>
        <li><a href="/login">LOGIN</a></li>
    @endguest

    @auth
        <li><a href="/restaurants">HOME</a></li>
        <li>
            <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                {{ __('LOGOUT') }}
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                @csrf
            </form>
        </li>
        <li><a href="/mypage">MYPAGE</a></li>
        @if (Auth::user()->role === 'admin')
            <li><a href="/admin">ADMIN</a></li>
        @endif
        @if (Auth::user()->role === 'store-owner')
            <li><a href="/owner">OWNER</a></li>
        @endif
    @endauth
@endsection

@section('content')
    <!--レストラン一覧-->
    <div class="all-shop">
        @foreach ($restaurants ?? [] as $restaurant)
            <div class="card">
                <div class="content-img">
                    <img src="{{ asset('img/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" />
                </div>
                <div class="text-box">
                    <h2>{{ $restaurant->name }}</h2>
                    <p class="area">#{{ $restaurant->area->area_name }}</p>
                    <p class="genre">#{{ $restaurant->genre->genre_name }}</p>
                    <div class="link">
                        <a href="{{ route('detail', $restaurant->id) }}">詳しくみる</a>

                        @auth
                            <button class="favorite-btn {{ in_array($restaurant->id, $favoriteIds) ? 'favorited' : '' }}"
                                onclick="toggleFavorite(this, {{ $restaurant->id }})">
                                &hearts;
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@section('js')
    <script src="{{ asset('js/restaurants.js') }}"></script>
@endsection
