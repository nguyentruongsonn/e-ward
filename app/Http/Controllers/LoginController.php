<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Kiểm tra nếu là admin thì redirect về admin dashboard
            if ($this->isAdmin($user)) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Chúc mừng bạn đã đăng nhập thành công!');
            }
            
            // Nếu không phải admin thì redirect về trang chủ như bình thường
            return redirect()->route('home')
                ->with('login_success', 'Chúc mừng bạn đã đăng nhập thành công!')
                ->with('close_modal', true); // Flag để đóng modal
        }

        // Nếu lỗi, quay lại và mở modal
        return redirect()->route('home')
            ->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.',
            ])
            ->withInput($request->only('email'))
            ->with('open_login_modal', true); // Flag để mở modal
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user ? $this->isAdmin($user) : false;
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Nếu là admin thì redirect về trang login admin, không thì về trang chủ
        if ($isAdmin) {
            return redirect()->route('admin.login');
        }
        
        return redirect()->route('home');
    }

    /**
     * Kiểm tra user có phải admin không
     */
    private function isAdmin($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        // Kiểm tra vaiTro trong bảng nguoi
        if ($user->vaiTro === 'Quản trị viên') {
            return true;
        }

        // Hoặc kiểm tra trong bảng quantrivien
        $isQuanTriVien = DB::table('quantrivien')
            ->where('IDnguoiDung', $user->IDnguoiDung)
            ->exists();

        return $isQuanTriVien;
    }
}
