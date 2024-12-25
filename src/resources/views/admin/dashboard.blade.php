@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endsection

@section('nav-js')
    <a href="{{ route('admin.logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        {{ __('LOGOUT') }}
    </a>
    <form id="logout-form" action="{{ route('admin.logout') }}" method="post" style="display: none;">
        @csrf
    </form>
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
    <div class="store-owner">
        <div class="store-owner__container">
            <h2 class="sub-ttl">店舗代表者作成フォーム</h2>
            <form class="store-owner__form" action="{{ route('admin.store_owner.create') }}" method="POST">
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
        <div class="store-owner__container">
            <h2>店舗代表者一覧</h2>
            @foreach ($storeOwners as $owner)
                @if ($owner->status === 'active')
                    <div class="owner-show">
                        <p class="owner-name">
                            {{ $loop->iteration }} .{{ $owner->name }}<br> ({{ $owner->email }})
                        </p>
                        <form action="{{ route('admin.store_owner.delete', $owner->id) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button class="owner-submit" type="submit">削除</button>
                        </form>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="store-owner__container">
            <h2>お知らせメール</h2>
            <form class="store-owner__form" action="{{ route('admin.notification.send') }}" method="POST">
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
