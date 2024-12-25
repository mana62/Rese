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
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Support\Facades\Route;

//ゲスト
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

//メール認証
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

//thanksページ
Route::get('/thanks', [RegisteredUserController::class, 'index'])->name('thanks');
Route::post('/thanks', [RegisteredUserController::class, 'store'])->name('thanks.store');

//認証済み
Route::middleware(['auth', 'verified'])->group(function () {
    Route::controller(RegisteredUserController::class)->group(function () {
        Route::get('/mypage', 'mypage')->name('mypage');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
        Route::post('/restaurants/{id}/favorite', 'toggleFavorite')->name('restaurants.favorite');
        Route::post('/reservations/{id}/cancel', 'cancelReservation')->name('reservations.cancel');
    });

    //予約関連
    Route::controller(ReservationController::class)->group(function () {
        Route::get('/booked', 'index')->name('booked');
        Route::post('/booked', 'store')->name('booked.store');
        Route::get('/qr/{id}', 'showQrCode')->name('qr');
        Route::patch('/reservations/{id}/update', 'update')->name('reservations.update');
    });
});

//レストラン関連
Route::controller(RestaurantController::class)->group(function () {
    Route::get('/restaurants', 'index')->name('restaurants.index');
    Route::get('/restaurants/{id}', 'show')->name('restaurants.show');
    Route::post('/restaurants/{id}/upload-image', [ImageController::class, 'saveImage'])->name('restaurants.uploadImage');
});

//レビュー
Route::post('/restaurants/{restaurant}/reviews', [ReviewController::class, 'storeReview'])->name('reviews.store');

//Stripe決済
Route::controller(CheckoutController::class)->group(function () {
    Route::get('/checkout/{reservation_id}', 'checkoutForm')->name('checkout');
    Route::post('/process-payment', 'processPayment')->name('checkout.process');
    Route::get('/checkout-return', 'handleReturn')->name('checkout.return');
    Route::get('/checkout_done', function () {
        return view('checkout_done');
    })->name('checkout.done');
});



//admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/store-owner/create', [AdminController::class, 'createStoreOwner'])->name('store_owner.create');
        Route::delete('/store-owner/{id}', [AdminController::class, 'deleteStoreOwner'])->name('store_owner.delete');
        Route::post('/notification/send', [AdminController::class, 'sendNotification'])->name('notification.send');
    });
});

//Owner
Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/login', [StoreOwnerController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StoreOwnerController::class, 'login'])->name('login.post');
    Route::post('/logout', [StoreOwnerController::class, 'logout'])->name('logout');
    Route::middleware(['auth', 'role:store-owner'])->group(function () {
        Route::get('/dashboard', [StoreOwnerController::class, 'index'])->name('dashboard');
        Route::get('/create', [StoreOwnerController::class, 'create'])->name('create');
        Route::post('/store', [StoreOwnerController::class, 'createStore'])->name('store');
        Route::post('/update', [StoreOwnerController::class, 'updateStore'])->name('update');
        Route::get('/search', [StoreOwnerController::class, 'searchStore'])->name('search');
    });
});