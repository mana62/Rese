@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('nav-js')
    <li><a href="/restaurants">HOME</a></li>
    <li><a href="/register">REGISTRATION</a></li>
    <li><a href="/login">LOGIN</a></li>
@endsection

@section('content')
    <div class="login">
        <div class="login__header"> Login </div>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="login__field">
                <img src="img/icon/icon_email.png" alt="Email Icon" class="login__icon">
                <input type="email" name="email" class="login__input" placeholder="Email" value="{{ old('email') }}"
                    autocomplete="email">
            </div>
            @error('email')
                <div class="login__error">{{ $message }}</div>
            @enderror

            <div class="login__field">
                <img src="img/icon/icon_password.png" alt="Password Icon" class="login__icon">
                <input type="password" name="password" class="login__input" placeholder="Password"
                    autocomplete="current-password">
            </div>
            @error('password')
                <div class="login__error">{{ $message }}</div>
            @enderror

            <div class="login__button">
                <button type="submit" class="login__button-submit">Login</button>
            </div>
        </form>
    </div>
@endsection
