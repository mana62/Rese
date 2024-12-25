@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_login.css') }}">
@endsection

@section('nav-js')
<li><a href="/restaurants">HOME</a></li>
<li><a href="/register">REGISTRATION</a></li>
<li><a href="{{ route('admin.login') }}">LOGIN</a></li>
@endsection

@section('content')
    <div class="admin">
        <h1 class="login__header">Admin Login</h1>
        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="login__field">
                <img src="../img/icon/icon_password.png" alt="Password Icon" class="login__icon">
                <input type="password" name="password" class="login__input" placeholder="Admin Password"
                    autocomplete="current-password">
            </div>
            @error('password')
                <div class="login__error">{{ $message }}</div>
            @enderror
            <div class="login__button">
                <button type="submit" class="login__button-submit">ログイン</button>
            </div>
            <div class="user">
                <a class="user-link" href="{{ route('login') }}">※一般の方のログイン画面はこちら</a>
            </div>
        </form>
    </div>
@endsection
