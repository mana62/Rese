<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Checkout;

class CheckoutController extends Controller
{
    public function checkoutForm()
    {
        return view('checkout');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'amount' => 'required|integer|min:1',
        ]);

        try {
            //Stripeのシークレットキーを設定
            Stripe::setApiKey(env('STRIPE_SECRET'));

            //支払いIntentを作成
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount * 100, //金額（最小単位: セント）
                'currency' => 'jpy',
                'payment_method' => $request->payment_method,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            Checkout::create([
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $request->amount,
                'status' => $paymentIntent->status,
                'currency' => 'jpy',
            ]);

            return response()->json(['success' => true, 'paymentIntent' => $paymentIntent]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
