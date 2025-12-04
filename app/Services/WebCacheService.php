<?php

namespace App\Services;

use App\Models\CongDan;
use App\Models\HoSoXuLy;
use App\Models\LichHen;
use App\Models\TTHC;
use App\Models\ThongBao;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class WebCacheService
{
    private const REDIS_CACHE_STORE = 'redis_db0';

    /**
     * Danh sách TTHC nổi bật cho trang outstanding-service.
     * Lưu trên Redis 10 phút.
     */
    public function getOutstandingProcedures()
    {
        return Cache::store(self::REDIS_CACHE_STORE)->remember('tthc:outstanding', 600, function () {
            return TTHC::with('doiTuongs')
                ->orderBy('tenTTHC', 'asc')
                ->get();
        });
    }

    /**
     * Danh sách tên TTHC (maTTHC => tenTTHC), cache 10 phút.
     * Key: tthc:names (lưu JSON thuần để dễ xem trên Another Redis)
     */
    public function getProcedureNames(): array
    {
        $key = 'tthc:names';

        // Ưu tiên đọc JSON thuần từ Redis để bạn xem cho dễ
        $raw = Redis::get($key);
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        // Nếu chưa có hoặc JSON lỗi: lấy từ DB rồi lưu lại
        $data = TTHC::orderBy('tenTTHC')
            ->pluck('tenTTHC', 'maTTHC')
            ->toArray();

        Redis::setex($key, 600, json_encode($data, JSON_UNESCAPED_UNICODE));

        return $data;
    }

    /**
     * Chi tiết TTHC (thông tin chung + cách thực hiện + thành phần hồ sơ + đối tượng).
     * Mỗi maTTHC cache 10 phút.
     */
    public function getProcedureDetail(string $maTTHC): array
    {
        $key = "tthc:detail:{$maTTHC}";

        return Cache::store(self::REDIS_CACHE_STORE)->remember($key, 600, function () use ($maTTHC) {
            $tthc = DB::table('tthc as t')
                ->leftJoin('linhvuc as l', 'l.maLinhVuc', '=', 't.maLinhVuc')
                ->select(
                    't.maTTHC',
                    't.tenTTHC',
                    't.trinhTuThucHien',
                    't.coQuanThucHien',
                    't.yeuCauDieuKien',
                    't.canCuPhapLy',
                    't.ketQuaThucHien',
                    'l.tenLinhVuc'
                )
                ->where('t.maTTHC', $maTTHC)
                ->first();

            if (!$tthc) {
                return ['tthc' => null];
            }

            $cachThucHiens = DB::table('cachthuchien')
                ->where('maTTHC', $maTTHC)
                ->select('kenh', 'thoiHanGiaiQuyet', 'moTaPhiLePhi', 'moTa')
                ->get();

            $thanhPhanHoSos = DB::table('thanhphanhoso as tph')
                ->leftJoin('thanhphangiayto as tpg', 'tpg.maThanhPhan', '=', 'tph.maThanhPhan')
                ->leftJoin('giayto as gt', 'gt.maGiayTo', '=', 'tpg.maGiayTo')
                ->where('tph.maTTHC', $maTTHC)
                ->select(
                    'tph.maThanhPhan',
                    'tph.tenThanhPhan',
                    'gt.tenGiayTo',
                    'tpg.soLuongBanChinh',
                    'tpg.soLuongBanSao'
                )
                ->get()
                ->groupBy('tenThanhPhan');

            $doiTuongs = DB::table('thutucdoituong as td')
                ->leftjoin('doituongthuchien as d', 'd.maDoiTuong', '=', 'td.maDoiTuong')
                ->where('td.maTTHC', $maTTHC)
                ->select('d.tenDoiTuong')
                ->get();

            return [
                'tthc'            => $tthc,
                'cachThucHiens'   => $cachThucHiens,
                'thanhPhanHoSos'  => $thanhPhanHoSos,
                'doiTuongs'       => $doiTuongs,
            ];
        });
    }

    /**
     * Thống kê đánh giá dịch vụ công (ratings) cho trang public.
     * Cache 10 phút.
     */
    public function getRatingsSummary()
    {
        return Cache::store(self::REDIS_CACHE_STORE)->remember('ratings:summary', 600, function () {
            return DB::table('danhgia')
                ->join('hosoxuly', 'danhgia.maHSXL', '=', 'hosoxuly.maHSXL')
                ->join('tthc', 'hosoxuly.maTTHC', '=', 'tthc.maTTHC')
                ->select(
                    'tthc.maTTHC',
                    'tthc.tenTTHC',
                    DB::raw('AVG(danhgia.soDiem) as avg_score'),
                    DB::raw('COUNT(danhgia.id) as total_ratings')
                )
                ->groupBy('tthc.maTTHC', 'tthc.tenTTHC')
                ->orderByDesc('avg_score')
                ->orderByDesc('total_ratings')
                ->get();
        });
    }

    /**
     * Thống kê chi tiết đánh giá cho 1 thủ tục.
     * Danh sách nhận xét phân trang vẫn để DB lo, chỉ cache phần thống kê.
     */
    public function getProcedureRatingStats(string $maTTHC)
    {
        $key = "ratings:procedure:stats:{$maTTHC}";

        return Cache::store(self::REDIS_CACHE_STORE)->remember($key, 600, function () use ($maTTHC) {
            return DB::table('danhgia')
                ->join('hosoxuly', 'danhgia.maHSXL', '=', 'hosoxuly.maHSXL')
                ->where('hosoxuly.maTTHC', $maTTHC)
                ->select(
                    DB::raw('AVG(danhgia.soDiem) as avg_score'),
                    DB::raw('COUNT(danhgia.id) as total_ratings'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 5 THEN 1 END) as five_star'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 4 THEN 1 END) as four_star'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 3 THEN 1 END) as three_star'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 2 THEN 1 END) as two_star'),
                    DB::raw('COUNT(CASE WHEN danhgia.soDiem = 1 THEN 1 END) as one_star')
                )
                ->first();
        });
    }

    /**
     * Tóm tắt dashboard cho công dân (profile).
     * - Số hồ sơ hoàn thành
     * - Số hồ sơ đang xử lý
     * - Số thông báo chưa đọc
     *
     * Cache 5 phút theo IDCD.
     */
    public function getCitizenSummary(int $IDCD): array
    {
        $key = "user:{$IDCD}:summary";

        return Cache::store(self::REDIS_CACHE_STORE)->remember($key, 300, function () use ($IDCD) {
            $hoSoHoanThanh = HoSoXuLy::where('IDCD', $IDCD)
                ->whereNotNull('ngayKetThucXuLy')
                ->count();

            $hoSoDangXuLy = HoSoXuLy::where('IDCD', $IDCD)
                ->whereNull('ngayKetThucXuLy')
                ->count();

            $unread = ThongBao::where('IDCD', $IDCD)
                ->where('is_read', false)
                ->count();

            return [
                'hoSoHoanThanh' => $hoSoHoanThanh,
                'hoSoDangXuLy'  => $hoSoDangXuLy,
                'unreadCount'   => $unread,
            ];
        });
    }

    /**
     * Thống kê dashboard cho admin.
     * Cache 60 giây để giảm tải DB.
     */
    public function getAdminDashboardStats(): array
    {
        return Cache::store(self::REDIS_CACHE_STORE)->remember('admin:dashboard:stats', 60, function () {
            $stats = [
                'total_hoso'     => HoSoXuLy::count(),
                'hoso_moi'       => HoSoXuLy::whereDate('ngayTiepNhan', today())->count(),
                'total_congdan'  => CongDan::count(),
                'total_lichhen'  => LichHen::count(),
                'lichhen_hom_nay'=> LichHen::whereDate('thoiGianHen', today())->count(),
                'total_tthc'     => TTHC::count(),
            ];

            $hososByMonth = HoSoXuLy::selectRaw('DATE_FORMAT(ngayTiepNhan, "%Y-%m") as month, COUNT(*) as total')
                ->whereNotNull('ngayTiepNhan')
                ->where('ngayTiepNhan', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('total', 'month')
                ->toArray();

            $monthlyLabels = [];
            $monthlyValues = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $key = $date->format('Y-m');
                $monthlyLabels[] = $date->format('m/Y');
                $monthlyValues[] = (int) ($hososByMonth[$key] ?? 0);
            }

            $hososByStatus = DB::table('trangthaihoso')
                ->leftJoin('hosoxuly', 'hosoxuly.maTrangThai', '=', 'trangthaihoso.maTrangThai')
                ->select('trangthaihoso.tenTrangThai as name', DB::raw('COUNT(hosoxuly.maHSXL) as total'))
                ->groupBy('trangthaihoso.maTrangThai', 'trangthaihoso.tenTrangThai')
                ->orderBy('trangthaihoso.maTrangThai')
                ->get();

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

            return [
                'stats'             => $stats,
                'monthlyLabels'     => $monthlyLabels,
                'monthlyValues'     => $monthlyValues,
                'hososByStatus'     => $hososByStatus,
                'appointmentLabels' => $appointmentLabels,
                'appointmentValues' => $appointmentValues,
                'revenueLabels'     => $revenueLabels,
                'revenueValues'     => $revenueValues,
            ];
        });
    }
}


