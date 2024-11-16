@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
    <h1 class="main-ttl">管理者ダッシュボード</h1>

    <div class="message">
        @if (session('message'))
            <div class="message-session">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <!-- 店舗代表者作成フォーム -->
    <div class="store-owner">
        <div class="store-owner__container">
            <h2 class="sub-ttl">店舗代表者作成フォーム</h2>
            <form class="store-owner__form" action="{{ route('admin.createStoreOwner') }}" method="POST">
                @csrf
                <div>
                    <label for="name">名前</label>
                    @error('name')
                        <div class="store-owner__error">{{ $message }}</div>
                    @enderror
                    <input type="text" id="name" name="name" autocomplete="name">
                </div>
                <div>
                    <label for="email">メールアドレス</label>
                    @error('email')
                        <div class="store-owner__error">{{ $message }}</div>
                    @enderror
                    <input type="email" id="email" name="email" autocomplete="email">
                </div>
                <div>
                    <label for="password">パスワード</label>
                    @error('password')
                        <div class="store-owner__error">{{ $message }}</div>
                    @enderror
                    <input type="password" id="password" name="password" autocomplete="password">
                </div>
                <div class="submit-container">
                    <button type="submit">店舗代表者を作成</button>
                </div>
            </form>
        </div>


        <!-- 店舗代表者一覧と削除 -->
        <div class="store-owner__container">
            <h2>店舗代表者一覧</h2>
            @foreach ($storeOwners as $storeOwner)
                @if ($storeOwner->status === 'active')
                    <div class="owner-show">
                        <p class="owner-name">
                            {{ $loop->iteration }} .{{ $storeOwner->name }} ({{ $storeOwner->email }})
                        <form action="{{ route('admin.deleteStoreOwner', $storeOwner->id) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            <button class="owner-submit" type="submit">削除</button>
                        </form>
                        </p>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- お知らせメール送信 -->
        <div class="store-owner__container">
            <h2>お知らせメール</h2>
            <form class="store-owner__form" action="{{ route('mail.notice') }}" method="POST">
                @csrf
                <label for="message">お知らせ内容</label>
                @error('message')
                    <div class="store-owner__error">{{ $message }}</div>
                @enderror
                <textarea name="message"></textarea>
                <label for="role">送信対象</label>
                <select class="role-select" name="role" id="role">
                    <option value="all">All</option>
                    <option value="user">USER</option>
                    <option value="store-owner">SHOP-OWNER</option>
                </select>

                <div class="submit-container">
                    <button type="submit">お知らせを送信</button>
                </div>
            </form>
        </div>
    @endsection
