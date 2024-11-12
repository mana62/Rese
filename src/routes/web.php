<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StoreOwnerController;

Route::get('/', function () {
    return view('auth.register');
});

//会員登録
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

Auth::routes(['verify' => true]);

//thanksページ
Route::get('/thanks', [RegisteredUserController::class, 'index'])->name('thanks');
Route::post('/thanks', [RegisteredUserController::class, 'store'])->name('thanks.store');

//ログイン
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

//マイページとログアウト
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/mypage', [RegisteredUserController::class, 'mypage'])->name('mypage');
    Route::post('/restaurants/{id}/favorite', [RegisteredUserController::class, 'toggleFavorite'])->name('restaurants.favorite');
    Route::post('/reservations/{id}/cancel', [RegisteredUserController::class, 'cancelReservation'])->name('reservations.cancel');
});

//レストラン
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('shop-detail');
Route::post('/restaurants/{id}/favorite', [RegisteredUserController::class, 'toggleFavorite'])->name('restaurants.favorite');


//予約完了
Route::get('/done-book', [ReservationController::class, 'index'])->name('done-book');
Route::post('/done-book', [ReservationController::class, 'store'])->name('done-book.store');

//QR
Route::get('/qr-code/{id}', [ReservationController::class, 'showQrCode'])->name('qr-code');

//予約変更
Route::post('/reservations/{id}/update', [ReservationController::class, 'update'])->name('reservations.update');

//評価
Route::post('/restaurants/{restaurant}/reviews', [ReviewController::class, 'storeReview'])->name('reviews.store');

//画像（ストレージ）
Route::post('/images/store', [ImageController::class, 'store'])->name('images.store');

//管理者
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::post('/admin/create', [AdminController::class, 'createStoreOwner'])->name('admin.createStoreOwner');
    Route::post('/admin/user-to-store-owner/{id}', [AdminController::class, 'userToStoreOwner'])->name('admin.userToStoreOwner');
    Route::post('/admin/store-owner/{id}/delete', [AdminController::class, 'deleteStoreOwner'])->name('admin.deleteStoreOwner');
    Route::post('/admin/send-notification', [AdminController::class, 'sendNotification'])->name('mail.notice');
});

//店舗代表者
Route::middleware(['auth', 'role:store-owner'])->group(function () {
    Route::get('/store-owner', [StoreOwnerController::class, 'index'])->name('store-owner');
    Route::get('/store-owner/create', [StoreOwnerController::class, 'create'])->name('store-owner.create');
    Route::post('/store-owner/store', [StoreOwnerController::class, 'createStore'])->name('store-owner.createStore');
    Route::post('/store-owner/update', [StoreOwnerController::class, 'updateStore'])->name('store-owner.updateStore');
});


