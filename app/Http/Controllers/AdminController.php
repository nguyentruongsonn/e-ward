<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\Nguoi;
use App\Models\HoSoXuLy;
use App\Models\CongDan;
use App\Models\LichHen;
use App\Models\TTHC;
use App\Models\TrangThaiHoSo;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function showLogin()
    {
        // Nếu đã đăng nhập admin thì redirect về dashboard
        if (Auth::check() && $this->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

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
            $user = Auth::user();

            // Kiểm tra quyền admin
            if ($this->isAdmin($user)) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Đăng nhập thành công!');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Bạn không có quyền truy cập trang quản trị.',
                ])->withInput($request->only('email'));
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * In giấy xác nhận tiếp nhận hồ sơ cho công dân
     */
    public function printConfirmation($maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $hoSo = HoSoXuLy::with(['tthc', 'congdan.nguoi'])->where('maHSXL', $maHSXL)->firstOrFail();

        // Thông tin công dân/người nộp
        $congDan = $hoSo->congdan ?? null;
        $nguoi = $congDan->nguoi ?? null;

        return view('admin.hosoxuly.confirmation', [
            'hoSo' => $hoSo,
            'tthc' => $hoSo->tthc,
            'congDan' => $congDan,
            'nguoi' => $nguoi,
        ]);
    }

    /**
     * In giấy xác nhận chuyển hồ sơ sang lãnh đạo (cho cán bộ thụ lý)
     */
    public function printLeaderTransfer($maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $user = Auth::user();
        if ($user->vaiTro !== 'Cán bộ thụ lý' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Cán bộ thụ lý mới được in giấy chuyển lãnh đạo.');
        }

        $hoSo = HoSoXuLy::with(['tthc', 'congdan.nguoi'])->where('maHSXL', $maHSXL)->firstOrFail();
        $congDan = $hoSo->congdan ?? null;
        $nguoi = $congDan->nguoi ?? null;

        return view('admin.hosoxuly.leader-transfer', [
            'hoSo' => $hoSo,
            'tthc' => $hoSo->tthc,
            'congDan' => $congDan,
            'nguoi' => $nguoi,
            'canBoThuLy' => $user,
        ]);
    }

    /**
     * In giấy xác nhận phê duyệt hồ sơ (cho Lãnh đạo)
     */
    public function printLeaderApproval($maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $user = Auth::user();
        if ($user->vaiTro !== 'Lãnh đạo' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Lãnh đạo mới được in giấy phê duyệt.');
        }

        $hoSo = HoSoXuLy::with(['tthc', 'congdan.nguoi'])->where('maHSXL', $maHSXL)->firstOrFail();
        $congDan = $hoSo->congdan ?? null;
        $nguoi = $congDan->nguoi ?? null;

        return view('admin.hosoxuly.leader-approval', [
            'hoSo' => $hoSo,
            'tthc' => $hoSo->tthc,
            'congDan' => $congDan,
            'nguoi' => $nguoi,
            'lanhDao' => $user,
        ]);
    }

    public function dashboard()
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Tự động cập nhật lại IDCD cho các hồ sơ có IDCD = 0 hoặc 1 (do bug cũ)
        $this->fixHoSoWithWrongIDCD();

        // Thống kê tổng quan
        $stats = [
            'total_hoso' => HoSoXuLy::count(),
            'hoso_moi' => HoSoXuLy::whereDate('ngayTiepNhan', today())->count(),
            'total_congdan' => CongDan::count(),
            'total_lichhen' => LichHen::count(),
            'lichhen_hom_nay' => LichHen::whereDate('thoiGianHen', today())->count(),
            'total_tthc' => TTHC::count(),
        ];

        // Hồ sơ theo tháng (12 tháng gần nhất)
        $hososByMonth = HoSoXuLy::selectRaw('DATE_FORMAT(ngayTiepNhan, "%Y-%m") as month, COUNT(*) as total')
            ->whereNotNull('ngayTiepNhan')
            ->where('ngayTiepNhan', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Chuẩn hóa đủ 12 tháng (nếu tháng nào không có hồ sơ thì = 0)
        $monthlyLabels = [];
        $monthlyValues = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $monthlyLabels[] = $date->format('m/Y');
            $monthlyValues[] = (int) ($hososByMonth[$key] ?? 0);
        }

        // Hồ sơ theo từng trạng thái chi tiết (kể cả trạng thái chưa có hồ sơ, để luôn có đủ màu trong chart)
        $hososByStatus = DB::table('trangthaihoso')
            ->leftJoin('hosoxuly', 'hosoxuly.maTrangThai', '=', 'trangthaihoso.maTrangThai')
            ->select('trangthaihoso.tenTrangThai as name', DB::raw('COUNT(hosoxuly.maHSXL) as total'))
            ->groupBy('trangthaihoso.maTrangThai', 'trangthaihoso.tenTrangThai')
            ->orderBy('trangthaihoso.maTrangThai')
            ->get();

        // Lịch hẹn 7 ngày gần nhất
        $appointmentsByDay = LichHen::selectRaw('DATE(thoiGianHen) as date, COUNT(*) as total')
            ->where('thoiGianHen', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $appointmentLabels = [];
        $appointmentValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $appointmentLabels[] = \Carbon\Carbon::parse($date)->format('d/m');
            $appointmentValues[] = (int) ($appointmentsByDay[$date] ?? 0);
        }

        // Doanh thu 7 ngày gần nhất (nếu có module thanh toán)
        $revenueByDay = DB::table('lichsuthanhtoan')
            ->selectRaw('DATE(ngayGD) as date, SUM(soTien) as total')
            ->where('trangThai', 'Thành công')
            ->where('ngayGD', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        $revenueLabels = [];
        $revenueValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenueLabels[] = \Carbon\Carbon::parse($date)->format('d/m');
            $revenueValues[] = (float) ($revenueByDay[$date] ?? 0);
        }

        // Hồ sơ mới nhất - Dùng Eloquent với model đã config đúng primary key VARCHAR
        // Model đã có: public $incrementing = false; và protected $keyType = 'string';
        $hosos = HoSoXuLy::with(['congdan.nguoi', 'tthc'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '')
            ->orderBy('ngayTiepNhan', 'desc')
            ->orderBy('maHSXL', 'desc')
            ->limit(10)
            ->get();

        // Đảm bảo maHSXL luôn là string (fix cho Eloquent)
        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        // Lịch hẹn sắp tới
        $lichhens = LichHen::with(['congdan.nguoi', 'tthc'])
            ->where('trangThai', '!=', 'Hoàn thành')
            ->where('trangThai', '!=', 'Đã hủy')
            ->orderBy('thoiGianHen', 'asc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'hosos' => $hosos,
            'lichhens' => $lichhens,
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
            'hososByStatus' => $hososByStatus,
            'appointmentLabels' => $appointmentLabels,
            'appointmentValues' => $appointmentValues,
            'revenueLabels' => $revenueLabels,
            'revenueValues' => $revenueValues,
        ]);
    }

    /**
     * Cập nhật lại IDCD cho các hồ sơ có IDCD sai (0 hoặc 1 do bug cũ)
     */
    private function fixHoSoWithWrongIDCD()
    {
        // Lấy các hồ sơ có IDCD = 0 hoặc 1 (do bug cũ)
        // Hoặc có maHSXL là "0" hoặc số 0 (không đúng format)
        // Sử dụng CAST để đảm bảo so sánh đúng với cả string và số
        // Tìm cả hồ sơ có maHSXL = '0' (không cần điều kiện email)
        $hososWithWrongIDCD = DB::table('hosoxuly')
            ->where(function($query) {
                // Tìm hồ sơ có maHSXL = '0' hoặc rỗng (ưu tiên)
                $query->where('maHSXL', '0')
                      ->orWhere('maHSXL', '')
                      ->orWhereRaw("CAST(maHSXL AS CHAR) = '0'")
                      ->orWhereRaw("LENGTH(TRIM(maHSXL)) = 0")
                      ->orWhereRaw("maHSXL NOT LIKE 'HSXL_%'");
            })
            ->get();

        // Nếu không có hồ sơ nào có maHSXL = '0', thì tìm hồ sơ có IDCD = 0 hoặc 1 (và có email)
        if ($hososWithWrongIDCD->isEmpty()) {
            $hososWithWrongIDCD = DB::table('hosoxuly')
                ->whereIn('IDCD', [0, 1])
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->get();
        }

        if ($hososWithWrongIDCD->isEmpty()) {
            return;
        }

        foreach ($hososWithWrongIDCD as $hosoItem) {
            try {
                // Convert object sang array để dễ xử lý
                $hoso = (object)$hosoItem;

                // Tìm người dùng theo email (nếu có)
                $nguoi = null;
                if (!empty($hoso->email)) {
                    $nguoi = DB::table('nguoi')->where('email', $hoso->email)->first();
                }

                // Nếu không tìm thấy người dùng theo email, thử tìm theo IDCD trong bảng congdan
                if (!$nguoi && !empty($hoso->IDCD) && $hoso->IDCD > 1) {
                    $congDanTemp = DB::table('congdan')->where('IDCD', $hoso->IDCD)->first();
                    if ($congDanTemp) {
                        $nguoi = DB::table('nguoi')->where('IDnguoiDung', $congDanTemp->IDnguoiDung)->first();
                    }
                }

                // Nếu vẫn không tìm thấy, bỏ qua hồ sơ này
                if (!$nguoi) {
                    continue;
                }

                // Tìm hoặc tạo công dân
                $congDan = DB::table('congdan')
                    ->where('IDnguoiDung', $nguoi->IDnguoiDung)
                    ->first();

                if (!$congDan) {
                    $IDCD = DB::table('congdan')->insertGetId([
                        'IDnguoiDung' => $nguoi->IDnguoiDung,
                    ]);
                } else {
                    $IDCD = $congDan->IDCD;
                }

                // Nếu IDCD đã đúng thì bỏ qua
                if ($hoso->IDCD == $IDCD && $IDCD > 1) {
                    continue;
                }

                // Đảm bảo oldMaHSXL là string và xử lý cả trường hợp là số 0
                $oldMaHSXL = (string)$hoso->maHSXL;
                if ($oldMaHSXL === '' || $oldMaHSXL === null || $oldMaHSXL === '0' || (int)$oldMaHSXL === 0) {
                    $oldMaHSXL = '0';
                }

                // Nếu mã cũ đã đúng format và IDCD đã đúng thì bỏ qua
                if (preg_match('/^HSXL_\d+_\d{8}_\d{4}$/', $oldMaHSXL)) {
                    // Chỉ cập nhật IDCD nếu sai
                    if ($hoso->IDCD != $IDCD) {
                        DB::table('hosoxuly')
                            ->where('maHSXL', $oldMaHSXL)
                            ->update(['IDCD' => $IDCD]);
                    }
                    continue;
                }

                // Nếu mã cũ là "0" hoặc rỗng hoặc không đúng format, cần tạo mã mới
                if ($oldMaHSXL === '0' || $oldMaHSXL === '' || !preg_match('/^HSXL_/', $oldMaHSXL)) {
                    // Tiếp tục để tạo mã mới
                } else {
                    continue;
                }

                // Trích xuất phần ngày từ mã cũ (nếu có)
                $datePart = '';
                if (preg_match('/_(\d{8})_/', $oldMaHSXL, $matches)) {
                    $datePart = $matches[1];
                } else {
                    // Lấy từ ngày tiếp nhận hoặc ngày hiện tại
                    if ($hoso->ngayTiepNhan) {
                        $datePart = \Carbon\Carbon::parse($hoso->ngayTiepNhan)->format('Ymd');
                    } else {
                        $datePart = now()->format('Ymd');
                    }
                }

                // Tạo mã mới với IDCD đúng - thử nhiều lần để đảm bảo unique
                $maxAttempts = 50;
                $attempts = 0;
                $newMaHSXL = null;

                do {
                    $rand = random_int(1000, 9999);
                    $newMaHSXL = 'HSXL_' . $IDCD . '_' . $datePart . '_' . $rand;
                    $attempts++;
                } while (DB::table('hosoxuly')->where('maHSXL', $newMaHSXL)->exists() && $attempts < $maxAttempts);

                if ($attempts >= $maxAttempts) {
                    // Nếu không tạo được mã unique, thử với timestamp (chỉ lấy 4 số cuối của timestamp)
                    $timestamp = time();
                    $lastFour = substr($timestamp, -4);
                    // Thử thêm một số ngẫu nhiên nhỏ để tăng tính unique
                    $extraRand = random_int(0, 9);
                    $combined = str_pad((int)$lastFour + $extraRand, 4, '0', STR_PAD_LEFT);
                    $newMaHSXL = 'HSXL_' . $IDCD . '_' . $datePart . '_' . $combined;
                }

                // Kiểm tra lại một lần nữa trước khi update
                if (DB::table('hosoxuly')->where('maHSXL', $newMaHSXL)->exists()) {
                    continue; // Bỏ qua nếu vẫn trùng
                }

                // Bắt đầu transaction để đảm bảo tính nhất quán
                DB::beginTransaction();

                try {
                    // Cập nhật các bảng liên quan trước
                    DB::table('tailieunop')
                        ->where('maHSXL', $oldMaHSXL)
                        ->update(['maHSXL' => $newMaHSXL]);

                    DB::table('lichsuthanhtoan')
                        ->where('maHSXL', $oldMaHSXL)
                        ->update(['maHSXL' => $newMaHSXL]);

                    // Cập nhật trong database - dùng raw query để update primary key
                    // Đảm bảo cả old và new đều là string, và dùng CAST để WHERE clause match đúng
                    // Sử dụng CAST trong WHERE để đảm bảo match cả string "0" và số 0
                    $updated = DB::statement(
                        "UPDATE hosoxuly SET maHSXL = ?, IDCD = ? WHERE (maHSXL = ? OR CAST(maHSXL AS CHAR) = ?)",
                        [
                            (string)$newMaHSXL,
                            (int)$IDCD,
                            (string)$oldMaHSXL,
                            (string)$oldMaHSXL
                        ]
                    );

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    // Log lỗi nhưng tiếp tục xử lý các hồ sơ khác
                    Log::error("Lỗi khi cập nhật hồ sơ {$oldMaHSXL}: " . $e->getMessage());
                }
            } catch (\Exception $e) {
                // Log lỗi nhưng tiếp tục xử lý các hồ sơ khác
                Log::error("Lỗi khi xử lý hồ sơ ID {$hoso->maHSXL}: " . $e->getMessage());
            }
        }
    }

    /**
     * Đảm bảo mỗi tài khoản cán bộ đều có bản ghi trong bảng canbo
     * để các chức năng xem/sửa/xóa hoạt động đúng.
     */
    private function ensureCanBoRecords()
    {
        $missingUsers = DB::table('nguoi')
            ->whereIn('vaiTro', ['Cán bộ một cửa', 'Cán bộ thụ lý'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('canbo')
                    ->whereColumn('canbo.IDnguoiDung', 'nguoi.IDnguoiDung');
            })
            ->pluck('IDnguoiDung');

        foreach ($missingUsers as $userId) {
            DB::table('canbo')->insert([
                'IDnguoiDung' => $userId,
                'maQuayLamViec' => null,
            ]);
        }
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

        // Kiểm tra vaiTro trong bảng nguoi - cho phép 4 role
        $allowedRoles = ['Quản trị viên', 'Cán bộ một cửa', 'Cán bộ thụ lý', 'Lãnh đạo'];
        if (in_array($user->vaiTro, $allowedRoles)) {
            return true;
        }

        // Hoặc kiểm tra trong bảng quantrivien
        $isQuanTriVien = DB::table('quantrivien')
            ->where('IDnguoiDung', $user->IDnguoiDung)
            ->exists();

        return $isQuanTriVien;
    }

    /**
     * Kiểm tra user có phải Quản trị viên (super admin) hay không
     * Dùng cho các chức năng chỉ dành riêng cho admin, ví dụ: CRUD TTHC, lĩnh vực, cấu hình hệ thống
     */
    private function isSuperAdmin($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return false;
        }

        return trim($user->vaiTro) === 'Quản trị viên';
    }

    /**
     * Hiển thị danh sách tất cả hồ sơ
     */
    public function indexHoSo(Request $request)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Query hồ sơ với filter
        $query = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '');

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maHSXL', 'LIKE', "%{$search}%")
                  ->orWhere('tenChuHoSo', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
            });
        }

        // Filter theo trạng thái
        if ($request->filled('maTrangThai')) {
            $query->where('maTrangThai', $request->maTrangThai);
        } else {
            // Mặc định chỉ hiển thị hồ sơ chờ tiếp nhận (maTrangThai = 1)
            $query->where('maTrangThai', 1);
        }

        // Filter theo ngày tiếp nhận
        if ($request->filled('ngayTiepNhan_from')) {
            $query->whereDate('ngayTiepNhan', '>=', $request->ngayTiepNhan_from);
        }
        if ($request->filled('ngayTiepNhan_to')) {
            $query->whereDate('ngayTiepNhan', '<=', $request->ngayTiepNhan_to);
        }

        // Sắp xếp
        $query->orderBy('ngayTiepNhan', 'desc')
              ->orderBy('maHSXL', 'desc');

        // Phân trang
        $hosos = $query->paginate(20)->withQueryString();

        // Đảm bảo maHSXL luôn là string
        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        // Lấy danh sách trạng thái để hiển thị trong filter
        $trangThais = TrangThaiHoSo::orderBy('maTrangThai')->get();

        return view('admin.hosoxuly.index', compact('hosos', 'trangThais'));
    }

    /**
     * Hiển thị danh sách TẤT CẢ hồ sơ (không filter theo trạng thái mặc định)
     */
    public function indexAllHoSo(Request $request)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $user = Auth::user();

        // Query hồ sơ với filter
        $query = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '');

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maHSXL', 'LIKE', "%{$search}%")
                  ->orWhere('tenChuHoSo', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
            });
        }

        // Filter theo trạng thái (không có giá trị mặc định - hiển thị tất cả)
        if ($request->filled('maTrangThai')) {
            $query->where('maTrangThai', $request->maTrangThai);
        }

        // Filter theo ngày tiếp nhận
        if ($request->filled('ngayTiepNhan_from')) {
            $query->whereDate('ngayTiepNhan', '>=', $request->ngayTiepNhan_from);
        }
        if ($request->filled('ngayTiepNhan_to')) {
            $query->whereDate('ngayTiepNhan', '<=', $request->ngayTiepNhan_to);
        }

        // Sắp xếp
        $query->orderBy('ngayTiepNhan', 'desc')
              ->orderBy('maHSXL', 'desc');

        // Phân trang
        $hosos = $query->paginate(20)->withQueryString();

        // Đảm bảo maHSXL luôn là string
        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        // Lấy danh sách trạng thái để hiển thị trong filter
        $trangThais = TrangThaiHoSo::orderBy('maTrangThai')->get();

        return view('admin.hosoxuly.all', compact('hosos', 'trangThais', 'user'));
    }

    /**
     * Hiển thị danh sách hồ sơ đã tiếp nhận (status = 2)
     */
    public function indexHoSoTiepNhan(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '')
            ->where('maTrangThai', 2); // Đã tiếp nhận

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maHSXL', 'LIKE', "%{$search}%")
                  ->orWhere('tenChuHoSo', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('ngayTiepNhan', 'desc')->orderBy('maHSXL', 'desc');
        $hosos = $query->paginate(20)->withQueryString();

        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        $trangThais = TrangThaiHoSo::orderBy('maTrangThai')->get();
        return view('admin.hosoxuly.index', compact('hosos', 'trangThais'));
    }

    /**
     * Hiển thị danh sách hồ sơ chờ xử lý (status = 4, đã chuyển lãnh đạo)
     */
    public function indexHoSoChoXuLy(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '')
            ->where('maTrangThai', 4); // Đang xử lý (chờ lãnh đạo duyệt)

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maHSXL', 'LIKE', "%{$search}%")
                  ->orWhere('tenChuHoSo', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('ngayTiepNhan', 'desc')->orderBy('maHSXL', 'desc');
        $hosos = $query->paginate(20)->withQueryString();

        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        $trangThais = TrangThaiHoSo::orderBy('maTrangThai')->get();
        return view('admin.hosoxuly.index', compact('hosos', 'trangThais'));
    }

    /**
     * Hiển thị danh sách hồ sơ nhận trực tiếp (status = 11)
     */
    public function indexHoSoNhanTrucTiep(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '')
            ->where('maTrangThai', 11); // Nhận trực tiếp

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maHSXL', 'LIKE', "%{$search}%")
                  ->orWhere('tenChuHoSo', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('ngayTiepNhan', 'desc')->orderBy('maHSXL', 'desc');
        $hosos = $query->paginate(20)->withQueryString();

        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        $trangThais = TrangThaiHoSo::orderBy('maTrangThai')->get();
        return view('admin.hosoxuly.index', compact('hosos', 'trangThais'));
    }

    /**
     * Hiển thị danh sách hồ sơ yêu cầu bổ sung (status = 5)
     */
    public function indexHoSoYeuCauBoSung(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '')
            ->where('maTrangThai', 5); // Yêu cầu bổ sung

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maHSXL', 'LIKE', "%{$search}%")
                  ->orWhere('tenChuHoSo', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('ngayTiepNhan', 'desc')->orderBy('maHSXL', 'desc');
        $hosos = $query->paginate(20)->withQueryString();

        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        $trangThais = TrangThaiHoSo::orderBy('maTrangThai')->get();
        return view('admin.hosoxuly.index', compact('hosos', 'trangThais'));
    }

    /**
     * Hiển thị danh sách hồ sơ đã xử lý xong (status = 9)
     */
    public function indexHoSoDaXuLyXong(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '')
            ->where('maTrangThai', 9); // Đã xử lý xong

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maHSXL', 'LIKE', "%{$search}%")
                  ->orWhere('tenChuHoSo', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('ngayKetThucXuLy', 'desc')->orderBy('maHSXL', 'desc');
        $hosos = $query->paginate(20)->withQueryString();

        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        $trangThais = TrangThaiHoSo::orderBy('maTrangThai')->get();
        return view('admin.hosoxuly.index', compact('hosos', 'trangThais'));
    }

    /**
     * Hiển thị danh sách hồ sơ đã trả kết quả (status = 10)
     */
    public function indexHoSoDaTraKetQua(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai'])
            ->whereRaw("maHSXL LIKE 'HSXL_%'")
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '0')
            ->where('maHSXL', '!=', '')
            ->where('maTrangThai', 10); // Đã trả kết quả

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maHSXL', 'LIKE', "%{$search}%")
                  ->orWhere('tenChuHoSo', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('ngayTra', 'desc')->orderBy('maHSXL', 'desc');
        $hosos = $query->paginate(20)->withQueryString();

        foreach ($hosos as $h) {
            if ($h->maHSXL !== null) {
                $h->setAttribute('maHSXL', (string)$h->maHSXL);
            }
        }

        $trangThais = TrangThaiHoSo::orderBy('maTrangThai')->get();
        return view('admin.hosoxuly.index', compact('hosos', 'trangThais'));
    }

    /**
     * Hiển thị chi tiết hồ sơ
     */
    public function showHoSo($maHSXL)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Lấy hồ sơ với các relationship
        $hoSo = HoSoXuLy::with(['tthc', 'trangThai', 'congdan.nguoi', 'mailHistory'])
            ->where('maHSXL', $maHSXL)
            ->firstOrFail();

        // Lấy tài liệu đã nộp (nếu có)
        $taiLieu = DB::table('tailieunop')
            ->where('maHSXL', $maHSXL)
            ->get();

        // Lấy lịch sử thanh toán (nếu có)
        $hoSo = HoSoXuLy::with(['congdan.nguoi', 'tthc', 'trangThai', 'files'])->where('maHSXL', $maHSXL)->firstOrFail();
        
        // Lấy lịch sử mail
        $mailHistory = \App\Models\HoSoXuLyMailHistory::where('maHSXL', $maHSXL)
            ->orderBy('sent_at', 'desc')
            ->get();

        // Lấy danh sách tài liệu đã nộp
        $taiLieu = DB::table('tailieunop')->where('maHSXL', $maHSXL)->get();

        // Lấy danh sách lãnh đạo để chuyển hồ sơ
        $lanhDaos = DB::table('nguoi')
            ->where('vaiTro', 'Lãnh đạo')
            ->select('IDnguoiDung', 'hoTen', 'vaiTro')
            ->get();

        return view('admin.hosoxuly.show', compact('hoSo', 'mailHistory', 'taiLieu', 'lanhDaos'));
    }

    /**
     * Gửi mail cho chủ hồ sơ
     */
    public function sendMailHoSo(Request $request, $maHSXL)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        }

        $request->validate([
            'loai_mail' => 'required|in:lien_lac,bo_sung',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        try {
            $hoSo = HoSoXuLy::with('tthc')->where('maHSXL', $maHSXL)->firstOrFail();

            if (!$hoSo->email) {
                return response()->json(['success' => false, 'message' => 'Hồ sơ không có email.']);
            }

            // Gửi email
            Mail::to($hoSo->email)->send(new \App\Mail\HoSoMail(
                $hoSo,
                $request->subject,
                $request->content,
                $request->loai_mail
            ));

            // Lưu thời gian gửi mail
            $hoSo->last_mail_sent_at = now();
            $hoSo->save();

            // Lưu lịch sử gửi mail
            \App\Models\HoSoXuLyMailHistory::create([
                'maHSXL' => $hoSo->maHSXL,
                'direction' => 'outgoing',
                'sender_type' => 'admin',
                'loai_mail' => $request->loai_mail,
                'subject' => $request->subject,
                'content' => $request->content,
                'email' => $hoSo->email,
                'sent_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi mail thành công',
                'email' => $hoSo->email,
                'last_mail_sent_at' => $hoSo->last_mail_sent_at->format('d/m/Y H:i')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gửi mail: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Thêm email reply từ công dân
     */
    public function addMailReply(Request $request, $maHSXL)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'email' => 'required|email',
        ]);

        try {
            $hoSo = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();

            // Lưu email reply từ công dân
            \App\Models\HoSoXuLyMailHistory::create([
                'maHSXL' => $hoSo->maHSXL,
                'direction' => 'incoming',
                'sender_type' => 'citizen',
                'loai_mail' => 'lien_lac', // Mặc định là liên lạc
                'subject' => $request->subject,
                'content' => $request->content,
                'email' => $request->email,
                'sent_at' => $request->filled('sent_at') ? \Carbon\Carbon::parse($request->sent_at) : now(), // Cho phép nhập thời gian hoặc dùng hiện tại
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm email reply từ công dân',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm email reply: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Webhook để nhận email reply từ công dân
     * Có thể được gọi từ email service provider (Mailgun, SendGrid, etc.) hoặc IMAP parser
     */
    public function receiveMailReply(Request $request)
    {
        try {
            // Lấy thông tin từ webhook
            // Format có thể khác nhau tùy email service provider
            $fromEmail = $request->input('from') ?? $request->input('sender') ?? $request->input('email');
            $subject = $this->decodeMimeHeader($request->input('subject') ?? '');
            $content = $request->input('text') ?? $request->input('body') ?? $request->input('content') ?? '';
            $timestamp = $request->input('timestamp') ?? $request->input('date') ?? now();

            // Decode content nếu là base64 hoặc quoted-printable
            $content = $this->decodeEmailContent($content);

            // Tìm hồ sơ theo email người gửi
            $hoSo = HoSoXuLy::where('email', $fromEmail)->first();

            if (!$hoSo) {
                // Nếu không tìm thấy, log và return
                \Log::info('Email reply từ email không có trong hệ thống: ' . $fromEmail);
                return response()->json(['success' => false, 'message' => 'Email không tìm thấy hồ sơ'], 404);
            }

            // Lưu email reply vào lịch sử
            \App\Models\HoSoXuLyMailHistory::create([
                'maHSXL' => $hoSo->maHSXL,
                'direction' => 'incoming',
                'sender_type' => 'citizen',
                'loai_mail' => 'lien_lac',
                'subject' => $subject ?: 'Re: ' . ($hoSo->tthc->tenTTHC ?? 'Hồ sơ'),
                'content' => $content,
                'email' => $fromEmail,
                'sent_at' => is_numeric($timestamp) ? \Carbon\Carbon::createFromTimestamp($timestamp) : \Carbon\Carbon::parse($timestamp),
            ]);

            return response()->json(['success' => true, 'message' => 'Đã nhận email reply']);
        } catch (\Exception $e) {
            \Log::error('Lỗi khi nhận email reply: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Decode MIME encoded-word header (RFC 2047)
     */
    private function decodeMimeHeader($header)
    {
        if (empty($header)) {
            return '';
        }

        // Sử dụng imap_mime_header_decode nếu có
        if (function_exists('imap_mime_header_decode')) {
            $decoded = imap_mime_header_decode($header);
            $result = '';
            foreach ($decoded as $part) {
                $result .= $part->text;
            }
            return $result;
        }

        // Fallback: decode thủ công
        $pattern = '/=\?([^?]+)\?([QB])\?([^?]+)\?=/i';

        return preg_replace_callback($pattern, function($matches) {
            $charset = $matches[1];
            $encoding = strtoupper($matches[2]);
            $text = $matches[3];

            if ($encoding == 'Q') {
                $text = str_replace('_', ' ', $text);
                $text = quoted_printable_decode($text);
            } elseif ($encoding == 'B') {
                $text = base64_decode($text);
            }

            if (function_exists('mb_convert_encoding') && strtoupper($charset) != 'UTF-8') {
                $text = mb_convert_encoding($text, 'UTF-8', $charset);
            }

            return $text;
        }, $header);
    }

    /**
     * Decode email content
     */
    private function decodeEmailContent($content)
    {
        if (empty($content)) {
            return '';
        }

        // Thử decode base64
        $decoded = @base64_decode($content, true);
        if ($decoded !== false && base64_encode($decoded) === $content) {
            $content = $decoded;
        }

        // Decode quoted-printable
        if (function_exists('quoted_printable_decode')) {
            $content = quoted_printable_decode($content);
        }

        // Loại bỏ HTML tags
        $content = strip_tags($content);

        // Decode HTML entities
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/=\r?\n/', '', $content);
        $content = preg_replace('/\n\s*\n/', "\n\n", $content);

        return trim($content);
    }

    /**
     * Cập nhật trạng thái hồ sơ
     */
    public function updateTrangThai(Request $request, $maHSXL)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.'
            ], 403);
        }

        try {
            $request->validate([
                'maTrangThai' => 'required|exists:trangthaihoso,maTrangThai',
            ]);

            // Đảm bảo maHSXL là string (vì primary key là VARCHAR)
            $maHSXLString = (string)$maHSXL;

            $hoso = HoSoXuLy::where('maHSXL', $maHSXLString)->first();

            if (!$hoso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hồ sơ với mã: ' . $maHSXLString
                ], 404);
            }

            $hoso->maTrangThai = (int)$request->maTrangThai;

            // Nếu là trạng thái "Đã trả kết quả" (9), cập nhật ngày trả
            if ($request->maTrangThai == 9 && !$hoso->ngayTra) {
                $hoso->ngayTra = now()->toDateString();
            }

            // Nếu là trạng thái "Đã xử lý xong" (8) hoặc "Đã trả kết quả" (9), cập nhật ngày kết thúc xử lý
            if (in_array($request->maTrangThai, [8, 9]) && !$hoso->ngayKetThucXuLy) {
                $hoso->ngayKetThucXuLy = now()->toDateString();
            }

            $hoso->save();

            $trangThai = TrangThaiHoSo::find($request->maTrangThai);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái thành công.',
                'trangThai' => $trangThai ? $trangThai->tenTrangThai : '-'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error khi cập nhật trạng thái:', [
                'maHSXL' => $maHSXL,
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ: ' . implode(', ', $e->errors()['maTrangThai'] ?? [])
            ], 422);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái:', [
                'maHSXL' => $maHSXL,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hiển thị trang quét QR code lịch hẹn
     */
    public function showScanQR(Request $request)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $token = $request->get('token');
        $lichHen = null;
        $tthc = null;
        $quay = null;
        $congDan = null;
        $nguoi = null;

        if ($token) {
            // Trim và làm sạch token
            $token = trim($token);
            
            $lichHen = DB::table('lichhen')
                ->where('checkin_token', $token)
                ->first();

            if ($lichHen) {
                $tthc = DB::table('tthc')->where('maTTHC', $lichHen->maTTHC)->first();

                if ($lichHen->maQuayLamViec) {
                    $quay = DB::table('quaylamviec')->where('maQuayLamViec', $lichHen->maQuayLamViec)->first();
                }

                $congDan = DB::table('congdan')->where('IDCD', $lichHen->IDCD)->first();
                if ($congDan) {
                    $nguoi = DB::table('nguoi')->where('IDnguoiDung', $congDan->IDnguoiDung)->first();
                }
            }
        }

        return view('admin.appointment.scan', compact('token', 'lichHen', 'tthc', 'quay', 'congDan', 'nguoi'));
    }

    /**
     * Hiển thị danh sách lịch hẹn
     */
    public function indexAppointments(Request $request)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Query lịch hẹn với filter
        $query = LichHen::with(['congdan.nguoi', 'tthc']);

        // Tự động gửi mail nhắc cho lịch hẹn < 24h (chỉ gửi 1 lần)
        $this->autoSendReminders();

        // Chỉ hiển thị lịch hẹn có ngày SAU ngày hiện tại (tương lai)
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $query->whereDate('thoiGianHen', '>', $now->toDateString());

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maLichHen', 'LIKE', "%{$search}%")
                  ->orWhereHas('congdan.nguoi', function($q2) use ($search) {
                      $q2->where('hoTen', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%")
                         ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter theo trạng thái
        if ($request->filled('trangThai')) {
            $query->where('trangThai', $request->trangThai);
        }

        // Filter theo ngày hẹn
        if ($request->filled('from_date')) {
            $query->whereDate('thoiGianHen', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('thoiGianHen', '<=', $request->to_date);
        }

        // Sắp xếp: lịch hẹn gần nhất lên đầu (tăng dần theo thời gian)
        $query->orderBy('thoiGianHen', 'asc');

        // Phân trang
        $appointments = $query->paginate(20)->withQueryString();

        $user = Auth::user();

        return view('admin.appointment.index', compact('appointments', 'user'));
    }

    /**
     * Cập nhật trạng thái lịch hẹn (Chỉ Cán bộ một cửa và Quản trị viên)
     * Tự động tạo HoSoXuLy khi trạng thái = "Hoàn thành"
     */
    public function updateAppointmentStatus(Request $request, $id)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        }

        $user = Auth::user();
        
        // Chỉ Cán bộ một cửa và Quản trị viên mới được cập nhật trạng thái lịch hẹn
        if (!in_array($user->vaiTro, ['Cán bộ một cửa', 'Quản trị viên'])) {
            return response()->json(['success' => false, 'message' => 'Chỉ Cán bộ một cửa và Quản trị viên mới được cập nhật trạng thái lịch hẹn.']);
        }

        $request->validate([
            'trangThai' => 'required|string|in:Đã đặt lịch,Chờ đến,Đang xử lý,Hoàn thành,Đã hủy,Yêu cầu bổ sung giấy tờ,Không đến',
        ]);

        try {
            $appointment = LichHen::find($id);

            if (!$appointment) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy lịch hẹn.']);
            }

            $oldStatus = $appointment->trangThai;
            $newStatus = $request->trangThai;

            // Cập nhật trạng thái
            $appointment->trangThai = $newStatus;
            $appointment->save();

            $responseData = [
                'success' => true,
                'message' => "Đã cập nhật trạng thái lịch hẹn từ '{$oldStatus}' sang '{$newStatus}'.",
            ];

            // Nếu trạng thái = "Hoàn thành", tự động tạo HoSoXuLy
            if ($newStatus === 'Hoàn thành') {
                // Generate maHSXL
                $IDCD = $appointment->IDCD;
                $date = now()->format('Ymd');
                $random = rand(1000, 9999);
                $maHSXL = "HSXL_{$IDCD}_{$date}_{$random}";

                // Check if maHSXL already exists (unlikely but safe)
                while (HoSoXuLy::where('maHSXL', $maHSXL)->exists()) {
                    $random = rand(1000, 9999);
                    $maHSXL = "HSXL_{$IDCD}_{$date}_{ $random}";
                }

                // Lấy thông tin công dân để copy dữ liệu
                $congDan = $appointment->congdan;
                $nguoi = $congDan ? $congDan->nguoi : null;

                // Tạo HoSoXuLy mới
                $hoSo = new HoSoXuLy();
                $hoSo->maHSXL = $maHSXL;
                $hoSo->IDCD = $IDCD;
                $hoSo->maTTHC = $appointment->maTTHC;
                $hoSo->maTrangThai = 11; // Nhận trực tiếp (chờ cán bộ điền thông tin chung)
                $hoSo->ngayTiepNhan = now();
                $hoSo->hinhThuc = 'Nhận trực tiếp'; // Từ lịch hẹn
                
                // Chuẩn bị dữ liệu JSON từ thông tin công dân
                $dulieu = [
                    'hinhThuc' => 'Nhận trực tiếp',
                    'tenTTHC' => $appointment->tthc->tenTTHC ?? '',
                ];

                if ($nguoi) {
                    $hoSo->tenChuHoSo = $nguoi->hoTen;
                    $hoSo->email = $nguoi->email;
                    $hoSo->soDienThoai = $nguoi->soDienThoai;

                    // Thêm các trường thông tin chung vào dulieu
                    $dulieu['hoTen'] = $nguoi->hoTen;
                    $dulieu['ngaySinh'] = $nguoi->ngaySinh;
                    $dulieu['gioiTinh'] = $nguoi->gioiTinh;
                    $dulieu['cccd'] = $nguoi->maCCCD;
                    $dulieu['email'] = $nguoi->email;
                    $dulieu['soDienThoai'] = $nguoi->soDienThoai;
                    $dulieu['diaChi'] = $nguoi->noiThuongTru ?? $nguoi->noiTamTru ?? '';
                    $dulieu['ngayCap'] = ''; // Initialize empty
                    $dulieu['noiCap'] = ''; // Initialize empty
                }

                $hoSo->dulieu = json_encode($dulieu, JSON_UNESCAPED_UNICODE);
                $hoSo->lePhi = 0; // Default fee
                $hoSo->donViXuLy = 'UBND Phường Hòa Hải'; // Default unit

                $hoSo->ghiChu = "Hồ sơ được tạo tự động từ lịch hẹn {$appointment->maLichHen} vào lúc " . now()->format('d/m/Y H:i');
                $hoSo->save();

                $responseData['message'] .= " Đã tự động tạo hồ sơ xử lý {$maHSXL}.";
                $responseData['hoSoCreated'] = true;
                $responseData['maHSXL'] = $maHSXL;
                $responseData['hoSoUrl'] = route('admin.hosoxuly.show', $maHSXL);
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            \Log::error('updateAppointmentStatus error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật trạng thái: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hiển thị lịch hẹn hôm nay
     */
    public function todayAppointments(Request $request)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Tự động gửi mail nhắc cho lịch hẹn < 24h (chỉ gửi 1 lần)
        $this->autoSendReminders();

        // Query lịch hẹn hôm nay
        $today = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();
        $query = LichHen::with(['congdan.nguoi', 'tthc'])
            ->whereDate('thoiGianHen', $today);

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('maLichHen', 'LIKE', "%{$search}%")
                  ->orWhereHas('congdan.nguoi', function($q2) use ($search) {
                      $q2->where('hoTen', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%")
                         ->orWhere('soDienThoai', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter theo trạng thái
        if ($request->filled('trangThai')) {
            $query->where('trangThai', $request->trangThai);
        }

        // Sắp xếp: lịch hẹn gần nhất lên đầu (càng gần càng hiện đầu)
        // Sắp xếp tăng dần theo thoiGianHen (gần nhất trước)
        $query->orderBy('thoiGianHen', 'asc');

        // Phân trang
        $appointments = $query->paginate(20)->withQueryString();

        // Phân trang
        $appointments = $query->paginate(20)->withQueryString();

        $user = Auth::user();

        return view('admin.appointment.today', compact('appointments', 'today', 'user'));
    }

    /**
     * Tự động gửi mail nhắc cho lịch hẹn < 24h
     */
    private function autoSendReminders()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $in24Hours = $now->copy()->addHours(24);

        // Lấy các lịch hẹn trong vòng 24 giờ tới, chưa gửi mail, và trạng thái hợp lệ
        $appointments = LichHen::with(['congdan.nguoi', 'tthc'])
            ->whereBetween('thoiGianHen', [$now, $in24Hours])
            ->whereNull('reminder_sent_at')
            ->whereNotIn('trangThai', ['Hoàn thành', 'Đã hủy', 'Không đến'])
            ->get();

        foreach ($appointments as $appointment) {
            try {
                $congDan = $appointment->congdan;
                if (!$congDan) continue;

                $nguoi = $congDan->nguoi;
                if (!$nguoi || !$nguoi->email) continue;

                $tthc = $appointment->tthc;

                // Gửi email
                Mail::to($nguoi->email)->send(new \App\Mail\AppointmentReminderMail($appointment, $tthc, $nguoi));

                // Đánh dấu đã gửi
                $appointment->reminder_sent_at = $now;
                $appointment->save();
            } catch (\Exception $e) {
                // Log lỗi nhưng không dừng quá trình
                Log::error('Lỗi gửi mail nhắc hẹn: ' . $e->getMessage());
            }
        }
    }

    /**
     * Gửi mail nhắc hẹn thủ công
     */
    public function sendReminder(Request $request)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        }

        $lichHenId = $request->input('lichhen_id');

        if (!$lichHenId) {
            return response()->json(['success' => false, 'message' => 'Thiếu thông tin lịch hẹn.']);
        }

        try {
            $appointment = LichHen::find($lichHenId);

            if (!$appointment) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy lịch hẹn.']);
            }

            // Kiểm tra thời gian (chỉ gửi nếu < 24h)
            $now = Carbon::now('Asia/Ho_Chi_Minh');
            $thoiGianHen = Carbon::parse($appointment->thoiGianHen)->setTimezone('Asia/Ho_Chi_Minh');
            $hoursUntil = $now->diffInHours($thoiGianHen, false);

            if ($hoursUntil < 0 || $hoursUntil > 24) {
                return response()->json(['success' => false, 'message' => 'Chỉ có thể gửi mail nhắc cho lịch hẹn trong vòng 24 giờ tới.']);
            }

            // Kiểm tra đã gửi chưa
            if ($appointment->reminder_sent_at) {
                return response()->json(['success' => false, 'message' => 'Mail nhắc đã được gửi trước đó.']);
            }

            // Lấy thông tin công dân và người dùng
            $congDan = $appointment->congdan;
            if (!$congDan) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin công dân.']);
            }

            $nguoi = $congDan->nguoi;
            if (!$nguoi || !$nguoi->email) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy email người dùng.']);
            }

            $tthc = $appointment->tthc;

            // Gửi email
            Mail::to($nguoi->email)->send(new \App\Mail\AppointmentReminderMail($appointment, $tthc, $nguoi));

            // Đánh dấu đã gửi
            $appointment->reminder_sent_at = $now;
            $appointment->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi mail nhắc hẹn thành công đến ' . $nguoi->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gửi mail: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Xử lý check-in lịch hẹn (chỉ admin mới được check-in)
     */
    public function processCheckin(Request $request, $token)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện thao tác này.',
            ], 403);
        }

        // Trim và làm sạch token
        $token = trim($token);
        
        $lichHen = DB::table('lichhen')
            ->where('checkin_token', $token)
            ->first();

        if (!$lichHen) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lịch hẹn với token này.',
            ], 404);
        }

        // Kiểm tra xem đã check-in chưa
        if (!empty($lichHen->checkin_time)) {
            return response()->json([
                'success' => false,
                'message' => 'Lịch hẹn này đã được check-in rồi.',
                'soThuTu' => $lichHen->soThuTu,
                'maQuayLamViec' => $lichHen->maQuayLamViec,
            ], 422);
        }

        // Tự động chọn quầy còn trống trong giờ đó
        $thoiGianHen = Carbon::parse($lichHen->thoiGianHen)->setTimezone('Asia/Ho_Chi_Minh');
        $startTime = $thoiGianHen->copy()->startOfHour();
        $endTime = $thoiGianHen->copy()->endOfHour();

        $allQuays = DB::table('quaylamviec')->pluck('maQuayLamViec')->toArray();
        $quayTrong = null;

        // Tìm quầy còn trống (chưa đầy 2 lịch trong giờ đó)
        foreach ($allQuays as $quay) {
            $soLuongLichHen = DB::table('lichhen')
                ->where('maTTHC', $lichHen->maTTHC)
                ->where('maQuayLamViec', $quay)
                ->whereBetween('thoiGianHen', [$startTime, $endTime])
                ->whereIn('trangThai', ['Đã đặt lịch', 'Chờ đến', 'Đang xử lý'])
                ->count();

            if ($soLuongLichHen < 2) {
                $quayTrong = $quay;
                break;
            }
        }

        // Nếu tất cả quầy đều đầy, random chọn một quầy
        if (!$quayTrong) {
            $quayTrong = $allQuays[array_rand($allQuays)];
        }

        // Lấy số thứ tự tiếp theo trong ngày cho quầy đã chọn
        $ngayHen = $thoiGianHen->copy()->startOfDay();
        $endOfDay = $thoiGianHen->copy()->endOfDay();

        // Đếm số người đã check-in trong ngày ở quầy này
        $soLuongDaCheckIn = DB::table('lichhen')
            ->where('maQuayLamViec', $quayTrong)
            ->whereBetween('thoiGianHen', [$ngayHen, $endOfDay])
            ->whereNotNull('checkin_time')
            ->whereNotNull('soThuTu')
            ->count();

        $soThuTu = $soLuongDaCheckIn + 1;

        // Cập nhật check-in với quầy đã chọn
        DB::table('lichhen')
            ->where('checkin_token', $token)
            ->update([
                'maQuayLamViec' => $quayTrong,
                'checkin_time' => now('Asia/Ho_Chi_Minh'),
                'soThuTu' => $soThuTu,
                'trangThai' => 'Chờ đến',
                'updated_at' => now('Asia/Ho_Chi_Minh'),
            ]);

        // Lấy thông tin quầy đã chọn
        $quay = DB::table('quaylamviec')->where('maQuayLamViec', $quayTrong)->first();

        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công!',
            'soThuTu' => $soThuTu,
            'maQuayLamViec' => $quayTrong,
            'tenQuayLamViec' => $quay->tenQuayLamViec ?? '',
            'thoiGianCheckIn' => now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
        ]);
    }

    /**
     * Cán bộ tiếp nhận hồ sơ
     */
    public function tiepNhanHoSo($maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Chỉ Cán bộ một cửa mới được tiếp nhận
        if ($user->vaiTro !== 'Cán bộ một cửa' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Cán bộ một cửa mới được tiếp nhận hồ sơ.');
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Cập nhật ngày tiếp nhận và người tiếp nhận
        $hoso->ngayTiepNhan = now();
        $hoso->nguoiTiepNhan = $user->IDnguoiDung;
        $hoso->maTrangThai = 2; // Được tiếp nhận

        // Tính ngày hẹn trả dựa trên thời hạn của TTHC
        $cachThucHien = \App\Models\CachThucHien::where('maTTHC', $hoso->maTTHC)
            ->first();

        if ($cachThucHien && $cachThucHien->thoiHan) {
            // Cộng thêm số ngày làm việc (đơn giản là cộng ngày, nếu cần logic ngày làm việc phức tạp hơn thì cần helper)
            $hoso->ngayHenTra = now()->addDays($cachThucHien->thoiHan);
        }

        $hoso->save();

        return back()->with('success', 'Đã tiếp nhận hồ sơ thành công.');
    }

    /**
     * Cán bộ một cửa chuyển hồ sơ sang cán bộ thụ lý
     */
    public function chuyenThuLy($maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Chỉ Cán bộ một cửa mới được chuyển
        if ($user->vaiTro !== 'Cán bộ một cửa' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Cán bộ một cửa mới được chuyển hồ sơ sang thụ lý.');
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Cập nhật trạng thái
        $hoso->maTrangThai = 2; // Đã tiếp nhận (chờ thụ lý xử lý)
        $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Đã chuyển sang cán bộ thụ lý.";
        $hoso->save();

        return back()->with('success', 'Đã chuyển hồ sơ sang cán bộ thụ lý.');
    }

    /**
     * Cán bộ một cửa trả kết quả cho công dân
     */
    public function traKetQua($maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Chỉ Cán bộ một cửa mới được trả kết quả
        if ($user->vaiTro !== 'Cán bộ một cửa' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Cán bộ một cửa mới được trả kết quả.');
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Cập nhật trạng thái
        $hoso->maTrangThai = 10; // Đã trả kết quả
        $hoso->ngayTra = now()->toDateString();
        $hoso->save();

        return back()->with('success', 'Đã trả kết quả cho công dân.');
    }

    /**
     * Lưu ý kiến xử lý và file đính kèm
     */
    public function yKienXuLy(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false]);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Lưu ý kiến xử lý
        $hoso->yKienXuLy = $request->yKienXuLy;
        
        // Xử lý file upload
        if ($request->hasFile('fileYKien')) {
            $newFiles = [];
            foreach ($request->file('fileYKien') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/ykien', $fileName);
                // Save full path
                $newFiles[] = 'storage/ykien/' . $fileName;
            }
            
            // Merge with existing files
            $existingFiles = json_decode($hoso->duongdanfileykien, true) ?? [];
            $allFiles = array_merge($existingFiles, $newFiles);
            $hoso->duongdanfileykien = json_encode($allFiles);
        }
        
        $hoso->save();

        return response()->json([
            'success' => true, 
            'message' => 'Đã lưu ý kiến xử lý.',
            'files' => json_decode($hoso->duongdanfileykien, true)
        ]);
    }

    /**
     * Lưu kết quả xử lý
     */
    public function ketQuaXuLy(Request $request, $maHSXL)
    {
        try {
            \Log::info('ketQuaXuLy called', [
                'maHSXL' => $maHSXL, 
                'has_files' => $request->hasFile('fileKetQua'),
                'has_converted' => $request->has('converted_files')
            ]);
            
            if (!$this->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized']);
            }

            $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
            
            // Case 1: Saving converted files list (from convertYKienFile)
            if ($request->has('converted_files')) {
                $convertedFiles = $request->converted_files;
                
                // If it's a JSON string, decode it
                if (is_string($convertedFiles)) {
                    $filesArray = json_decode($convertedFiles, true);
                    if (is_array($filesArray)) {
                        $hoso->duongdanfileketqua = json_encode($filesArray);
                        $hoso->save();
                        \Log::info('Converted files saved', ['count' => count($filesArray)]);
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Đã lưu danh sách file kết quả',
                            'files' => $filesArray
                        ]);
                    }
                }
                
                return response()->json(['success' => false, 'message' => 'Invalid converted_files format']);
            }
            
            // Case 2: Uploading new files
            if (!$request->hasFile('fileKetQua')) {
                \Log::warning('No files received in request');
                return response()->json(['success' => false, 'message' => 'Không có file được chọn']);
            }
            
            // Ensure directory exists
            $storageDir = storage_path('app/public/ketqua');
            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
                \Log::info('Created directory: ' . $storageDir);
            }
            
            // Process uploaded files
            $newFiles = [];
            $uploadedFiles = $request->file('fileKetQua');
            
            // Ensure it's an array
            if (!is_array($uploadedFiles)) {
                $uploadedFiles = [$uploadedFiles];
            }
            
            foreach ($uploadedFiles as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('public/ketqua', $fileName);
                    $newFiles[] = 'storage/ketqua/' . $fileName;
                    \Log::info('File uploaded', ['name' => $fileName, 'path' => $path]);
                } else {
                    \Log::error('Invalid file', ['error' => $file->getErrorMessage()]);
                }
            }
            
            if (empty($newFiles)) {
                return response()->json(['success' => false, 'message' => 'Không có file hợp lệ']);
            }
            
            // Get existing files safely
            $existingFiles = [];
            if (!empty($hoso->duongdanfileketqua)) {
                $decoded = json_decode($hoso->duongdanfileketqua, true);
                if (is_array($decoded)) {
                    $existingFiles = $decoded;
                }
            }
            
            // Merge and save
            $allFiles = array_merge($existingFiles, $newFiles);
            $hoso->duongdanfileketqua = json_encode($allFiles);
            $hoso->save();
            
            \Log::info('Files saved successfully', ['count' => count($newFiles), 'total' => count($allFiles)]);

            return response()->json([
                'success' => true, 
                'message' => 'Đã tải lên ' . count($newFiles) . ' file.',
                'files' => $allFiles
            ]);
        } catch (\Exception $e) {
            \Log::error('ketQuaXuLy failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload opinion files
     */
    public function uploadYKien(Request $request, $maHSXL)
    {
        try {
            \Log::info('uploadYKien called', ['maHSXL' => $maHSXL, 'has_files' => $request->hasFile('files')]);
            
            if (!$this->isAdmin()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized']);
            }

            $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
            
            // Check if files exist
            if (!$request->hasFile('files')) {
                \Log::warning('No files received in request');
                return response()->json(['success' => false, 'message' => 'Không có file được chọn']);
            }
            
            // Ensure directory exists
            $storageDir = storage_path('app/public/ykien');
            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0755, true);
                \Log::info('Created directory: ' . $storageDir);
            }
            
            // Process uploaded files
            $newFiles = [];
            $uploadedFiles = $request->file('files');
            
            // Ensure it's an array
            if (!is_array($uploadedFiles)) {
                $uploadedFiles = [$uploadedFiles];
            }
            
            foreach ($uploadedFiles as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('public/ykien', $fileName);
                    $newFiles[] = 'storage/ykien/' . $fileName;
                    \Log::info('File uploaded', ['name' => $fileName, 'path' => $path]);
                } else {
                    \Log::error('Invalid file', ['error' => $file->getErrorMessage()]);
                }
            }
            
            if (empty($newFiles)) {
                return response()->json(['success' => false, 'message' => 'Không có file hợp lệ']);
            }
            
            // Get existing files safely
            $existingFiles = [];
            if (!empty($hoso->duongdanfileykien)) {
                $decoded = json_decode($hoso->duongdanfileykien, true);
                if (is_array($decoded)) {
                    $existingFiles = $decoded;
                }
            }
            
            // Merge and save
            $allFiles = array_merge($existingFiles, $newFiles);
            $hoso->duongdanfileykien = json_encode($allFiles);
            $hoso->save();
            
            \Log::info('Files saved successfully', ['count' => count($newFiles), 'total' => count($allFiles)]);

            return response()->json([
                'success' => true,
                'message' => 'Đã tải lên ' . count($newFiles) . ' file.',
                'files' => $allFiles
            ]);
        } catch (\Exception $e) {
            \Log::error('uploadYKien failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Save opinion comment
     */
    public function saveYKien(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false]);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        $hoso->yKienXuLy = $request->noiDung;
        $hoso->save();

        return response()->json(['success' => true]);
    }

    /**
     * Convert opinion file to result file
     */
    public function convertToResult(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false]);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        $targetFile = $request->file; // The file path to convert
        
        if ($hoso->duongdanfileykien) {
            $filesYKien = json_decode($hoso->duongdanfileykien, true) ?? [];
            
            if (in_array($targetFile, $filesYKien)) {
                $fileName = basename($targetFile);
                $sourcePath = storage_path('app/public/ykien/' . $fileName);
                $destPath = storage_path('app/public/ketqua/' . $fileName);
                
                // Ensure directory exists
                if (!file_exists(dirname($destPath))) {
                    mkdir(dirname($destPath), 0755, true);
                }

                if (file_exists($sourcePath)) {
                    copy($sourcePath, $destPath);
                    
                    // Add to result files
                    $existingFiles = json_decode($hoso->duongdanfileketqua, true) ?? [];
                    $newResultFile = 'storage/ketqua/' . $fileName;
                    
                    if (!in_array($newResultFile, $existingFiles)) {
                        $existingFiles[] = $newResultFile;
                        $hoso->duongdanfileketqua = json_encode($existingFiles);
                        $hoso->save();
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Đã chuyển file thành kết quả',
                        'files' => $existingFiles
                    ]);
                }
            }
        }

        return response()->json(['success' => false, 'message' => 'File không tồn tại']);
    }

    /**
     * Remove opinion file
     */
    public function removeYKienFile(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false]);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        $files = json_decode($hoso->duongdanfileykien, true) ?? [];
        $targetFile = $request->file;

        // Remove from array
        $files = array_values(array_filter($files, fn($f) => $f !== $targetFile));
        $hoso->duongdanfileykien = json_encode($files);
        $hoso->save();

        // Delete physical file
        $fileName = basename($targetFile);
        @unlink(storage_path('app/public/ykien/' . $fileName));

        return response()->json(['success' => true]);
    }

    /**
     * Remove result file
     */
    public function removeKetQuaFile(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false]);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        $files = json_decode($hoso->duongdanfileketqua, true) ?? [];
        $targetFile = $request->file;

        // Remove from array
        $files = array_values(array_filter($files, fn($f) => $f !== $targetFile));
        $hoso->duongdanfileketqua = json_encode($files);
        $hoso->save();

        // Delete physical file
        $fileName = basename($targetFile);
        @unlink(storage_path('app/public/ketqua/' . $fileName));

        return response()->json(['success' => true]);
    }



    /**
     * Cán bộ chuyển  hồ sơ sang lãnh đạo
     */
    public function chuyenLanhDao(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Chỉ Cán bộ thụ lý mới được chuyển sang lãnh đạo
        if ($user->vaiTro !== 'Cán bộ thụ lý' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Cán bộ thụ lý mới được chuyển hồ sơ sang lãnh đạo.');
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Cập nhật trạng thái chuyển lãnh đạo
        $hoso->maTrangThai = 4; // Đang xử lý (chờ lãnh đạo duyệt)
        
        // Lưu danh sách file kết quả (nếu có thay đổi từ frontend)
        if ($request->has('ketQuaFiles')) {
            $files = $request->ketQuaFiles;
            // Ensure valid JSON or empty array
            if (empty($files) || $files === 'null' || $files === 'undefined') {
                $files = '[]';
            }
            $hoso->duongdanfileketqua = $files;
        }

        // Lưu thông tin người được chuyển đến (nếu có)
        if ($request->filled('nguoiDuyet')) {
            // Tạm thời lưu vào cột nguoiDuyet (hoặc có thể cần cột riêng như nguoiDuocChuyenDen)
            // Trong flow này, nguoiDuyet thường là người ĐÃ duyệt, nhưng ở đây ta dùng để chỉ định người SẼ duyệt
            // Tuy nhiên, logic pheDuyet sẽ ghi đè lại cột này bằng người thực tế duyệt.
            // Để đơn giản, ta có thể lưu vào ghi chú hoặc thêm cột mới.
            // Với yêu cầu hiện tại, ta sẽ ghi vào ghi chú người được chỉ định.
            $lanhDao = \App\Models\Nguoi::find($request->nguoiDuyet);
            $tenLanhDao = $lanhDao ? $lanhDao->hoTen : 'Unknown';
            $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Chuyển đến lãnh đạo: " . $tenLanhDao;
        }

        // Lưu bình luận
        if ($request->filled('ghiChu')) {
            $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Ghi chú chuyển: " . $request->ghiChu;
        } else {
            $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Đã chuyển lãnh đạo phê duyệt.";
        }

        $hoso->save();

        return back()->with('success', 'Đã chuyển hồ sơ sang lãnh đạo.');
    }

    /**
     * Lãnh đạo phê duyệt hồ sơ
     */
    public function pheDuyet(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Chỉ Lãnh đạo hoặc Quản trị viên mới được phê duyệt
        if ($user->vaiTro !== 'Lãnh đạo' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Lãnh đạo mới được phê duyệt hồ sơ.');
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Cập nhật trạng thái đã xử lý xong, chuyển về cán bộ một cửa để trả kết quả
        $hoso->maTrangThai = 9; // Đã xử lý xong
        $hoso->nguoiDuyet = $user->IDnguoiDung;
        $hoso->ngayDuyet = now();
        $hoso->ngayKetThucXuLy = now();
        
        // Lưu danh sách file kết quả (nếu có thay đổi từ frontend)
        if ($request->has('ketQuaFiles')) {
            $files = $request->ketQuaFiles;
            // Ensure valid JSON or empty array
            if (empty($files) || $files === 'null' || $files === 'undefined') {
                $files = '[]';
            }
            $hoso->duongdanfileketqua = $files;
        }

        // Lưu ý kiến duyệt
        if ($request->filled('yKienDuyet')) {
            $hoso->yKienDuyet = $request->yKienDuyet;
            $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Lãnh đạo phê duyệt. Hồ sơ đã xử lý xong, chuyển về cán bộ một cửa để trả kết quả: " . $request->yKienDuyet;
        } else {
            $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Lãnh đạo đã phê duyệt. Hồ sơ đã xử lý xong, chuyển về cán bộ một cửa để trả kết quả.";
        }

        $hoso->save();

        return back()->with('success', 'Đã phê duyệt hồ sơ. Hồ sơ đã chuyển về cán bộ một cửa để trả kết quả.');
    }


    /**
     * Lãnh đạo dừng xử lý hồ sơ
     */
    public function traLai(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Chỉ Lãnh đạo mới được dừng xử lý
        if ($user->vaiTro !== 'Lãnh đạo' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Lãnh đạo mới được dừng xử lý hồ sơ.');
        }

        $request->validate([
            'lyDo' => 'required|string|max:1000',
        ]);

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Cập nhật trạng thái dừng xử lý
        $hoso->maTrangThai = 8; // Dừng xử lý
        $hoso->nguoiDuyet = $user->IDnguoiDung;
        $hoso->ngayDuyet = now();
        $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Lãnh đạo dừng xử lý hồ sơ: " . $request->input('lyDo');
        $hoso->save();

        // TODO: Gửi email thông báo cho công dân

        return redirect()->route('admin.hosoxuly.cho-xuly')->with('success', 'Đã dừng xử lý hồ sơ.');
    }

    /**
     * Cập nhật thông tin chung hồ sơ (cho trạng thái 11)
     */
    public function updateGeneralInfo(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();

        // Cập nhật thông tin chính
        $hoso->tenChuHoSo = $request->hoTen;
        $hoso->email = $request->email;
        $hoso->soDienThoai = $request->soDienThoai;

        // Cập nhật dulieu JSON
        $dulieu = json_decode($hoso->dulieu, true) ?? [];
        $dulieu['hoTen'] = $request->hoTen;
        $dulieu['ngaySinh'] = $request->ngaySinh;
        $dulieu['gioiTinh'] = $request->gioiTinh;
        $dulieu['cccd'] = $request->cccd;
        $dulieu['ngayCap'] = $request->ngayCap;
        $dulieu['noiCap'] = $request->noiCap;
        $dulieu['email'] = $request->email;
        $dulieu['soDienThoai'] = $request->soDienThoai;
        $dulieu['diaChi'] = $request->diaChi;
        
        $hoso->dulieu = json_encode($dulieu, JSON_UNESCAPED_UNICODE);
        $hoso->save();

        return back()->with('success', 'Đã cập nhật thông tin chung.');
    }

    /**
     * Upload file thành phần hồ sơ (cho trạng thái 11)
     */
    public function uploadComponentFile(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        }

        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'maThanhPhan' => 'required|string',
        ]);

        try {
            $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
            
            // Tìm giấy tờ tương ứng với thành phần (lấy cái đầu tiên nếu có nhiều)
            $thanhPhan = DB::table('thanhphanhoso')
                ->where('maTTHC', $hoso->maTTHC)
                ->where('tenThanhPhan', $request->maThanhPhan) // Ở view sẽ gửi tên thành phần
                ->first();

            $maGiayTo = $thanhPhan ? $thanhPhan->maGiayTo : null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . $originalName;
                $path = $file->storeAs('public/hoso/' . $maHSXL, $fileName);
                
                // Lưu vào bảng tailieunop
                DB::table('tailieunop')->insert([
                    'maHSXL' => $maHSXL,
                    'maGiayTo' => $maGiayTo,
                    'tenTep' => $originalName,
                    'duongDan' => 'hoso/' . $maHSXL . '/' . $fileName,
                    'kichThuoc' => $file->getSize(),
                    'ngayNop' => now(),
                    'nguoiNop' => Auth::user()->IDnguoiDung
                ]);

                return back()->with('success', 'Đã tải lên tài liệu thành công.');
            }

            return back()->with('error', 'Vui lòng chọn file.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi tải file: ' . $e->getMessage());
        }
    }

    /**
     * Ký số điện tử file kết quả
     */
    public function signFile(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền truy cập']);
        }

        $user = Auth::user();
        
        // Chỉ Lãnh đạo mới được ký số
        if ($user->vaiTro !== 'Lãnh đạo' && $user->vaiTro !== 'Quản trị viên') {
            return response()->json(['success' => false, 'message' => 'Chỉ Lãnh đạo mới được ký số']);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        $filePath = $request->input('file_path');
        $signatureMethod = $request->input('signature_method', 'digital_ca');
        $note = $request->input('note');
        
        // Get existing signatures or create new array
        $signatures = json_decode($hoso->file_signatures ?? '{}', true);
        
        // Check if file is already signed
        if (isset($signatures[$filePath])) {
            return response()->json(['success' => false, 'message' => 'File đã được ký số trước đó']);
        }
        
        // Add signature info
        $signatures[$filePath] = [
            'signed_by' => $user->IDnguoiDung,
            'signed_at' => now()->toDateTimeString(),
            'signature_method' => $signatureMethod,
            'note' => $note
        ];
        
        $hoso->file_signatures = json_encode($signatures);
        $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Lãnh đạo đã ký số file: " . basename($filePath);
        $hoso->save();

        return response()->json([
            'success' => true,
            'message' => 'Ký số thành công'
        ]);
    }

    /**
     * Lãnh đạo yêu cầu xử lý lại
     */
    public function yeuCauXuLyLai(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Chỉ Lãnh đạo mới được yêu cầu xử lý lại
        if ($user->vaiTro !== 'Lãnh đạo' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Lãnh đạo mới được yêu cầu xử lý lại.');
        }

        $request->validate([
            'noiDung' => 'required|string|max:1000',
        ]);

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Chuyển lại cho cán bộ thụ lý
        $hoso->maTrangThai = 2; // Đã tiếp nhận (chờ thụ lý xử lý lại)
        $hoso->nguoiDuyet = null; // Reset người duyệt
        $hoso->ngayDuyet = null;
        $hoso->yKienDuyet = null;
        $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Lãnh đạo yêu cầu xử lý lại: " . $request->noiDung;
        $hoso->save();

        return back()->with('success', 'Đã gửi yêu cầu xử lý lại cho cán bộ thụ lý.');
    }

    /**
     * Yêu cầu bổ sung giấy tờ
     */
    public function yeuCauBoSung(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Cán bộ một cửa và cán bộ thụ lý có thể yêu cầu bổ sung
        if (!in_array($user->vaiTro, ['Cán bộ một cửa', 'Cán bộ thụ lý', 'Quản trị viên'])) {
            return back()->with('error', 'Bạn không có quyền yêu cầu bổ sung giấy tờ.');
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        $giayToCanBoSung = $request->input('giayto', []);
        $ghiChu = $request->input('ghiChu');
        
        if (empty($giayToCanBoSung)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một giấy tờ cần bổ sung.');
        }
        
        // Get document names
        $giayToNames = DB::table('giayto')
            ->whereIn('maGiayTo', $giayToCanBoSung)
            ->pluck('tenGiayTo')
            ->toArray();
        
        // Store supplement request
        $supplementData = [
            'giayto' => $giayToCanBoSung,
            'giayto_names' => $giayToNames,
            'ghi_chu' => $ghiChu,
            'requested_by' => $user->IDnguoiDung,
            'requested_at' => now()->toDateTimeString()
        ];
        
        // Backup current status
        $hoso->maTrangThai_backup = $hoso->maTrangThai;
        $hoso->yeu_cau_bo_sung = json_encode($supplementData);
        $hoso->maTrangThai = 5; // Yêu cầu bổ sung
        $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Yêu cầu bổ sung giấy tờ: " . implode(', ', $giayToNames);
        $hoso->save();

        // TODO: Send email to citizen
        // Mail::to($hoso->email)->send(new DocumentSupplementRequest($hoso, $giayToNames, $ghiChu));

        return back()->with('success', 'Đã gửi yêu cầu bổ sung giấy tờ cho công dân.');
    }

    /**
     * Hiển thị danh sách công dân
     */
    public function indexCongDan(Request $request)
    {
        // Kiểm tra quyền admin
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = DB::table('nguoi')
            ->where('vaiTro', 'Công dân/ Tổ chức')
            ->leftJoin('congdan', 'nguoi.IDnguoiDung', '=', 'congdan.IDnguoiDung')
            ->select('nguoi.*', 'congdan.IDCD');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nguoi.hoTen', 'LIKE', "%{$search}%")
                  ->orWhere('nguoi.email', 'LIKE', "%{$search}%")
                  ->orWhere('nguoi.soDienThoai', 'LIKE', "%{$search}%")
                  ->orWhere('nguoi.maCCCD', 'LIKE', "%{$search}%");
            });
        }

        $congDans = $query->orderBy('nguoi.IDnguoiDung', 'desc')->paginate(20);

        return view('admin.users.congdan', compact('congDans'));
    }

    /**
     * Xem chi tiết một công dân
     */
    public function showCongDan($id)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $congDan = DB::table('nguoi')
            ->where('nguoi.IDnguoiDung', $id)
            ->where('vaiTro', 'Công dân/ Tổ chức')
            ->leftJoin('congdan', 'nguoi.IDnguoiDung', '=', 'congdan.IDnguoiDung')
            ->select('nguoi.*', 'congdan.*')
            ->first();

        if (!$congDan) {
            return redirect()->route('admin.users.congdan')
                ->withErrors(['error' => 'Không tìm thấy công dân.']);
        }

        return view('admin.users.congdan_show', compact('congDan'));
    }

    /**
     * Hiển thị form thêm công dân
     */
    public function createCongDan()
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        return view('admin.users.congdan_create');
    }

    /**
     * Lưu công dân mới
     */
    public function storeCongDan(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $validated = $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi,email',
            'password' => 'required|string|min:6|confirmed',
            'soDienThoai' => 'nullable|string|max:20',
            'maCCCD' => 'required|string|max:20|unique:nguoi,maCCCD',
            'gioiTinh' => 'nullable|string|max:20',
            'ngaySinh' => 'nullable|date',
            'queQuan' => 'nullable|string|max:255',
            'noiThuongTru' => 'nullable|string|max:255',
            'noiTamTru' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $idNguoiDung = DB::table('nguoi')->insertGetId([
                'maCCCD' => $validated['maCCCD'],
                'hoTen' => $validated['hoTen'],
                'gioiTinh' => $validated['gioiTinh'] ?? 'Không xác định',
                'ngaySinh' => $validated['ngaySinh'] ?? null,
                'queQuan' => $validated['queQuan'] ?? null,
                'noiThuongTru' => $validated['noiThuongTru'] ?? null,
                'noiTamTru' => $validated['noiTamTru'] ?? null,
                'soDienThoai' => $validated['soDienThoai'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'vaiTro' => 'Công dân/ Tổ chức',
            ]);

            DB::table('congdan')->insert([
                'IDnguoiDung' => $idNguoiDung,
            ]);

            DB::commit();

            return redirect()->route('admin.users.congdan')
                ->with('success', 'Thêm công dân mới thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi tạo công dân mới', ['error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi lưu công dân. Vui lòng thử lại sau.'])
                ->withInput();
        }
    }

    /**
     * Hiển thị form sửa công dân
     */
    public function editCongDan($id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $congDan = DB::table('nguoi')
            ->leftJoin('congdan', 'nguoi.IDnguoiDung', '=', 'congdan.IDnguoiDung')
            ->where('nguoi.IDnguoiDung', $id)
            ->select('nguoi.*', 'congdan.IDCD')
            ->first();

        if (!$congDan) {
            return redirect()->route('admin.users.congdan')
                ->withErrors(['error' => 'Không tìm thấy công dân.']);
        }

        return view('admin.users.congdan_edit', compact('congDan'));
    }

    /**
     * Cập nhật công dân
     */
    public function updateCongDan(Request $request, $id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $congDan = DB::table('congdan')->where('IDnguoiDung', $id)->first();
        if (!$congDan) {
            return redirect()->route('admin.users.congdan')
                ->withErrors(['error' => 'Không tìm thấy công dân.']);
        }

        $validated = $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi,email,' . $id . ',IDnguoiDung',
            'password' => 'nullable|string|min:6|confirmed',
            'soDienThoai' => 'nullable|string|max:20',
            'maCCCD' => 'required|string|max:20|unique:nguoi,maCCCD,' . $id . ',IDnguoiDung',
            'gioiTinh' => 'nullable|string|max:20',
            'ngaySinh' => 'nullable|date',
            'queQuan' => 'nullable|string|max:255',
            'noiThuongTru' => 'nullable|string|max:255',
            'noiTamTru' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $updateData = [
                'maCCCD' => $validated['maCCCD'],
                'hoTen' => $validated['hoTen'],
                'gioiTinh' => $validated['gioiTinh'] ?? 'Không xác định',
                'ngaySinh' => $validated['ngaySinh'] ?? null,
                'queQuan' => $validated['queQuan'] ?? null,
                'noiThuongTru' => $validated['noiThuongTru'] ?? null,
                'noiTamTru' => $validated['noiTamTru'] ?? null,
                'soDienThoai' => $validated['soDienThoai'] ?? null,
                'email' => $validated['email'],
                'vaiTro' => 'Công dân/ Tổ chức',
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            DB::table('nguoi')
                ->where('IDnguoiDung', $id)
                ->update($updateData);

            DB::commit();

            return redirect()->route('admin.users.congdan')
                ->with('success', 'Cập nhật công dân thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật công dân', ['error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật công dân. Vui lòng thử lại sau.'])
                ->withInput();
        }
    }

    /**
     * Xóa công dân
     */
    public function destroyCongDan($id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $congDan = DB::table('congdan')->where('IDnguoiDung', $id)->first();
        if (!$congDan) {
            return redirect()->route('admin.users.congdan')
                ->withErrors(['error' => 'Không tìm thấy công dân.']);
        }

        $hoSoCount = DB::table('hosoxuly')->where('IDCD', $congDan->IDCD)->count();
        if ($hoSoCount > 0) {
            return redirect()->route('admin.users.congdan')
                ->withErrors(['error' => 'Không thể xóa công dân vì đang có ' . $hoSoCount . ' hồ sơ xử lý liên quan.']);
        }

        DB::beginTransaction();

        try {
            DB::table('congdan')->where('IDnguoiDung', $id)->delete();
            DB::table('nguoi')->where('IDnguoiDung', $id)->delete();

            DB::commit();

            return redirect()->route('admin.users.congdan')
                ->with('success', 'Xóa công dân thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi xóa công dân', ['error' => $e->getMessage()]);

            return redirect()->route('admin.users.congdan')
                ->withErrors(['error' => 'Có lỗi xảy ra khi xóa công dân. Vui lòng thử lại sau.']);
        }
    }

    /**
     * Hiển thị danh sách cán bộ
     */
    public function indexCanBo(Request $request)
    {
        // Chỉ tài khoản Quản trị viên mới được xem danh sách cán bộ
        $user = Auth::user();
        if (!$user || trim($user->vaiTro) !== 'Quản trị viên') {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Đảm bảo các tài khoản cán bộ đều có bản ghi trong bảng canbo để có thể thao tác
        $this->ensureCanBoRecords();

        $query = DB::table('nguoi')
            ->whereIn('vaiTro', ['Cán bộ một cửa', 'Cán bộ thụ lý'])
            ->leftJoin('canbo', 'nguoi.IDnguoiDung', '=', 'canbo.IDnguoiDung')
            ->leftJoin('quaylamviec', 'canbo.maQuayLamViec', '=', 'quaylamviec.maQuayLamViec')
            ->select('nguoi.*', 'canbo.IDCB', 'canbo.maQuayLamViec', 'quaylamviec.tenQuayLamViec');

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nguoi.hoTen', 'LIKE', "%{$search}%")
                  ->orWhere('nguoi.email', 'LIKE', "%{$search}%")
                  ->orWhere('nguoi.soDienThoai', 'LIKE', "%{$search}%")
                  ->orWhere('nguoi.maCCCD', 'LIKE', "%{$search}%");
            });
        }

        // Lọc theo vai trò
        if ($request->filled('vaiTro')) {
            $query->where('nguoi.vaiTro', $request->vaiTro);
        }

        $canBos = $query->orderBy('nguoi.IDnguoiDung', 'desc')->paginate(20);

        return view('admin.users.canbo', compact('canBos'));
    }

    /**
     * Hiển thị form thêm cán bộ
     */
    public function createCanBo()
    {
        $user = Auth::user();
        if (!$user || trim($user->vaiTro) !== 'Quản trị viên') {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $quayLamViecs = DB::table('quaylamviec')->orderBy('maQuayLamViec')->get();

        return view('admin.users.canbo_create', compact('quayLamViecs'));
    }

    /**
     * Lưu thông tin cán bộ mới
     */
    public function storeCanBo(Request $request)
    {
        $user = Auth::user();
        if (!$user || trim($user->vaiTro) !== 'Quản trị viên') {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $validated = $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi,email',
            'password' => 'required|string|min:6|confirmed',
            'soDienThoai' => 'nullable|string|max:20',
            'maCCCD' => 'required|string|max:20|unique:nguoi,maCCCD',
            'vaiTro' => 'required|in:Cán bộ một cửa,Cán bộ thụ lý',
            'maQuayLamViec' => 'nullable|exists:quaylamviec,maQuayLamViec',
        ]);

        DB::beginTransaction();

        try {
            // Tạo bản ghi trong bảng nguoi
            $idNguoiDung = DB::table('nguoi')->insertGetId([
                'maCCCD' => $validated['maCCCD'],
                'hoTen' => $validated['hoTen'],
                'gioiTinh' => $request->input('gioiTinh', 'Không xác định'),
                'ngaySinh' => $request->input('ngaySinh'),
                'queQuan' => $request->input('queQuan'),
                'noiThuongTru' => $request->input('noiThuongTru'),
                'noiTamTru' => $request->input('noiTamTru'),
                'soDienThoai' => $validated['soDienThoai'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'vaiTro' => $validated['vaiTro'],
            ]);

            // Nếu là cán bộ, lưu vào bảng canbo
            if (in_array($validated['vaiTro'], ['Cán bộ một cửa', 'Cán bộ thụ lý'])) {
                DB::table('canbo')->insert([
                    'IDnguoiDung' => $idNguoiDung,
                    'maQuayLamViec' => $validated['maQuayLamViec'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.users.canbo')
                ->with('success', 'Thêm cán bộ mới thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi tạo cán bộ mới', ['error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi lưu cán bộ. Vui lòng thử lại sau.'])
                ->withInput();
        }
    }

    /**
     * Xem chi tiết một cán bộ
     */
    public function showCanBo($id)
    {
        $user = Auth::user();
        if (!$user || trim($user->vaiTro) !== 'Quản trị viên') {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $canBo = DB::table('nguoi')
            ->join('canbo', 'nguoi.IDnguoiDung', '=', 'canbo.IDnguoiDung')
            ->leftJoin('quaylamviec', 'canbo.maQuayLamViec', '=', 'quaylamviec.maQuayLamViec')
            ->where('canbo.IDCB', $id)
            ->select('nguoi.*', 'canbo.*', 'quaylamviec.tenQuayLamViec')
            ->first();

        if (!$canBo) {
            return redirect()->route('admin.users.canbo')
                ->withErrors(['error' => 'Không tìm thấy cán bộ.']);
        }

        return view('admin.users.canbo_show', compact('canBo'));
    }

    /**
     * Hiển thị form sửa cán bộ
     */
    public function editCanBo($id)
    {
        $user = Auth::user();
        if (!$user || trim($user->vaiTro) !== 'Quản trị viên') {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $canBo = DB::table('nguoi')
            ->join('canbo', 'nguoi.IDnguoiDung', '=', 'canbo.IDnguoiDung')
            ->leftJoin('quaylamviec', 'canbo.maQuayLamViec', '=', 'quaylamviec.maQuayLamViec')
            ->where('canbo.IDCB', $id)
            ->select('nguoi.*', 'canbo.*', 'quaylamviec.tenQuayLamViec')
            ->first();

        if (!$canBo) {
            return redirect()->route('admin.users.canbo')
                ->withErrors(['error' => 'Không tìm thấy cán bộ.']);
        }

        $quayLamViecs = DB::table('quaylamviec')->orderBy('maQuayLamViec')->get();

        return view('admin.users.canbo_edit', compact('canBo', 'quayLamViecs'));
    }

    /**
     * Cập nhật thông tin cán bộ
     */
    public function updateCanBo(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || trim($user->vaiTro) !== 'Quản trị viên') {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $canBo = DB::table('canbo')->where('IDCB', $id)->first();
        if (!$canBo) {
            return redirect()->route('admin.users.canbo')
                ->withErrors(['error' => 'Không tìm thấy cán bộ.']);
        }

        $validated = $request->validate([
            'hoTen' => 'required|string|max:255',
            'email' => 'required|email|unique:nguoi,email,' . $canBo->IDnguoiDung . ',IDnguoiDung',
            'password' => 'nullable|string|min:6|confirmed',
            'soDienThoai' => 'nullable|string|max:20',
            'maCCCD' => 'required|string|max:20|unique:nguoi,maCCCD,' . $canBo->IDnguoiDung . ',IDnguoiDung',
            'vaiTro' => 'required|in:Cán bộ một cửa,Cán bộ thụ lý',
            'maQuayLamViec' => 'nullable|exists:quaylamviec,maQuayLamViec',
        ]);

        DB::beginTransaction();

        try {
            // Cập nhật bảng nguoi
            $updateNguoi = [
                'maCCCD' => $validated['maCCCD'],
                'hoTen' => $validated['hoTen'],
                'gioiTinh' => $request->input('gioiTinh', 'Không xác định'),
                'ngaySinh' => $request->input('ngaySinh'),
                'queQuan' => $request->input('queQuan'),
                'noiThuongTru' => $request->input('noiThuongTru'),
                'noiTamTru' => $request->input('noiTamTru'),
                'soDienThoai' => $validated['soDienThoai'] ?? null,
                'email' => $validated['email'],
                'vaiTro' => $validated['vaiTro'],
            ];

            if (!empty($validated['password'])) {
                $updateNguoi['password'] = Hash::make($validated['password']);
            }

            DB::table('nguoi')
                ->where('IDnguoiDung', $canBo->IDnguoiDung)
                ->update($updateNguoi);

            // Cập nhật bảng canbo
            if (in_array($validated['vaiTro'], ['Cán bộ một cửa', 'Cán bộ thụ lý'])) {
                DB::table('canbo')
                    ->where('IDCB', $id)
                    ->update([
                        'maQuayLamViec' => $validated['maQuayLamViec'],
                    ]);
            } else {
                // Nếu chuyển vai trò khác, xóa bản ghi trong bảng canbo
                DB::table('canbo')
                    ->where('IDCB', $id)
                    ->delete();
            }

            DB::commit();

            return redirect()->route('admin.users.canbo')
                ->with('success', 'Cập nhật thông tin cán bộ thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi cập nhật cán bộ', ['error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi cập nhật cán bộ. Vui lòng thử lại sau.'])
                ->withInput();
        }
    }

    /**
     * Xóa cán bộ
     */
    public function destroyCanBo($id)
    {
        $user = Auth::user();
        if (!$user || trim($user->vaiTro) !== 'Quản trị viên') {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $canBo = DB::table('canbo')->where('IDCB', $id)->first();
        if (!$canBo) {
            return redirect()->route('admin.users.canbo')
                ->withErrors(['error' => 'Không tìm thấy cán bộ.']);
        }

        DB::beginTransaction();

        try {
            // Xóa bản ghi trong bảng canbo
            DB::table('canbo')->where('IDCB', $id)->delete();

            // Xóa người dùng tương ứng
            DB::table('nguoi')->where('IDnguoiDung', $canBo->IDnguoiDung)->delete();

            DB::commit();

            return redirect()->route('admin.users.canbo')
                ->with('success', 'Xóa cán bộ thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi xóa cán bộ', ['error' => $e->getMessage()]);

            return redirect()->route('admin.users.canbo')
                ->withErrors(['error' => 'Có lỗi xảy ra khi xóa cán bộ. Vui lòng thử lại sau.']);
        }
    }

    // ==================== QUẢN LÝ LĨNH VỰC ====================
    
    /**
     * Hiển thị danh sách lĩnh vực
     */
    public function indexLinhVuc(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = DB::table('linhvuc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tenLinhVuc', 'LIKE', "%{$search}%");
        }

        $linhVucs = $query->orderBy('maLinhVuc', 'desc')->paginate(20);

        return view('admin.tthc.linhvuc.index', compact('linhVucs'));
    }

    /**
     * Hiển thị form thêm lĩnh vực
     */
    public function createLinhVuc()
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        return view('admin.tthc.linhvuc.create');
    }

    /**
     * Lưu lĩnh vực mới
     */
    public function storeLinhVuc(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $validated = $request->validate([
            'tenLinhVuc' => 'required|string|max:500',
        ]);

        DB::table('linhvuc')->insert([
            'tenLinhVuc' => $validated['tenLinhVuc'],
        ]);

        return redirect()->route('admin.tthc.linhvuc.index')
            ->with('success', 'Thêm lĩnh vực thành công!');
    }

    /**
     * Hiển thị form sửa lĩnh vực
     */
    public function editLinhVuc($id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $linhVuc = DB::table('linhvuc')->where('maLinhVuc', $id)->first();

        if (!$linhVuc) {
            return redirect()->route('admin.tthc.linhvuc.index')
                ->withErrors(['error' => 'Không tìm thấy lĩnh vực.']);
        }

        return view('admin.tthc.linhvuc.edit', compact('linhVuc'));
    }

    /**
     * Cập nhật lĩnh vực
     */
    public function updateLinhVuc(Request $request, $id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $validated = $request->validate([
            'tenLinhVuc' => 'required|string|max:500',
        ]);

        DB::table('linhvuc')
            ->where('maLinhVuc', $id)
            ->update([
                'tenLinhVuc' => $validated['tenLinhVuc'],
            ]);

        return redirect()->route('admin.tthc.linhvuc.index')
            ->with('success', 'Cập nhật lĩnh vực thành công!');
    }

    /**
     * Xóa lĩnh vực
     */
    public function destroyLinhVuc($id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Kiểm tra xem lĩnh vực có đang được sử dụng không
        $count = DB::table('tthc')->where('maLinhVuc', $id)->count();
        if ($count > 0) {
            return redirect()->route('admin.tthc.linhvuc.index')
                ->withErrors(['error' => 'Không thể xóa lĩnh vực này vì đang có ' . $count . ' thủ tục hành chính sử dụng.']);
        }

        DB::table('linhvuc')->where('maLinhVuc', $id)->delete();

        return redirect()->route('admin.tthc.linhvuc.index')
            ->with('success', 'Xóa lĩnh vực thành công!');
    }

    // ==================== QUẢN LÝ TTHC ====================

    /**
     * Hiển thị danh sách TTHC
     */
    public function indexTTHC(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = DB::table('tthc')
            ->leftJoin('linhvuc', 'tthc.maLinhVuc', '=', 'linhvuc.maLinhVuc')
            ->leftJoin('quaylamviec', 'tthc.maQuayLamViec', '=', 'quaylamviec.maQuayLamViec')
            ->select('tthc.*', 'linhvuc.tenLinhVuc', 'quaylamviec.tenQuayLamViec');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tthc.tenTTHC', 'LIKE', "%{$search}%")
                  ->orWhere('linhvuc.tenLinhVuc', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('maLinhVuc')) {
            $query->where('tthc.maLinhVuc', $request->maLinhVuc);
        }

        if ($request->filled('trangThai')) {
            $query->where('tthc.trangThai', $request->trangThai);
        }

        $tthcs = $query->orderBy('tthc.maTTHC', 'desc')->paginate(20);
        $linhVucs = DB::table('linhvuc')->orderBy('tenLinhVuc')->get();

        return view('admin.tthc.index', compact('tthcs', 'linhVucs'));
    }

    /**
     * Hiển thị form thêm TTHC
     */
    public function createTTHC()
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $linhVucs = DB::table('linhvuc')->orderBy('tenLinhVuc')->get();
        $quayLamViecs = DB::table('quaylamviec')->orderBy('maQuayLamViec')->get();
        $doiTuongs = DB::table('doituongthuchien')->orderBy('tenDoiTuong')->get();
        $giayTos = DB::table('giayto')->orderBy('tenGiayTo')->get();

        return view('admin.tthc.create', compact('linhVucs', 'quayLamViecs', 'doiTuongs', 'giayTos'));
    }

    /**
     * Lưu TTHC mới
     */
    public function storeTTHC(Request $request)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $validated = $request->validate([
            'tenTTHC' => 'required|string|max:500',
            'maLinhVuc' => 'required|exists:linhvuc,maLinhVuc',
            'maQuayLamViec' => 'nullable|exists:quaylamviec,maQuayLamViec',
            'trinhTuThucHien' => 'required|string',
            'doiTuongThucHien' => 'required|string',
            'coQuanThucHien' => 'required|string',
            'trangThai' => 'nullable|in:Công khai,Chờ công khai,Bãi bỏ',
            'yeuCauDieuKien' => 'required|string',
            'canCuPhapLy' => 'required|string',
            'ketQuaThucHien' => 'required|string|max:500',
            'doiTuongThucHienThem' => 'nullable|array',
            'doiTuongThucHienThem.*' => 'exists:doituongthuchien,maDoiTuong',
            'cachThucHien' => 'nullable|array',
            'cachThucHien.*.kenh' => 'nullable|string|max:255',
            'cachThucHien.*.thoiHanGiaiQuyet' => 'nullable|string',
            'cachThucHien.*.moTaPhiLePhi' => 'nullable|string',
            'cachThucHien.*.thoiHan' => 'nullable|integer|min:0',
            'cachThucHien.*.moTa' => 'nullable|string',
            'lePhi' => 'nullable|array',
            'lePhi.*.loaiLePhi' => 'nullable|string|max:255',
            'lePhi.*.soTien' => 'nullable|numeric|min:0',
            'lePhi.*.batBuoc' => 'nullable|in:Có,Không',
            'lePhi.*.moTa' => 'nullable|string|max:2000',
            'form_cau_hinh' => 'nullable|json',
            'thanhPhanHoSo' => 'nullable|array',
            'thanhPhanHoSo.*.tenThanhPhan' => 'nullable|string|max:500',
            'thanhPhanHoSo.*.giayTo' => 'nullable|array',
            'thanhPhanHoSo.*.giayTo.*.maGiayTo' => 'nullable|exists:giayto,maGiayTo',
            'thanhPhanHoSo.*.giayTo.*.soLuongBanChinh' => 'nullable|integer|min:0',
            'thanhPhanHoSo.*.giayTo.*.soLuongBanSao' => 'nullable|integer|min:0',
        ], [
            'form_cau_hinh.json' => 'Cấu hình form phải là chuỗi JSON hợp lệ.',
        ]);

        $doiTuongBoSung = collect($request->input('doiTuongThucHienThem', []))
            ->filter()
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values();

        $cachThucHienData = collect($request->input('cachThucHien', []))
            ->map(function ($item) {
                return [
                    'kenh' => trim($item['kenh'] ?? ''),
                    'thoiHanGiaiQuyet' => trim($item['thoiHanGiaiQuyet'] ?? ''),
                    'moTaPhiLePhi' => trim($item['moTaPhiLePhi'] ?? ''),
                    'thoiHan' => isset($item['thoiHan']) && $item['thoiHan'] !== '' ? (int)$item['thoiHan'] : null,
                    'moTa' => trim($item['moTa'] ?? ''),
                ];
            })
            ->filter(function ($item) {
                return $item['kenh'] !== '' ||
                    $item['thoiHanGiaiQuyet'] !== '' ||
                    $item['moTaPhiLePhi'] !== '' ||
                    $item['moTa'] !== '' ||
                    $item['thoiHan'] !== null;
            })
            ->values();

        foreach ($cachThucHienData as $item) {
            if ($item['kenh'] === '') {
                return back()->withErrors(['cachThucHien' => 'Vui lòng nhập tên kênh ở mỗi cách thực hiện.'])->withInput();
            }
        }
        $cachThucHienData = $cachThucHienData->map(function ($item) {
            $item['thoiHan'] = $item['thoiHan'] ?? 0;
            return $item;
        });

        $lePhiData = collect($request->input('lePhi', []))
            ->map(function ($item) {
                return [
                    'loaiLePhi' => trim($item['loaiLePhi'] ?? ''),
                    'soTien' => isset($item['soTien']) && $item['soTien'] !== '' ? (float)$item['soTien'] : null,
                    'batBuoc' => $item['batBuoc'] ?? null,
                    'moTa' => trim($item['moTa'] ?? ''),
                ];
            })
            ->filter(function ($item) {
                return $item['loaiLePhi'] !== '' ||
                    $item['soTien'] !== null ||
                    $item['batBuoc'] ||
                    $item['moTa'] !== '';
            })
            ->values();

        foreach ($lePhiData as $item) {
            if ($item['loaiLePhi'] === '') {
                return back()->withErrors(['lePhi' => 'Mỗi dòng lệ phí phải có tên loại lệ phí.'])->withInput();
            }
            $item['soTien'] = $item['soTien'] ?? 0;
        }

        $thanhPhanData = collect($request->input('thanhPhanHoSo', []))
            ->map(function ($item) {
                $giayTo = collect($item['giayTo'] ?? [])
                    ->map(function ($giay) {
                        return [
                            'maGiayTo' => $giay['maGiayTo'] ?? null,
                            'soLuongBanChinh' => isset($giay['soLuongBanChinh']) && $giay['soLuongBanChinh'] !== '' ? (int)$giay['soLuongBanChinh'] : 0,
                            'soLuongBanSao' => isset($giay['soLuongBanSao']) && $giay['soLuongBanSao'] !== '' ? (int)$giay['soLuongBanSao'] : 0,
                        ];
                    })
                    ->filter(fn($giay) => !empty($giay['maGiayTo']))
                    ->values()
                    ->toArray();

                return [
                    'tenThanhPhan' => trim($item['tenThanhPhan'] ?? ''),
                    'giayTo' => $giayTo,
                ];
            })
            ->filter(fn($item) => $item['tenThanhPhan'] !== '')
            ->values();

        DB::beginTransaction();

        try {
            $maTTHC = DB::table('tthc')->insertGetId([
                'tenTTHC' => $validated['tenTTHC'],
                'maLinhVuc' => $validated['maLinhVuc'],
                'maQuayLamViec' => $validated['maQuayLamViec'] ?? null,
                'trinhTuThucHien' => $validated['trinhTuThucHien'],
                'doiTuongThucHien' => $validated['doiTuongThucHien'],
                'coQuanThucHien' => $validated['coQuanThucHien'],
                'trangThai' => $validated['trangThai'] ?? 'Chờ công khai',
                'yeuCauDieuKien' => $validated['yeuCauDieuKien'],
                'canCuPhapLy' => $validated['canCuPhapLy'],
                'ketQuaThucHien' => $validated['ketQuaThucHien'],
            ]);

            if ($doiTuongBoSung->isNotEmpty()) {
                DB::table('thutucdoituong')->insert(
                    $doiTuongBoSung->map(fn($id) => [
                        'maTTHC' => $maTTHC,
                        'maDoiTuong' => $id,
                    ])->toArray()
                );
            }

            foreach ($cachThucHienData as $item) {
                DB::table('cachthuchien')->insert([
                    'maTTHC' => $maTTHC,
                    'kenh' => $item['kenh'],
                    'thoiHanGiaiQuyet' => $item['thoiHanGiaiQuyet'] ?: null,
                    'moTaPhiLePhi' => $item['moTaPhiLePhi'] ?: null,
                    'thoiHan' => $item['thoiHan'] ?? 0,
                    'moTa' => $item['moTa'] ?: null,
                ]);
            }

            if ($lePhiData->isNotEmpty()) {
                DB::table('lephi')->insert(
                    $lePhiData->map(fn($item) => [
                        'loaiLePhi' => $item['loaiLePhi'],
                        'maTTHC' => $maTTHC,
                        'soTien' => $item['soTien'] ?? 0,
                        'batBuoc' => $item['batBuoc'] ?? null,
                        'moTa' => $item['moTa'] ?: null,
                    ])->toArray()
                );
            }

            if ($request->filled('form_cau_hinh')) {
                DB::table('formtructuyen')->insert([
                    'maTTHC' => $maTTHC,
                    'cauHinhForm' => $request->input('form_cau_hinh'),
                ]);
            }

            foreach ($thanhPhanData as $item) {
                $maThanhPhan = DB::table('thanhphanhoso')->insertGetId([
                    'maTTHC' => $maTTHC,
                    'tenThanhPhan' => $item['tenThanhPhan'],
                ]);

                if (!empty($item['giayTo'])) {
                    DB::table('thanhphangiayto')->insert(
                        collect($item['giayTo'])->map(fn($giay) => [
                            'maThanhPhan' => $maThanhPhan,
                            'maGiayTo' => $giay['maGiayTo'],
                            'soLuongBanChinh' => $giay['soLuongBanChinh'] ?? 0,
                            'soLuongBanSao' => $giay['soLuongBanSao'] ?? 0,
                        ])->toArray()
                    );
                }
            }

            DB::commit();

            return redirect()->route('admin.tthc.index')
                ->with('success', 'Thêm thủ tục hành chính thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Lỗi tạo thủ tục hành chính', ['error' => $e->getMessage()]);

            return back()->withErrors(['error' => 'Có lỗi xảy ra khi lưu thủ tục hành chính. Vui lòng thử lại.'])
                ->withInput();
        }
    }

    /**
     * Hiển thị form sửa TTHC
     */
    public function editTTHC($id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $tthc = DB::table('tthc')->where('maTTHC', $id)->first();

        if (!$tthc) {
            return redirect()->route('admin.tthc.index')
                ->withErrors(['error' => 'Không tìm thấy thủ tục hành chính.']);
        }

        $linhVucs = DB::table('linhvuc')->orderBy('tenLinhVuc')->get();
        $quayLamViecs = DB::table('quaylamviec')->orderBy('maQuayLamViec')->get();

        return view('admin.tthc.edit', compact('tthc', 'linhVucs', 'quayLamViecs'));
    }

    /**
     * Cập nhật TTHC
     */
    public function updateTTHC(Request $request, $id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $validated = $request->validate([
            'tenTTHC' => 'required|string|max:500',
            'maLinhVuc' => 'required|exists:linhvuc,maLinhVuc',
            'maQuayLamViec' => 'nullable|exists:quaylamviec,maQuayLamViec',
            'trinhTuThucHien' => 'required|string',
            'doiTuongThucHien' => 'required|string',
            'coQuanThucHien' => 'required|string',
            'trangThai' => 'nullable|in:Công khai,Chờ công khai,Bãi bỏ',
            'yeuCauDieuKien' => 'required|string',
            'canCuPhapLy' => 'required|string',
            'ketQuaThucHien' => 'required|string|max:500',
        ]);

        DB::table('tthc')
            ->where('maTTHC', $id)
            ->update([
                'tenTTHC' => $validated['tenTTHC'],
                'maLinhVuc' => $validated['maLinhVuc'],
                'maQuayLamViec' => $validated['maQuayLamViec'] ?? null,
                'trinhTuThucHien' => $validated['trinhTuThucHien'],
                'doiTuongThucHien' => $validated['doiTuongThucHien'],
                'coQuanThucHien' => $validated['coQuanThucHien'],
                'trangThai' => $validated['trangThai'] ?? 'Chờ công khai',
                'yeuCauDieuKien' => $validated['yeuCauDieuKien'],
                'canCuPhapLy' => $validated['canCuPhapLy'],
                'ketQuaThucHien' => $validated['ketQuaThucHien'],
            ]);

        return redirect()->route('admin.tthc.index')
            ->with('success', 'Cập nhật thủ tục hành chính thành công!');
    }

    /**
     * Xóa TTHC
     */
    public function destroyTTHC($id)
    {
        if (!$this->isSuperAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Kiểm tra xem TTHC có đang được sử dụng không
        $count = DB::table('hosoxuly')->where('maTTHC', $id)->count();
        if ($count > 0) {
            return redirect()->route('admin.tthc.index')
                ->withErrors(['error' => 'Không thể xóa thủ tục này vì đang có ' . $count . ' hồ sơ sử dụng.']);
        }

        DB::table('tthc')->where('maTTHC', $id)->delete();

        return redirect()->route('admin.tthc.index')
            ->with('success', 'Xóa thủ tục hành chính thành công!');
    }

    // ==================== QUẢN LÝ THANH TOÁN ====================

    /**
     * Hiển thị lịch sử thanh toán
     */
    public function indexPaymentHistory(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $query = DB::table('lichsuthanhtoan')
            ->leftJoin('congdan', 'lichsuthanhtoan.IDCD', '=', 'congdan.IDCD')
            ->leftJoin('nguoi', 'congdan.IDnguoiDung', '=', 'nguoi.IDnguoiDung')
            ->leftJoin('hosoxuly', 'lichsuthanhtoan.maHSXL', '=', 'hosoxuly.maHSXL')
            ->select(
                'lichsuthanhtoan.*',
                'nguoi.hoTen',
                'nguoi.email',
                'nguoi.soDienThoai',
                'hosoxuly.tenChuHoSo'
            );

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('lichsuthanhtoan.maGD', 'LIKE', "%{$search}%")
                  ->orWhere('nguoi.hoTen', 'LIKE', "%{$search}%")
                  ->orWhere('nguoi.email', 'LIKE', "%{$search}%")
                  ->orWhere('hosoxuly.tenChuHoSo', 'LIKE', "%{$search}%");
            });
        }

        // Lọc theo loại giao dịch
        if ($request->filled('loaiGD')) {
            $query->where('lichsuthanhtoan.loaiGD', $request->loaiGD);
        }

        // Lọc theo trạng thái
        if ($request->filled('trangThai')) {
            $query->where('lichsuthanhtoan.trangThai', $request->trangThai);
        }

        // Lọc theo ngày
        if ($request->filled('from_date')) {
            $query->whereDate('lichsuthanhtoan.ngayGD', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('lichsuthanhtoan.ngayGD', '<=', $request->to_date);
        }

        $payments = $query->orderBy('lichsuthanhtoan.ngayGD', 'desc')->paginate(20);

        // Thống kê nhanh
        $stats = [
            'total' => DB::table('lichsuthanhtoan')->where('trangThai', 'Thành công')->sum('soTien'),
            'today' => DB::table('lichsuthanhtoan')
                ->where('trangThai', 'Thành công')
                ->whereDate('ngayGD', today())
                ->sum('soTien'),
            'this_month' => DB::table('lichsuthanhtoan')
                ->where('trangThai', 'Thành công')
                ->whereMonth('ngayGD', now()->month)
                ->whereYear('ngayGD', now()->year)
                ->sum('soTien'),
            'count' => DB::table('lichsuthanhtoan')->where('trangThai', 'Thành công')->count(),
        ];

        return view('admin.payment.history', compact('payments', 'stats'));
    }

    /**
     * Báo cáo doanh thu
     */
    public function revenueReport(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        // Mặc định: tháng hiện tại
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $period = $request->get('period', 'month'); // month, year, custom

        // Thống kê tổng quan
        $totalRevenue = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->sum('soTien');

        $todayRevenue = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->whereDate('ngayGD', today())
            ->sum('soTien');

        $thisMonthRevenue = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->whereMonth('ngayGD', now()->month)
            ->whereYear('ngayGD', now()->year)
            ->sum('soTien');

        $thisYearRevenue = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->whereYear('ngayGD', now()->year)
            ->sum('soTien');

        // Dữ liệu biểu đồ theo ngày (30 ngày gần nhất)
        $dailyData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = DB::table('lichsuthanhtoan')
                ->where('trangThai', 'Thành công')
                ->whereDate('ngayGD', $date->format('Y-m-d'))
                ->sum('soTien');
            
            $dailyData[] = [
                'date' => $date->format('d/m'),
                'revenue' => (float) $revenue
            ];
        }

        // Dữ liệu biểu đồ theo tháng (12 tháng gần nhất)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = DB::table('lichsuthanhtoan')
                ->where('trangThai', 'Thành công')
                ->whereMonth('ngayGD', $date->month)
                ->whereYear('ngayGD', $date->year)
                ->sum('soTien');
            
            $monthlyData[] = [
                'month' => $date->format('m/Y'),
                'revenue' => (float) $revenue
            ];
        }

        // Dữ liệu theo loại giao dịch
        $byPaymentType = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->select('loaiGD', DB::raw('SUM(soTien) as total'))
            ->groupBy('loaiGD')
            ->get();

        // Top 10 hồ sơ có giá trị cao nhất
        $topHoSos = DB::table('lichsuthanhtoan')
            ->leftJoin('hosoxuly', 'lichsuthanhtoan.maHSXL', '=', 'hosoxuly.maHSXL')
            ->leftJoin('congdan', 'lichsuthanhtoan.IDCD', '=', 'congdan.IDCD')
            ->leftJoin('nguoi', 'congdan.IDnguoiDung', '=', 'nguoi.IDnguoiDung')
            ->where('lichsuthanhtoan.trangThai', 'Thành công')
            ->select(
                'lichsuthanhtoan.maHSXL',
                'hosoxuly.tenChuHoSo',
                'nguoi.hoTen',
                DB::raw('SUM(lichsuthanhtoan.soTien) as total')
            )
            ->groupBy('lichsuthanhtoan.maHSXL', 'hosoxuly.tenChuHoSo', 'nguoi.hoTen')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('admin.payment.revenue', compact(
            'totalRevenue',
            'todayRevenue',
            'thisMonthRevenue',
            'thisYearRevenue',
            'dailyData',
            'monthlyData',
            'byPaymentType',
            'topHoSos'
        ));
    }

    /**
     * Xuất Excel lịch sử thanh toán (sử dụng PhpSpreadsheet, không dùng Laravel Excel)
     */
    public function exportPaymentHistory(Request $request)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $filters = [
            'search' => $request->get('search'),
            'loaiGD' => $request->get('loaiGD'),
            'trangThai' => $request->get('trangThai'),
            'from_date' => $request->get('from_date'),
            'to_date' => $request->get('to_date'),
        ];

        $filename = 'lich_su_thanh_toan_' . date('Y-m-d_His') . '.xlsx';

        // Lấy dữ liệu lịch sử thanh toán với filter giống trang lịch sử
        $query = DB::table('lichsuthanhtoan')
            ->leftJoin('congdan', 'lichsuthanhtoan.IDCD', '=', 'congdan.IDCD')
            ->leftJoin('nguoi', 'congdan.IDnguoiDung', '=', 'nguoi.IDnguoiDung')
            ->leftJoin('hosoxuly', 'lichsuthanhtoan.maHSXL', '=', 'hosoxuly.maHSXL')
            ->select(
                'lichsuthanhtoan.*',
                'nguoi.hoTen',
                'nguoi.email',
                'nguoi.soDienThoai',
                'hosoxuly.tenChuHoSo'
            );

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('lichsuthanhtoan.maGD', 'LIKE', "%{$search}%")
                    ->orWhere('nguoi.hoTen', 'LIKE', "%{$search}%")
                    ->orWhere('nguoi.email', 'LIKE', "%{$search}%")
                    ->orWhere('hosoxuly.tenChuHoSo', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($filters['loaiGD'])) {
            $query->where('lichsuthanhtoan.loaiGD', $filters['loaiGD']);
        }

        if (!empty($filters['trangThai'])) {
            $query->where('lichsuthanhtoan.trangThai', $filters['trangThai']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('lichsuthanhtoan.ngayGD', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('lichsuthanhtoan.ngayGD', '<=', $filters['to_date']);
        }

        $payments = $query->orderBy('lichsuthanhtoan.ngayGD', 'desc')->get();

        // Tạo file Excel bằng PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Lịch sử thanh toán');

        // Header
        $headers = [
            'Mã GD',
            'Số GD',
            'Người thanh toán',
            'Email',
            'Số điện thoại',
            'Mã hồ sơ',
            'Chủ hồ sơ',
            'Loại GD',
            'Ngày GD',
            'Số tiền (VNĐ)',
            'Trạng thái',
            'Mô tả',
        ];

        $sheet->fromArray($headers, null, 'A1');

        // Style header
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Dữ liệu
        $row = 2;
        foreach ($payments as $payment) {
            $sheet->setCellValue('A' . $row, $payment->maGD ?? '');
            $sheet->setCellValue('B' . $row, $payment->soGD ?? '');
            $sheet->setCellValue('C' . $row, $payment->hoTen ?? '');
            $sheet->setCellValue('D' . $row, $payment->email ?? '');
            $sheet->setCellValue('E' . $row, $payment->soDienThoai ?? '');
            $sheet->setCellValue('F' . $row, $payment->maHSXL ?? '');
            $sheet->setCellValue('G' . $row, $payment->tenChuHoSo ?? '');
            $sheet->setCellValue('H' . $row, $payment->loaiGD ?? '');
            $sheet->setCellValue(
                'I' . $row,
                $payment->ngayGD ? \Carbon\Carbon::parse($payment->ngayGD)->format('d/m/Y H:i:s') : ''
            );
            $sheet->setCellValue('J' . $row, (float) ($payment->soTien ?? 0));
            $sheet->setCellValue('K' . $row, $payment->trangThai ?? '');
            $sheet->setCellValue('L' . $row, $payment->moTa ?? '');
            $row++;
        }

        // Định dạng cột số tiền
        $sheet->getStyle('J2:J' . ($row - 1))
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Set độ rộng cột
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(18);
        $sheet->getColumnDimension('K')->setWidth(15);
        $sheet->getColumnDimension('L')->setWidth(40);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Xuất Excel báo cáo doanh thu (sử dụng PhpSpreadsheet, không dùng Laravel Excel)
     */
    public function exportRevenueReport()
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Bạn không có quyền truy cập.']);
        }

        $filename = 'bao_cao_doanh_thu_' . date('Y-m-d_His') . '.xlsx';

        // Sheet 1: Thống kê tổng quan
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Thống kê tổng quan');

        $totalRevenue = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->sum('soTien');

        $todayRevenue = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->whereDate('ngayGD', today())
            ->sum('soTien');

        $thisMonthRevenue = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->whereMonth('ngayGD', now()->month)
            ->whereYear('ngayGD', now()->year)
            ->sum('soTien');

        $thisYearRevenue = DB::table('lichsuthanhtoan')
            ->where('trangThai', 'Thành công')
            ->whereYear('ngayGD', now()->year)
            ->sum('soTien');

        $sheet1->setCellValue('A1', 'Chỉ tiêu');
        $sheet1->setCellValue('B1', 'Giá trị');
        $sheet1->setCellValue('A2', 'Tổng doanh thu');
        $sheet1->setCellValue('B2', number_format($totalRevenue, 0, ',', '.') . ' đ');
        $sheet1->setCellValue('A3', 'Doanh thu hôm nay');
        $sheet1->setCellValue('B3', number_format($todayRevenue, 0, ',', '.') . ' đ');
        $sheet1->setCellValue('A4', 'Doanh thu tháng này');
        $sheet1->setCellValue('B4', number_format($thisMonthRevenue, 0, ',', '.') . ' đ');
        $sheet1->setCellValue('A5', 'Doanh thu năm này');
        $sheet1->setCellValue('B5', number_format($thisYearRevenue, 0, ',', '.') . ' đ');

        $sheet1->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet1->getColumnDimension('A')->setWidth(30);
        $sheet1->getColumnDimension('B')->setWidth(25);

        // Sheet 2: Doanh thu theo tháng (12 tháng gần nhất)
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Doanh thu theo tháng');

        $sheet2->setCellValue('A1', 'Tháng');
        $sheet2->setCellValue('B1', 'Doanh thu (VNĐ)');

        $row = 2;
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = DB::table('lichsuthanhtoan')
                ->where('trangThai', 'Thành công')
                ->whereMonth('ngayGD', $date->month)
                ->whereYear('ngayGD', $date->year)
                ->sum('soTien');

            $sheet2->setCellValue('A' . $row, $date->format('m/Y'));
            $sheet2->setCellValue('B' . $row, number_format($revenue, 0, ',', '.'));
            $row++;
        }

        $sheet2->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet2->getColumnDimension('A')->setWidth(20);
        $sheet2->getColumnDimension('B')->setWidth(25);

        // Sheet 3: Top 10 hồ sơ
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Top 10 hồ sơ');

        $sheet3->setCellValue('A1', 'STT');
        $sheet3->setCellValue('B1', 'Mã hồ sơ');
        $sheet3->setCellValue('C1', 'Chủ hồ sơ');
        $sheet3->setCellValue('D1', 'Người nộp');
        $sheet3->setCellValue('E1', 'Tổng tiền (VNĐ)');

        $sheet3->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $topHoSos = DB::table('lichsuthanhtoan')
            ->leftJoin('hosoxuly', 'lichsuthanhtoan.maHSXL', '=', 'hosoxuly.maHSXL')
            ->leftJoin('congdan', 'lichsuthanhtoan.IDCD', '=', 'congdan.IDCD')
            ->leftJoin('nguoi', 'congdan.IDnguoiDung', '=', 'nguoi.IDnguoiDung')
            ->where('lichsuthanhtoan.trangThai', 'Thành công')
            ->select(
                'lichsuthanhtoan.maHSXL',
                'hosoxuly.tenChuHoSo',
                'nguoi.hoTen',
                DB::raw('SUM(lichsuthanhtoan.soTien) as total')
            )
            ->groupBy('lichsuthanhtoan.maHSXL', 'hosoxuly.tenChuHoSo', 'nguoi.hoTen')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $row = 2;
        foreach ($topHoSos as $index => $hoSo) {
            $sheet3->setCellValue('A' . $row, $index + 1);
            $sheet3->setCellValue('B' . $row, $hoSo->maHSXL ?? '');
            $sheet3->setCellValue('C' . $row, $hoSo->tenChuHoSo ?? '');
            $sheet3->setCellValue('D' . $row, $hoSo->hoTen ?? '');
            $sheet3->setCellValue('E' . $row, number_format($hoSo->total, 0, ',', '.'));
            $row++;
        }

        $sheet3->getColumnDimension('A')->setWidth(10);
        $sheet3->getColumnDimension('B')->setWidth(20);
        $sheet3->getColumnDimension('C')->setWidth(30);
        $sheet3->getColumnDimension('D')->setWidth(25);
        $sheet3->getColumnDimension('E')->setWidth(20);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}

