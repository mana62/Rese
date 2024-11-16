@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/owner.css') }}">
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
        <li><a href="/owner">OWNER</a></li>
    @endif
@endsection

@section('content')
    <h1 class="main-ttl">店舗管理ダッシュボード</h1>

    <div class="message">
        @if (session('message'))
            <div class="message-session">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <div class="store-management">
        <!-- 店舗情報作成 -->
        <div class="store-form">
            <h2>店舗情報を作成</h2>
            <form action="{{ route('owner.createStore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="name">店舗名</label>
                @error('name')
                    <div class="store-form__error">{{ $message }}</div>
                @enderror
                <input type="text" id="name" name="name" value="{{ old('name', $restaurant->name ?? '') }}">

                <label for="address">住所</label>
                @error('address')
                    <div class="store-form__error">{{ $message }}</div>
                @enderror
                <input type="text" id="address" name="address"
                    value="{{ old('address', $restaurant->address ?? '') }}">

                <label for="area">エリア</label>
                @error('area_id')
                    <div class="store-info__error">{{ $message }}</div>
                @enderror
                <select id="area" name="area_id">
                    <option value="">選択してください</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}"
                            {{ old('area_id', $restaurant->area_id ?? '') == $area->id ? 'selected' : '' }}>
                            {{ $area->area_name }}
                        </option>
                    @endforeach
                </select>

                <label for="genre">ジャンル</label>
                @error('genre_id')
                    <div class="store-info__error">{{ $message }}</div>
                @enderror
                <select id="genre" name="genre_id" value="{{ old('genre_id') }}">
                    <option value="">選択してください</option>
                    @foreach ($genres as $genre)
                        <option value="{{ $genre->id }}"
                            {{ old('genre_id', $restaurant->genre_id ?? '') == $genre->id ? 'selected' : '' }}>
                            {{ $genre->genre_name }}
                        </option>
                    @endforeach
                </select>

                <label for="description">店舗説明</label>
                @error('description')
                    <div class="store-form__error">{{ $message }}</div>
                @enderror
                <textarea id="description" name="description">{{ old('description', $restaurant->description ?? '') }}</textarea>

                <label for="image">画像</label>
                <input type="file" id="image" name="image" value="{{ old('image') }}">
                @if ($restaurant && $restaurant->image)
                    <img src="{{ asset('storage/' . $restaurant->image) }}" alt="店舗画像" width="200">
                @endif



                <button type="submit">作成</button>
            </form>
        </div>

        <!-- 店舗情報更新 -->
        <div class="store-info">
            <h2>店舗情報の更新</h2>
            @if ($restaurant)
                <form action="{{ route('owner.updateStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="name">店舗名</label>
                    @error('name')
                        <div class="store-info__error">{{ $message }}</div>
                    @enderror
                    <input type="text" id="name" name="name" value="{{ old('name', $restaurant->name ?? '') }}">

                    <label for="address">住所</label>
                    @error('address')
                        <div class="store-info__error">{{ $message }}</div>
                    @enderror
                    <input type="text" id="address" name="address"
                        value="{{ old('address', $restaurant->address ?? '') }}">

                    <label for="area">エリア</label>
                    @error('area_id')
                        <div class="store-info__error">{{ $message }}</div>
                    @enderror
                    <select id="area" name="area_id">
                        <option value="">選択してください</option>
                        @foreach ($areas as $area)
                            <option value="{{ $genre->id }}"
                                {{ old('genre_id', $restaurant->genre_id ?? '') == $genre->id ? 'selected' : '' }}>
                                {{ $genre->genre_name }}
                            </option>
                        @endforeach
                    </select>

                    <label for="genre">ジャンル</label>
                    @error('genre_id')
                        <div class="store-info__error">{{ $message }}</div>
                    @enderror
                    <select id="genre" name="genre_id">
                        <option value="">選択してください</option>
                        @foreach ($genres as $genre)
                            <option value="{{ $genre->id }}"
                                {{ old('genre_id', $restaurant->genre_id ?? '') == $genre->id ? 'selected' : '' }}>
                                {{ $genre->genre_name }}
                            </option>
                        @endforeach
                    </select>

                    <label for="description">店舗説明</label>
                    @error('description')
                        <div class="store-info__error">{{ $message }}</div>
                    @enderror
                    <textarea id="description" name="description">{{ old('description', $restaurant->description ?? '') }}</textarea>

                    <button type="submit">更新</button>

                    <label for="image">画像</label>
                    <input type="file" id="image" name="image">
                    @if ($restaurant && $restaurant->image)
                        <img src="{{ asset('storage/' . $restaurant->image) }}" alt="店舗画像" width="200">
                    @endif


                </form>
            @else
                <p>店舗情報が設定されていません</p>
            @endif
        </div>

        <!-- 予約確認 -->
        <div class="reservations">
            <h2>予約一覧</h2>
            @if ($reservations->isEmpty())
                <p>予約がありません</p>
            @else
                <ul>
                    @foreach ($reservations as $reservation)
                        <li>{{ $reservation->date }} - {{ $reservation->user->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
