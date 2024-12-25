@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
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
    <div class="login_required">
        @if ($errors->has('login_required'))
            <div class="alert alert-danger">
                {{ $errors->first('login_required') }}
            </div>
        @endif
    </div>
    <div class="restaurant-details">
        <div class="left">
            <div class="header-flex">
                <h1>
                    <a href="{{ route('restaurants.index') }}" class="back-arrow">&lt;</a>
                    {{ $restaurant->name }}
                </h1>
                <form class="form-storage" action="{{ route('restaurants.uploadImage', ['id' => $restaurant->id]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="storage">
                        <div class="message-img">
                            @if (session('message'))
                                <div class="message-img__session">{{ session('message') }}</div>
                            @endif
                        </div>
                        <img id=imgMark src="{{ asset('img/icon/icon_download.png') }}" alt="Download Icon"
                            class="icon_download">
                        <input type="hidden" name="restaurantId" value="{{ $restaurant->id }}">
                        <button type="submit" class="save-button">画像を保存</button>
                    </div>
            </div>
            </form>
            <img src="{{ asset('img/' . $restaurant->image) }}" alt="{{ $restaurant->name }}">
            <p class="tag"><strong>#</strong> {{ $restaurant->area->area_name }}</p>
            <p class="tag"><strong>#</strong> {{ $restaurant->genre->genre_name }}</p>
            <p class="description">{{ $restaurant->description }}</p>
        </div>
        <div class="right">
            <h2>予約</h2>
            <form id="reservationForm" action="{{ route('booked.store') }}" method="POST">
                @csrf
                <input class="form-input" type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                <div>
                    <input type="date" id="date" name="date" required>
                    @error('date')
                        <div class="book__error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <input class="form-input" type="time" id="time" name="time" required placeholder="00:00"
                        value="10:00">
                    @error('time')
                        <div class="book__error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <input class="form-input" type="number" id="guests" name="guests" required min="1"
                        placeholder="1人">
                    @error('guests')
                        <div class="book__error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="confirm">
                    <p class="confirm-p"><strong class="strong">SHOP</strong> <span
                            class="restaurant-name">{{ $restaurant->name }}</span></p>
                    <p class="confirm-p" id="confirmDate"><strong class="strong">Date</strong>
                        <span id="confirmDateValue" class="confirm-value">未選択</span>
                    </p>
                    <p class="confirm-p" id="confirmTime"><strong class="strong">Time</strong>
                        <span id="confirmTimeValue" class="confirm-value">未選択</span>
                    </p>
                    <p class="confirm-p" id="confirmGuests"><strong class="strong">Number</strong>
                        <span id="confirmGuestsValue" class="confirm-value">未選択</span>
                    </p>
                </div>
                <button type="submit">予約する</button>
            </form>
        </div>
    </div>
    <div class="reviews">
        <div class="review-show">
            <h3 class="sub-ttl">レビュー一覧</h3>
            @foreach ($restaurant->reviews as $review)
                <div>
                    <div class="comment-strong">{{ $loop->iteration }} . {{ $review->user->name }}さんの投稿</div>
                    <div class="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star {{ $i <= $review->rating ? 'filled' : '' }}">&#9733;</span>
                        @endfor
                    </div>
                    <p class="comment-p">{{ $review->comment }}</p>
                </div>
            @endforeach
        </div>
        <div>
            @if (auth()->check())
                <h3 class="sub-ttl">レビューを投稿</h3>
                <form action="{{ route('reviews.store', $restaurant->id) }}" method="POST">
                    @csrf
                    <div class="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <input type="radio" name="rating" id="rating-{{ $i }}"
                                value="{{ $i }}" hidden>
                            <label for="rating-{{ $i }}" class="star">&#9733;</label>
                        @endfor
                    </div>
                    <div>
                        <label class="comment-label" for="comment">コメント</label>
                    </div>
                    <textarea class="comment" name="comment" id="comment" rows="3" required></textarea>
                    <button class="comment-submit" type="submit">評価を投稿</button>
                </form>
            @endif
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/detail.js') }}" defer></script>
    <script src="{{ asset('js/reviews.js') }}"></script>
@endsection
