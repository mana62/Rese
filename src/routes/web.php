<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StoreOwnerController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

//ゲストユーザールート
//ログインしていないユーザー（会員登録、ログイン）
Route::middleware(['guest'])->group(function () {
    //会員登録
    Route::get('/', [RegisteredUserController::class, 'create'])->name('auth.register');
    Route::post('/', [RegisteredUserController::class, 'store'])->name('register.store');

    //ログイン
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

//メール認証
Auth::routes(['verify' => true]); // Laravelが提供するメール認証関連のルート

//認証済み
Route::middleware(['auth', 'verified'])->group(function () {
    //マイページ関連
    Route::controller(RegisteredUserController::class)->group(function () {
        //マイページ表示
        Route::get('/mypage', 'mypage')->name('mypage');
        //ログアウト
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        //お気に入り登録
        Route::post('/restaurants/{id}/favorite', 'toggleFavorite')->name('restaurants.favorite');
        //予約キャンセル
        Route::post('/reservations/{id}/cancel', 'cancelReservation')->name('reservations.cancel');
    });

    //予約関連
    Route::controller(ReservationController::class)->group(function () {
        //予約完了ページ
        Route::get('/booked', 'index')->name('booked');
        //予約を保存
        Route::post('/booked', 'store')->name('booked.store');
        //QRコード表示
        Route::get('/qr/{id}', 'showQrCode')->name('qr');
        //予約変更
        Route::post('/reservations/{id}/update', 'update')->name('reservations.update');
    });
});

//レストラン関連
Route::controller(RestaurantController::class)->group(function () {
    //レストラン一覧
    Route::get('/restaurants', 'index')->name('restaurants.index');
    //レストラン詳細
    Route::get('/restaurants/{id}', 'show')->name('detail');
    //画像アップロード
    Route::post('/restaurants/{id}/upload-image', [ImageController::class, 'saveImage'])->name('restaurants.uploadImage');
});

//レビュー
Route::post('/restaurants/{restaurant}/reviews', [ReviewController::class, 'storeReview'])->name('reviews.store');

//Stripe決済
Route::controller(CheckoutController::class)->group(function () {
    //決済画面
    Route::get('/checkout/{reservation_id}', 'checkoutForm')->name('checkout');
    //決済処理
    Route::post('/process-payment', 'processPayment')->name('checkout.process');
    //決済完了後のリダイレクト
    Route::get('/checkout-return', 'handleReturn')->name('checkout.return');
});

//管理者
Route::middleware(['auth', 'role:admin'])->controller(AdminController::class)->group(function () {
    //管理者ページ
    Route::get('/admin', 'index')->name('admin');
    //店舗代表者作成
    Route::post('/admin/create', 'createStoreOwner')->name('admin.createStoreOwner');
    //ユーザーを店舗代表者に設定
    Route::post('/admin/user-to-store-owner/{id}', 'userToStoreOwner')->name('admin.userToStoreOwner');
    //店舗代表者削除
    Route::post('/admin/owner/{id}/delete', 'deleteStoreOwner')->name('admin.deleteStoreOwner');
    //通知送信
    Route::post('/admin/send-notification', 'sendNotification')->name('mail.notice');
});

//店舗代表者用ルート
Route::middleware(['auth', 'role:store-owner'])->controller(StoreOwnerController::class)->group(function () {
    //店舗代表者ページ
    Route::get('/owner', 'index')->name('owner');
    //店舗作成フォーム
    Route::get('/owner/create', 'create')->name('owner.create');
    //店舗作成処理
    Route::post('/owner/store', 'createStore')->name('owner.createStore');
    //店舗情報更新
    Route::post('/owner/update', 'updateStore')->name('owner.updateStore');
    //店舗検索
    Route::get('/owner/search', 'searchStore')->name('owner.searchStore');
});

//thanksページ
Route::get('/thanks', [RegisteredUserController::class, 'index'])->name('thanks');
Route::post('/thanks', [RegisteredUserController::class, 'store'])->name('thanks.store');
