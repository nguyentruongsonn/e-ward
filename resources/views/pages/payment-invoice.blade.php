@extends('layouts.app')

@section('content')
<style>
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 13px;
        }

        header, nav, footer, .navbar, .breadcrumb,
        .btn, .chat-toggle-button, .back-to-top, .chatbot-container,
        .footer {
            display: none !important;
        }

        main, .container, .invoice-wrapper, .invoice-card {
            border: none !important;
            box-shadow: none !important;
        }

        .invoice-card {
            border: 1px solid #000 !important;
        }

        .invoice-header {
            border-bottom: 1px solid #000 !important;
        }

        .invoice-title {
            font-size: 18px !important;
        }

        .signature-name {
            color: #c0392b !important;
        }

        .no-print {
            display: none !important;
        }
    }

    .invoice-card {
        border: 1px solid #dee2e6;
    }

    .invoice-header {
        border-bottom: 1px solid #dee2e6;
    }

    .signature-name {
        color: #c0392b;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-3 no-print">
                <a href="{{ route('profile.payments') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại lịch sử thanh toán
                </a>
            </div>

            <div class="invoice-wrapper">
                <div class="card invoice-card">
                    <div class="card-body pb-2 invoice-header">
                        <div class="row">
                            <div class="col-7">
                                <h6 class="mb-1 text-uppercase" style="color:#c0392b; font-weight:700;">
                                    ỦY BAN NHÂN DÂN PHƯỜNG ABC
                                </h6>
                                <h6 class="mb-1 text-uppercase" style="font-weight:700;">
                                    BỘ PHẬN MỘT CỬA
                                </h6>
                                <small class="text-muted d-block">
                                    Địa chỉ: ......................................................................
                                </small>
                                <small class="text-muted d-block">
                                    Điện thoại: ..................................................................
                                </small>
                            </div>
                            <div class="col-5 text-end">
                                <h6 class="mb-1 text-uppercase" style="font-weight:700;">
                                    CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM
                                </h6>
                                <small class="text-muted d-block">
                                    Độc lập - Tự do - Hạnh phúc
                                </small>
                                <hr class="my-2">
                                <h6 class="mb-1 text-uppercase" style="color:#c0392b; font-weight:700;">
                                    HÓA ĐƠN THU LỆ PHÍ
                                </h6>
                                <small class="text-muted d-block">
                                    (Dịch vụ công trực tuyến)
                                </small>
                                <small class="text-muted d-block">
                                    Số: {{ $payment->id ?? '...............' }} /HĐ-LP
                                </small>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small>
                                Ngày {{ optional($payment->ngayGD)->format('d') ?? now()->format('d') }}
                                tháng {{ optional($payment->ngayGD)->format('m') ?? now()->format('m') }}
                                năm {{ optional($payment->ngayGD)->format('Y') ?? now()->format('Y') }}
                            </small>
                        </div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="text-center mb-3">
                            <h4 class="mb-1 fw-bold invoice-title text-uppercase">
                                PHIẾU THU LỆ PHÍ DỊCH VỤ CÔNG
                            </h4>
                            @if($payment->maHSXL)
                                <small class="text-muted">
                                    Mã hồ sơ: <strong>{{ $payment->maHSXL }}</strong>
                                </small>
                            @endif
                        </div>

                        <p class="mb-1"><strong>I. Thông tin người nộp</strong></p>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <strong>Họ tên:</strong>
                                {{ $nguoi->hoTen ?? $user->email ?? '—' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Số điện thoại:</strong>
                                {{ $nguoi->soDienThoai ?? '—' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Địa chỉ:</strong>
                                {{ $nguoi->noiThuongTru ?? '—' }}
                            </div>
                            @if($tthc)
                                <div class="col-md-6 mb-2">
                                    <strong>Dịch vụ:</strong>
                                    {{ $tthc->tenTTHC }}
                                </div>
                            @endif
                        </div>

                        <p class="mb-1"><strong>II. Thông tin giao dịch</strong></p>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <strong>Mã giao dịch:</strong>
                                {{ $payment->maGD ?? '-' }}
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Số giao dịch:</strong>
                                {{ $payment->soGD ?? '-' }}
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Thời gian:</strong>
                                {{ $payment->ngayGD ? $payment->ngayGD->format('d/m/Y H:i') : '-' }}
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Loại giao dịch:</strong>
                                {{ $payment->loaiGD ?? '-' }}
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong>Trạng thái:</strong>
                                {{ $payment->trangThai ?? '-' }}
                            </div>
                        </div>

                        <p class="mb-1"><strong>III. Chi tiết số tiền</strong></p>
                        <div class="table-responsive mb-2">
                            <table class="table table-bordered mb-2">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:60px;">STT</th>
                                        <th>Nội dung</th>
                                        <th class="text-end" style="width:180px;">Số tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>
                                            {{ $payment->moTa ?? 'Thu lệ phí dịch vụ công trực tuyến' }}
                                        </td>
                                        <td class="text-end">
                                            {{ $payment->soTien !== null ? number_format($payment->soTien, 0, ',', '.') . ' đ' : '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Tổng cộng</th>
                                        <th class="text-end text-danger">
                                            {{ $payment->soTien !== null ? number_format($payment->soTien, 0, ',', '.') . ' đ' : '0 đ' }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

        <div class="row mt-4">
            <div class="col-4 text-center">
                <strong>Người nộp tiền</strong><br>
                <small class="text-muted">(Ký, ghi rõ họ tên)</small>
            </div>
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <strong>Cán bộ thu tiền</strong><br>
                <small class="text-muted d-block">(Ký, ghi rõ họ tên)</small>
                <div style="margin-top:24px;">
                    <span class="signature-name"
                          style="display:inline-block;font-size:20px;font-family:'Segoe Script','Brush Script MT',cursive;color:#c0392b;">
                        P. Trung Nghĩa
                    </span>
                </div>
                <div>
                    <small class="text-muted">(Phạm Trung Nghĩa)</small>
                </div>
            </div>
        </div>

                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-3 no-print">
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="fa fa-print"></i> In hóa đơn
                    </button>
                    <a href="{{ route('profile.payments') }}" class="btn btn-secondary">
                        <i class="fa fa-times"></i> Đóng
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


