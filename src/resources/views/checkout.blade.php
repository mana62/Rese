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
    <h1>Stripe Checkout</h1>
    <form id="payment-form">
        @csrf
        <input type="hidden" id="csrf-token" value="{{ csrf_token() }}">
        <input type="number" id="amount" placeholder="金額を入力 (円)" required>
        <div id="card-element"></div>
        <button type="submit" id="submit-button">支払う</button>
    </form>
    <div id="payment-result"></div>
@endsection

<script>
    window.stripePublicKey = "{{ env('STRIPE_KEY') }}";
</script>
<script src="{{ asset('js/checkout.js') }}"></script>
