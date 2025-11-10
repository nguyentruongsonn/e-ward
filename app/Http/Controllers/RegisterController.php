<?php

namespace App\Http\Controllers;

use App\Mail\OtpCodeMail;
use App\Models\EmailOtp;
use App\Models\Nguoi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'hovaten' => 'required|string|max:255',
            'cccd' => 'required|string|max:50',
            'email' => 'required|email:rfc,dns|max:255',
            'password' => 'required|confirmed|min:6',
            'phone' => 'required|string|max:50',
            'quequan' => 'required|string|max:255',
            'thuongtru' => 'required|string|max:255',
            'tamtru' => 'required|string|max:255',
            'gender' => 'required|in:Nam,Nữ',
        ]);

        // Prevent duplicate registration by email
        if (Nguoi::where('email', $validated['email'])->exists() || Nguoi::where('email', $validated['email'])->exists()) {
            return back()->withErrors(['email' => 'Email đã được sử dụng.'])->withInput();
        }

        // Generate OTP
        $code = (string) random_int(100000, 999999);

        // Persist OTP (10 minutes expiry) and store pending registration in session
        EmailOtp::where('email', $validated['email'])->delete();
        EmailOtp::create([
            'email' => $validated['email'],
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'attempts' => 0,
        ]);

        $request->session()->put('pending_registration', [
            'hovaten' => $validated['hovaten'],
            'cccd' => $validated['cccd'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'],
            'quequan' => $validated['quequan'],
            'thuongtru' => $validated['thuongtru'],
            'tamtru' => $validated['tamtru'],
            'gender' => $validated['gender'],
        ]);

        // Send email
        try {
            Mail::to($validated['email'])->send(new OtpCodeMail($code));
            return redirect()->route('register.otp.show')->with('status', 'Mã OTP đã được gửi tới email của bạn. Vui lòng kiểm tra hộp thư.');
        } catch (\Throwable $e) {
            $message = 'Không gửi được email OTP. Vui lòng thử lại sau.';
            if (app()->environment('local')) {
                $message .= ' Lỗi: ' . $e->getMessage();
            }
            return back()->withErrors(['email' => $message])->withInput();
        }
    }

    public function showOtpForm(Request $request)
    {
        if (!$request->session()->has('pending_registration')) {
            return redirect()->route('register');
        }
        $email = $request->session()->get('pending_registration.email');
        return view('pages.verify-otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $pending = $request->session()->get('pending_registration');
        if (!$pending) {
            return redirect()->route('register');
        }

        $otp = EmailOtp::where('email', $pending['email'])->first();
        if (!$otp) {
            return back()->withErrors(['code' => 'OTP không tồn tại.']);
        }

        if (Carbon::now()->greaterThan($otp->expires_at)) {
            $otp->delete();
            return back()->withErrors(['code' => 'OTP đã hết hạn. Vui lòng yêu cầu lại.']);
        }

        if ($otp->code !== $request->input('code')) {
            $otp->increment('attempts');
            return back()->withErrors(['code' => 'Mã OTP không đúng.']);
        }

        // OTP valid -> create records
        DB::beginTransaction();
        try {
            // Xử lý số điện thoại: chỉ lấy 10 ký tự cuối cùng (bỏ số 0 đầu nếu có)
            $phone = $pending['phone'];
            $phone = preg_replace('/[^0-9]/', '', $phone); // Chỉ giữ số
            if (strlen($phone) > 10) {
                $phone = substr($phone, -10); // Lấy 10 ký tự cuối
            }
            // Đảm bảo không rỗng
            if (empty($phone)) {
                throw new \Exception('Số điện thoại không hợp lệ');
            }

            $nguoi = Nguoi::create([
                'maCCCD' => $pending['cccd'],
                'hoTen' => $pending['hovaten'],
                'gioiTinh' => $pending['gender'],
                'queQuan' => $pending['quequan'],
                'noiThuongTru' => $pending['thuongtru'],
                'noiTamTru' => $pending['tamtru'],
                'email' => $pending['email'],
                'password' => Hash::make($pending['password']),
                'soDienThoai' => $phone,
                'vaiTro' => 'Công dân/ Tổ chức', // Sửa lại theo enum trong DB
            ]);
            DB::table('congdan')->insert([
            'IDnguoiDung' => $nguoi->IDnguoiDung,
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $errorMessage = 'Có lỗi xảy ra khi tạo tài khoản.';
            if (app()->environment('local')) {
                $errorMessage .= ' Chi tiết: ' . $e->getMessage();
                // Log để debug
                Log::error('Registration error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'pending_data' => $pending
                ]);
            }
            return back()->withErrors(['code' => $errorMessage])->withInput();
        }

        // Cleanup
        $request->session()->forget('pending_registration');
        $otp->delete();

        return redirect()->route('home')->with('register_success', 'Chúc mừng bạn đã đăng ký thành công!');
    }

    public function resendOtp(Request $request)
    {
        $pending = $request->session()->get('pending_registration');
        if (!$pending) {
            return redirect()->route('register');
        }

        $code = (string) random_int(100000, 999999);
        EmailOtp::updateOrCreate(
            ['email' => $pending['email']],
            ['code' => $code, 'expires_at' => Carbon::now()->addMinutes(10), 'attempts' => 0]
        );

        try {
            Mail::to($pending['email'])->send(new OtpCodeMail($code));
        } catch (\Throwable $e) {
            $message = 'Không gửi được email OTP. Vui lòng thử lại sau.';
            if (app()->environment('local')) {
                $message .= ' Lỗi: ' . $e->getMessage();
            }
            return back()->withErrors(['code' => $message]);
        }

        return back()->with('status', 'Đã gửi lại mã OTP.');
    }
}
