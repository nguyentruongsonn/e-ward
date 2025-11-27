<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TTHC;

class PublicServiceController extends Controller
{
    /**
     * Display public service ratings
     */
    public function ratings()
    {
        // Get average ratings per procedure
        $ratings = DB::table('danhgia')
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

        return view('pages.service-ratings', compact('ratings'));
    }

    /**
     * Show detailed ratings for a specific procedure
     */
    public function showProcedureRatings($maTTHC)
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

        // Calculate stats
        $stats = DB::table('danhgia')
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

        return view('pages.service-rating-detail', compact('procedure', 'ratings', 'stats'));
    }
}
