@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/owner_dashboard.css') }}">
@endsection

@section('nav-js')
    <li>
        <a href="{{ route('owner.logout') }}"
            onclick="event.preventDefault();document.getElementById('logout-form').submit();">
            {{ __('LOGOUT') }}
        </a>
        <form id="logout-form" action="{{ route('owner.logout') }}" method="post" style="display: none;">
            @csrf
        </form>
    </li>
@endsection

@section('content')
    <div class="header-container">
        <h1 class="main-ttl">店舗オーナーダッシュボード</h1>
        <div class="search-bar">
            <form class="search-bar__form" action="{{ route('owner.search') }}" method="GET">
                <input class="search-bar__input" type="text" name="search" placeholder="店舗名で検索"
                    value="{{ request('search') }}">
                <button class="search-bar__button" type="submit">検索</button>
            </form>
        </div>
    </div>
    <div class="message">
        @if (session('message'))
            <div class="message-session">
                {{ session('message') }}
            </div>
        @endif
    </div>
    <div class="store-management">
        <div class="store-form">
            <h2>店舗情報を作成</h2>
            <form action="{{ route('owner.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="name">店舗名</label>
                @error('name')
                    <div class="store-form__error">{{ $message }}</div>
                @enderror
                <input type="text" id="name" name="name" value="{{ old('name') }}">
                <label for="address">住所</label>
                @error('address')
                    <div class="store-form__error">{{ $message }}</div>
                @enderror
                <input type="text" id="address" name="address" value="{{ old('address') }}">
                <label for="area">エリア</label>
                @error('area_id')
                    <div class="store-info__error">{{ $message }}</div>
                @enderror
                <select id="area" name="area_id">
                    <option value="">選択してください</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                            {{ $area->area_name }}
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
                        <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>
                            {{ $genre->genre_name }}
                        </option>
                    @endforeach
                </select>
                <label for="description">店舗説明</label>
                @error('description')
                    <div class="store-form__error">{{ $message }}</div>
                @enderror
                <textarea id="description" name="description">{{ old('description') }}</textarea>
                <label for="image">画像</label>
                <input type="file" id="image" name="image">
                <button type="submit">作成</button>
            </form>
        </div>
        <div class="store-info">
            <h2>店舗情報の更新</h2>
            @if ($restaurant)
                <form action="{{ route('owner.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                    <label for="name">店舗名</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $restaurant->name) }}">
                    <label for="address">住所</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $restaurant->address) }}">
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
                        <div class="store-form__error">{{ $message }}</div>
                    @enderror
                    <textarea id="description" name="description">{{ old('description', $restaurant->description ?? '') }}</textarea>
                    <label for="image">画像</label>
                    <input type="file" id="image" name="image">
                    <button type="submit">更新</button>
                </form>
            @else
                <p>店舗情報がありません</p>
            @endif
        </div>
        <div class="reservations">
            <h2>予約一覧</h2>
            @if ($reservations->isEmpty())
                <p>予約がありません</p>
            @else
                @foreach ($reservations as $reservation)
                    <table class="reservations-table">
                        <tr class="reservations-tr">
                            <th class="reservations-th">お客様名</th>
                            <th class="reservations-th">曜日</th>
                            <th class="reservations-th">時間</th>
                            <th class="reservations-th">人数</th>
                        </tr>
                        <tr class="{{ $reservation->date < now() ? 'expired' : '' }}">
                            <td class="reservations-td">{{ $reservation->user->name }}様</td>
                            <td class="reservations-td">{{ $reservation->date }}</td>
                            <td class="reservations-td">{{ $reservation->time }}</td>
                            <td class="reservations-td">{{ $reservation->guests }}</td>
                        </tr>
                @endforeach
                </table>
                {{ $reservations->links() }}
            @endif
        </div>
    </div>
@endsection
