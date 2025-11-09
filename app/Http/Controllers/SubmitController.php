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

        // Kiểm tra nếu có session data từ submit thành công
        $isSuccess = session('success', false);
        $maHSXL = session('maHSXL');
        $hoSo = session('hoSo');
        $dulieu = session('dulieu', []);
        $tailieuNop = session('tailieuNop', collect());
        $lePhiChiTiet = session('lePhiChiTiet', []);

        // Nếu có maHSXL từ session, lấy lại dữ liệu từ database
        if ($isSuccess && $maHSXL) {
            if (!$hoSo) {
                $hoSo = DB::table('hosoxuly')->where('maHSXL', $maHSXL)->first();
            }
            if ($hoSo && empty($dulieu)) {
                $dulieu = json_decode($hoSo->dulieu ?? '{}', true);
            }
            if ($tailieuNop->isEmpty()) {
                $tailieuNop = DB::table('tailieunop')
                    ->where('maHSXL', $maHSXL)
                    ->get()
                    ->groupBy('maGiayTo');
            }
        }

        return view('pages.submit', [
            'maTTHC' => $maTTHC,
            'tthc' => $tthc,
            'config' => $config,
            'thanhPhanHoSos' => $thanhPhanHoSos,
            'lePhis' => $lePhis,
            'isSuccess' => $isSuccess,
            'maHSXL' => $maHSXL,
            'hoSo' => $hoSo,
            'dulieu' => $dulieu,
            'tailieuNop' => $tailieuNop,
            'lePhiChiTiet' => $lePhiChiTiet,
        ]);
    }

    public function submitApi(Request $request, int $maTTHC)
    {
        $form = DB::table('formtructuyen')->where('maTTHC', $maTTHC)->first();
        $maForm = $form->maForm ?? null;

        $payload = $request->except(['_token']);

        // Xử lý file tài liệu nộp kèm
        $taiLieuFiles = [];
        if ($request->hasFile('taiLieu')) {
            foreach ($request->file('taiLieu') as $maGiayTo => $files) {
                if (is_array($files)) {
                    foreach ($files as $fileArray) {
                        if (is_array($fileArray)) {
                            foreach ($fileArray as $file) {
                                if ($file && $file->isValid()) {
                                    $taiLieuFiles[] = [
                                        'maGiayTo' => (int) $maGiayTo,
                                        'file' => $file,
                                    ];
                                }
                            }
                        } elseif ($fileArray && $fileArray->isValid()) {
                            $taiLieuFiles[] = [
                                'maGiayTo' => (int) $maGiayTo,
                                'file' => $fileArray,
                            ];
                        }
                    }
                }
            }
        }

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
                        'url' => asset('storage/' . $path),
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
                        'url' => asset('storage/' . $path),
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

        // Tạo mã hồ sơ xử lý (maHSXL) duy nhất
        do {
            $rand = random_int(1000, 9999);
            $maHSXL = 'HSXL_' . $IDCD . '_' . now()->format('Ymd') . '_' . $rand;
        } while (DB::table('hosoxuly')->where('maHSXL', $maHSXL)->exists());

        // Tính tổng lệ phí từ dữ liệu form
        $tongLePhi = 0;
        if (isset($payload['tong_tien'])) {
            $tongLePhi = (float) $payload['tong_tien'];
        } elseif (isset($payload['le_phi_so_luong']) && isset($payload['muc_le_phi'])) {
            // Tính từ số lượng và mức lệ phí
            foreach ($payload['le_phi_so_luong'] as $maLePhi => $soLuong) {
                $mucLePhi = (float) ($payload['muc_le_phi'][$maLePhi] ?? 0);
                $tongLePhi += $soLuong * $mucLePhi;
            }
        }

        DB::table('hosoxuly')->insert([
            'maHSXL' => $maHSXL,
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
            'lePhi' => $tongLePhi,
            'hinhThuc' => $payload['hinh_thuc_nhan_ket_qua'] ?? 'Nhận trực tuyến',
            'ngayKetThucXuLy' => null,
            'donViXuLy' => $donViXuLy,
            'ghiChu' => null,
        ]);

        // Lưu các file tài liệu vào bảng tailieunop
        foreach ($taiLieuFiles as $taiLieu) {
            $file = $taiLieu['file'];
            $maGiayTo = $taiLieu['maGiayTo'];

            $path = $file->store('hoso_uploads', 'public');
            $tenTep = $file->getClientOriginalName();
            $duongDan = $path;
            $dinhDang = $file->getClientMimeType();
            $kichThuoc = $file->getSize();

            DB::table('tailieunop')->insert([
                'maHSXL' => $maHSXL,
                'maGiayTo' => $maGiayTo,
                'tenTep' => $tenTep,
                'duongDan' => $duongDan,
                'dinhDang' => $dinhDang,
                'kichThuoc' => $kichThuoc,
                'ngayTai' => now(),
            ]);
        }

        // Lấy lại dữ liệu hồ sơ vừa tạo để hiển thị
        $hoSo = DB::table('hosoxuly')->where('maHSXL', $maHSXL)->first();
        $dulieu = json_decode($hoSo->dulieu ?? '{}', true);

        // Lấy danh sách tài liệu đã nộp
        $tailieuNop = DB::table('tailieunop')
            ->where('maHSXL', $maHSXL)
            ->get()
            ->groupBy('maGiayTo');

        // Lấy lại thành phần hồ sơ để hiển thị
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

        // Tính toán chi tiết lệ phí
        $lePhiChiTiet = [];
        if (isset($payload['le_phi_so_luong']) && isset($payload['muc_le_phi'])) {
            foreach ($payload['le_phi_so_luong'] as $maLePhi => $soLuong) {
                $lePhi = DB::table('lephi')->where('maLePhi', $maLePhi)->first();
                if ($lePhi) {
                    $mucLePhi = (float) ($payload['muc_le_phi'][$maLePhi] ?? $lePhi->soTien);
                    $lePhiChiTiet[] = [
                        'loaiLePhi' => $lePhi->loaiLePhi,
                        'soLuong' => $soLuong,
                        'mucLePhi' => $mucLePhi,
                        'thanhTien' => $soLuong * $mucLePhi,
                        'moTa' => $lePhi->moTa ?? '',
                    ];
                }
            }
        }

        return redirect()->route('nop-ho-so.show', ['maTTHC' => $maTTHC])
            ->with('success', true)
            ->with('maHSXL', $maHSXL)
            ->with('hoSo', $hoSo)
            ->with('dulieu', $dulieu)
            ->with('tailieuNop', $tailieuNop)
            ->with('thanhPhanHoSos', $thanhPhanHoSos)
            ->with('lePhiChiTiet', $lePhiChiTiet);
    }
}


