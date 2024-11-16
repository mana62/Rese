@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verifys.css') }}">
@endsection

@section('nav-js')
    <li><a href="/restaurants">HOME</a></li>
    <li><a href="/register">REGISTRATION</a></li>
    <li><a href="/login">LOGIN</a></li>
@endsection

@section('content')
    <div class="verify-email">
        <h1 class="verify-email__ttl">メールアドレスの確認が必要です</h1>
        <p class="verify-email__paragraph">登録したメールアドレスに確認メールを送信しました</p>
        <p class="verify-email__paragraph">リンクをクリックして確認を完了してください</p>
        <form class="verify-email__form" method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="verify-email-button">
                <button class="verify-email-button__submit" type="submit">再送信</button>
            </div>
        </form>
    </div>
@endsection
