<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $role)
    {

        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['message' => 'ログインが必要です']);
        }

        $user = Auth::user();

        if ($user->role !== $role) {
            if ($user->role === 'user') {
                return redirect()->route('mypage');
            }
            return redirect()->route('login')->withErrors(['message' => '権限がありません']);
        }

        if (!Auth::check() || Auth::user()->role !== 'store-owner') {
            return redirect('/owner/login')->withErrors(['message' => 'アクセス権限がありません']);
        }

        return $next($request);
    }
}
