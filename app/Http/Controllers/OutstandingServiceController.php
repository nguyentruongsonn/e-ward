<?php

namespace App\Http\Controllers;

use App\Services\WebCacheService;

class OutstandingServiceController extends Controller
{
    public function index(WebCacheService $cache)
    {
        // Lấy danh sách các thủ tục hành chính nổi bật (cache Redis 10 phút)
        $tthcs = $cache->getOutstandingProcedures();

        // Đồng thời khởi tạo key tthc:names (danh sách tên TTHC dạng JSON thuần) để bạn dễ kiểm tra
        $cache->getProcedureNames();

        return view('pages.outstanding-service', compact('tthcs'));
    }

    public function show($id, WebCacheService $cache)
    {
        $detail = $cache->getProcedureDetail($id);

        if (!$detail['tthc']) {
            abort(404);
        }

        return view('pages.outstanding-service-detail', [
            'tthc'            => $detail['tthc'],
            'cachThucHiens'  => $detail['cachThucHiens'] ?? collect(),
            'thanhPhanHoSos' => $detail['thanhPhanHoSos'] ?? collect(),
            'doiTuongs'      => $detail['doiTuongs'] ?? collect(),
        ]);
    }
}
