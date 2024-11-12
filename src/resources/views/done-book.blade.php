@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/done-book.css') }}">
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
    <div class="done">
        <p class="done__message">
            ご予約ありがとうございます
        </p>
        <div class="back__link">
            <a href="{{ route('restaurants.index') }}">戻る</a>
        </div>
    </div>
@endsection
