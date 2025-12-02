<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LichHenController extends Controller
{
    public function show($id)
    {
        // Kiểm tra đăng nhập - redirect với flag để mở modal
        if (!Auth::check()) {
            return redirect()->back()->with('open_login_modal', true)->with('error', 'Vui lòng đăng nhập để đặt lịch.');
        }

        $maTTHC = (int) $id;
        $tthc = DB::table('tthc')->where('maTTHC', $maTTHC)->first();

        if (!$tthc) {
            abort(404, 'Không tìm thấy thủ tục hành chính');
        }

        // Lấy thông tin người dùng
        $authUser = Auth::user();
        $nguoi = null;
        $congDan = null;

        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $nguoi = $authUser->nguoi ?? null;
        }

        if (!$nguoi) {
            return redirect()->route('home')->with('error', 'Không tìm thấy thông tin người dùng.');
        }

        $congDan = DB::table('congdan')
            ->where('IDnguoiDung', $nguoi->IDnguoiDung)
            ->first();

        // Nếu chưa có bản ghi công dân, tự động tạo
        if (!$congDan) {
            $IDCD = DB::table('congdan')->insertGetId([
                'IDnguoiDung' => $nguoi->IDnguoiDung,
            ]);
            $congDan = DB::table('congdan')->where('IDCD', $IDCD)->first();
        }

        // Lấy danh sách quầy làm việc
        $quayLamViecs = DB::table('quaylamviec')
            ->orderBy('maQuayLamViec')
            ->get();

        return view('pages.appointment', [
            'tthc' => $tthc,
            'nguoi' => $nguoi,
            'congDan' => $congDan,
            'quayLamViecs' => $quayLamViecs,
        ]);
    }

    public function store(Request $request, $id)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để đặt lịch.',
            ], 401);
        }

        $maTTHC = (int) $id;

        $validator = Validator::make($request->all(), [
            'ngay_hen' => 'required|date|after_or_equal:today',
            'gio_hen' => 'required|date_format:H:i',
        ], [
            'ngay_hen.required' => 'Vui lòng chọn ngày hẹn.',
            'ngay_hen.after_or_equal' => 'Ngày hẹn phải từ hôm nay trở đi.',
            'gio_hen.required' => 'Vui lòng chọn giờ hẹn.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Lấy thông tin người dùng
        $authUser = Auth::user();
        $nguoi = null;

        if ($authUser instanceof \App\Models\Nguoi) {
            $nguoi = $authUser;
        } else {
            $nguoi = $authUser->nguoi ?? null;
        }

        if (!$nguoi) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng.',
            ], 404);
        }

        $congDan = DB::table('congdan')
            ->where('IDnguoiDung', $nguoi->IDnguoiDung)
            ->first();

        // Nếu chưa có bản ghi công dân, tự động tạo
        if (!$congDan) {
            $IDCD = DB::table('congdan')->insertGetId([
                'IDnguoiDung' => $nguoi->IDnguoiDung,
            ]);
            $congDan = DB::table('congdan')->where('IDCD', $IDCD)->first();
        }

        $IDCD = $congDan->IDCD;
        $ngayHen = $request->ngay_hen;
        $gioHen = $request->gio_hen;

        // Kiểm tra không cho đặt trước giờ hiện tại (sử dụng timezone Việt Nam)
        // Parse với timezone Việt Nam ngay từ đầu để tránh lệch thời gian
        $thoiGianHen = Carbon::createFromFormat('Y-m-d H:i', $ngayHen . ' ' . $gioHen, 'Asia/Ho_Chi_Minh');
        $now = Carbon::now('Asia/Ho_Chi_Minh');

        if ($thoiGianHen->lte($now)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể đặt lịch trước giờ hiện tại. Vui lòng chọn thời gian sau ' . $now->format('d/m/Y H:i'),
            ], 422);
        }

        // Không cần chọn quầy khi đặt lịch, sẽ chọn khi check-in
        $maQuayLamViec = null;

        // Kiểm tra số lượng lịch hẹn trong giờ này (tối đa 6 lịch = 3 quầy x 2 lịch/quầy)
        $startTime = $thoiGianHen->copy()->startOfHour();
        $endTime = $thoiGianHen->copy()->endOfHour();

        $soLuongLichHen = DB::table('lichhen')
            ->where('maTTHC', $maTTHC)
            ->whereBetween('thoiGianHen', [$startTime, $endTime])
            ->whereIn('trangThai', ['Đã đặt lịch', 'Chờ đến', 'Đang xử lý'])
            ->count();

        if ($soLuongLichHen >= 6) {
            return response()->json([
                'success' => false,
                'message' => 'Giờ này đã đầy (tối đa 6 lịch hẹn/giờ). Vui lòng chọn giờ khác.',
            ], 422);
        }

        // Tạo mã lịch hẹn: matthc_ngày hiện tại_số ngẫu nhiên
        do {
            $rand = random_int(1000, 9999);
            $maLichHen = $maTTHC . '_' . now('Asia/Ho_Chi_Minh')->format('Ymd') . '_' . $rand;
        } while (DB::table('lichhen')->where('maLichHen', $maLichHen)->exists());

        // Tạo checkin token
        $checkinToken = \Illuminate\Support\Str::uuid();

        // Lưu lịch hẹn
        DB::table('lichhen')->insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'maLichHen' => $maLichHen,
            'IDCD' => $IDCD,
            'maTTHC' => $maTTHC,
            'maQuayLamViec' => $maQuayLamViec,
            'thoiGianHen' => $thoiGianHen,
            'trangThai' => 'Đã đặt lịch',
            'checkin_token' => $checkinToken,
            'created_at' => now('Asia/Ho_Chi_Minh'),
            'updated_at' => now('Asia/Ho_Chi_Minh'),
        ]);

        // Tạo QR code URL cho check-in
        $qrCodeUrl = route('appointment.checkin', ['token' => $checkinToken]);

        // Tạo QR code image (sử dụng API hoặc library)
        $qrCodeImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($checkinToken);

        return response()->json([
            'success' => true,
            'message' => 'Đặt lịch thành công!',
            'maLichHen' => $maLichHen,
            'thoiGianHen' => $thoiGianHen->format('d/m/Y H:i'),
            'checkin_token' => $checkinToken,
            'qr_code_url' => $qrCodeUrl,
            'qr_code_image' => $qrCodeImageUrl,
        ]);
    }

    public function getAvailableSlots(Request $request, $id)
    {
        $maTTHC = (int) $id;
        $ngayHen = $request->input('ngay_hen');

        if (!$ngayHen) {
            return response()->json([
                'success' => false,
                'message' => 'Thiếu thông tin ngày hẹn.',
            ], 422);
        }

        // Lấy danh sách giờ đã đầy (>= 2 lịch hẹn ở tất cả quầy)
        $ngay = Carbon::parse($ngayHen)->setTimezone('Asia/Ho_Chi_Minh');
        $startOfDay = $ngay->copy()->startOfDay();
        $endOfDay = $ngay->copy()->endOfDay();

        // Lấy tất cả lịch hẹn trong ngày - nhóm theo giờ
        $lichHens = DB::table('lichhen')
            ->where('maTTHC', $maTTHC)
            ->whereBetween('thoiGianHen', [$startOfDay, $endOfDay])
            ->whereIn('trangThai', ['Đã đặt lịch', 'Chờ đến', 'Đang xử lý'])
            ->get()
            ->groupBy(function ($item) {
                $time = Carbon::parse($item->thoiGianHen)->setTimezone('Asia/Ho_Chi_Minh');
                return $time->format('H:i');
            });

        // Kiểm tra giờ nào đã đầy ở tất cả quầy (mỗi giờ tối đa 2 lịch/quầy, có 3 quầy = tối đa 6 lịch)
        $gioDaDay = [];
        $allQuays = DB::table('quaylamviec')->pluck('maQuayLamViec')->toArray();

        // Lấy tất cả giờ làm việc
        $gioLamViec = [];
        for ($h = 7; $h <= 11; $h++) {
            $gioLamViec[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':30';
        }
        for ($h = 13; $h <= 17; $h++) {
            $gioLamViec[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':30';
        }

        // Kiểm tra từng giờ làm việc
        foreach ($gioLamViec as $gio) {
            // Đếm số lịch hẹn trong giờ này theo từng quầy
            $countByQuay = [];
            $totalCount = 0;

            // Lấy tất cả lịch hẹn trong giờ này (không phân biệt quầy vì có thể chưa có quầy)
            // Parse giờ với format H:i và set vào ngày
            $timeParts = explode(':', $gio);
            $hour = (int)$timeParts[0];
            $minute = (int)$timeParts[1];
            $startTime = $ngay->copy()->setTime($hour, $minute, 0)->startOfHour();
            $endTime = $ngay->copy()->setTime($hour, $minute, 0)->endOfHour();

            $lichHensTrongGio = DB::table('lichhen')
                ->where('maTTHC', $maTTHC)
                ->whereBetween('thoiGianHen', [$startTime, $endTime])
                ->whereIn('trangThai', ['Đã đặt lịch', 'Chờ đến', 'Đang xử lý'])
                ->get();

            $totalCount = $lichHensTrongGio->count();

            // Đếm theo quầy (chỉ những lịch đã có quầy)
            foreach ($lichHensTrongGio as $item) {
                $quay = $item->maQuayLamViec;
                if ($quay) {
                    $countByQuay[$quay] = ($countByQuay[$quay] ?? 0) + 1;
                }
            }

            // Kiểm tra: nếu tổng số lịch >= 6 (3 quầy x 2 lịch/quầy) thì giờ đó đầy
            // Hoặc nếu tất cả quầy đều đã có >= 2 lịch
            $allFull = true;
            foreach ($allQuays as $quay) {
                if (($countByQuay[$quay] ?? 0) < 2) {
                    $allFull = false;
                    break;
                }
            }

            // Nếu tổng số lịch >= 6 hoặc tất cả quầy đều đầy
            if ($totalCount >= 6 || $allFull) {
                $gioDaDay[] = $gio;
            }
        }

        // Giờ làm việc đã được định nghĩa ở trên

        // Kiểm tra giờ hiện tại nếu là hôm nay (sử dụng timezone Việt Nam)
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $gioKhongChoDat = [];
        if ($ngay->isToday()) {
            $thoiGianHienTai = $now->format('H:i');
            foreach ($gioLamViec as $gio) {
                if ($gio <= $thoiGianHienTai) {
                    $gioKhongChoDat[] = $gio;
                }
            }
        }

        return response()->json([
            'success' => true,
            'gioDaDay' => $gioDaDay,
            'gioKhongChoDat' => $gioKhongChoDat,
            'gioLamViec' => $gioLamViec,
        ]);
    }

    public function checkin($token)
    {
        // Trim và làm sạch token
        $token = trim($token);
        
        $lichHen = DB::table('lichhen')
            ->where('checkin_token', $token)
            ->first();

        if (!$lichHen) {
            abort(404, 'Không tìm thấy lịch hẹn với token này');
        }

        // Kiểm tra xem đã check-in chưa
        $daCheckIn = !empty($lichHen->checkin_time);
        $soThuTu = $lichHen->soThuTu;

        // Lấy thông tin thủ tục
        $tthc = DB::table('tthc')->where('maTTHC', $lichHen->maTTHC)->first();
        $quay = null;
        if ($lichHen->maQuayLamViec) {
            $quay = DB::table('quaylamviec')->where('maQuayLamViec', $lichHen->maQuayLamViec)->first();
        }

        return view('pages.appointment-checkin', [
            'lichHen' => $lichHen,
            'tthc' => $tthc,
            'quay' => $quay,
            'daCheckIn' => $daCheckIn,
            'soThuTu' => $soThuTu,
        ]);
    }

    public function processCheckin(Request $request, $token)
    {
        // Trim và làm sạch token
        $token = trim($token);
        
        $lichHen = DB::table('lichhen')
            ->where('checkin_token', $token)
            ->first();

        if (!$lichHen) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lịch hẹn với token này.',
            ], 404);
        }

        // Kiểm tra xem đã check-in chưa
        if (!empty($lichHen->checkin_time)) {
            return response()->json([
                'success' => false,
                'message' => 'Lịch hẹn này đã được check-in rồi.',
                'soThuTu' => $lichHen->soThuTu,
                'maQuayLamViec' => $lichHen->maQuayLamViec,
            ], 422);
        }

        // Tự động chọn quầy còn trống trong giờ đó
        $thoiGianHen = Carbon::parse($lichHen->thoiGianHen)->setTimezone('Asia/Ho_Chi_Minh');
        $startTime = $thoiGianHen->copy()->startOfHour();
        $endTime = $thoiGianHen->copy()->endOfHour();

        $allQuays = DB::table('quaylamviec')->pluck('maQuayLamViec')->toArray();
        $quayTrong = null;

        // Tìm quầy còn trống (chưa đầy 2 lịch trong giờ đó)
        foreach ($allQuays as $quay) {
            $soLuongLichHen = DB::table('lichhen')
                ->where('maTTHC', $lichHen->maTTHC)
                ->where('maQuayLamViec', $quay)
                ->whereBetween('thoiGianHen', [$startTime, $endTime])
                ->whereIn('trangThai', ['Đã đặt lịch', 'Chờ đến', 'Đang xử lý'])
                ->count();

            if ($soLuongLichHen < 2) {
                $quayTrong = $quay;
                break;
            }
        }

        // Nếu tất cả quầy đều đầy, random chọn một quầy
        if (!$quayTrong) {
            $quayTrong = $allQuays[array_rand($allQuays)];
        }

        // Lấy số thứ tự tiếp theo trong ngày cho quầy đã chọn
        $ngayHen = $thoiGianHen->copy()->startOfDay();
        $endOfDay = $thoiGianHen->copy()->endOfDay();

        // Đếm số người đã check-in trong ngày ở quầy này
        $soLuongDaCheckIn = DB::table('lichhen')
            ->where('maQuayLamViec', $quayTrong)
            ->whereBetween('thoiGianHen', [$ngayHen, $endOfDay])
            ->whereNotNull('checkin_time')
            ->whereNotNull('soThuTu')
            ->count();

        $soThuTu = $soLuongDaCheckIn + 1;

        // Cập nhật check-in với quầy đã chọn
        DB::table('lichhen')
            ->where('checkin_token', $token)
            ->update([
                'maQuayLamViec' => $quayTrong,
                'checkin_time' => now('Asia/Ho_Chi_Minh'),
                'soThuTu' => $soThuTu,
                'trangThai' => 'Chờ đến',
                'updated_at' => now('Asia/Ho_Chi_Minh'),
            ]);

        // Lấy thông tin quầy đã chọn
        $quay = DB::table('quaylamviec')->where('maQuayLamViec', $quayTrong)->first();

        return response()->json([
            'success' => true,
            'message' => 'Check-in thành công!',
            'soThuTu' => $soThuTu,
            'maQuayLamViec' => $quayTrong,
            'tenQuayLamViec' => $quay->tenQuayLamViec ?? '',
            'thoiGianCheckIn' => now('Asia/Ho_Chi_Minh')->format('d/m/Y H:i'),
        ]);
    }
}
