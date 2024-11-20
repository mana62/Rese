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

//ゲストユーザー用
Route::middleware(['guest'])->group(function () {
    // 会員登録
    Route::get('/', [RegisteredUserController::class, 'create'])->name('auth.register');
    Route::post('/', [RegisteredUserController::class, 'store'])->name('register.store');

    // ログイン
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

//メール認証
Auth::routes(['verify' => true]);

//thanksページ
Route::get('/thanks', [RegisteredUserController::class, 'index'])->name('thanks');
Route::post('/thanks', [RegisteredUserController::class, 'store'])->name('thanks.store');

//レストラン一覧
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index')->middleware('guest');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('detail');

//認証済みユーザー用 (auth)
Route::middleware(['auth'])->group(function () {
    //マイページ
    Route::get('/mypage', [RegisteredUserController::class, 'mypage'])
        ->middleware('verified')
        ->name('mypage');

    //メール認証通知ページ
    Route::get('/verify', function () {
        return view('auth.verify');
    })->middleware('auth')->name('verification.notice');

    //ログアウト
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    //お気に入り登録
    Route::post('/restaurants/{id}/favorite', [RegisteredUserController::class, 'toggleFavorite'])->name('restaurants.favorite');

    //予約キャンセル
    Route::post('/reservations/{id}/cancel', [RegisteredUserController::class, 'cancelReservation'])->name('reservations.cancel');
});


//レストラン関連
//レストランの画像アップロード
Route::post('/restaurants/{id}/upload-image', [ImageController::class, 'store'])->name('restaurants.uploadImage');

//予約完了ページ
Route::get('/booked', [ReservationController::class, 'index'])->name('booked');
Route::post('/booked', [ReservationController::class, 'store'])->name('booked.store');

//QRコード表示
Route::get('/qr/{id}', [ReservationController::class, 'showQrCode'])->name('qr');

//予約変更
Route::post('/reservations/{id}/update', [ReservationController::class, 'update'])->name('reservations.update');

//評価投稿
Route::post('/restaurants/{restaurant}/reviews', [ReviewController::class, 'storeReview'])->name('reviews.store');


//管理者 (Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::post('/admin/create', [AdminController::class, 'createStoreOwner'])->name('admin.createStoreOwner');
    Route::post('/admin/user-to-store-owner/{id}', [AdminController::class, 'userToStoreOwner'])->name('admin.userToStoreOwner');
    Route::post('/admin/owner/{id}/delete', [AdminController::class, 'deleteStoreOwner'])->name('admin.deleteStoreOwner');
    Route::post('/admin/send-notification', [AdminController::class, 'sendNotification'])->name('mail.notice');
});


//店舗代表者 (Store Owner)
Route::middleware(['auth', 'role:store-owner'])->group(function () {
    Route::get('/owner', [StoreOwnerController::class, 'index'])->name('owner');
    Route::get('/owner/create', [StoreOwnerController::class, 'create'])->name('owner.create');
    Route::post('/owner/store', [StoreOwnerController::class, 'createStore'])->name('owner.createStore');
    Route::post('/owner/update', [StoreOwnerController::class, 'updateStore'])->name('owner.updateStore');
    Route::get('/owner/search', [StoreOwnerController::class, 'searchStore'])->name('owner.searchStore');
});

//Stripe（決済）
Route::get('/checkout', [CheckoutController::class, 'checkoutForm'])->name('checkout.form');
Route::post('/checkout', [CheckoutController::class, 'processPayment'])->name('checkout.process');