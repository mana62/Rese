@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
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
    <a href="{{ route('mypage') }}" class="back-arrow">&lt;</a>
    <h1 class="ttl">Checkout</h1>
    <div class="message" id="payment-result"></div>
    <input type="hidden" id="reservation-id" value="{{ $reservation->id }}">
    <input type="hidden" id="restaurant-id" value="{{ $reservation->restaurant_id }}">
    <form class="payment-form-content" id="payment-form">
        @csrf
        <input type="hidden" id="reservation-id" value="{{ $reservation->id }}">
        <input type="text" id="amount" class="payment-input" placeholder="金額を入力 (円)" required>
        <div class="card-element-form" id="card-element"></div>
        <div class="payment-button">
            <button class="payment-button-submit" type="submit">支払う</button>
        </div>
    </form>
@endsection

@section('js')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        window.stripePublicKey = "{{ env('STRIPE_KEY') }}";
    </script>
    <script src="{{ asset('js/checkout.js') }}"></script>
@endsection
