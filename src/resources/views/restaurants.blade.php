@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/restaurants.css') }}">
@endsection

@section('nav')
    <div class="nav">
        <form action="{{ route('restaurants.index') }}" method="GET" class="nav-search">
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
            <div class="search">
                <img class="search__icon" src="{{ asset('img/icon/search_icon.png') }}" alt="search icon">
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
    @endauth
@endsection

@section('content')
    <div class="all-shop">
        @foreach ($restaurants ?? [] as $restaurant)
            <article class="card">
                <div class="content-img">
                    <img src="{{ asset('img/' . $restaurant->image) }}" alt="{{ $restaurant->name }}" />
                </div>
                <div class="text-box">
                    <h1>{{ $restaurant->name }}</h1>
                    <p class="area">#{{ $restaurant->area->area_name }}</p>
                    <p class="genre">#{{ $restaurant->genre->genre_name }}</p>
                    <div class="link">
                        <a href="{{ route('restaurants.show', $restaurant->id) }}">詳しくみる</a>
                        @auth
                            <button id="favorite-btn-{{ $restaurant->id }}"
                                class="favorite-btn {{ in_array($restaurant->id, $favoriteIds) ? 'favorited' : '' }}"
                                onclick="toggleFavorite(this, {{ $restaurant->id }})">
                                &hearts;
                            </button>
                        @endauth
                    </div>
                </div>
            </article>
        @endforeach
    </div>

@section('js')
    <script src="{{ asset('js/restaurants.js') }}"></script>
@endsection
