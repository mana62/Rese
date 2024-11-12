@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/restaurants.css') }}">
@endsection

@section('nav')
    <div class="nav">
        <div class="nav-search">
            <div class="nav-search__area">
                <select class="nav-search__area-select" name="area">
                    <option class="nav-search__area-option" value="" disabled {{ !$areaId ? 'selected' : '' }}>All area
                    </option>
                    @foreach ($areas ?? [] as $area)
                        <option class="nav-search__area-option" value="{{ $area->id }}"
                            {{ $areaId == $area->id ? 'selected' : '' }}>{{ $area->area_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="nav-search__genre">
                <select class="nav-search__genre-select" name="genre">
                    <option class="nav-search__genre-option" value="" disabled {{ !$genreId ? 'selected' : '' }}>All
                        genre</option>
                    @foreach ($genres ?? [] as $genre)
                        <option class="nav-search__genre-option" value="{{ $genre->id }}"
                            {{ $genreId == $genre->id ? 'selected' : '' }}>{{ $genre->genre_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="search">
                <form class="search__form" action="{{ route('restaurants.index') }}" method="GET">
                    @csrf
                    <img src="{{ asset('img/search-icon.png') }}" alt="search icon" class="search__icon">
                    <input class="search__form-input" type="text" name="input" placeholder="search..."
                        value="{{ $input ?? '' }}">
                </form>
            </div>
        </div>
    </div>
@endsection

@section('nav-js')
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
        <li><a href="/store-owner">OWNER</a></li>
    @endif
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
                        <a href="{{ route('shop-detail', $restaurant->id) }}">詳しくみる</a>
                        <button class="favorite-btn {{ in_array($restaurant->id, $favoriteIds) ? 'favorited' : '' }}"
                            onclick="toggleFavorite(this, {{ $restaurant->id }})">
                            &hearts;
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

        <!--ストレージ-->
        <form action="{{ route('images.store') }}" method="POST" enctype="multipart/form-data">
            <!--フォームでファイルをアップロードする時に必須の設定:enctype="multipart/form-data"-->
            @csrf
            <label for="image">画像をアップロード</label>
            <input type="file" name="image" id="image" required>
            <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
            <button type="submit">アップロード</button>
        </form>

    @section('js')
        <script src="{{ asset('js/restaurants.js') }}"></script>
    @endsection
