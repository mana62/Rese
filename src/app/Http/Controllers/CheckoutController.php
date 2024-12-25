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
        $reservation = Reservation::find($reservation_id);

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
            $validated = $request->all();

            $reservation = Reservation::findOrFail($validated['reservation_id']);
            if ($reservation->status === Reservation::STATUS_CANCELED) {
                return response()->json([
                    'success' => false,
                    'status' => 'canceled',
                    'message' => '予約がキャンセルされているため、お支払いできません',
                ], 400);
            }

            if ($reservation->status === Reservation::STATUS_CANCELED) {
                return response()->json(['success' => false, 'error' => '予約がキャンセルされているためお支払いできません'], 400);
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $validated['amount'] * 100,
                'currency' => $validated['currency'],
                'payment_method' => $validated['payment_method'],
                'confirmation_method' => 'automatic',
                'confirm' => true,
                'return_url' => route('checkout.return'),
                'metadata' => [
                    'user_id' => $reservation->user_id,
                    'reservation_id' => $reservation->id,
                ],
            ]);

            if ($paymentIntent->status === 'succeeded') {
                DB::transaction(function () use ($reservation, $paymentIntent) {
                    $checkout = Checkout::updateOrCreate(
                        ['reservation_id' => $reservation->id],
                        [
                            'user_id' => $reservation->user_id,
                            'payment_intent_id' => $paymentIntent->id,
                            'amount' => $paymentIntent->amount / 100,
                            'status' => 'success',
                            'currency' => $paymentIntent->currency,
                        ]
                    );
                });
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('checkout.done'),
                ]);

            }
            return response()->json([
                'success' => false,
                'status' => 'pending',
                'message' => '支払いが未完了です',
            ]);

        } catch (\Stripe\Exception\CardException $e) {

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