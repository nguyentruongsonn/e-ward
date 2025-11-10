<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        // 1. VALIDATION DỮ LIỆU ĐẦU VÀO
        // Đây là bước quan trọng nhất để đảm bảo dữ liệu hợp lệ trước khi xử lý
        $validator = Validator::make($request->all(), [
            // Các trường thông tin cơ bản
            'ho_ten' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'so_dien_thoai' => 'required|string|max:15',
            'hinh_thuc_nhan_ket_qua' => 'required|string',
            'xac_nhan_thong_tin' => 'required|accepted',

            // Validation cho file:
            // - 'taiLieu' phải là một mảng nếu tồn tại
            // - 'taiLieu.*' tương ứng với mỗi mã giấy tờ, cũng phải là mảng
            // - 'taiLieu.*.*' là từng file thực tế
            'taiLieu' => 'nullable|array',
            'taiLieu.*' => 'nullable|array',
            'taiLieu.*.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:10240', // Tối đa 10MB
        ]);

        if ($validator->fails()) {
            // Nếu validation thất bại, quay lại form và hiển thị lỗi
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Lấy tất cả dữ liệu đã được validate
        $payload = $validator->validated();

        // 2. BẮT ĐẦU TRANSACTION ĐỂ ĐẢM BẢO AN TOÀN DỮ LIỆU
        try {
            DB::beginTransaction();

            // 3. CHUẨN BỊ DỮ LIỆU ĐỂ LƯU VÀO DATABASE
            $form = DB::table('formtructuyen')->where('maTTHC', $maTTHC)->first();
            $donViXuLy = DB::table('tthc')->where('maTTHC', $maTTHC)->value('coQuanThucHien') ?? 'Bộ phận Một cửa';
            
            // Lấy IDCD từ người dùng đang đăng nhập
            $authUser = Auth::user();
            $nguoi = null;
            
            if ($authUser instanceof \App\Models\Nguoi) {
                $nguoi = $authUser;
            } else {
                $nguoi = $authUser->nguoi ?? null;
            }
            
            if (!$nguoi) {
                return redirect()->back()
                    ->with('error', 'Vui lòng đăng nhập để nộp hồ sơ.')
                    ->withInput();
            }
            
            $congDan = DB::table('congdan')
                ->where('IDnguoiDung', $nguoi->IDnguoiDung)
                ->first();
            
            // Nếu chưa có bản ghi công dân, tự động tạo
            if (!$congDan) {
                $IDCD = DB::table('congdan')->insertGetId([
                    'IDnguoiDung' => $nguoi->IDnguoiDung,
                ]);
            } else {
                $IDCD = $congDan->IDCD;
            }

            $maTrangThai = DB::table('trangthaihoso')->where('tenTrangThai', 'Mới nộp')->value('maTrangThai');
            if (!$maTrangThai) {
                // Tự động tạo trạng thái "Mới nộp" nếu chưa có
                $maTrangThai = DB::table('trangthaihoso')->insertGetId(['tenTrangThai' => 'Mới nộp']);
            }

            // Tạo mã HSXL duy nhất
            do {
                $rand = random_int(1000, 9999);
                $maHSXL = 'HSXL_' . $IDCD . '_' . now()->format('Ymd') . '_' . $rand;
            } while (DB::table('hosoxuly')->where('maHSXL', $maHSXL)->exists());

            // 4. LƯU THÔNG TIN HỒ SƠ CHÍNH
            DB::table('hosoxuly')->insert([
                'maHSXL' => $maHSXL,
                'maTTHC' => $maTTHC,
                'IDCD' => $IDCD,
                'maForm' => $form->maForm ?? null,
                'tenChuHoSo' => $payload['ho_ten'],
                'email' => $payload['email'],
                'soDienThoai' => $payload['so_dien_thoai'],
                'dulieu' => json_encode($request->except(['_token', 'taiLieu'])), // Lưu tất cả dữ liệu form trừ token và file
                'ngayTiepNhan' => now(),
                'maTrangThai' => $maTrangThai,
                'lePhi' => (float) ($request->input('tong_tien') ?? 0),
                'hinhThuc' => $payload['hinh_thuc_nhan_ket_qua'],
                'donViXuLy' => $donViXuLy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. XỬ LÝ UPLOAD VÀ LƯU THÔNG TIN FILE (PHIÊN BẢN ĐƠN GIẢN VÀ ĐÚNG)
            if ($request->hasFile('taiLieu')) {
                foreach ($request->file('taiLieu') as $maGiayTo => $files) {
                    foreach ($files as $file) {
                        if ($file && $file->isValid()) {
                            // Lưu file vào thư mục 'storage/app/public/hoso_uploads'
                            $path = $file->store('hoso_uploads', 'public');

                            // Lưu thông tin file vào database
                            DB::table('tailieunop')->insert([
                                'maHSXL' => $maHSXL,
                                'maGiayTo' => (int) $maGiayTo,
                                'tenTep' => $file->getClientOriginalName(),
                                'duongDan' => $path,
                                'dinhDang' => $file->getClientMimeType(),
                                'kichThuoc' => $file->getSize(),
                                'ngayTai' => now(),
                            ]);
                        }
                    }
                }
            }

            // 6. HOÀN TẤT TRANSACTION
            DB::commit();

        } catch (Throwable $e) {
            // Nếu có bất kỳ lỗi nào xảy ra, hủy bỏ mọi thay đổi trong database
            DB::rollBack();

            // Ghi lại lỗi chi tiết để debug
            Log::error('Lỗi nghiêm trọng khi nộp hồ sơ: ' . $e->getMessage(), [
                'maTTHC' => $maTTHC,
                'user_input' => $request->except('taiLieu'), // Không log file
                'exception_trace' => $e->getTraceAsString()
            ]);

            // Trả người dùng về form với thông báo lỗi
            return redirect()->back()
                ->with('error', 'Đã có lỗi xảy ra trong quá trình xử lý. Vui lòng thử lại.')
                ->withInput();
        }

        // 7. CHUYỂN HƯỚNG ĐẾN TRANG THÀNH CÔNG
        // Lấy lại dữ liệu đã lưu để hiển thị ở trang kết quả
        $hoSo = DB::table('hosoxuly')->where('maHSXL', $maHSXL)->first();
        $tailieuNop = DB::table('tailieunop')->where('maHSXL', $maHSXL)->get()->groupBy('maGiayTo');

        // Chuyển hướng và gửi dữ liệu qua session
        return redirect()->route('nop-ho-so.show', ['maTTHC' => $maTTHC])
            ->with('success', true)
            ->with('maHSXL', $maHSXL)
            ->with('hoSo', $hoSo)
            ->with('dulieu', json_decode($hoSo->dulieu, true))
            ->with('tailieuNop', $tailieuNop)
            ->with('lePhiChiTiet', []); // Bạn có thể tính toán lại lePhiChiTiet nếu cần
    }
}


