<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Checkout;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CheckoutRequest;

class CheckoutController extends Controller
{
    public function checkoutForm($reservation_id)
    {
        //お店の予約を見つけて支払いイメージを表示
        $reservation = Reservation::find($reservation_id);

        //予約がなかったらマイページへ
        if (!$reservation) {
            return redirect()->route('mypage')->with('error', '予約が見つかりません');
        }

        return view('checkout', compact('reservation'));
    }


    //支払い処理を実行
    public function processPayment(CheckoutRequest $request)
    {
        Log::info('Received payment request:', $request->all());

        try {
            //リクエスト取得
            $validated = $request->all();

            //予約情報を取得し、キャンセル済みでないか確認
            $reservation = Reservation::findOrFail($validated['reservation_id']);
            if ($reservation->status === Reservation::STATUS_CANCELED) {
                return response()->json([
                    'success' => false,
                    'status' => 'canceled',
                    'message' => '予約がキャンセルされているため、お支払いできません',
                ], 400);
            }

            //キャンセルされた予約の場合
            if ($reservation->status === Reservation::STATUS_CANCELED) {
                return response()->json(['success' => false, 'error' => '予約がキャンセルされているためお支払いできません'], 400);
            }

            //Stripeの初期化
            Stripe::setApiKey(env('STRIPE_SECRET'));

            //決済情報（PaymentIntent）の作成
            $paymentIntent = PaymentIntent::create([
                'amount' => $validated['amount'] * 100, //最小単位（1円=100）で指定
                'currency' => $validated['currency'], //通貨を円に設定
                'payment_method' => $validated['payment_method'], //支払い方法
                'confirmation_method' => 'automatic',
                'confirm' => true, //支払いをすぐに実行
                'return_url' => route('checkout.return'), //支払いが完了した後にユーザーが戻るページ
                'metadata' => [
                    'user_id' => $reservation->user_id,
                    'reservation_id' => $reservation->id,
                ],
            ]);

            //支払い成功時の処理
            if ($paymentIntent->status === 'succeeded') {

                //データベースに保存(DB::transaction = 操作の途中でエラーが起きても変更が中途半端に適用されないようにする仕組み)
                DB::transaction(function () use ($reservation, $paymentIntent) {
                    //Checkout テーブルのステータス更新または作成
                    $checkout = Checkout::updateOrCreate(
                        ['reservation_id' => $reservation->id],
                        [
                            'user_id' => $reservation->user_id,
                            'payment_intent_id' => $paymentIntent->id,
                            'amount' => $paymentIntent->amount / 100, //金額を元の単位に戻す
                            'status' => 'success',
                            'currency' => $paymentIntent->currency,
                        ]
                    );
                });

                //成功の場合checkout_doneに飛ぶ
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('checkout.done'),
                ]);

            }

            //支払いが未完了の場合の処理
            return response()->json([
                'success' => false,
                'status' => 'pending',
                'message' => '支払いが未完了です',
            ]);

            //エラー (Exception) が発生したときの内容を変数$eに入れる ($e->getMessage() = エラーの内容を$eに代入)
        } catch (\Stripe\Exception\CardException $e) {

            //カードエラーの処理
            Log::error('Card error during payment', [
                'stripe_code' => $e->getStripeCode(),
                'error_message' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'カードエラーが発生しました：' . $e->getMessage(),
            ], 400);

        } catch (\Exception $e) {
            //その他のエラー
            Log::error('Payment processing error', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'エラーが発生しました',
            ], 500);
        }
    }
}