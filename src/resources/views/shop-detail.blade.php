@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/shop-detail.css') }}">
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
    <div class="restaurant-details">
        <!--レストラン詳細-->
        <div class="left">
            <h2>
                <a href="{{ route('restaurants.index') }}" class="back-arrow">&lt;</a>
                {{ $restaurant->name }}
            </h2>
            <img src="{{ asset('img/' . $restaurant->image) }}" alt="{{ $restaurant->name }}">
            <p><strong>#</strong> {{ $restaurant->area->area_name }}</p>
            <p><strong>#</strong> {{ $restaurant->genre->genre_name }}</p>
            <p>{{ $restaurant->description }}</p>
        </div>

        <div class="right">
            <!--予約-->
            <h2>予約</h2>
            <form id="reservationForm" action="{{ route('done-book.store') }}" method="POST">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                <div>
                    <input type="date" id="date" name="date" required>
                    @error('date')
                        <div class="book__error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <input type="time" id="time" name="time" required placeholder="00:00" value="10:00">
                    @error('time')
                        <div class="book__error">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <input type="number" id="guests" name="guests" required min="1" placeholder="1人"
                        value="1人">
                    @error('guests')
                        <div class="book__error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="confirm">
                    <p class="confirm-p"><strong class="strong">Shop</strong> {{ $restaurant->name }}</p>
                    <p class="confirm-p" id="confirmDate"><strong class="strong">Date</strong></p>
                    <p class="confirm-p" id="confirmTime"><strong class="strong">Time</strong></p>
                    <p class="confirm-p" id="confirmGuests"><strong class="strong">Number</strong>人</p>
                </div>
                <button type="submit">予約する</button>
            </form>
        </div>
    </div>

    <!--レビュー投稿-->
    <div class="reviews">
        <div>
            @if (auth()->check())
                <h3>レビューを投稿</h3>
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

        <div class="review-show">
            <h3>レビュー一覧</h3>
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
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/shop-detail.js') }}"></script>
    <script src="{{ asset('js/reviews.js') }}"></script>
@endsection
