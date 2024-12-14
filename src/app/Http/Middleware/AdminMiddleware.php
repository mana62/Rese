<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 管理者セッションがない場合、アクセスを拒否
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('admin_login_form')->withErrors(['message' => '管理者としてログインしてください']);
        }

        return $next($request);
    }
}
