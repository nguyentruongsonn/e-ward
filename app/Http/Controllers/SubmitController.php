<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SubmitController extends Controller
{
    public function showByTTHC(int $maTTHC)
    {
        $form = DB::table('formtructuyen')->where('maTTHC', $maTTHC)->first();
        $tthc = DB::table('tthc')->where('maTTHC', $maTTHC)->first();
        if (!$form || !$tthc) {
            abort(404);
        }
        $config = json_decode($form->cauHinhForm, true) ?: [];

        // Thành phần hồ sơ (group theo tên thành phần)
        $thanhPhanHoSos = DB::table('thanhphanhoso as tph')
            ->leftJoin('thanhphangiayto as tpg', 'tpg.maThanhPhan', '=', 'tph.maThanhPhan')
            ->leftJoin('giayto as gt', 'gt.maGiayTo', '=', 'tpg.maGiayTo')
            ->where('tph.maTTHC', $maTTHC)
            ->select(
                'tph.maThanhPhan',
                'tph.tenThanhPhan',
                'gt.maGiayTo',
                'gt.tenGiayTo',
                'tpg.soLuongBanChinh',
                'tpg.soLuongBanSao'
            )
            ->get()
            ->groupBy('tenThanhPhan');

        // Lấy danh sách lệ phí từ bảng lephi
        $lePhis = DB::table('lephi')
            ->where('maTTHC', $maTTHC)
            ->orderBy('maLePhi')
            ->get();

        return view('pages.submit', [
            'maTTHC' => $maTTHC,
            'tthc' => $tthc,
            'config' => $config,
            'thanhPhanHoSos' => $thanhPhanHoSos,
            'lePhis' => $lePhis,
        ]);
    }

    public function submitApi(Request $request, int $maTTHC)
    {
        $form = DB::table('formtructuyen')->where('maTTHC', $maTTHC)->first();
        $maForm = $form->maForm ?? null;

        $payload = $request->except(['_token']);

        if ($request->hasFile('tep_dinh_kem')) {
            $storedFiles = [];
            foreach ((array) $request->file('tep_dinh_kem') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('hoso_uploads', 'public');
                    $storedFiles[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'path' => $path,
                        'url' => Storage::disk('public')->url($path),
                    ];
                }
            }
            $payload['tep_dinh_kem'] = $storedFiles;
        }
        if ($request->hasFile('fileHoSo')) { // alternate name in view
            $storedFiles = [];
            foreach ((array) $request->file('fileHoSo') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('hoso_uploads', 'public');
                    $storedFiles[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'path' => $path,
                        'url' => Storage::disk('public')->url($path),
                    ];
                }
            }
            $payload['fileHoSo'] = $storedFiles;
        }

        $tenChuHoSo = $payload['ho_ten']
            ?? $payload['hoTen']
            ?? $payload['tenChuHoSo']
            ?? $payload['nguoi_dai_dien_ho_ten']
            ?? 'Người nộp không rõ tên';
        $email = $payload['email']
            ?? $payload['nguoi_dai_dien_email']
            ?? 'no-reply@example.com';
        $soDienThoai = $payload['so_dien_thoai']
            ?? $payload['soDienThoai']
            ?? $payload['nguoi_dai_dien_sdt']
            ?? '0000000000';

        $validator = Validator::make([
            'tenChuHoSo' => $tenChuHoSo,
            'email' => $email,
            'soDienThoai' => $soDienThoai,
        ], [
            'tenChuHoSo' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255'],
            'soDienThoai' => ['required', 'string', 'max:10'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $IDCD = DB::table('congdan')->value('IDCD') ?? 1;
        $maTrangThai = DB::table('trangthaihoso')->value('maTrangThai');
        if (!$maTrangThai) {
            $maTrangThai = DB::table('trangthaihoso')->insertGetId(['tenTrangThai' => 'Mới nộp']);
        }
        $donViXuLy = DB::table('tthc')->where('maTTHC', $maTTHC)->value('coQuanThucHien') ?? 'Bộ phận Một cửa';

        DB::table('hosoxuly')->insert([
            'maTTHC' => $maTTHC,
            'IDCD' => $IDCD,
            'maForm' => $maForm,
            'tenChuHoSo' => $tenChuHoSo,
            'doiTuongThucHien' => $payload['truong_hop'] ?? null,
            'email' => $email,
            'soDienThoai' => substr(preg_replace('/\D/', '', $soDienThoai), 0, 10),
            'dulieu' => json_encode($payload),
            'ngayTiepNhan' => null,
            'ngayHenTra' => null,
            'maTrangThai' => $maTrangThai,
            'ngayTra' => null,
            'hanBoSung' => null,
            'thongTinTra' => null,
            'lePhi' => (float) ($payload['le_phi_so_tien'] ?? 0),
            'hinhThuc' => 'Nhận trực tuyến',
            'ngayKetThucXuLy' => null,
            'donViXuLy' => $donViXuLy,
            'ghiChu' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nộp hồ sơ thành công',
        ]);
    }
}


