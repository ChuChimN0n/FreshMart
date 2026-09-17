<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        abort_unless($permission !== null
            ? $user->hasPermission($permission)
            : $user->canAccessRoute($request->route()->getName() ?? ''), 403, 'Bạn không có quyền truy cập');

        return $next($request);
    }
}
