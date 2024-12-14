<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'password.required' => '管理者パスワードを入力してください',
        ];
    }
}
