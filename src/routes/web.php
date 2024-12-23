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
    //会員登録
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    //ログイン
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
        Route::patch('/reservations/{id}/update', 'update')->name('reservations.update');
    });
});

//レストラン関連
Route::controller(RestaurantController::class)->group(function () {
    //レストラン一覧
    Route::get('/restaurants', 'index')->name('restaurants.index');
    //レストラン詳細
    Route::get('/restaurants/{id}', 'show')->name('restaurants.show');
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
    //決済完了の表示
    Route::get('/checkout_done', function () {
        return view('checkout_done');
    })->name('checkout.done');
});



//管理者
Route::prefix('admin')->name('admin.')->group(function () {
    //管理者ログイン
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    //管理者ログイン処理
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    //管理者ログアウト
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    //管理者専用ページ
    Route::middleware('admin')->group(function () {
        //ダッシュボード
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        //店舗代表者作成
        Route::post('/store-owner/create', [AdminController::class, 'createStoreOwner'])->name('store_owner.create');
        //店舗代表者削除
        Route::delete('/store-owner/{id}', [AdminController::class, 'deleteStoreOwner'])->name('store_owner.delete');
        //お知らせ送信
        Route::post('/notification/send', [AdminController::class, 'sendNotification'])->name('notification.send');
    });
});

//Owner
Route::prefix('owner')->name('owner.')->group(function () {
    //ログイン画面
    Route::get('/login', [StoreOwnerController::class, 'showLoginForm'])->name('login');
    //ログイン処理
    Route::post('/login', [StoreOwnerController::class, 'login'])->name('login.post');
    //ログアウト
    Route::post('/logout', [StoreOwnerController::class, 'logout'])->name('logout');

    //Ownerダッシュボード
    Route::middleware(['auth', 'role:store-owner'])->group(function () {
        //ダッシュボード
        Route::get('/dashboard', [StoreOwnerController::class, 'index'])->name('dashboard');
        //店舗作成フォーム
        Route::get('/create', [StoreOwnerController::class, 'create'])->name('create');
        //店舗作成処理
        Route::post('/store', [StoreOwnerController::class, 'createStore'])->name('store');
        //店舗情報更新
        Route::post('/update', [StoreOwnerController::class, 'updateStore'])->name('update');
        //店舗検索
        Route::get('/search', [StoreOwnerController::class, 'searchStore'])->name('search');
    });
});