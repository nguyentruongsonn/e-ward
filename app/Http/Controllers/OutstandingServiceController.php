<?php

namespace App\Http\Controllers;

use App\Models\TTHC;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OutstandingServiceController extends Controller
{
    public function index()
    {
        // Lấy danh sách các thủ tục hành chính nổi bật
        // eager load doiTuongs to avoid N+1 queries
        $tthcs = TTHC::with('doiTuongs')->orderBy('tenTTHC', 'asc')->get();

        return view('pages.outstanding-service', compact('tthcs'));
    }

public function show($id)
{
    // Lấy thông tin thủ tục + lĩnh vực bằng JOIN
    $tthc = DB::table('tthc as t')
        ->leftJoin('linhvuc as l', 'l.maLinhVuc', '=', 't.maLinhVuc')
        ->select(
            't.maTTHC', 't.tenTTHC', 't.trinhTuThucHien', 't.coQuanThucHien',
            't.yeuCauDieuKien', 't.canCuPhapLy', 't.ketQuaThucHien',
            'l.tenLinhVuc'
        )
        ->where('t.maTTHC', $id)
        ->first();

    if (!$tthc) {
        abort(404);
    }

    // Cách thực hiện (1-n)
    $cachThucHiens = DB::table('cachthuchien')
        ->where('maTTHC', $id)
        ->select('kenh', 'thoiHanGiaiQuyet', 'moTaPhiLePhi', 'moTa')
        ->get();

    // Thành phần hồ sơ với chi tiết giấy tờ (grouped by tenThanhPhan)
    $thanhPhanHoSos = DB::table('thanhphanhoso as tph')
        ->leftJoin('thanhphangiayto as tpg', 'tpg.maThanhPhan', '=', 'tph.maThanhPhan')
        ->leftJoin('giayto as gt', 'gt.maGiayTo', '=', 'tpg.maGiayTo')
        ->where('tph.maTTHC', $id)
        ->select(
            'tph.maThanhPhan',
            'tph.tenThanhPhan',
            'gt.tenGiayTo',
            'tpg.soLuongBanChinh',
            'tpg.soLuongBanSao'
        )
        ->get()
        ->groupBy('tenThanhPhan');

    // Đối tượng thực hiện (n-n qua thutucdoituong)
    $doiTuongs = DB::table('thutucdoituong as td')
        ->leftjoin('doituongthuchien as d', 'd.maDoiTuong', '=', 'td.maDoiTuong')
        ->where('td.maTTHC', $id)
        ->select('d.tenDoiTuong')
        ->get();

    return view('pages.outstanding-service-detail', [
        'tthc' => $tthc,
        'cachThucHiens' => $cachThucHiens,
        'thanhPhanHoSos' => $thanhPhanHoSos,
        'doiTuongs' => $doiTuongs,
    ]);
}
}
