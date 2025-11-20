<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CauHinhForm;
use App\Models\TTHC;
use App\Models\ThanhPhanHoSo;

class FormTrucTuyenController extends Controller
{
    public function showForm($maTTHC)
    {
        // Lấy thông tin thủ tục hành chính
        $tthc = TTHC::findOrFail($maTTHC);

        // Lấy cấu hình form trực tuyến (nếu có)
        $form = CauHinhForm::where('maTTHC', $maTTHC)->first();

        // Nếu không có form -> báo lỗi rõ ràng
        if (!$form) {
            abort(404, 'Không tìm thấy cấu hình form trực tuyến cho thủ tục này.');
        }

        // Giải mã JSON cấu hình form
        $cauHinhForm = json_decode($form->cauHinhForm, true);

        // Trả về view
        return view('pages.submit', [
            'tthc' => $tthc,
            'form' => $form,
            'maTTHC' => $maTTHC,
            'config' => json_decode($form->cauHinhForm, true)
        ]);

    }
}
