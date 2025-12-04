<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TTHC;
use App\Services\WebCacheService;

class PublicServiceController extends Controller
{
    /**
     * Display public service ratings
     */
    public function ratings(WebCacheService $cache)
    {
        // Thống kê đánh giá dịch vụ công (cache Redis 10 phút)
        $ratings = $cache->getRatingsSummary();

        return view('pages.service-ratings', compact('ratings'));
    }

    /**
     * Show detailed ratings for a specific procedure
     */
    public function showProcedureRatings($maTTHC, WebCacheService $cache)
    {
        $procedure = TTHC::where('maTTHC', $maTTHC)->firstOrFail();

        $ratings = DB::table('danhgia')
            ->join('hosoxuly', 'danhgia.maHSXL', '=', 'hosoxuly.maHSXL')
            ->where('hosoxuly.maTTHC', $maTTHC)
            ->whereNotNull('danhgia.nhanXet')
            ->select(
                'danhgia.soDiem',
                'danhgia.nhanXet',
                'danhgia.ngayDanhGia',
                'danhgia.IDCD' // Optional: to show user name if needed
            )
            ->orderByDesc('danhgia.ngayDanhGia')
            ->paginate(10);

        // Thống kê chi tiết cho thủ tục (cache Redis 10 phút)
        $stats = $cache->getProcedureRatingStats($maTTHC);

        return view('pages.service-rating-detail', compact('procedure', 'ratings', 'stats'));
    }
}
