<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\HoSoXuLy;
use App\Models\CongDan;
use App\Models\TTHC;
use App\Models\LichSuThanhToan;
use App\Models\ThongBao;
use App\Models\PasswordChangeOtp;
use App\Mail\PasswordChangeOtpMail;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Helper method to get unread notification count
     */
    private function getUnreadCount($IDCD)
    {
        return ThongBao::where('IDCD', $IDCD)->where('is_read', false)->count();
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();
        
        // Kiểm tra xem Auth::user() trả về Nguoi hay User
        // Nếu là Nguoi (theo config auth), dùng trực tiếp
        // Nếu là User, lấy nguoi từ user
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user; // Lấy User nếu có
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }
        
        // Kiểm tra nếu nguoi null
        if (!$nguoi) {
            abort(404, 'Không tìm thấy thông tin người dùng');
        }
        
        // Lấy IDCD từ công dân
        $congDan = $nguoi->congDan;
        
        if (!$congDan) {
            // Nếu chưa có công dân, tạo mới
            $congDan = CongDan::create([
                'IDnguoiDung' => $nguoi->IDnguoiDung
            ]);
        }
        
        $IDCD = $congDan->IDCD;
        
        // Đếm số hồ sơ đã hoàn thành (có ngayKetThucXuLy không null)
        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)
            ->whereNotNull('ngayKetThucXuLy')
            ->count();
        
        // Đếm số hồ sơ đang xử lý (chưa có ngayKetThucXuLy)
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)
            ->whereNull('ngayKetThucXuLy')
            ->count();
        
        // Xử lý tìm kiếm
        $query = HoSoXuLy::where('IDCD', $IDCD)->with('tthc');
        
        if ($request->filled('ten_dich_vu')) {
            $query->whereHas('tthc', function($q) use ($request) {
                $q->where('tenTTHC', 'like', '%' . $request->ten_dich_vu . '%');
            });
        }
        
        if ($request->filled('ma_ho_so')) {
            $query->where('maHSXL', $request->ma_ho_so);
        }
        
        if ($request->filled('trang_thai')) {
            if ($request->trang_thai == 'da_hoan_thanh') {
                $query->whereNotNull('ngayKetThucXuLy');
            } elseif ($request->trang_thai == 'dang_xu_ly') {
                $query->whereNull('ngayKetThucXuLy');
            } else {
                $query->where('maTrangThai', $request->trang_thai);
            }
        }
        
        // Hiển thị 5 hồ sơ mỗi trang
        $hoSoList = $query->orderBy('ngayTiepNhan', 'desc')->paginate(5)->withQueryString();
        $unreadCount = $this->getUnreadCount($IDCD);
        
        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'hoSoList' => $hoSoList,
            'unreadCount' => $unreadCount,
            'activePage' => 'services',
        ]);
    }

    public function identityInfo()
    {
        $authUser = Auth::user();
        
        // Kiểm tra xem Auth::user() trả về Nguoi hay User
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }
        
        // Kiểm tra nếu nguoi null
        if (!$nguoi) {
            abort(404, 'Không tìm thấy thông tin người dùng');
        }
        
        // Lấy IDCD từ công dân để đếm hồ sơ
        $congDan = $nguoi->congDan;
        
        if (!$congDan) {
            $congDan = CongDan::create([
                'IDnguoiDung' => $nguoi->IDnguoiDung
            ]);
        }
        
        $IDCD = $congDan->IDCD;
        
        // Đếm số hồ sơ
        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)
            ->whereNotNull('ngayKetThucXuLy')
            ->count();
        
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)
            ->whereNull('ngayKetThucXuLy')
            ->count();
        
        $unreadCount = $this->getUnreadCount($IDCD);
        
        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'unreadCount' => $unreadCount,
            'activePage' => 'identity',
        ]);
    }

    // Return JSON detail for a given ho so (belongs to current user)
    public function showHoSo(Request $request, $maHSXL)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $nguoi = $authUser->nguoi;
        }

        if (!$nguoi || !$nguoi->congDan) {
            abort(404);
        }

        $IDCD = $nguoi->congDan->IDCD;

        $hoSo = HoSoXuLy::with('tthc')
            ->where('IDCD', $IDCD)
            ->findOrFail($maHSXL);

        return response()->json([
            'maHSXL' => $hoSo->maHSXL,
            'tenTTHC' => optional($hoSo->tthc)->tenTTHC,
            'tenChuHoSo' => $hoSo->tenChuHoSo,
            'doiTuongThucHien' => $hoSo->doiTuongThucHien,
            'email' => $hoSo->email,
            'soDienThoai' => $hoSo->soDienThoai,
            'ngayTiepNhan' => $hoSo->ngayTiepNhan ? $hoSo->ngayTiepNhan->format('d/m/Y') : null,
            'ngayHenTra' => $hoSo->ngayHenTra ? $hoSo->ngayHenTra->format('d/m/Y') : null,
            'ngayTra' => $hoSo->ngayTra ? $hoSo->ngayTra->format('d/m/Y') : null,
            'ngayKetThucXuLy' => $hoSo->ngayKetThucXuLy ? $hoSo->ngayKetThucXuLy->format('d/m/Y') : null,
            'maTrangThai' => $hoSo->maTrangThai,
            'donViXuLy' => $hoSo->donViXuLy,
            'lePhi' => $hoSo->lePhi,
            'hinhThuc' => $hoSo->hinhThuc,
            'thongTinTra' => $hoSo->thongTinTra,
            'ghiChu' => $hoSo->ghiChu,
            'dulieu' => $hoSo->dulieu,
        ]);
    }

    public function payments(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi) {
            abort(404, 'Không tìm thấy thông tin người dùng');
        }

        $congDan = $nguoi->congDan ?? CongDan::create(['IDnguoiDung' => $nguoi->IDnguoiDung]);
        $IDCD = $congDan->IDCD;

        // Counters (sidebar)
        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();

        // Filters
        $query = LichSuThanhToan::where('IDCD', $IDCD);

        if ($request->filled('loai_gd') && $request->loai_gd !== 'all') {
            $query->where('loaiGD', $request->loai_gd);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('ngayGD', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('ngayGD', '<=', $request->to_date);
        }

        $payments = $query->orderBy('ngayGD', 'desc')->paginate(10)->withQueryString();
        $unreadCount = $this->getUnreadCount($IDCD);

        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'payments' => $payments,
            'unreadCount' => $unreadCount,
            'activePage' => 'payments',
        ]);
    }

    public function notifications(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi) {
            abort(404, 'Không tìm thấy thông tin người dùng');
        }

        $congDan = $nguoi->congDan ?? CongDan::create(['IDnguoiDung' => $nguoi->IDnguoiDung]);
        $IDCD = $congDan->IDCD;

        // Sidebar counters
        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();

        $onlyUnread = $request->boolean('only_unread');
        $query = ThongBao::where('IDCD', $IDCD);
        if ($onlyUnread) {
            $query->where('is_read', false);
        }
        // Hiển thị 5 thông báo mỗi trang
        $notifications = $query->orderByDesc('created_at')->paginate(5)->withQueryString();
        $unreadCount = $this->getUnreadCount($IDCD);

        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'activePage' => 'notifications',
        ]);
    }

    public function markNotificationAsRead($id)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi) {
            abort(404, 'Không tìm thấy thông tin người dùng');
        }

        $congDan = $nguoi->congDan;
        if (!$congDan) {
            abort(404, 'Không tìm thấy thông tin công dân');
        }

        $notification = ThongBao::where('id', $id)
            ->where('IDCD', $congDan->IDCD)
            ->first();

        if (!$notification) {
            abort(404, 'Không tìm thấy thông báo');
        }

        $notification->is_read = true;
        $notification->save();

        return redirect()->route('profile.notifications')
            ->with('success', 'Đã đánh dấu thông báo là đã đọc');
    }

    public function getNotificationDetail($id)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi) {
            return response()->json(['error' => 'Không tìm thấy thông tin người dùng'], 404);
        }

        $congDan = $nguoi->congDan;
        if (!$congDan) {
            return response()->json(['error' => 'Không tìm thấy thông tin công dân'], 404);
        }

        $notification = ThongBao::where('id', $id)
            ->where('IDCD', $congDan->IDCD)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Không tìm thấy thông báo'], 404);
        }

        // Mark as read if not already read
        if (!$notification->is_read) {
            $notification->is_read = true;
            $notification->save();
        }

        return response()->json([
            'id' => $notification->id,
            'tieuDe' => $notification->tieuDe,
            'noiDung' => $notification->noiDung,
            'loai' => $notification->loai,
            'is_read' => $notification->is_read,
            'created_at' => $notification->created_at ? $notification->created_at->format('d/m/Y H:i') : null,
        ]);
    }

    public function loadMoreNotifications(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi) {
            return response()->json(['error' => 'Không tìm thấy thông tin người dùng'], 404);
        }

        $congDan = $nguoi->congDan;
        if (!$congDan) {
            return response()->json(['error' => 'Không tìm thấy thông tin công dân'], 404);
        }

        $IDCD = $congDan->IDCD;
        $page = $request->get('page', 2); // Bắt đầu từ trang 2
        $onlyUnread = $request->boolean('only_unread');

        $query = ThongBao::where('IDCD', $IDCD);
        if ($onlyUnread) {
            $query->where('is_read', false);
        }

        $notifications = $query->orderByDesc('created_at')
            ->paginate(5, ['*'], 'page', $page);

        // Trả về HTML để append vào danh sách
        $html = view('partials.notification-items', [
            'notifications' => $notifications
        ])->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $notifications->hasMorePages(),
            'nextPage' => $notifications->hasMorePages() ? ($page + 1) : null,
        ]);
    }

    public function loadMoreServices(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi) {
            return response()->json(['error' => 'Không tìm thấy thông tin người dùng'], 404);
        }

        $congDan = $nguoi->congDan;
        if (!$congDan) {
            return response()->json(['error' => 'Không tìm thấy thông tin công dân'], 404);
        }

        $IDCD = $congDan->IDCD;
        $page = $request->get('page', 2);

        // Xử lý tìm kiếm giống như method index
        $query = HoSoXuLy::where('IDCD', $IDCD)->with('tthc');

        if ($request->filled('ten_dich_vu')) {
            $query->whereHas('tthc', function($q) use ($request) {
                $q->where('tenTTHC', 'like', '%' . $request->ten_dich_vu . '%');
            });
        }

        if ($request->filled('ma_ho_so')) {
            $query->where('maHSXL', $request->ma_ho_so);
        }

        if ($request->filled('trang_thai')) {
            if ($request->trang_thai == 'da_hoan_thanh') {
                $query->whereNotNull('ngayKetThucXuLy');
            } elseif ($request->trang_thai == 'dang_xu_ly') {
                $query->whereNull('ngayKetThucXuLy');
            } else {
                $query->where('maTrangThai', $request->trang_thai);
            }
        }

        $hoSoList = $query->orderBy('ngayTiepNhan', 'desc')
            ->paginate(5, ['*'], 'page', $page);

        // Trả về HTML để append vào bảng
        $html = view('partials.service-items', [
            'hoSoList' => $hoSoList
        ])->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $hoSoList->hasMorePages(),
            'nextPage' => $hoSoList->hasMorePages() ? ($page + 1) : null,
        ]);
    }

    public function showPasswordChangeForm()
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi) {
            abort(404, 'Không tìm thấy thông tin người dùng');
        }

        $congDan = $nguoi->congDan ?? CongDan::create(['IDnguoiDung' => $nguoi->IDnguoiDung]);
        $IDCD = $congDan->IDCD;

        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();
        $unreadCount = $this->getUnreadCount($IDCD);

        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'unreadCount' => $unreadCount,
            'activePage' => 'password-change',
        ]);
    }

    public function requestPasswordChange(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
        ]);

        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $nguoi = $authUser->nguoi;
        }

        if (!$nguoi) {
            return back()->withErrors(['current_password' => 'Không tìm thấy thông tin người dùng.']);
        }

        // Verify current password
        if (!Hash::check($validated['current_password'], $nguoi->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $email = $nguoi->email ?? ($authUser->email ?? null);
        if (!$email) {
            return back()->withErrors(['current_password' => 'Không tìm thấy email của bạn.']);
        }

        // Generate OTP
        $code = (string) random_int(100000, 999999);

        // Delete old OTPs for this email
        PasswordChangeOtp::where('email', $email)->delete();

        // Create new OTP (10 minutes expiry)
        PasswordChangeOtp::create([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'attempts' => 0,
        ]);

        // Store pending password change in session
        $request->session()->put('pending_password_change', [
            'email' => $email,
            'verified' => false,
        ]);

        // Send email
        try {
            Mail::to($email)->send(new PasswordChangeOtpMail($code));
            return redirect()->route('profile.password-change.verify')->with('status', 'Mã OTP đã được gửi tới email của bạn. Vui lòng kiểm tra hộp thư.');
        } catch (\Throwable $e) {
            $message = 'Không gửi được email OTP. Vui lòng thử lại sau.';
            if (app()->environment('local')) {
                $message .= ' Lỗi: ' . $e->getMessage();
            }
            return back()->withErrors(['current_password' => $message])->withInput();
        }
    }

    public function showVerifyOtpForm(Request $request)
    {
        if (!$request->session()->has('pending_password_change')) {
            return redirect()->route('profile.password-change');
        }

        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi) {
            abort(404, 'Không tìm thấy thông tin người dùng');
        }

        $congDan = $nguoi->congDan ?? CongDan::create(['IDnguoiDung' => $nguoi->IDnguoiDung]);
        $IDCD = $congDan->IDCD;

        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();
        $unreadCount = $this->getUnreadCount($IDCD);

        $pending = $request->session()->get('pending_password_change');
        $email = $pending['email'] ?? null;

        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'unreadCount' => $unreadCount,
            'activePage' => 'password-change-verify',
            'email' => $email,
        ]);
    }

    public function verifyPasswordChangeOtp(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'new_password' => 'required|confirmed|min:6',
        ]);

        $pending = $request->session()->get('pending_password_change');
        if (!$pending) {
            return redirect()->route('profile.password-change');
        }

        $email = $pending['email'];
        $otp = PasswordChangeOtp::where('email', $email)->first();

        if (!$otp) {
            return back()->withErrors(['code' => 'OTP không tồn tại.']);
        }

        if (Carbon::now()->greaterThan($otp->expires_at)) {
            $otp->delete();
            return back()->withErrors(['code' => 'OTP đã hết hạn. Vui lòng yêu cầu lại.']);
        }

        if ($otp->code !== $validated['code']) {
            $otp->increment('attempts');
            return back()->withErrors(['code' => 'Mã OTP không đúng.']);
        }

        // OTP valid -> change password
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $nguoi = $authUser->nguoi;
        }

        if (!$nguoi) {
            return back()->withErrors(['code' => 'Không tìm thấy thông tin người dùng.']);
        }

        // Update password
        $nguoi->password = Hash::make($validated['new_password']);
        $nguoi->save();

        // Also update password in users table if exists
        if ($nguoi->user) {
            $nguoi->user->password = Hash::make($validated['new_password']);
            $nguoi->user->save();
        }

        // Cleanup
        $request->session()->forget('pending_password_change');
        $otp->delete();

        return redirect()->route('profile.password-change')->with('success', 'Đổi mật khẩu thành công!');
    }

    public function resendPasswordChangeOtp(Request $request)
    {
        $pending = $request->session()->get('pending_password_change');
        if (!$pending) {
            return redirect()->route('profile.password-change');
        }

        $email = $pending['email'];
        $code = (string) random_int(100000, 999999);

        PasswordChangeOtp::updateOrCreate(
            ['email' => $email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10),
                'attempts' => 0
            ]
        );

        try {
            Mail::to($email)->send(new PasswordChangeOtpMail($code));
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
