@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/thanks.css') }}">
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
    <div id="thanks-message" class="thanks">
        <p class="thanks__message">
            会員登録ありがとうございます
        </p>
        <div class="login__link">
            <a href="{{ route('login') }}">ログインする</a>
        </div>
    </div>
@endsection
