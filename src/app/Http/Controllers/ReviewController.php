<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;


class ReviewController extends Controller
{
    //レビューを取得
    public function storeReview(ReviewRequest $request, $restaurantId)
    {
        Review::create([
            'user_id' => auth()->id(),
            'restaurant_id' => $restaurantId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('shop-detail', $restaurantId)->with('message', '評価を投稿しました');
    }
}