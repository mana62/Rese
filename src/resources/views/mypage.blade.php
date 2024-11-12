@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
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
    <div class="message">
        <p class="message-name">{{ $user->name }}さん</p>

        @if (session('message'))
            <div class="message-session">
                {{ session('message') }}
            </div>
        @endif
    </div>
    <div class="mypage">
        <!--予約状況-->
        <div class="left">
            <h2>予約状況</h2>
            @foreach ($reservations as $reservation)
                <div class="reservation-card" id="reservation-card-{{ $reservation->id }}">
                    <div class="reservation-header">
                        <div class="login__field">
                            <img src="img/icon_clock.png" alt="Clock Icon" class="reservation__icon">
                            <span class="reservation-title">予約 {{ $loop->iteration }}</span>
                            <button class="reservation-cancel-btn"
                                onclick="cancelReservation({{ $reservation->id }})">✕</button>
                        </div>
                    </div>
                    <div class="reservation-details">
                        <p><strong class="strong">Shop</strong> {{ $reservation->restaurant->name }}</p>
                        <p><strong class="strong">Date</strong> {{ $reservation->date }}</p>
                        <p><strong class="strong">Time</strong>
                            {{ \Carbon\Carbon::parse($reservation->time)->format('H:i') }}</p>
                        <p><strong class="strong">Number</strong> {{ $reservation->guests }}人</p>

                        <!--QR-->
                        <div class="qr-link">
                            <a href="{{ route('qr-code', $reservation->id) }}">QRコードを確認する</a>
                        </div>

                        <!--予約変更-->
                        <div class="button">
                            <button class="change-book__button"
                                onclick="document.getElementById('editReservationForm-{{ $reservation->id }}').style.display='block'">
                                予約を変更
                            </button>
                        </div>
                        <form class="change-book" id="editReservationForm-{{ $reservation->id }}" style="display:none"
                            action="{{ route('reservations.update', $reservation->id) }}" method="POST">
                            @csrf
                            <div class="change-book__div">
                                <label class="change-book_label">新しい日付</label>
                                <input class="change-book_input" type="date" name="date"
                                    value="{{ $reservation->date }}">
                            </div>
                            <div class="change-book__div">
                                <label class="change-book_label">新しい時間</label>
                                <input class="change-book_input" type="time" name="time"
                                    value="{{ \Carbon\Carbon::parse($reservation->time)->format('H:i') }}">
                            </div>
                            <div class="change-book__div">
                                <label class="change-book_label">人数</label>
                                <input class="change-book_input" type="number" name="guests" min="1"
                                    value="{{ $reservation->guests }}">
                            </div>
                            <div class="button">
                                <button class="change-book__submit" type="submit">変更を保存</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!--お気に入り店舗一覧-->
        <div class="right">
            <h2>お気に入り店舗</h2>
            <div class="favorite-shops">
                @foreach ($favorites as $favorite)
                    <div class="favorite-card" id="favorite-card-{{ $favorite->id }}">
                        <img src="{{ asset('img/' . $favorite->image) }}" alt="" class="favorite-img">
                        <div class="favorite-details">
                            <h3>{{ $favorite->name }}</h3>
                            <p> #{{ $favorite->area->area_name }} #{{ $favorite->genre->genre_name }}</p>
                            <a href="{{ route('shop-detail', $favorite->id) }}" class="favorite-link">詳しくみる</a>
                        </div>
                        <button class="favorite-btn {{ in_array($favorite->id, $favoriteIds) ? 'favorited' : '' }}"
                            onclick="toggleFavorite({{ $favorite->id }})">
                            &hearts;
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/mypage.js') }}"></script>
@endsection
