<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $nguoiInfo = null;
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

            // Lấy thông tin người dùng từ bảng nguoi
            if ($hoSo && $hoSo->IDCD) {
                $congDan = DB::table('congdan')->where('IDCD', $hoSo->IDCD)->first();
                if ($congDan) {
                    $nguoiInfo = DB::table('nguoi')->where('IDnguoiDung', $congDan->IDnguoiDung)->first();
                }
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
            'nguoiInfo' => $nguoiInfo,
        ]);
    }

    public function submitApi(Request $request, int $maTTHC)
    {
        $form = DB::table('formtructuyen')->where('maTTHC', $maTTHC)->first();
        $maForm = $form->maForm ?? null;

        $payload = $request->except(['_token']);

        // Xử lý file tài liệu nộp kèm - cấu trúc taiLieu[maGiayTo][]
        $taiLieuFiles = [];

        // Cách 1: Sử dụng allFiles() - cách này thường hoạt động tốt nhất
        $allFiles = $request->allFiles();
        if (!empty($allFiles) && isset($allFiles['taiLieu'])) {
            foreach ($allFiles['taiLieu'] as $maGiayTo => $files) {
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                            $taiLieuFiles[] = [
                                'maGiayTo' => (int) $maGiayTo,
                                'file' => $file,
                            ];
                        }
                    }
                } elseif ($files instanceof \Illuminate\Http\UploadedFile && $files->isValid()) {
                    $taiLieuFiles[] = [
                        'maGiayTo' => (int) $maGiayTo,
                        'file' => $files,
                    ];
                }
            }
        }

        // Cách 2: Fallback - sử dụng file() method
        if (empty($taiLieuFiles) && $request->hasFile('taiLieu')) {
            $taiLieuInput = $request->file('taiLieu');
            if (is_array($taiLieuInput)) {
                foreach ($taiLieuInput as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $file) {
                            if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                                $taiLieuFiles[] = [
                                    'maGiayTo' => (int) $key,
                                    'file' => $file,
                                ];
                            }
                        }
                    } elseif ($value instanceof \Illuminate\Http\UploadedFile && $value->isValid()) {
                        $taiLieuFiles[] = [
                            'maGiayTo' => (int) $key,
                            'file' => $value,
                        ];
                    }
                }
            }
        }

        Log::info('=== FILE UPLOAD DEBUG ===', [
            'has_taiLieu' => $request->hasFile('taiLieu'),
            'total_files_collected' => count($taiLieuFiles),
            'files_details' => array_map(function($item) {
                return [
                    'maGiayTo' => $item['maGiayTo'],
                    'filename' => $item['file']->getClientOriginalName(),
                    'size' => $item['file']->getSize()
                ];
            }, $taiLieuFiles),
        ]);

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

            // 3. CHUẨN BỊ DỮ LIỆU ĐỂ LƯU VÀO DATABASE
            $form = DB::table('formtructuyen')->where('maTTHC', $maTTHC)->first();
            $donViXuLy = DB::table('tthc')->where('maTTHC', $maTTHC)->value('coQuanThucHien') ?? 'Bộ phận Một cửa';
            $IDCD = DB::table('congdan')->value('IDCD') ?? 1; // Nên có logic lấy IDCD của người dùng đang đăng nhập

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

        // Map giá trị hinhThuc từ form sang giá trị enum hợp lệ
        $hinhThucNhanKetQua = $payload['hinh_thuc_nhan_ket_qua'] ?? 'Nhận trực tuyến';
        $hinhThuc = 'Nhận trực tuyến'; // Mặc định

        // Map các giá trị từ form sang enum
        if (stripos($hinhThucNhanKetQua, 'trực tiếp') !== false ||
            stripos($hinhThucNhanKetQua, 'truc tiep') !== false) {
            $hinhThuc = 'Nhận trực tiếp';
        } elseif (stripos($hinhThucNhanKetQua, 'trực tuyến') !== false ||
                   stripos($hinhThucNhanKetQua, 'truc tuyen') !== false ||
                   stripos($hinhThucNhanKetQua, 'bưu chính') !== false ||
                   stripos($hinhThucNhanKetQua, 'buu chinh') !== false ||
                   stripos($hinhThucNhanKetQua, 'dịch vụ') !== false ||
                   stripos($hinhThucNhanKetQua, 'dich vu') !== false) {
            $hinhThuc = 'Nhận trực tuyến';
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
            'hinhThuc' => $hinhThuc,
            'ngayKetThucXuLy' => null,
            'donViXuLy' => $donViXuLy,
            'ghiChu' => null,
        ]);

        // Lưu các file tài liệu vào bảng tailieunop
        $filesSaved = 0;
        $filesErrors = 0;

        Log::info('=== STARTING FILE SAVE PROCESS ===', [
            'maHSXL' => $maHSXL,
            'total_files_to_save' => count($taiLieuFiles),
        ]);

        foreach ($taiLieuFiles as $taiLieu) {
            try {
                $file = $taiLieu['file'];
                $maGiayTo = $taiLieu['maGiayTo'];

                if (!$file || !$file->isValid()) {
                    Log::warning('File không hợp lệ', [
                        'maGiayTo' => $maGiayTo,
                        'isValid' => $file ? $file->isValid() : false,
                    ]);
                    $filesErrors++;
                    continue;
                }

                // Lưu file vào thư mục 'storage/app/public/hoso_uploads'
                $path = $file->store('hoso_uploads', 'public');
                $tenTep = $file->getClientOriginalName();
                $dinhDang = $file->getClientMimeType();
                $kichThuoc = (string) $file->getSize();

                // Lưu thông tin file vào database
                DB::table('tailieunop')->insert([
                    'maHSXL' => $maHSXL,
                    'maGiayTo' => $maGiayTo,
                    'tenTep' => $tenTep,
                    'duongDan' => $path,
                    'dinhDang' => $dinhDang,
                    'kichThuoc' => $kichThuoc,
                    'ngayTai' => now(),
                ]);

                $filesSaved++;
                Log::info('File saved successfully', [
                    'maHSXL' => $maHSXL,
                    'maGiayTo' => $maGiayTo,
                    'filename' => $tenTep,
                    'path' => $path,
                    'size' => $kichThuoc,
                ]);
            } catch (\Exception $e) {
                $filesErrors++;
                Log::error('Lỗi khi lưu file cho maGiayTo ' . $maGiayTo . ': ' . $e->getMessage(), [
                    'maHSXL' => $maHSXL,
                    'maGiayTo' => $maGiayTo,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Không throw exception ở đây, tiếp tục xử lý các file khác
            }
        }

        Log::info('=== FILE SAVE SUMMARY ===', [
            'maHSXL' => $maHSXL,
            'total_files' => count($taiLieuFiles),
            'files_saved' => $filesSaved,
            'files_errors' => $filesErrors,
        ]);

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

        // Lưu lịch sử thanh toán nếu đã thanh toán thành công
        $maGiaoDich = $payload['ma_giao_dich'] ?? null;
        $tongTien = (float) ($payload['tong_tien'] ?? 0);
        $hinhThucThanhToan = $payload['hinh_thuc_thanh_toan'] ?? 'Thanh toán QR';

        if ($maGiaoDich && $tongTien > 0) {
            try {
                // Kiểm tra xem đã lưu chưa (tránh duplicate)
                $existing = DB::table('lichsuthanhtoan')
                    ->where('maGD', $maGiaoDich)
                    ->where('maHSXL', $maHSXL)
                    ->first();

                if (!$existing) {
                    // Lấy IDCD từ hồ sơ
                    $hoSoForIDCD = DB::table('hosoxuly')->where('maHSXL', $maHSXL)->first();
                    $IDCDForPayment = $hoSoForIDCD->IDCD ?? $IDCD;

                    DB::table('lichsuthanhtoan')->insert([
                        'maGD' => $maGiaoDich,
                        'soGD' => $maGiaoDich,
                        'loaiGD' => $hinhThucThanhToan,
                        'ngayGD' => now(),
                        'soTien' => $tongTien,
                        'trangThai' => 'Thành công',
                        'IDCD' => $IDCDForPayment,
                        'maHSXL' => $maHSXL,
                        'moTa' => 'Thanh toán lệ phí nộp hồ sơ trực tuyến - Mã hồ sơ: ' . $maHSXL,
                    ]);

                    Log::info('Lịch sử thanh toán đã được lưu thành công', [
                        'maGD' => $maGiaoDich,
                        'maHSXL' => $maHSXL,
                        'soTien' => $tongTien,
                        'IDCD' => $IDCDForPayment,
                    ]);
                } else {
                    Log::info('Lịch sử thanh toán đã tồn tại, bỏ qua', [
                        'maGD' => $maGiaoDich,
                        'maHSXL' => $maHSXL,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Lỗi khi lưu lịch sử thanh toán: ' . $e->getMessage(), [
                    'maGiaoDich' => $maGiaoDich,
                    'maHSXL' => $maHSXL,
                    'tongTien' => $tongTien,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Không throw exception ở đây, vì hồ sơ đã được lưu thành công
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

