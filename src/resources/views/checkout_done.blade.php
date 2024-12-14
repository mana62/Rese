@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/checkout_done.css') }}">
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
@endsection

@section('content')
    <div class="checkout-done">
        <p class="checkout-done__message">お支払いありがとうございます</p>
        <div class="mypage__link">
            <a href="{{ route('mypage') }}">マイページへ戻る</a>
        </div>
    </div>
@endsection
