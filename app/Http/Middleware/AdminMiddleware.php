<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Vui lòng đăng nhập để tiếp tục.']);
        }

        $user = Auth::user();

        // Chỉ chặn riêng vai trò "Công dân/ Tổ chức" không cho vào admin
        // Các vai trò khác (Cán bộ một cửa, Cán bộ thụ lý, Lãnh đạo, Quản trị viên, ...) đều được phép.
        if (trim($user->vaiTro) === 'Công dân/ Tổ chức') {
            Auth::logout();
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Tài khoản Công dân/ Tổ chức không có quyền truy cập trang quản trị.']);
        }

        return $next($request);
    }
}

