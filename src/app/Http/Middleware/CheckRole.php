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
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $role)
    {
        $user = Auth::user();

        if (!$user) {
            //ユーザーがログインしていない場合
            return redirect()->route('login')->withErrors(['role' => '権限がありません']);
        }

        if ($user->role !== $role) {
            if ($user->role === 'user') {
                return redirect()->route('mypage');
            }
            return redirect()->route('login')->withErrors(['role' => '権限がありません']);
        }

        return $next($request);
    }
}
