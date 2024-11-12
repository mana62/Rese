<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //管理者ページ
    public function index()
    {
        $storeOwners = User::where('role', 'store-owner')->get();
        $users = User::where('role', 'user')->get();

        return view('admin', compact('storeOwners', 'users'));
    }

    //店舗代表者を作成
    public function createStoreOwner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.unique' => '指定のメールアドレスは既に使用されています',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'store-owner',
            'status' => 'active',
        ]);

        return redirect()->route('admin')->with('message', '店舗代表者を作成しました');
    }

    // 店舗代表者削除
    public function deleteStoreOwner(Request $request, $id)
    {
        $storeOwner = User::findOrFail($id);
        $storeOwner->update([
            'role' => 'user',
        ]);

        return redirect()->route('admin')->with('message', '店舗代表者を削除しました');
    }


    //お知らせメール
    public function sendNotification(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ], [
            'message.required' => 'お知らせを入力してください',
            'message.max' => 'お知らせは255文字以内で記載してください',
        ]);

        if ($request->role === 'all') {
            $recipients = User::all();
        } else {
            $recipients = User::where('role', $request->role)->get();
        }

        try {
            foreach ($recipients as $recipient) {
                Mail::to($recipient->email)->send(new \App\Mail\UserNoticeMail($request->message));
            }
            return redirect()->route('admin')->with('message', 'お知らせメールを送信しました');
        } catch (\Exception $e) {
            return redirect()->route('admin')->with('error', 'お知らせメールの送信に失敗しました: ' . $e->getMessage());
        }
    }
}