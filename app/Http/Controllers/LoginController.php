<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
            
            // Lưu session success để hiện alert
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
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
}
