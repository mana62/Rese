@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/qr-code.css') }}">
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
    <h1>QRコード</h1>
    <p>以下のQRコードを店舗で提示してください</p>

    <div class="qr-container">
        @if ($reservation->qr_code)
            <div class="qr-img">
                <img src="{{ asset('storage/' . $reservation->qr_code) }}" alt="QR Code">
            </div>
        @else
            <p>QRコードが生成されていません</p>
        @endif
    </div>

    <div class="qr-link">
        <a href="{{ route('mypage') }}" class="btn btn-secondary">マイページに戻る</a>
    </div>
@endsection
