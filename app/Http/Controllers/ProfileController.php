<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\HoSoXuLy;
use App\Models\CongDan;
use App\Models\TTHC;
use App\Models\LichSuThanhToan;
use App\Models\LichHen;
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

    /**
     * Helper method to ensure all lich hen have corresponding ho so xu ly
     */
    private function ensureHoSoForLichHen($IDCD, $nguoi)
    {
        // Lấy tất cả lịch hẹn của công dân
        $lichHens = LichHen::where('IDCD', $IDCD)->get();
        foreach ($lichHens as $lichHen) {
            // Kiểm tra xem đã có hồ sơ xử lý tương ứng chưa
            $hoSoXuLy = HoSoXuLy::where('IDCD', $IDCD)
                ->where('maTTHC', $lichHen->maTTHC)
                ->first();
            
            // Nếu chưa có hồ sơ, tạo mới
            if (!$hoSoXuLy) {
                $maTrangThai = DB::table('trangthaihoso')->where('tenTrangThai', 'Mới nộp')->value('maTrangThai');
                if (!$maTrangThai) {
                    $maTrangThai = DB::table('trangthaihoso')->insertGetId(['tenTrangThai' => 'Mới nộp']);
                }

                $tthc = DB::table('tthc')->where('maTTHC', $lichHen->maTTHC)->first();
                $donViXuLy = $tthc && $tthc->coQuanThucHien ? $tthc->coQuanThucHien : 'Bộ phận Một cửa';

                $tenChuHoSo = $nguoi->hoTen ? $nguoi->hoTen : 'Công dân';
                $email = $nguoi->email ? $nguoi->email : '';
                $soDienThoai = $nguoi->soDienThoai ? $nguoi->soDienThoai : '';

                // Sử dụng Model để tự động tạo maHSXL (Model sẽ tự động tạo trong boot method)
                HoSoXuLy::create([
                    'maTTHC' => $lichHen->maTTHC,
                    'IDCD' => $IDCD,
                    'maForm' => null,
                    'tenChuHoSo' => $tenChuHoSo,
                    'doiTuongThucHien' => null,
                    'email' => $email ? $email : '',
                    'soDienThoai' => $soDienThoai ? $soDienThoai : '',
                'dulieu' => [],
                'ngayTiepNhan' => now('Asia/Ho_Chi_Minh')->toDateString(),
                'ngayHenTra' => null,
                    'maTrangThai' => $maTrangThai,
                    'ngayTra' => null,
                    'hanBoSung' => null,
                    'thongTinTra' => null,
                    'lePhi' => 0,
                    'hinhThuc' => 'Nhận trực tiếp',
                    'ngayKetThucXuLy' => null,
                    'donViXuLy' => $donViXuLy,
                    'ghiChu' => 'Hồ sơ được tạo tự động khi đặt lịch hẹn',
                ]);
            }
        }
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
        
        // Đảm bảo tất cả lịch hẹn đều có hồ sơ xử lý tương ứng
        $this->ensureHoSoForLichHen($IDCD, $nguoi);
        
        // Đếm số hồ sơ đã hoàn thành (có ngayKetThucXuLy không null)
        // Đối với hồ sơ nộp trực tuyến: Đếm theo maTTHC (group), chỉ tính 1 lần cho mỗi maTTHC
        // Đối với hồ sơ từ lịch hẹn: Đếm tất cả
        $allHoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)
            ->whereNotNull('ngayKetThucXuLy')
            ->get();
        
        // Group hồ sơ nộp trực tuyến theo maTTHC
        $hoSoTrucTuyenHoanThanh = $allHoSoHoanThanh->filter(function($hoSo) {
            return $hoSo->hinhThuc === 'Nhận trực tuyến';
        })->groupBy('maTTHC')->count();
        
        // Hồ sơ từ lịch hẹn: Đếm tất cả
        $hoSoTuLichHenHoanThanh = $allHoSoHoanThanh->filter(function($hoSo) {
            return $hoSo->hinhThuc === 'Nhận trực tiếp';
        })->count();
        
        $hoSoHoanThanh = $hoSoTrucTuyenHoanThanh + $hoSoTuLichHenHoanThanh;
        
        // Đếm số hồ sơ đang xử lý (chưa có ngayKetThucXuLy)
        // Đối với hồ sơ nộp trực tuyến: Đếm theo maTTHC (group), chỉ tính 1 lần cho mỗi maTTHC
        // Đối với hồ sơ từ lịch hẹn: Đếm tất cả
        $allHoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)
            ->whereNull('ngayKetThucXuLy')
            ->get();
        
        // Group hồ sơ nộp trực tuyến theo maTTHC
        $hoSoTrucTuyenDangXuLy = $allHoSoDangXuLy->filter(function($hoSo) {
            return $hoSo->hinhThuc === 'Nhận trực tuyến';
        })->groupBy('maTTHC')->count();
        
        // Hồ sơ từ lịch hẹn: Đếm tất cả
        $hoSoTuLichHenDangXuLy = $allHoSoDangXuLy->filter(function($hoSo) {
            return $hoSo->hinhThuc === 'Nhận trực tiếp';
        })->count();
        
        $hoSoDangXuLy = $hoSoTrucTuyenDangXuLy + $hoSoTuLichHenDangXuLy;
        
        // Xử lý tìm kiếm
        // Đối với hồ sơ nộp trực tuyến: Group theo maTTHC, chỉ lấy hồ sơ mới nhất (giống như đặt lịch hẹn)
        // Đối với hồ sơ từ lịch hẹn: Hiển thị tất cả (mỗi lịch hẹn = 1 hồ sơ)
        
        $query = HoSoXuLy::where('IDCD', $IDCD)
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '')
            ->where('maHSXL', '!=', '0')
            ->with(['tthc']);
        
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
        
        // Lấy tất cả hồ sơ trước
        $allHoSo = $query->orderBy('ngayTiepNhan', 'desc')->get();
        
        // Group hồ sơ nộp trực tuyến theo maTTHC, chỉ lấy hồ sơ mới nhất
        $hoSoTrucTuyen = $allHoSo->filter(function($hoSo) {
            return $hoSo->hinhThuc === 'Nhận trực tuyến';
        })->groupBy('maTTHC')->map(function($group) {
            // Lấy hồ sơ mới nhất trong nhóm
            return $group->sortByDesc('ngayTiepNhan')->first();
        });
        
        // Hồ sơ từ lịch hẹn: Giữ nguyên tất cả
        $hoSoTuLichHen = $allHoSo->filter(function($hoSo) {
            return $hoSo->hinhThuc === 'Nhận trực tiếp';
        });
        
        // Gộp lại và sắp xếp theo ngày tiếp nhận
        $hoSoList = $hoSoTrucTuyen->merge($hoSoTuLichHen)
            ->sortByDesc('ngayTiepNhan')
            ->values();
        
        // Phân trang thủ công
        $page = $request->get('page', 1);
        $perPage = 5;
        $total = $hoSoList->count();
        $items = $hoSoList->slice(($page - 1) * $perPage, $perPage)->values();
        
        // Tạo paginator thủ công
        $hoSoList = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Load tất cả lịch hẹn của người dùng và gắn vào từng hồ sơ tương ứng
        // CHỈ gắn lịch hẹn cho hồ sơ từ lịch hẹn (hinhThuc = 'Nhận trực tiếp' và có ghiChu chứa "đặt lịch hẹn")
        // KHÔNG gắn lịch hẹn cho hồ sơ nộp trực tuyến (hinhThuc = 'Nhận trực tuyến')
        
        // Lấy danh sách maTTHC từ các hồ sơ có thể có lịch hẹn (hồ sơ từ lịch hẹn)
        $hoSoTuLichHen = $hoSoList->filter(function($hoSo) {
            // Chỉ xét hồ sơ có hinhThuc = 'Nhận trực tiếp' và ghiChu chứa "đặt lịch hẹn"
            return $hoSo->hinhThuc === 'Nhận trực tiếp' 
                && $hoSo->ghiChu 
                && (stripos($hoSo->ghiChu, 'đặt lịch hẹn') !== false || stripos($hoSo->ghiChu, 'Mã lịch hẹn') !== false);
        });
        
        $maTTHCs = $hoSoTuLichHen->pluck('maTTHC')->unique()->toArray();
        
        if (!empty($maTTHCs)) {
            // Load tất cả lịch hẹn cho các maTTHC này
            $lichHens = LichHen::where('IDCD', $IDCD)
                ->whereIn('maTTHC', $maTTHCs)
                ->with('tthc')
                ->orderByDesc('thoiGianHen')
                ->get()
                ->groupBy('maTTHC'); // Nhóm theo maTTHC
            
            // Chỉ gắn lịch hẹn cho hồ sơ từ lịch hẹn
            foreach ($hoSoList as $hoSo) {
                // Bỏ qua hồ sơ nộp trực tuyến - KHÔNG gắn lịch hẹn
                if ($hoSo->hinhThuc === 'Nhận trực tuyến') {
                    continue;
                }
                
                // Chỉ gắn lịch hẹn cho hồ sơ có hinhThuc = 'Nhận trực tiếp' và có ghiChu liên quan đến lịch hẹn
                if ($hoSo->hinhThuc === 'Nhận trực tiếp' 
                    && $hoSo->ghiChu 
                    && (stripos($hoSo->ghiChu, 'đặt lịch hẹn') !== false || stripos($hoSo->ghiChu, 'Mã lịch hẹn') !== false)
                    && isset($lichHens[$hoSo->maTTHC])) {
                    
                    // Nếu có maLichHen trong ghiChu, tìm lịch hẹn cụ thể đó
                    if (preg_match('/Mã lịch hẹn:\s*([^\s]+)/', $hoSo->ghiChu, $matches)) {
                        $maLichHen = $matches[1];
                        $lichHenCuThe = $lichHens[$hoSo->maTTHC]->firstWhere('maLichHen', $maLichHen);
                        if ($lichHenCuThe) {
                            $hoSo->setRelation('lichHenGanNhat', $lichHenCuThe);
                            continue;
                        }
                    }
                    
                    // Nếu không có maLichHen cụ thể, lấy lịch hẹn gần nhất cùng maTTHC
                    $lichHenGanNhat = $lichHens[$hoSo->maTTHC]->first();
                    if ($lichHenGanNhat) {
                        $hoSo->setRelation('lichHenGanNhat', $lichHenGanNhat);
                    }
                }
            }
        }
        
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
        
        // Đảm bảo tất cả lịch hẹn đều có hồ sơ xử lý tương ứng
        $this->ensureHoSoForLichHen($IDCD, $nguoi);
        
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
        try {
            // Validate maHSXL parameter
            if (empty($maHSXL) || $maHSXL === '0' || $maHSXL === 0) {
                Log::warning('Invalid maHSXL parameter', ['maHSXL' => $maHSXL, 'type' => gettype($maHSXL)]);
                return response()->json([
                    'error' => 'Invalid parameter',
                    'message' => 'Mã hồ sơ không hợp lệ. Vui lòng thử lại.'
                ], 400);
            }

            $authUser = Auth::user();
            if (!$authUser) {
                return response()->json([
                    'error' => 'Unauthorized',
                    'message' => 'Vui lòng đăng nhập để xem chi tiết hồ sơ.'
                ], 401);
            }

            if ($authUser instanceof \App\Models\Nguoi) {
                $nguoi = $authUser;
            } else {
                $nguoi = $authUser->nguoi ?? null;
            }

            if (!$nguoi) {
                return response()->json([
                    'error' => 'User not found',
                    'message' => 'Không tìm thấy thông tin người dùng.'
                ], 404);
            }

            // Đảm bảo có congDan
            if (!$nguoi->congDan) {
                // Tự động tạo congDan nếu chưa có
                $congDan = CongDan::create([
                    'IDnguoiDung' => $nguoi->IDnguoiDung
                ]);
            } else {
                $congDan = $nguoi->congDan;
            }

            $IDCD = $congDan->IDCD;

            // Convert to string to ensure proper matching
            $maHSXL = (string) $maHSXL;
            
            Log::info('Looking for ho so', ['maHSXL' => $maHSXL, 'IDCD' => $IDCD]);

            $hoSo = HoSoXuLy::with('tthc')
                ->where('IDCD', $IDCD)
                ->where('maHSXL', $maHSXL)
                ->first();

            if (!$hoSo) {
                Log::warning('Ho so not found', ['maHSXL' => $maHSXL, 'IDCD' => $IDCD]);
                return response()->json([
                    'error' => 'Not found',
                    'message' => 'Không tìm thấy hồ sơ với mã: ' . $maHSXL
                ], 404);
            }

        // Kiểm tra xem hồ sơ có phải từ đặt lịch hẹn không
        // Logic: 
        // 1. Nếu hinhThuc = 'Nhận trực tuyến' → KHÔNG phải từ đặt lịch (hồ sơ nộp trực tuyến)
        // 2. Nếu hinhThuc = 'Nhận trực tiếp' và ghiChu có chứa "đặt lịch hẹn" hoặc "Mã lịch hẹn" → từ đặt lịch
        // 3. Nếu hinhThuc = 'Nhận trực tiếp' và có lịch hẹn và dữ liệu rỗng/null → từ đặt lịch
        // 4. Ngược lại → từ nộp trực tuyến (KHÔNG hiển thị lịch hẹn)
        $isFromAppointment = false;
        
        // Kiểm tra hình thức: Hồ sơ nộp trực tuyến KHÔNG phải từ đặt lịch
        if ($hoSo->hinhThuc === 'Nhận trực tuyến') {
            $isFromAppointment = false;
        } else if ($hoSo->hinhThuc === 'Nhận trực tiếp') {
            // Kiểm tra ghi chú
            if ($hoSo->ghiChu && (stripos($hoSo->ghiChu, 'đặt lịch hẹn') !== false || 
                                  stripos($hoSo->ghiChu, 'dat lich hen') !== false ||
                                  stripos($hoSo->ghiChu, 'Mã lịch hẹn') !== false)) {
                $isFromAppointment = true;
            } else {
                // Kiểm tra xem có lịch hẹn nào không
                $lichHenCount = LichHen::where('IDCD', $IDCD)
                    ->where('maTTHC', $hoSo->maTTHC)
                    ->count();
                
                if ($lichHenCount > 0) {
                    // Kiểm tra dữ liệu hồ sơ
                    $dulieu = $hoSo->dulieu;
                    $isEmptyData = empty($dulieu) || 
                                  (is_array($dulieu) && count($dulieu) === 0) ||
                                  (is_string($dulieu) && trim($dulieu) === '') ||
                                  $dulieu === null ||
                                  $dulieu === '[]' ||
                                  $dulieu === '{}';
                    
                    // Nếu có lịch hẹn và dữ liệu rỗng → từ đặt lịch
                    if ($isEmptyData) {
                        $isFromAppointment = true;
                    }
                }
            }
        }

        if ($isFromAppointment) {
            // Hồ sơ từ đặt lịch hẹn → trả về thông tin lịch hẹn
            $lichHens = LichHen::where('IDCD', $IDCD)
                ->where('maTTHC', $hoSo->maTTHC)
                ->with('tthc')
                ->orderByDesc('thoiGianHen')
                ->get();

            // Load tất cả quầy làm việc một lần để tránh N+1
            $quayIds = $lichHens->pluck('maQuayLamViec')->filter()->unique()->toArray();
            $quays = [];
            if (!empty($quayIds)) {
                $quays = DB::table('quaylamviec')
                    ->whereIn('maQuayLamViec', $quayIds)
                    ->get()
                    ->keyBy('maQuayLamViec');
            }

            $lichHenList = $lichHens->map(function ($lichHen) use ($quays) {
                $quayInfo = $lichHen->maQuayLamViec ? ($quays[$lichHen->maQuayLamViec] ?? null) : null;

                return [
                    'maLichHen' => $lichHen->maLichHen,
                    'thoiGianHen' => $lichHen->thoiGianHen ? $lichHen->thoiGianHen->format('d/m/Y H:i') : null,
                    'trangThai' => $lichHen->trangThai,
                    'maQuayLamViec' => $lichHen->maQuayLamViec,
                    'tenQuayLamViec' => $quayInfo ? $quayInfo->tenQuayLamViec : null,
                    'soThuTu' => $lichHen->soThuTu,
                    'checkin_time' => $lichHen->checkin_time ? $lichHen->checkin_time->format('d/m/Y H:i') : null,
                    'checkin_token' => $lichHen->checkin_token,
                ];
            });

            $lichHenGanNhat = $lichHens->first();

            return response()->json([
                'type' => 'appointment', // Flag để frontend biết đây là modal lịch hẹn
                'maHSXL' => $hoSo->maHSXL,
                'tenTTHC' => $hoSo->tthc ? $hoSo->tthc->tenTTHC : null,
                'lichHenGanNhat' => $lichHenGanNhat ? [
                    'maLichHen' => $lichHenGanNhat->maLichHen,
                    'thoiGianHen' => $lichHenGanNhat->thoiGianHen ? $lichHenGanNhat->thoiGianHen->format('d/m/Y H:i') : null,
                    'trangThai' => $lichHenGanNhat->trangThai,
                    'maQuayLamViec' => $lichHenGanNhat->maQuayLamViec,
                    'tenQuayLamViec' => $lichHenGanNhat->maQuayLamViec ? ($quays[$lichHenGanNhat->maQuayLamViec]->tenQuayLamViec ?? null) : null,
                    'soThuTu' => $lichHenGanNhat->soThuTu,
                    'checkin_time' => $lichHenGanNhat->checkin_time ? $lichHenGanNhat->checkin_time->format('d/m/Y H:i') : null,
                    'checkin_token' => $lichHenGanNhat->checkin_token,
                ] : null,
                'lichHenList' => $lichHenList,
            ]);
        } else {
            // Hồ sơ từ nộp trực tuyến → trả về thông tin hồ sơ và DANH SÁCH TẤT CẢ các lần nộp cùng maTTHC
            // Load tất cả các hồ sơ nộp trực tuyến cùng maTTHC để hiển thị "Danh sách đã nộp"
            $allHoSoTrucTuyen = HoSoXuLy::where('IDCD', $IDCD)
                ->where('maTTHC', $hoSo->maTTHC)
                ->where('hinhThuc', 'Nhận trực tuyến')
                ->orderByDesc('ngayTiepNhan')
                ->get();
            
            // Format danh sách các lần nộp với đầy đủ thông tin + 3 trường (ghiChu, dulieu, thongTinTra)
            $danhSachNop = $allHoSoTrucTuyen->map(function($hs) {
                return [
                    'maHSXL' => $hs->maHSXL,
                    'tenChuHoSo' => $hs->tenChuHoSo,
                    'soDienThoai' => $hs->soDienThoai,
                    'email' => $hs->email,
                    'donViXuLy' => $hs->donViXuLy,
                    'ngayTiepNhan' => $hs->ngayTiepNhan ? $hs->ngayTiepNhan->format('d/m/Y') : null,
                    'ngayHenTra' => $hs->ngayHenTra ? $hs->ngayHenTra->format('d/m/Y') : null,
                    'ngayKetThucXuLy' => $hs->ngayKetThucXuLy ? $hs->ngayKetThucXuLy->format('d/m/Y') : null,
                    'maTrangThai' => $hs->maTrangThai,
                    'trangThai' => $hs->ngayKetThucXuLy ? 'Đã hoàn thành' : 'Đang xử lý',
                    'lePhi' => $hs->lePhi,
                    'hinhThuc' => $hs->hinhThuc,
                    'ghiChu' => $hs->ghiChu,
                    'dulieu' => $hs->dulieu,
                    'thongTinTra' => $hs->thongTinTra,
                ];
            })->toArray();
            
            return response()->json([
                'type' => 'service', // Flag để frontend biết đây là modal hồ sơ
                'maHSXL' => $hoSo->maHSXL,
                'tenTTHC' => $hoSo->tthc ? $hoSo->tthc->tenTTHC : null,
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
                'lichHenList' => [], // Hồ sơ nộp trực tuyến KHÔNG có lịch hẹn
                'danhSachNop' => $danhSachNop, // Danh sách tất cả các lần nộp cùng maTTHC
            ]);
        }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error in showHoSo: ' . $e->getMessage());
            return response()->json([
                'error' => 'Database error',
                'message' => 'Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error in showHoSo: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Server error',
                'message' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
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

        // Đảm bảo tất cả lịch hẹn đều có hồ sơ xử lý tương ứng
        $this->ensureHoSoForLichHen($IDCD, $nguoi);

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

        // Đảm bảo tất cả lịch hẹn đều có hồ sơ xử lý tương ứng
        $this->ensureHoSoForLichHen($IDCD, $nguoi);

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

        // Đảm bảo tất cả lịch hẹn đều có hồ sơ xử lý tương ứng
        $this->ensureHoSoForLichHen($IDCD, $nguoi);

        // Xử lý tìm kiếm giống như method index
        // Đối với hồ sơ nộp trực tuyến: Group theo maTTHC, chỉ lấy hồ sơ mới nhất
        $query = HoSoXuLy::where('IDCD', $IDCD)
            ->whereNotNull('maHSXL')
            ->where('maHSXL', '!=', '')
            ->where('maHSXL', '!=', '0')
            ->with('tthc');

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

        // Lấy tất cả hồ sơ trước
        $allHoSo = $query->orderBy('ngayTiepNhan', 'desc')->get();
        
        // Group hồ sơ nộp trực tuyến theo maTTHC, chỉ lấy hồ sơ mới nhất
        $hoSoTrucTuyen = $allHoSo->filter(function($hoSo) {
            return $hoSo->hinhThuc === 'Nhận trực tuyến';
        })->groupBy('maTTHC')->map(function($group) {
            // Lấy hồ sơ mới nhất trong nhóm
            return $group->sortByDesc('ngayTiepNhan')->first();
        });
        
        // Hồ sơ từ lịch hẹn: Giữ nguyên tất cả
        $hoSoTuLichHen = $allHoSo->filter(function($hoSo) {
            return $hoSo->hinhThuc === 'Nhận trực tiếp';
        });
        
        // Gộp lại và sắp xếp theo ngày tiếp nhận
        $hoSoList = $hoSoTrucTuyen->merge($hoSoTuLichHen)
            ->sortByDesc('ngayTiepNhan')
            ->values();
        
        // Phân trang thủ công
        $perPage = 5;
        $total = $hoSoList->count();
        $items = $hoSoList->slice(($page - 1) * $perPage, $perPage)->values();
        
        // Tạo paginator thủ công
        $hoSoList = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Load lịch hẹn và gắn vào từng hồ sơ (giống như method index)
        // CHỈ gắn lịch hẹn cho hồ sơ từ lịch hẹn (hinhThuc = 'Nhận trực tiếp' và có ghiChu chứa "đặt lịch hẹn")
        // KHÔNG gắn lịch hẹn cho hồ sơ nộp trực tuyến (hinhThuc = 'Nhận trực tuyến')
        
        // Lấy danh sách maTTHC từ các hồ sơ có thể có lịch hẹn (hồ sơ từ lịch hẹn)
        $hoSoTuLichHen = $hoSoList->filter(function($hoSo) {
            // Chỉ xét hồ sơ có hinhThuc = 'Nhận trực tiếp' và ghiChu chứa "đặt lịch hẹn"
            return $hoSo->hinhThuc === 'Nhận trực tiếp' 
                && $hoSo->ghiChu 
                && (stripos($hoSo->ghiChu, 'đặt lịch hẹn') !== false || stripos($hoSo->ghiChu, 'Mã lịch hẹn') !== false);
        });
        
        $maTTHCs = $hoSoTuLichHen->pluck('maTTHC')->unique()->toArray();
        
        if (!empty($maTTHCs)) {
            $lichHens = LichHen::where('IDCD', $IDCD)
                ->whereIn('maTTHC', $maTTHCs)
                ->with('tthc')
                ->orderByDesc('thoiGianHen')
                ->get()
                ->groupBy('maTTHC');
            
            // Chỉ gắn lịch hẹn cho hồ sơ từ lịch hẹn
            foreach ($hoSoList as $hoSo) {
                // Bỏ qua hồ sơ nộp trực tuyến - KHÔNG gắn lịch hẹn
                if ($hoSo->hinhThuc === 'Nhận trực tuyến') {
                    continue;
                }
                
                // Chỉ gắn lịch hẹn cho hồ sơ có hinhThuc = 'Nhận trực tiếp' và có ghiChu liên quan đến lịch hẹn
                if ($hoSo->hinhThuc === 'Nhận trực tiếp' 
                    && $hoSo->ghiChu 
                    && (stripos($hoSo->ghiChu, 'đặt lịch hẹn') !== false || stripos($hoSo->ghiChu, 'Mã lịch hẹn') !== false)
                    && isset($lichHens[$hoSo->maTTHC])) {
                    
                    // Nếu có maLichHen trong ghiChu, tìm lịch hẹn cụ thể đó
                    if (preg_match('/Mã lịch hẹn:\s*([^\s]+)/', $hoSo->ghiChu, $matches)) {
                        $maLichHen = $matches[1];
                        $lichHenCuThe = $lichHens[$hoSo->maTTHC]->firstWhere('maLichHen', $maLichHen);
                        if ($lichHenCuThe) {
                            $hoSo->setRelation('lichHenGanNhat', $lichHenCuThe);
                            continue;
                        }
                    }
                    
                    // Nếu không có maLichHen cụ thể, lấy lịch hẹn gần nhất cùng maTTHC
                    $lichHenGanNhat = $lichHens[$hoSo->maTTHC]->first();
                    if ($lichHenGanNhat) {
                        $hoSo->setRelation('lichHenGanNhat', $lichHenGanNhat);
                    }
                }
            }
        }

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

        // Đảm bảo tất cả lịch hẹn đều có hồ sơ xử lý tương ứng
        $this->ensureHoSoForLichHen($IDCD, $nguoi);

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

        // Đảm bảo tất cả lịch hẹn đều có hồ sơ xử lý tương ứng
        $this->ensureHoSoForLichHen($IDCD, $nguoi);

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
