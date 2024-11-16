<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    //レビューを取得
    public function storeReview(ReviewRequest $request, $restaurantId)
    {
        if (!Auth::check()) {
            return redirect()->route('detail', $restaurantId)
                ->withErrors(['login_required' => 'レビューを投稿するにはログインしてください']);
        }

        Review::create([
            'user_id' => auth()->id(),
            'restaurant_id' => $restaurantId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('detail', $restaurantId)->with('success', 'レビューを投稿しました');
    }
}