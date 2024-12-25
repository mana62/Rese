@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/booked.css') }}">
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
    <div class="done">
        <p class="done__message">
            ご予約ありがとうございます
        </p>
        <div class="back__link">
            <a href="{{ route('restaurants.index') }}">戻る</a>
        </div>
    </div>
@endsection
