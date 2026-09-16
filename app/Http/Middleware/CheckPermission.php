<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if ($user->trangThai !== 'HOAT_DONG') {
            Auth::logout();

            return redirect('/login')->withErrors(['tenDangNhap' => 'Tài khoản đã bị khóa']);
        }

        if (! $user->hasPermission($permission)) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập');
        }

        return $next($request);
    }
}
