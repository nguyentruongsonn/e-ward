<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\HoSoXuLy;
use App\Models\CongDan;
use App\Models\TTHC;
use App\Models\LichSuThanhToan;
use App\Models\ThongBao;
use App\Models\LichHen;
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
        $query = HoSoXuLy::where('IDCD', $IDCD)->with(['tthc', 'trangThai']);
        
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
        
        // Kiểm tra quyền admin
        $isAdmin = false;
        if ($nguoi->vaiTro === 'Quản trị viên') {
            $isAdmin = true;
        } else {
            $isAdmin = DB::table('quantrivien')
                ->where('IDnguoiDung', $nguoi->IDnguoiDung)
                ->exists();
        }
        
        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'hoSoList' => $hoSoList,
            'unreadCount' => $unreadCount,
            'activePage' => 'services',
            'isAdmin' => $isAdmin,
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

    // Show full application detail page for citizen
    public function showHoSo(Request $request, $maHSXL)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } else {
            $user = $authUser;
            $nguoi = $authUser->nguoi;
        }

        if (!$nguoi || !$nguoi->congDan) {
            abort(404);
        }

        $IDCD = $nguoi->congDan->IDCD;

        // Get application with relationships
        $hoSo = HoSoXuLy::with(['tthc', 'trangThai', 'congdan.nguoi'])
            ->where('IDCD', $IDCD)
            ->where('maHSXL', $maHSXL)
            ->firstOrFail();

        // Get form configuration from formtructuyen (same as admin)
        $form = DB::table('formtructuyen')->where('maTTHC', $hoSo->maTTHC)->first();
        $cauHinhForm = $form ? json_decode($form->cauHinhForm, true) : [];
        
        // Handle dulieu structure (same logic as admin)
        $dulieuRaw = is_array($hoSo->dulieu) ? $hoSo->dulieu : json_decode($hoSo->dulieu ?? '{}', true);
        
        $cauHinhFormMerged = null;
        $payload = null;
        
        if (isset($dulieuRaw['cauHinhForm']) && isset($dulieuRaw['payload'])) {
            // New structure: has both cauHinhForm and payload
            $cauHinhFormMerged = $dulieuRaw['cauHinhForm'];
            $payload = $dulieuRaw['payload'];
        } elseif (isset($dulieuRaw[0]) && isset($dulieuRaw[0]['group'])) {
            // dulieu is merged cauHinhForm (old structure)
            $cauHinhFormMerged = $dulieuRaw;
            $payload = [];
        } else {
            // dulieu is original payload
            $payload = $dulieuRaw;
        }
        
        // Use cauHinhFormMerged if available, otherwise use cauHinhForm from DB
        if ($cauHinhFormMerged) {
            $cauHinhForm = $cauHinhFormMerged;
        }
        
        // dulieu for display
        $dulieu = $payload;

        // Get documents
        $taiLieu = DB::table('tailieunop')
            ->where('maHSXL', $maHSXL)
            ->get();

        // Get document groups (same logic as admin)
        $thanhPhanHoSos = collect();
        if ($hoSo->tthc) {
            $thanhPhanHoSos = DB::table('thanhphanhoso as tph')
                ->leftJoin('thanhphangiayto as tpg', 'tpg.maThanhPhan', '=', 'tph.maThanhPhan')
                ->leftJoin('giayto as gt', 'gt.maGiayTo', '=', 'tpg.maGiayTo')
                ->where('tph.maTTHC', $hoSo->tthc->maTTHC)
                ->select(
                    'tph.maThanhPhan',
                    'tph.tenThanhPhan',
                    'gt.maGiayTo',
                    'gt.tenGiayTo',
                    'tpg.soLuongBanChinh',
                    'tpg.soLuongBanSao'
                )
                ->get()
                ->groupBy('tenThanhPhan');
        }

        // Parse supplement request if exists
        $yeuCauBoSung = null;
        if ($hoSo->yeu_cau_bo_sung) {
            $yeuCauBoSung = json_decode($hoSo->yeu_cau_bo_sung, true);
        }

        // Get payment history
        $lichSuThanhToan = DB::table('lichsuthanhtoan')
            ->where('maHSXL', $maHSXL)
            ->get();

        // Check if can rate (status 10, within 10 days, not rated yet)
        $canRate = false;
        $daysRemaining = 0;
        $existingRating = null;
        
        if ($hoSo->maTrangThai == 10 && $hoSo->ngayTra) {
            $ngayTra = Carbon::parse($hoSo->ngayTra);
            $daysSince = $ngayTra->diffInDays(now());
            $daysRemaining = 10 - $daysSince;
            
            $existingRating = DB::table('danhgia')->where('maHSXL', $maHSXL)->first();
            $canRate = ($daysSince <= 10) && !$existingRating;
        }

        // Counters for sidebar
        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();
        $unreadCount = $this->getUnreadCount($IDCD);

        return view('pages.application-detail', compact(
            'user',
            'nguoi',
            'hoSo',
            'cauHinhForm',
            'dulieu',
            'taiLieu',
            'thanhPhanHoSos',
            'yeuCauBoSung',
            'canRate',
            'daysRemaining',
            'existingRating',
            'lichSuThanhToan',
            'hoSoHoanThanh',
            'hoSoDangXuLy',
            'unreadCount'
        ));
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

    /**
     * Hiển thị hóa đơn cho một giao dịch thanh toán cụ thể
     */
    public function paymentInvoice($id)
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

        // Lấy giao dịch thuộc về công dân hiện tại
        $payment = LichSuThanhToan::where('id', $id)
            ->where('IDCD', $IDCD)
            ->firstOrFail();

        // Lấy thông tin hồ sơ nếu có
        $hoSo = null;
        $tthc = null;
        if ($payment->maHSXL) {
            $hoSo = DB::table('hosoxuly')->where('maHSXL', $payment->maHSXL)->first();
            if ($hoSo) {
                $tthc = DB::table('tthc')->where('maTTHC', $hoSo->maTTHC)->first();
            }
        }

        // Sidebar counters
        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();
        $unreadCount = $this->getUnreadCount($IDCD);

        return view('pages.payment-invoice', [
            'user' => $user,
            'nguoi' => $nguoi,
            'payment' => $payment,
            'hoSo' => $hoSo,
            'tthc' => $tthc,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'unreadCount' => $unreadCount,
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
        $query = HoSoXuLy::where('IDCD', $IDCD)->with(['tthc', 'trangThai']);

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

    public function appointments(Request $request)
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
        $unreadCount = $this->getUnreadCount($IDCD);

        // Query lịch hẹn
        $query = LichHen::where('IDCD', $IDCD)->with(['tthc', 'quaylamviec']);

        // Filter theo trạng thái nếu có
        if ($request->filled('trang_thai')) {
            $query->where('trangThai', $request->trang_thai);
        }

        // Filter theo ngày nếu có
        if ($request->filled('from_date')) {
            $query->whereDate('thoiGianHen', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('thoiGianHen', '<=', $request->to_date);
        }

        // Sắp xếp: lịch hẹn gần nhất lên đầu (càng gần càng hiện đầu)
        // Sắp xếp tăng dần theo thoiGianHen (gần nhất trước)
        $appointments = $query->orderBy('thoiGianHen', 'asc')->paginate(5)->withQueryString();

        // Kiểm tra quyền admin
        $isAdmin = false;
        if ($nguoi->vaiTro === 'Quản trị viên') {
            $isAdmin = true;
        } else {
            $isAdmin = DB::table('quantrivien')
                ->where('IDnguoiDung', $nguoi->IDnguoiDung)
                ->exists();
        }

        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'appointments' => $appointments,
            'unreadCount' => $unreadCount,
            'activePage' => 'appointments',
            'isAdmin' => $isAdmin,
        ]);
    }

    public function loadMoreAppointments(Request $request)
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

        // Xử lý tìm kiếm giống như method appointments
        $query = LichHen::where('IDCD', $IDCD)->with(['tthc', 'quaylamviec']);

        // Filter theo trạng thái nếu có
        if ($request->filled('trang_thai')) {
            $query->where('trangThai', $request->trang_thai);
        }

        // Filter theo ngày nếu có
        if ($request->filled('from_date')) {
            $query->whereDate('thoiGianHen', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('thoiGianHen', '<=', $request->to_date);
        }

        // Sắp xếp: lịch hẹn gần nhất lên đầu (càng gần càng hiện đầu)
        $appointments = $query->orderBy('thoiGianHen', 'asc')
            ->paginate(5, ['*'], 'page', $page);

        // Trả về HTML để append vào bảng
        $html = view('partials.appointment-items', [
            'appointments' => $appointments
        ])->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $appointments->hasMorePages(),
            'nextPage' => $appointments->hasMorePages() ? ($page + 1) : null,
        ]);
    }

    /**
     * Hiển thị chi tiết một lịch hẹn của công dân
     */
    public function showAppointment($id)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi || !$nguoi->congDan) {
            abort(404, 'Không tìm thấy thông tin người dùng');
        }

        $congDan = $nguoi->congDan;
        $IDCD = $congDan->IDCD;

        // Chỉ cho xem lịch hẹn thuộc công dân hiện tại
        $appointment = LichHen::with(['tthc', 'quaylamviec', 'congdan'])
            ->where('IDCD', $IDCD)
            ->where('id', $id)
            ->firstOrFail();

        // Sidebar counters
        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();
        $unreadCount = $this->getUnreadCount($IDCD);

        return view('pages.appointment-detail', [
            'user' => $user,
            'nguoi' => $nguoi,
            'appointment' => $appointment,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Hủy lịch hẹn (chỉ khi còn hiệu lực và thuộc về công dân hiện tại)
     */
    public function cancelAppointment(Request $request, $id)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $user = $authUser;
            $nguoi = $user->nguoi;
        }

        if (!$nguoi || !$nguoi->congDan) {
            return redirect()->route('profile.appointments')
                ->with('error', 'Không tìm thấy thông tin người dùng');
        }

        $congDan = $nguoi->congDan;
        $IDCD = $congDan->IDCD;

        /** @var LichHen|null $appointment */
        $appointment = LichHen::where('IDCD', $IDCD)
            ->where('id', $id)
            ->first();

        if (!$appointment) {
            return redirect()->route('profile.appointments')
                ->with('error', 'Không tìm thấy lịch hẹn');
        }

        // Chỉ cho phép hủy các lịch còn ở trạng thái "Đã đặt lịch" hoặc "Chờ đến"
        if (!in_array($appointment->trangThai, ['Đã đặt lịch', 'Chờ đến'])) {
            return redirect()->route('profile.appointments')
                ->with('error', 'Chỉ có thể hủy các lịch hẹn chưa được xử lý.');
        }

        // Nếu thời gian hẹn đã qua thì không cho hủy
        if ($appointment->thoiGianHen && $appointment->thoiGianHen->lt(Carbon::now('Asia/Ho_Chi_Minh'))) {
            return redirect()->route('profile.appointments')
                ->with('error', 'Không thể hủy lịch hẹn đã quá thời gian.');
        }

        $appointment->trangThai = 'Đã hủy';
        // Xóa token checkin để không thể sử dụng QR cũ
        $appointment->checkin_token = null;
        $appointment->save();

        return redirect()->route('profile.appointments')
            ->with('success', 'Đã hủy lịch hẹn thành công.');
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

    /**
     * Upload supplemental documents for application requiring supplements
     */
    public function uploadSupplementDocuments(Request $request, $maHSXL)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $nguoi = $authUser->nguoi;
        }

        if (!$nguoi || !$nguoi->congDan) {
            return back()->withErrors(['error' => 'Không tìm thấy thông tin người dùng']);
        }

        $IDCD = $nguoi->congDan->IDCD;

        // Verify ownership and status
        $hoSo = HoSoXuLy::where('IDCD', $IDCD)
            ->where('maHSXL', $maHSXL)
            ->firstOrFail();

        if ($hoSo->maTrangThai != 5) {
            return back()->withErrors(['error' => 'Hồ sơ không ở trạng thái yêu cầu bổ sung']);
        }

        $request->validate([
            'files.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'maGiayTo.*' => 'required|integer'
        ]);

        $uploadedFiles = [];
        
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $maGiayTo = $request->maGiayTo[$index] ?? null;
                
                if (!$maGiayTo) continue;

                // Store file
                $fileName = time() . '_' . $index . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('tailieu', $fileName, 'public');

                // Create tailieunop record
                DB::table('tailieunop')->insert([
                    'maHSXL' => $maHSXL,
                    'maGiayTo' => $maGiayTo,
                    'tenTep' => $file->getClientOriginalName(),
                    'duongDan' => $path,
                    'dinhDang' => $file->getClientOriginalExtension(),
                    'kichThuoc' => $file->getSize(),
                    'ngayTai' => now(),
                ]);

                $uploadedFiles[] = $fileName;
            }
        }


        // Set status back to "Đã tiếp nhận" (received) for processing
        $hoSo->maTrangThai = 2;
        $hoSo->maTrangThai_backup = null;


        // Clear supplement request
        $hoSo->yeu_cau_bo_sung = null;
        
        // Log activity
        $hoSo->ghiChu = ($hoSo->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Công dân đã bổ sung " . count($uploadedFiles) . " tài liệu.";
        $hoSo->save();

        return back()->with('success', 'Đã nộp bổ sung tài liệu thành công. Hồ sơ sẽ được xử lý tiếp.');
    }

    /**
     * Submit service rating for completed application
     */
    public function rateService(Request $request, $maHSXL)
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $nguoi = $authUser->nguoi;
        }

        if (!$nguoi || !$nguoi->congDan) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin người dùng']);
        }

        $IDCD = $nguoi->congDan->IDCD;

        // Verify ownership and status
        $hoSo = HoSoXuLy::where('IDCD', $IDCD)
            ->where('maHSXL', $maHSXL)
            ->firstOrFail();

        if ($hoSo->maTrangThai != 10) {
            return response()->json(['success' => false, 'message' => 'Hồ sơ chưa được trả kết quả']);
        }

        // Check if within 10 days
        if (!$hoSo->ngayTra) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy ngày trả kết quả']);
        }

        $ngayTra = Carbon::parse($hoSo->ngayTra);
        $daysSince = $ngayTra->diffInDays(now());

        if ($daysSince > 10) {
            return response()->json(['success' => false, 'message' => 'Đã quá thời hạn đánh giá (10 ngày)']);
        }

        // Check if already rated
        $existingRating = DB::table('danhgia')->where('maHSXL', $maHSXL)->first();
        if ($existingRating) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đánh giá hồ sơ này rồi']);
        }

        $request->validate([
            'soDiem' => 'required|integer|min:1|max:5',
            'nhanXet' => 'nullable|string|max:1000'
        ]);

        // Create rating
        DB::table('danhgia')->insert([
            'maHSXL' => $maHSXL,
            'soDiem' => $request->soDiem,
            'nhanXet' => $request->nhanXet,
            'IDCD' => $IDCD,
            'ngayDanhGia' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Cảm ơn bạn đã đánh giá!']);
    }

    /**
     * Show citizen ratings history
     */
    public function showRatings()
    {
        $authUser = Auth::user();
        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
            $user = $authUser->user;
        } else {
            $user = $authUser;
            $nguoi = $authUser->nguoi;
        }

        if (!$nguoi || !$nguoi->congDan) {
            abort(404);
        }

        $IDCD = $nguoi->congDan->IDCD;

        // Get ratings with related application info
        $ratings = DB::table('danhgia')
            ->join('hosoxuly', 'danhgia.maHSXL', '=', 'hosoxuly.maHSXL')
            ->join('tthc', 'hosoxuly.maTTHC', '=', 'tthc.maTTHC')
            ->where('danhgia.IDCD', $IDCD)
            ->select(
                'danhgia.*',
                'hosoxuly.maHSXL',
                'tthc.tenTTHC'
            )
            ->orderBy('danhgia.ngayDanhGia', 'desc')
            ->get();

        // Counters for sidebar
        $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)->whereNotNull('ngayKetThucXuLy')->count();
        $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)->whereNull('ngayKetThucXuLy')->count();
        $unreadCount = $this->getUnreadCount($IDCD);

        return view('pages.profile', [
            'user' => $user,
            'nguoi' => $nguoi,
            'activePage' => 'ratings',
            'ratings' => $ratings,
            'hoSoHoanThanh' => $hoSoHoanThanh,
            'hoSoDangXuLy' => $hoSoDangXuLy,
            'unreadCount' => $unreadCount
        ]);
    }
}
