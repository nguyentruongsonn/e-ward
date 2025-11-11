<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LichSuThanhToan;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function vnpay_payment()
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:8000/vnpay_return"; // ✅ đổi sang route Laravel của bạn
        $vnp_TmnCode = "ZHCUERW9"; // Mã website tại VNPAY
        $vnp_HashSecret = "WVVVZOCW55HQ2QB3EKAGR0QYFPIV6XYX"; // Chuỗi bí mật

        $vnp_TxnRef = '1230001';
        $vnp_OrderInfo = '1233123113';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = 1000000 * 100;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        // ✅ Redirect luôn sang trang VNPAY thay vì trả JSON
        return redirect()->away($vnp_Url);
    }

    public function vnpay_return(Request $request)
    {
        // Lấy secret (nên đưa vào env/config trong thực tế)
        $vnp_HashSecret = "WVVVZOCW55HQ2QB3EKAGR0QYFPIV6XYX";

        // Lấy toàn bộ tham số trả về
        $inputData = $request->all();

        // Xác thực chữ ký
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if (substr($key, 0, 4) === 'vnp_') {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            Log::warning('VNPAY return: invalid checksum', ['params' => $request->all()]);
            return redirect()->route('profile.payments')
                ->with('error', 'Xác thực thanh toán thất bại (checksum).');
        }

        // Kiểm tra mã phản hồi
        $responseCode = $request->input('vnp_ResponseCode');
        $txnRef = $request->input('vnp_TxnRef'); // mã đơn hàng của hệ thống
        $transactionNo = $request->input('vnp_TransactionNo'); // mã giao dịch tại VNPAY
        $amount = ((int) ($request->input('vnp_Amount') ?? 0)) / 100;
        $orderInfo = $request->input('vnp_OrderInfo'); // có thể chứa maHSXL
        $payDate = $request->input('vnp_PayDate'); // format yyyyMMddHHmmss

        // Xác định IDCD từ user hiện tại (nếu có)
        $IDCD = null;
        try {
            $authUser = Auth::user();
            if ($authUser) {
                // có thể là \App\Models\Nguoi hoặc User có quan hệ nguoi -> congDan
                if ($authUser instanceof \App\Models\Nguoi) {
                    $nguoi = $authUser;
                } else {
                    $nguoi = $authUser->nguoi ?? null;
                }
                $IDCD = $nguoi && $nguoi->congDan ? $nguoi->congDan->IDCD : null;
            }
        } catch (\Throwable $e) {
            Log::warning('Không lấy được IDCD từ Auth', ['e' => $e->getMessage()]);
        }

        // Tìm maHSXL nếu bạn encode trong OrderInfo theo định dạng: HSXL:{maHSXL}
        $maHSXL = null;
        if (is_string($orderInfo) && preg_match('/HSXL:([A-Za-z0-9_\-]+)/', $orderInfo, $m)) {
            $maHSXL = $m[1];
        }

        // Chuẩn hóa ngày
        $ngayGD = null;
        if ($payDate && preg_match('/^\d{14}$/', $payDate)) {
            $ngayGD = Carbon::createFromFormat('YmdHis', $payDate);
        } else {
            $ngayGD = now();
        }

        // Tạo khóa maGD duy nhất từ TxnRef + TransactionNo
        $maGD = trim(($txnRef ?? '') . '-' . ($transactionNo ?? ''));

        if ($responseCode === '00') {
            // Thành công: ghi vào bảng lịch sử thanh toán (idempotent theo maGD)
            try {
                DB::transaction(function () use ($maGD, $transactionNo, $txnRef, $amount, $ngayGD, $IDCD, $maHSXL, $orderInfo) {
                    $exists = LichSuThanhToan::where('maGD', $maGD)->exists();
                    if (!$exists) {
                        LichSuThanhToan::create([
                            'maGD' => $maGD,
                            'soGD' => (string) ($transactionNo ?? ''),
                            'loaiGD' => 'VNPAY',
                            'ngayGD' => $ngayGD,
                            'soTien' => $amount,
                            'trangThai' => 'THANH_CONG',
                            'IDCD' => $IDCD ?? 1, // fallback nếu chưa xác định được
                            'maHSXL' => $maHSXL,
                            'moTa' => 'Thanh toán VNPAY cho đơn ' . ($txnRef ?? '') . ' | Info: ' . ($orderInfo ?? ''),
                        ]);
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Lỗi lưu lịch sử thanh toán', ['error' => $e->getMessage(), 'maGD' => $maGD]);
                return redirect()->route('profile.payments')
                    ->with('error', 'Thanh toán thành công nhưng lưu lịch sử thất bại.');
            }

            return redirect()->route('profile.payments')
                ->with('success', 'Thanh toán thành công. Đã ghi vào lịch sử.');
        } else {
            // Không thành công: vẫn có thể log nếu muốn
            Log::info('VNPAY return: payment failed', [
                'code' => $responseCode,
                'params' => $request->all()
            ]);
            return redirect()->route('profile.payments')
                ->with('error', 'Thanh toán thất bại. Mã: ' . $responseCode);
        }
    }
}
