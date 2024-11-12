<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => ['required', 'date', 'after:today'],
            'time' => ['required'],
            'guests' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }

    public function messages()
    {
        return [
            'date.required' => '日付を入力してください',
            'date.after' => '今日以降の日付を入力してください',
            'time.required' => '時間を入力してください',
            'guests.required' => '人数を入力してください',
            'guests.integer' => '人数は数字で入力してください',
            'guests.min' => '人数は1人以上で入力してください',
            'guests.max' => '人数は20人以下で入力してください',
        ];
    }
}
