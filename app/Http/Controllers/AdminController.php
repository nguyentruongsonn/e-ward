<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        return redirect()->route('admin.login');
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

        // Thống kê
        $stats = [
            'total_hoso' => HoSoXuLy::count(),
            'hoso_moi' => HoSoXuLy::whereDate('ngayTiepNhan', today())->count(),
            'total_congdan' => CongDan::count(),
            'total_lichhen' => LichHen::count(),
            'lichhen_hom_nay' => LichHen::whereDate('thoiGianHen', today())->count(),
            'total_tthc' => TTHC::count(),
        ];

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

        return view('admin.dashboard', compact('stats', 'hosos', 'lichhens'));
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

        return view('admin.appointment.index', compact('appointments'));
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

        return view('admin.appointment.today', compact('appointments', 'today'));
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

        $lichHen = DB::table('lichhen')
            ->where('checkin_token', $token)
            ->first();

        if (!$lichHen) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lịch hẹn.',
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
        if (!$this->isAdmin()) {
            return response()->json(['success' => false]);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Xử lý file upload mới
        if ($request->hasFile('fileKetQua')) {
            $newFiles = [];
            foreach ($request->file('fileKetQua') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/ketqua', $fileName);
                // Save full path
                $newFiles[] = 'storage/ketqua/' . $fileName;
            }
            
            // Merge with existing files
            $existingFiles = json_decode($hoso->duongdanfileketqua, true) ?? [];
            $allFiles = array_merge($existingFiles, $newFiles);
            $hoso->duongdanfileketqua = json_encode($allFiles);
        }
        
        $hoso->save();

        return response()->json([
            'success' => true, 
            'message' => 'Đã tải lên kết quả xử lý.',
            'files' => json_decode($hoso->duongdanfileketqua, true)
        ]);
    }

    /**
     * Convert opinion files to result files
     */
    public function convertToResult($maHSXL)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false]);
        }

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        if ($hoso->duongdanfileykien) {
            $filesYKien = json_decode($hoso->duongdanfileykien, true) ?? [];
            $copiedFiles = [];
            
            // Copy each file from ykien to ketqua folder
            foreach ($filesYKien as $filePath) {
                // Extract filename from path
                $fileName = basename($filePath);
                $sourcePath = storage_path('app/public/ykien/' . $fileName);
                $destPath = storage_path('app/public/ketqua/' . $fileName);
                
                if (file_exists($sourcePath)) {
                    copy($sourcePath, $destPath);
                    $copiedFiles[] = 'storage/ketqua/' . $fileName;
                }
            }
            
            // Append to existing result files if any
            $existingFiles = json_decode($hoso->duongdanfileketqua, true) ?? [];
            $allFiles = array_unique(array_merge($existingFiles, $copiedFiles));
            
            $hoso->duongdanfileketqua = json_encode(array_values($allFiles));
            $hoso->save();
        }

        return response()->json([
            'success' => true,
            'files' => json_decode($hoso->duongdanfileketqua, true)
        ]);
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
        $files = array_filter($files, fn($f) => $f !== $request->file);
        $hoso->duongdanfileykien = json_encode(array_values($files));
        $hoso->save();

        // Delete physical file
        $fileName = basename($request->file);
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
        $files = array_filter($files, fn($f) => $f !== $request->file);
        $hoso->duongdanfileketqua = json_encode(array_values($files));
        $hoso->save();

        // Delete physical file
        $fileName = basename($request->file);
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
        
        // Chỉ Lãnh đạo mới được phê duyệt
        if ($user->vaiTro !== 'Lãnh đạo' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Lãnh đạo mới được phê duyệt hồ sơ.');
        }

        $request->validate([
            'yKien' => 'nullable|string|max:1000',
        ]);

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Cập nhật phê duyệt
        $hoso->nguoiDuyet = $user->IDnguoiDung;
        $hoso->ngayDuyet = now();
        $hoso->yKienDuyet = $request->input('yKien', 'Đồng ý');
        $hoso->maTrangThai = 9; // Đã xử lý xong
        $hoso->ngayKetThucXuLy = now()->toDateString();
        $hoso->save();

        return back()->with('success', 'Đã phê duyệt hồ sơ thành công.');
    }

    /**
     * Lãnh đạo trả lại hồ sơ cho cán bộ
     */
    public function traLai(Request $request, $maHSXL)
    {
        if (!$this->isAdmin()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        
        // Chỉ Lãnh đạo mới được trả lại
        if ($user->vaiTro !== 'Lãnh đạo' && $user->vaiTro !== 'Quản trị viên') {
            return back()->with('error', 'Chỉ Lãnh đạo mới được trả lại hồ sơ.');
        }

        $request->validate([
            'lyDo' => 'required|string|max:1000',
        ]);

        $hoso = HoSoXuLy::where('maHSXL', $maHSXL)->firstOrFail();
        
        // Trả lại hồ sơ cho cán bộ
        $hoso->maTrangThai = 5; // Yêu cầu bổ sung
        $hoso->ghiChu = ($hoso->ghiChu ?? '') . "\n[" . now()->format('d/m/Y H:i') . "] Lãnh đạo yêu cầu: " . $request->input('lyDo');
        $hoso->save();

        return back()->with('error', 'Đã trả lại hồ sơ cho cán bộ.');
    }
}

