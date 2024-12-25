@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('nav-js')
    <li><a href="/restaurants">HOME</a></li>
    <li><a href="/register">REGISTRATION</a></li>
    <li><a href="/login">LOGIN</a></li>
@endsection

@section('content')
    <div class="registration">
        <h1 class="registration__header"> Registration </h1>
        <form action="{{ route('register.store') }}" method="POST">
            @csrf
            <div class="registration__field">
                <img src="img/icon/icon_user.png" alt="User Icon" class="registration__icon">
                <input type="text" name="name" class="registration__input" placeholder="Username"
                    value="{{ old('name') }}" autocomplete="name">
            </div>
            @error('name')
                <div class="register__error">{{ $message }}</div>
            @enderror
            <div class="registration__field">
                <img src="img/icon/icon_email.png" alt="Email Icon" class="registration__icon">
                <input type="email" name="email" class="registration__input" placeholder="Email"
                    value="{{ old('email') }}" autocomplete="email">
            </div>
            @error('email')
                <div class="register__error">{{ $message }}</div>
            @enderror
            <div class="registration__field">
                <img src="img/icon/icon_password.png" alt="Password Icon" class="registration__icon">
                <input type="password" name="password" class="registration__input" placeholder="Password"
                    autocomplete="new-password">
            </div>
            @error('password')
                <div class="register__error">{{ $message }}</div>
            @enderror
            <div class="registration__field">
                <img src="img/icon/icon_password.png" alt="Confirm Password Icon" class="registration__icon">
                <input type="password" name="password_confirmation" class="registration__input"
                    placeholder="Confirm Password" autocomplete="new-password">
            </div>
            @error('password_confirmation')
                <div class="register__error">{{ $message }}</div>
            @enderror
            <div class="registration__button">
                <button type="submit" class="registration__button-submit">登録</button>
            </div>
        </form>
    </div>
@endsection
