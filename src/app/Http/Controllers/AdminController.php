<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\AdminRequest;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Requests\sendNotificationRequest;


class AdminController extends Controller
{
    //管理者ログイン画面を表示
    public function showLoginForm()
    {
        return view('admin.login');
    }

    //管理者ログイン処理
    public function login(AdminLoginRequest $request)
    {
        //パスワードを検証
        if ($request->password === 'admin_pass') {

            //管理者ログイン状態をセッションに保存
            $request->session()->put('is_admin', true);
            return redirect()->route('admin.dashboard');
        }

        //パスワードが間違っている場合
        return back()->withErrors(['message' => 'パスワードが違います']);
    }

    //ログアウト処理
    public function logout(Request $request)
    {
        //セッションから管理者フラグを削除
        $request->session()->forget('is_admin');

        return redirect()->route('admin.login');
    }

    //管理者ページの表示
    public function index()
    {
        //店舗代表者と一般ユーザーを取得
        $storeOwners = User::where('role', 'store-owner')->get();

        return view('admin.dashboard', compact('storeOwners'));
    }

    //店舗代表者を作成
    public function createStoreOwner(AdminRequest $request)
    {

        //データベースに保存
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'store-owner',
            'status' => 'active',
        ]);

        return redirect()->route('admin.dashboard')->with('message', '店舗代表者を作成しました');
    }

    //店舗代表者を削除し、一般ユーザーに戻す
    public function deleteStoreOwner(Request $request, $id)
    {
        //userテーブルからidを見つけてロールをユーザーに変更
        $storeOwner = User::where('id', $id)->where('role', 'store-owner')->firstOrFail();
        $storeOwner->update(['role' => 'user']);

        return redirect()->route('admin.dashboard')->with('message', '店舗代表者を削除しました');
    }


    //お知らせメール
    public function sendNotification(sendNotificationRequest $request)
    {

        if ($request->role === 'all') {
            $recipients = User::all(); //全ユーザー
        } else {
            $recipients = User::where('role', $request->role)->get(); //ownerのみ
        }

        try {

            //メールを各ユーザーに送信
            foreach ($recipients as $recipient) {
                Mail::to($recipient->email)->send(new \App\Mail\UserNoticeMail($request->message));
            }
            return redirect()->route('admin.dashboard')->with('message', 'お知らせメールを送信しました');

            //送信エラー時に管理者に通知
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'お知らせメールの送信に失敗しました');
        }
    }
}