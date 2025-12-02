<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
}
