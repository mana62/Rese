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

    public function processPayment(CheckoutRequest $request)
    {
        Log::info('Received payment request:', $request->all());

        try {
            //バリデーションを取得
            $validated = $request->all();

            //予約があるか探す
            $reservation = Reservation::find($validated['reservation_id']);
            if (!$reservation) {
                return response()->json(['success' => false, 'error' => '予約が見つかりません'], 404);
            }

            //キャンセルされた予約の場合
            if ($reservation->status === Reservation::STATUS_CANCELED) {
                return response()->json(['success' => false, 'error' => '予約がキャンセルされているためお支払いできません'], 400);
            }

            //stripeを初期化
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
                    'restaurant_id' => $reservation->restaurant_id,
                    'reservation_id' => $reservation->id,
                ],
            ]);

            //情報をデータベースに保存 (DB::transaction = 操作の途中でエラーが起きても変更が中途半端に適用されないようにする仕組み)
            DB::transaction(function () use ($paymentIntent, $reservation, $validated) {
                Checkout::create([
                    'user_id' => $reservation->user_id,
                    'restaurant_id' => $reservation->restaurant_id,
                    'reservation_id' => $reservation->id,
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $validated['amount'],
                    'status' => $paymentIntent->status === 'succeeded' ? 'success' : 'pending',
                    'currency' => $validated['currency'],
                ]);
            });

            //支払い後のメッセージ表示
            if ($paymentIntent->status === 'succeeded') {
                return response()->json(['success' => true, 'message' => '支払いが完了しました']);
            } else {
                return response()->json(['success' => false, 'message' => '支払いが未完了です']);
            }

            //エラー (Exception) が発生したときの内容を変数$eに入れる ($e->getMessage() = エラーの内容を$eに代入)
        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Card error during payment', [
                'stripe_code' => $e->getStripeCode(),
                'error_message' => $e->getMessage(),
                'error_param' => $e->getError()->param ?? null,
            ]);
            return response()->json(['success' => false, 'error' => 'カードエラー: ' . $e->getMessage()], 400);
        } catch (\Exception $e) {
            Log::error('Payment processing error', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'error' => 'エラー: ' . $e->getMessage()], 500);
        }
    }
}
