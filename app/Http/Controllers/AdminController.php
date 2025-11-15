<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Nguoi;
use App\Models\HoSoXuLy;
use App\Models\CongDan;
use App\Models\LichHen;
use App\Models\TTHC;
use App\Models\TrangThaiHoSo;

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
}

