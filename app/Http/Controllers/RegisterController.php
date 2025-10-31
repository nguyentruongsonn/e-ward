<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Nguoi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Kiểm tra dữ liệu người dùng nhập
        $validated = $request->validate([
            'hovaten'     => 'required|string|max:255',
            'cccd'        => 'required|digits:12|unique:nguoi,maCCCD',
            'email'       => 'required|email|unique:nguoi,email|unique:users,email',
            'password'    => 'required|min:6|confirmed',
            'phone'       => 'required|digits_between:9,11',
            'quequan'     => 'required|string|max:255',
            'thuongtru'   => 'required|string|max:255',
            'tamtru'      => 'required|string|max:255',
            'gender'      => 'required|in:Nam,Nữ',
        ]);

        DB::beginTransaction(); // ⚠️ Nếu một bảng lưu lỗi thì rollback hết

        try {
            // ✅ 1. Lưu vào bảng nguoi
            $nguoi = Nguoi::create([
                'maCCCD'        => $validated['cccd'],
                'hoTen'         => $validated['hovaten'],
                'gioiTinh'      => $validated['gender'],
                'queQuan'       => $validated['quequan'],
                'noiThuongTru'  => $validated['thuongtru'],
                'noiTamTru'     => $validated['tamtru'],
                'email'         => $validated['email'],
                'soDienThoai'   => $validated['phone'],
                'vaiTro'        => 'Người dân', // mặc định
            ]);

            // ✅ 2. Lưu vào bảng users, gắn IDnguoiDung từ bảng nguoi
            User::create([
                'IDnguoiDung' => $nguoi->IDnguoiDung,
                'email'       => $validated['email'],
                'password'    => Hash::make($validated['password']),
            ]);

            DB::commit();

            return redirect()->route('home')->with('success', 'Đăng ký thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Đã xảy ra lỗi khi đăng ký: ' . $e->getMessage()]);
        }
    }
}
