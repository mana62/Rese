<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes()
    {
        return [
            'rating' => '評価',
            'comment' => 'コメント',
        ];
    }

    public function messages()
    {
        return [
            'rating.integer' => '評価は整数で設定してください',
            'rating.min' => '評価は1以上で設定してください',
            'rating.max' => '評価は5以下で設定してください',
            'comment.max' => 'コメントは255文字以下で記載してください',
        ];
    }
}
