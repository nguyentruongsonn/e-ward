<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HoSoXuLy;
use App\Models\TTHC;

class PublicController extends Controller
{
    /**
     * Display the tracking form
     */
    public function trackingForm()
    {
        return view('pages.tracking');
    }

    /**
     * Search for application by code
     */
    public function trackingSearch(Request $request)
    {
        $request->validate([
            'maHSXL' => 'required|string'
        ], [
            'maHSXL.required' => 'Vui lòng nhập mã hồ sơ'
        ]);

        $maHSXL = trim($request->maHSXL);

        // Find application
        $hoSo = HoSoXuLy::with(['trangThai', 'tthc'])
            ->where('maHSXL', $maHSXL)
            ->first();

        if (!$hoSo) {
            return back()->with('error', 'Không tìm thấy hồ sơ với mã: ' . $maHSXL);
        }

        // Extract owner name from dulieu
        $tenChuHoSo = 'N/A';
        if ($hoSo->dulieu) {
            $dulieu = is_array($hoSo->dulieu) ? $hoSo->dulieu : json_decode($hoSo->dulieu, true);
            
            // Try different possible keys
            if (isset($dulieu['hoTen'])) {
                $tenChuHoSo = $dulieu['hoTen'];
            } elseif (isset($dulieu['ho_ten'])) {
                $tenChuHoSo = $dulieu['ho_ten'];
            } elseif (isset($dulieu['payload']['hoTen'])) {
                $tenChuHoSo = $dulieu['payload']['hoTen'];
            }
        }

        return view('pages.tracking', compact('hoSo', 'tenChuHoSo'));
    }

    /**
     * Display public list of TTHC services
     */
    public function services(Request $request)
    {
        $query = DB::table('tthc')
            ->leftJoin('linhvuc', 'tthc.maLinhVuc', '=', 'linhvuc.maLinhVuc')
            ->select('tthc.*', 'linhvuc.tenLinhVuc')
            ->where('tthc.trangThai', 'Công khai'); // Chỉ hiển thị dịch vụ công khai

        // Filter theo tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tthc.tenTTHC', 'LIKE', "%{$search}%")
                  ->orWhere('linhvuc.tenLinhVuc', 'LIKE', "%{$search}%");
            });
        }

        // Filter theo lĩnh vực
        if ($request->filled('maLinhVuc')) {
            $query->where('tthc.maLinhVuc', $request->maLinhVuc);
        }

        $tthcs = $query->orderBy('tthc.tenTTHC', 'asc')->paginate(20)->withQueryString();
        $linhVucs = DB::table('linhvuc')->orderBy('tenLinhVuc')->get();

        return view('pages.services', compact('tthcs', 'linhVucs'));
    }
    /**
     * API to get all provinces
     */
    public function getProvinces()
    {
        $tinhs = DB::table('tinh')->orderBy('tenTinh')->get();
        return response()->json($tinhs);
    }

    /**
     * API to get wards by province ID
     */
    public function getWards($maTinh)
    {
        $xas = DB::table('xa')->where('maTinh', $maTinh)->orderBy('tenXa')->get();
        return response()->json($xas);
    }
}
