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

        // Kiểm tra vaiTro - cho phép: Quản trị viên, Cán bộ một cửa, Cán bộ thụ lý, Lãnh đạo
        $allowedRoles = ['Quản trị viên', 'Cán bộ một cửa', 'Cán bộ thụ lý', 'Lãnh đạo'];
        
        if (!in_array($user->vaiTro, $allowedRoles)) {
            // Nếu không phải 4 role trên, kiểm tra trong bảng quantrivien
            $isQuanTriVien = DB::table('quantrivien')
                ->where('IDnguoiDung', $user->IDnguoiDung)
                ->exists();

            if (!$isQuanTriVien) {
                Auth::logout();
                return redirect()->route('admin.login')
                    ->withErrors(['error' => 'Bạn không có quyền truy cập trang quản trị.']);
            }
        }

        return $next($request);
    }
}

