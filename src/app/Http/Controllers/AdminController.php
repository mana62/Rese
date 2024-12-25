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
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(AdminLoginRequest $request)
    {
        if ($request->password === 'admin_pass') {

            $request->session()->put('is_admin', true);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['message' => 'パスワードが違います']);
    }


    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');

        return redirect()->route('admin.login');
    }

    //管理者ページの表示
    public function index()
    {
        $storeOwners = User::where('role', 'store-owner')->get();

        return view('admin.dashboard', compact('storeOwners'));
    }

    //店舗代表者を作成
    public function createStoreOwner(AdminRequest $request)
    {

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'store-owner',
            'status' => 'active',
        ]);

        return redirect()->route('admin.dashboard')->with('message', '店舗代表者を作成しました');
    }

    public function deleteStoreOwner(Request $request, $id)
    {
        $storeOwner = User::where('id', $id)->where('role', 'store-owner')->firstOrFail();
        $storeOwner->update(['role' => 'user']);

        return redirect()->route('admin.dashboard')->with('message', '店舗代表者を削除しました');
    }


    //お知らせメール
    public function sendNotification(sendNotificationRequest $request)
    {

        if ($request->role === 'all') {
            $recipients = User::all();
        } else {
            $recipients = User::where('role', $request->role)->get();
        }

        try {

            foreach ($recipients as $recipient) {
                Mail::to($recipient->email)->send(new \App\Mail\UserNoticeMail($request->message));
            }
            return redirect()->route('admin.dashboard')->with('message', 'お知らせメールを送信しました');

        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'お知らせメールの送信に失敗しました');
        }
    }
}