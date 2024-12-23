<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class sendNotificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'message' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'message.required' => 'お知らせを入力してください',
            'message.max' => 'お知らせは255文字以内で記載してください',
        ];
    }
}
