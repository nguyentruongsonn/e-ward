@extends('layouts.app')

@section('content')
<style>
    /* ====== BREADCRUMB ====== */

    .breadcrumb a {
        color: #007bff;
        text-decoration: none;
    }

    /* ====== STEP WIZARD ====== */
    .step-wizard {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        margin-bottom: 35px;
    }
    .step-wizard::before {
        content: "";
        position: absolute;
        top: 25px;
        left: 10%;
        width: 80%;
        height: 3px;
        background-color: #ddd;
        z-index: 0;
    }
    .step {
        position: relative;
        text-align: center;
        width: 25%;
        z-index: 1;
    }
    .step .circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #ccc;
        color: #fff;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 20px;
        transition: 0.3s;
    }
    .step.active .circle {
        background-color: #007bff;
        box-shadow: 0 0 15px #2C097F;
    }
    .step.completed .circle {
        background-color: #007bff;
    }
    .step p {
        margin-top: 8px;
        font-weight: 500;
        color: #333;
    }


    /* ====== FORM ====== */
    .form-section {
        border: 1px solid #eee;
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    .form-section h5 {
        font-size: 18px;
        font-weight: 600;
        color: #007bff;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }
    label {
        font-weight: 500;
        margin-top: 10px;
        color: #1A2A36;
    }

    .required::after {
        content: " *";
        color: red;
    }
    .hidden {
        display: none !important;
    }

    /* Thêm style cho input lỗi */
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
    }

    /* ====== PRINT STYLES FOR RECEIPT / INVOICE ====== */
    @media print {
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-size: 13px;
        }

        header, nav, footer, .navbar, .breadcrumb, .step-wizard,
        .btn, .chat-toggle-button, .back-to-top, .chatbot-container,
        .footer {
            display: none !important;
        }

        main, .container, .form-section, .print-invoice-wrapper {
            border: none !important;
            box-shadow: none !important;
        }

        .print-invoice-wrapper {
            margin: 0 auto;
            padding: 0;
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
</style>

<div class="container py-4">

    {{-- BREADCRUMB --}}
    <div class="breadcrumb">
        <a href="/">Trang chủ</a>
        <strong> / {{ $tthc->tenTTHC}}</strong>
    </div>

    {{-- STEP WIZARD --}}
    <div class="step-wizard mb-4">
        <div class="step active" data-step="1">
            <div class="circle"><i class="fa fa-pen"></i></div>
            <p>Thông tin hồ sơ</p>
        </div>
        <div class="step" data-step="2">
            <div class="circle">2</div>
            <p>Thành phần hồ sơ</p>
        </div>
        <div class="step" data-step="3">
            <div class="circle">3</div>
            <p>Thông tin phí, lệ phí</p>
        </div>
        <div class="step" data-step="4">
            <div class="circle">4</div>
            <p>Nộp hồ sơ</p>
        </div>
    </div>

    {{-- FORM --}}
    {{-- Đặt id cho form để JS có thể gọi reportValidity() --}}
<form id="nopHoSoForm" method="POST" action="" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="maTTHC" value="{{ $maTTHC ?? ($hoSo->maTTHC ?? '') }}" id="maTTHCInput">
        <input type="hidden" name="tong_tien" id="tongTienInput" value="0">

        {{-- ========================== STEP 1: THÔNG TIN HỒ SƠ ========================== --}}
        <div class="form-section" data-step="1">
            <h5>Thông tin hồ sơ</h5>
            <div class="row">
                @if(isset($config) && is_array($config))
                    @foreach($config as $group)
                        @if(isset($group['group']) && !empty($group['fields']))
                            {{-- Tiêu đề nhóm lớn (Ví dụ: Thông tin người nộp) --}}
                            <div class="col-12">
                                <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:30px;">{{ $group['group'] }}</h5>
                            </div>

                            @foreach($group['fields'] as $field)
                                {{-- TRƯỜNG HỢP 1: LÀ DÒNG CHÚ THÍCH (CONTENT) ĐỨNG RIÊNG --}}
                                @if(isset($field['type']) && $field['type'] === 'content')
                                    <div class="col-12 {{ $field['class'] ?? '' }}"
                                        @if(isset($field['attributes']))
                                            @foreach($field['attributes'] as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                                        @endif
                                    >
                                        {!! $field['content'] ?? '' !!}
                                    </div>

                                {{-- TRƯỜNG HỢP 2: LÀ DÒNG NHẬP LIỆU (ROW) --}}
                                @elseif(isset($field['type']) && $field['type'] === 'row')
                                    <div class="col-12">
                                        {{-- Tiêu đề của Row --}}
                                        @if(isset($field['title']) && !empty($field['title']))
                                            <p class="mt-3 mb-2 " style="color: black; font-size: 17px; font-style: italic;">{{ $field['title'] }}</p>
                                        @endif

                                        <div class="row g-3 mb-2">
                                            @foreach(($field['columns'] ?? []) as $column)
                                                @php
                                                    $c_name = $column['name'] ?? \Illuminate\Support\Str::slug($column['label'] ?? 'field', '_');
                                                    $c_label = $column['label'] ?? '';
                                                    $c_required = isset($column['required']) && $column['required'];
                                                    $c_attributes = $column['attributes'] ?? [];
                                                @endphp

                                                <div class="{{ 'col-md-' . ($column['col'] ?? '6') }}">

                                                    {{-- Kiểm tra xem Cột này là INPUT hay là CONTENT --}}
                                                    @if(isset($column['type']) && $column['type'] === 'content')
                                                        {{-- Hiển thị chú thích nằm trong cột --}}
                                                        <div class="{{ $column['class'] ?? '' }}"
                                                            @foreach($c_attributes as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                                                        >
                                                            {!! $column['content'] ?? $column['label'] ?? '' !!}
                                                        </div>
                                                    @else
                                                        {{-- Hiển thị Input bình thường --}}
                                                        <label for="{{ $c_name }}" class="form-label @if($c_required) required @endif">{{ $c_label }}</label>

                                                        @switch($column['type'] ?? 'text')
                                                            @case('select')
                                                                <select
                                                                    class="form-select"
                                                                    name="{{ $c_name }}"
                                                                    id="{{ $c_name }}"
                                                                    @if($c_required) required @endif
                                                                    @foreach($c_attributes as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                                                                >
                                                                    <option value="">-- Vui lòng chọn --</option>
                                                                    @foreach($column['options'] ?? [] as $key => $value)
                                                                        @php
                                                                            $optionValue = is_int($key) ? $value : $key;
                                                                            $isSelected = isset($column['value']) && $column['value'] == $optionValue;
                                                                        @endphp
                                                                        <option value="{{ $optionValue }}" @if($isSelected) selected @endif>{{ $value }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @break
                                                            @default
                                                                <input
                                                                    type="{{ $column['type'] ?? 'text' }}"
                                                                    class="form-control"
                                                                    name="{{ $c_name }}"
                                                                    id="{{ $c_name }}"
                                                                    @if(isset($column['value'])) value="{{ $column['value'] }}" @endif
                                                                    @if($c_required) required @endif
                                                                    @if(isset($column['placeholder'])) placeholder="{{ $column['placeholder'] }}" @endif
                                                                    @if(isset($column['min'])) min="{{ $column['min'] }}" @endif
                                                                    @if(isset($column['max'])) max="{{ $column['max'] }}" @endif
                                                                    @foreach($c_attributes as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                                                                >
                                                        @endswitch
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                @else
                    <div class="col-12">
                        <p class="text-muted">Không có thông tin form để hiển thị.</p>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-secondary prev-step" disabled>Quay lại</button>
                <button type="button" class="btn btn-color next-step">Tiếp tục</button>
            </div>
        </div>

        {{-- ========================== STEP 2: THÀNH PHẦN HỒ SƠ (GIỮ NGUYÊN) ========================== --}}
        <div class="form-section hidden" data-step="2">
            <h5>Thành phần hồ sơ</h5>
            
            @php 
                $allDocuments = collect();
                foreach($thanhPhanHoSos as $tenThanhPhan => $giayTos) {
                    $requiredGiayTos = $giayTos->filter(function ($tp) {
                        return ($tp->soLuongBanChinh ?? 0) >= 1 || ($tp->soLuongBanSao ?? 0) >= 1;
                    });
                    foreach($requiredGiayTos as $doc) {
                        $doc->thanhPhanName = $tenThanhPhan;
                        $allDocuments->push($doc);
                    }
                }
            @endphp

            @if($allDocuments->isNotEmpty())
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="bg-color">
                            <tr class="text-center">
                                <th class="text-white" style="width: 50px;">STT</th>
                                <th class="text-white">Tên giấy tờ</th>
                                <th class="text-white" style="width: 100px;">Bản chính</th>
                                <th class="text-white" style="width: 100px;">Bản sao</th>
                                <th class="text-white" style="width: 35%;">Nộp file</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allDocuments as $index => $tp)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        {{ $tp->tenGiayTo }}
                                        @if($tp->yeuCau === 'Bắt buộc')
                                            <span class="badge bg-danger ms-2">Bắt buộc</span>
                                        @endif
                                    </td>
                                    <td class="text-center"><span class="badge bg-success">{{ $tp->soLuongBanChinh }}</span></td>
                                    <td class="text-center">@if($tp->soLuongBanSao) <span class="badge bg-info">{{ $tp->soLuongBanSao }}</span> @else <span class="text-muted">—</span> @endif</td>
                                    <td><input type="file" name="taiLieu[{{ $tp->maGiayTo }}][]" class="form-control form-control-sm document-upload" data-yeucau="{{ $tp->yeuCau }}" @if($tp->yeuCau === 'Bắt buộc') required @endif multiple ></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-success text-center mt-3">✅ Không có giấy tờ nào cần nộp trực tuyến.</div>
            @endif

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary prev-step">Quay lại</button>
                <button type="button" class="btn btn-color next-step">Tiếp tục</button>
            </div>
        </div>

        {{-- ========================== STEP 3: PHÍ, LỆ PHÍ (GIỮ NGUYÊN) ========================== --}}
        <div class="form-section hidden" data-step="3">
            <h5>Thông tin phí, lệ phí</h5>
            <div class="mb-4">
                <div class="row">
                    <h5>Hình thức nhận kết quả</h5>
                    <div class="col-4">
                        <select name="hinh_thuc_nhan_ket_qua" id="hinhThucNhanKetQua" class="form-select" required>
                            <option value="">-- Chọn hình thức --</option>
                            <option value="Nhận trực tuyến">Nhận trực tuyến</option>
                            <option value="Nhận dịch vụ bưu chính">Nhận dịch vụ bưu chính</option>
                            <option value="Nhận trực tiếp">Nhận trực tiếp</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="buuChinhSection" class="mb-5" style="display: none;">
                <div class="mb-3">
                    <div class="form-check">
                        <input class="" type="checkbox" name="dang_ky_nop_ho_so_tai_nha" id="dangKyNopHoSoTaiNha">
                        <label class="" for="dangKyNopHoSoTaiNha">Đăng ký nộp hồ sơ tại nhà</label>
                    </div>
                    <div class="form-check">
                        <input class="" type="checkbox" name="dang_ky_nhan_ket_qua_tai_nha" id="dangKyNhanKetQuaTaiNha">
                        <label class="" for="dangKyNhanKetQuaTaiNha">Đăng ký nhận kết quả tại nhà</label>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><label class="form-label">Tên</label><input type="text" name="ten_buu_chinh" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Số điện thoại</label><input type="text" name="sdt_buu_chinh" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email_buu_chinh" class="form-control"></div>
                </div>
                <div id="diaChiNopHoSo" class="mb-3" style="display: none;">
                    <p class="mt-5 mb-1" style="color: #007bff;">Địa chỉ bưu cục đến nhận hồ sơ</p>
                    <div class="row">
                        <div class="col-md-4"><label class="form-label required">Tỉnh/TP</label><select name="tinh_tp_nop_ho_so" class="form-select"><option value="">-- Chọn Tỉnh/TP --</option></select></div>
                        <div class="col-md-4"><label class="form-label">Phường/Xã</label><input type="text" name="phuong_xa_nop_ho_so" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Địa chỉ chi tiết</label><input type="text" name="dia_chi_chi_tiet_nop_ho_so" class="form-control"></div>
                    </div>
                </div>
                <div id="diaChiNhanKetQua" class="mb-3" style="display: none;">
                    <p class="mt-5 mb-1" style="color:#007bff;">Địa chỉ bưu cục gửi kết quả</p>
                    <div class="row">
                        <div class="col-md-4"><label class="form-label required">Tỉnh/TP nhận kết quả</label><select name="tinh_tp_nhan_ket_qua" class="form-select"><option value="">-- Chọn Tỉnh/TP --</option></select></div>
                        <div class="col-md-4"><label class="form-label">Phường/Xã nhận kết quả</label><input type="text" name="phuong_xa_nhan_ket_qua" class="form-control"></div>
                        <div class="col-md-4"><label class="form-label">Địa chỉ chi tiết</label><input type="text" name="dia_chi_chi_tiet_nhan_ket_qua" class="form-control"></div>
                    </div>
                </div>
                <div class="mt-4 mb-1">
                    <h6 class="mt-5">Thông tin thu phí hồ sơ/ trả về kết quả tại nhà</h6>
                    <p class="text-danger">(Nhân viên bưu chính sẽ thu phí trực tiếp tại nhà)</p>
                </div>
            </div>
            <div class="mt-5 mb-1 ">
                <h5>Thông tin phí, lệ phí</h5>
                <div class="table-responsive rounded">
                    <table class="table table-bordered align-middle rounded" id="lePhiTable">
                        <thead class="bg-color">
                            <tr>
                                <th class="text-dark">Loại lệ phí</th>
                                <th style="width: 100px" class="text-dark">Số lượng</th>
                                <th style="width: 200px" class="text-dark">Mức lệ phí</th>
                                <th style="width: 150px" class="text-dark">Thành tiền</th>
                                <th style="width: 100px" class="text-dark">Bắt buộc</th>
                                <th class="text-dark">Số lượng>Mô tả</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lePhis as $index => $lePhi)
                                <tr data-le-phi="{{ $lePhi->soTien }}">
                                    <td>{{ $lePhi->loaiLePhi }}</td>
                                    <td><input type="number" name="le_phi_so_luong[{{ $lePhi->maLePhi }}]" class="form-control form-control-sm so-luong" value="1" min="1" data-muc-le-phi="{{ $lePhi->soTien }}"></td>
                                    <td><select name="muc_le_phi[{{ $lePhi->maLePhi }}]" class="form-select form-select-sm muc-le-phi"><option value="{{ $lePhi->soTien }}">{{ number_format($lePhi->soTien, 0, ',', '.') }} VNĐ</option></select></td>
                                    <td class="thanh-tien">{{ number_format($lePhi->soTien, 0, ',', '.') }} VNĐ</td>
                                    <td class="text-center">{{ $lePhi->batBuoc ?? 'Không' }}</td>
                                    <td class="small">{{ $lePhi->moTa ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-3">Chưa có thông tin lệ phí</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-color rounded-bottom">
                            <tr>
                                <td colspan="3" class="text-end text-dark"><strong>Tổng</strong></td>
                                <td class="text-dark fw-bold" id="tongLePhi">@if($lePhis->isNotEmpty()) {{ number_format($lePhis->sum('soTien'), 0, ',', '.') }} VNĐ @else 0 VNĐ @endif</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="mt-5 mb-1 ">
                <div class="row">
                    <h5>Hình thức thanh toán</h5>
                    <div class="col-8">
                        <select name="hinh_thuc_thanh_toan" id="hinhThucThanhToan" class="form-select mt-2" required>
                            <option value="" selected>Chọn phương thức thanh toán</option>
                            <option value="Thanh toán QR" >Thanh toán QR</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="qrPaymentSection" class="mt-4 mb-4" style="display: none;">
                <div class="card border-color">
                    <div class="card-header bg-color text-white"><h5 class="mb-0"><i class="fa fa-qrcode"></i> Thanh toán qua QR Code</h5></div>
                    <div class="card-body text-center">
                        <p class="text-muted mb-3">Vui lòng quét mã QR bên dưới để thanh toán</p>
                        <div id="qrCodeContainer" class="mb-3"><img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 300px;"></div>
                        <div class="alert alert-info"><strong>Số tiền cần thanh toán:</strong> <span id="qrAmount" class="fw-bold "></span></div>
                        <div id="paymentStatus" class="mt-3 small text-center"></div>
                        <input type="hidden" name="ma_giao_dich" id="maGiaoDich" value="">
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <div class="form-check">
                    <input class="" type="checkbox" name="xac_nhan_thong_tin" id="xacNhanThongTin" required>
                    <label class="" for="xacNhanThongTin">Tôi chắc chắn rằng các thông tin khai báo trên là đúng sự thật.</label>
                </div>
            </div>
            <div class="mt-5 mb-1">
                <h5>Đăng ký thông tin hoàn tiền</h5>
                <div class="row g-3">
                    <div class="col-md-4"><label class="form-label">Số tài khoản</label><input type="text" name="so_tai_khoan" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Chủ tài khoản</label><input type="text" name="chu_tai_khoan" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Tên ngân hàng</label><input type="text" name="ten_ngan_hang" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Địa chỉ đơn vị hưởng thụ</label><input type="text" name="dia_chi_don_vi_huong_thu" class="form-control"></div>
                    <div class="col-md-4"><label class="form-label">Chi nhánh ngân hàng</label><input type="text" name="chi_nhanh_ngan_hang" class="form-control"></div>
                </div>
            </div>
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary prev-step">Quay lại</button>
                <div><button type="button" class="btn btn-color" id="btnSubmitHoSo">Nộp hồ sơ</button></div>
            </div>
        </div>

        {{-- ========================== STEP 4: HOÀN THÀNH (GIỮ NGUYÊN) ========================== --}}
        <div class="form-section hidden" data-step="4">
            @if(isset($isSuccess) && $isSuccess)
                <div class="print-invoice-wrapper">
                    <div class="card mb-4 invoice-card">
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
                                        Số: ............... /HĐ-LP
                                    </small>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <small>
                                    Ngày {{ now()->format('d') }} tháng {{ now()->format('m') }} năm {{ now()->format('Y') }}
                                </small>
                            </div>
                        </div>

                        <div class="card-body pt-3">
                            <div class="text-center mb-3">
                                <h4 class="mb-1 fw-bold invoice-title text-uppercase">
                                    PHIẾU TIẾP NHẬN HỒ SƠ VÀ THU LỆ PHÍ
                                </h4>
                                <small class="text-muted">
                                    Mã hồ sơ: <strong>{{ $maHSXL ?? ($hoSo->maHSXL ?? '') }}</strong>
                                </small>
                            </div>

                            <p class="mb-1"><strong>I. Thông tin người nộp</strong></p>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <strong>Họ tên:</strong>
                                    {{ $hoSo->tenChuHoSo ?? ($dulieu['ho_ten'] ?? ($nguoiInfo->hoTen ?? '—')) }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Ngày sinh:</strong>
                                    {{ $dulieu['ngay_sinh'] ?? ($nguoiInfo->ngaySinh ?? '—') }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Số điện thoại:</strong>
                                    {{ $hoSo->soDienThoai ?? ($dulieu['so_dien_thoai'] ?? ($nguoiInfo->soDienThoai ?? '—')) }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Địa chỉ:</strong>
                                    {{ $dulieu['dia_chi'] ?? ($nguoiInfo->noiThuongTru ?? '—') }}
                                </div>
                            </div>

                            {{-- Thông tin thanh toán / hóa đơn --}}
                @php
                    $tongThanhToan = 0;
                    if (!empty($lePhiChiTiet)) {
                        foreach ($lePhiChiTiet as $item) {
                            $tongThanhToan += $item['thanhTien'] ?? 0;
                        }
                    } else {
                        $tongThanhToan = $hoSo->lePhi ?? 0;
                    }
                @endphp

                @if($tongThanhToan > 0)
                            <p class="mb-1"><strong>II. Thông tin lệ phí</strong></p>

                            @if(!empty($lePhiChiTiet))
                                <div class="table-responsive mb-2">
                                    <table class="table table-bordered mb-2">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Loại lệ phí</th>
                                                <th class="text-center" style="width: 80px;">SL</th>
                                                <th class="text-end" style="width: 120px;">Đơn giá</th>
                                                <th class="text-end" style="width: 140px;">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lePhiChiTiet as $item)
                                                <tr>
                                                    <td>{{ $item['loaiLePhi'] ?? '' }}</td>
                                                    <td class="text-center">{{ $item['soLuong'] ?? 0 }}</td>
                                                    <td class="text-end">
                                                        {{ isset($item['mucLePhi']) ? number_format($item['mucLePhi'], 0, ',', '.') : '0' }}
                                                    </td>
                                                    <td class="text-end">
                                                        {{ isset($item['thanhTien']) ? number_format($item['thanhTien'], 0, ',', '.') : '0' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Tổng cộng</th>
                                                <th class="text-end text-danger">
                                                    {{ number_format($tongThanhToan, 0, ',', '.') }} đ
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="mb-2">
                                    <strong>Số tiền đã thanh toán:</strong>
                                    <span class="text-danger">{{ number_format($tongThanhToan, 0, ',', '.') }} đ</span>
                                </p>
                            @endif
                        @endif

                        <div class="row mt-4">
                            <div class="col-4 text-center">
                                <strong>Người nộp tiền</strong><br>
                                <small class="text-muted">(Ký, ghi rõ họ tên)</small>
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4 text-center">
                                <strong>Cán bộ tiếp nhận</strong><br>
                                <small class="text-muted d-block">(Ký, ghi rõ họ tên)</small>
                                {{-- Chữ ký dạng viết tay --}}
                                <div style="margin-top:24px;">
                                    <span class="signature-name"
                                          style="display:inline-block;font-size:20px;font-family:'Segoe Script','Brush Script MT',cursive;color:#c0392b;">
                                        P. Trung Nghĩa
                                    </span>
                                </div>
                                {{-- Họ tên in rõ nét dưới chữ ký --}}
                                <div>
                                    <small class="text-muted">(Phạm Trung Nghĩa)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mb-4 no-print">
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="fa fa-print"></i> In phiếu / hóa đơn
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-color"><i class="fa fa-check"></i> Đồng ý</a>
                </div>
            @else
                <h5>Nộp hồ sơ</h5>
                <p class="text-muted">Vui lòng hoàn thành các bước trước để nộp hồ sơ.</p>
            @endif
        </div>

    </form>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let currentStep = 1;
    const totalSteps = 4;
    const form = document.getElementById("nopHoSoForm");

    function showStep(step) {
        document.querySelectorAll(".form-section").forEach(el => el.classList.add("hidden"));
        const activeSection = document.querySelector(`.form-section[data-step='${step}']`);
        if (activeSection) {
            activeSection.classList.remove("hidden");
        }

        document.querySelectorAll(".step").forEach((s, i) => {
            const stepNumber = i + 1;
            s.classList.remove("active", "completed");
            if (stepNumber < step) {
                s.classList.add("completed");
                // Cập nhật icon thành checkmark khi hoàn thành
                s.querySelector('.circle').innerHTML = '<i class="fa fa-check"></i>';
            } else if (stepNumber === step) {
                s.classList.add("active");
            }
        });

        document.querySelectorAll(".prev-step").forEach(btn => {
            btn.disabled = (step === 1);
        });
    }

    // ========== VALIDATION LOGIC ==========
    function validateStep(step) {
        const currentSection = document.querySelector(`.form-section[data-step='${step}']`);
        if (!currentSection) return true;

        const requiredInputs = currentSection.querySelectorAll("[required]");
        let isValid = true;
        let missingFiles = [];

        requiredInputs.forEach(input => {
            // Special handling for file inputs
            if (input.type === 'file' && input.hasAttribute('required')) {
                if (input.files.length === 0) {
                    input.classList.add('is-invalid');
                    isValid = false;
                    // Get the document name from the row
                    const row = input.closest('tr');
                    if (row) {
                        const docName = row.querySelector('td:nth-child(2)')?.textContent || 'Giấy tờ';
                        missingFiles.push(docName);
                    }
                } else {
                    input.classList.remove('is-invalid');
                }
            } else {
                if (!input.checkValidity()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            }
        });

        if (!isValid) {
            if (step === 2 && missingFiles.length > 0) {
                alert('Vui lòng tải lên các giấy tờ bắt buộc sau:\n\n' + missingFiles.map((doc, i) => `${i + 1}. ${doc}`).join('\n'));
            } else {
                // Hiển thị thông báo lỗi mặc định của trình duyệt cho trường không hợp lệ đầu tiên
                form.reportValidity();
            }
        }

        return isValid;
    }


    // Kiểm tra nếu đã nộp thành công, tự động chuyển sang step 4
    @if(isset($isSuccess) && $isSuccess)
        currentStep = 4;
    @endif

    showStep(currentStep);

    document.querySelectorAll(".next-step").forEach(btn => {
        btn.addEventListener("click", () => {
            // Chỉ chuyển step khi step hiện tại hợp lệ
            if (validateStep(currentStep) && currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    document.querySelectorAll(".prev-step").forEach(btn => {
        btn.addEventListener("click", () => {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });

 

    // ========== XỬ LÝ HIỂN THỊ/ẨN KHI CHỌN "NHẬN DỊCH VỤ BƯU CHÍNH" ==========
    const hinhThucNhanKetQua = document.getElementById('hinhThucNhanKetQua');
    const buuChinhSection = document.getElementById('buuChinhSection');
    const dangKyNopHoSoTaiNha = document.getElementById('dangKyNopHoSoTaiNha');
    const dangKyNhanKetQuaTaiNha = document.getElementById('dangKyNhanKetQuaTaiNha');
    const diaChiNopHoSo = document.getElementById('diaChiNopHoSo');
    const diaChiNhanKetQua = document.getElementById('diaChiNhanKetQua');

    function toggleBuuChinhSection() {
        if (hinhThucNhanKetQua && buuChinhSection) {
            if (hinhThucNhanKetQua.value === 'Nhận dịch vụ bưu chính') {
                buuChinhSection.style.display = 'block';
            } else {
                buuChinhSection.style.display = 'none';
                // Reset các checkbox khi ẩn section
                if (dangKyNopHoSoTaiNha) dangKyNopHoSoTaiNha.checked = false;
                if (dangKyNhanKetQuaTaiNha) dangKyNhanKetQuaTaiNha.checked = false;
                if (diaChiNopHoSo) diaChiNopHoSo.style.display = 'none';
                if (diaChiNhanKetQua) diaChiNhanKetQua.style.display = 'none';
            }
        }
    }

    function toggleDiaChiNopHoSo() {
        if (dangKyNopHoSoTaiNha && diaChiNopHoSo) {
            diaChiNopHoSo.style.display = dangKyNopHoSoTaiNha.checked ? 'block' : 'none';
        }
    }

    function toggleDiaChiNhanKetQua() {
        if (dangKyNhanKetQuaTaiNha && diaChiNhanKetQua) {
            diaChiNhanKetQua.style.display = dangKyNhanKetQuaTaiNha.checked ? 'block' : 'none';
        }
    }

    if (hinhThucNhanKetQua) {
        hinhThucNhanKetQua.addEventListener('change', toggleBuuChinhSection);
    }

    if (dangKyNopHoSoTaiNha) {
        dangKyNopHoSoTaiNha.addEventListener('change', toggleDiaChiNopHoSo);
    }

    if (dangKyNhanKetQuaTaiNha) {
        dangKyNhanKetQuaTaiNha.addEventListener('change', toggleDiaChiNhanKetQua);
    }



    // Khởi tạo trạng thái ban đầu
    toggleBuuChinhSection();

    // ========== DYNAMIC DROPDOWN LOGIC ==========
    const provinceSelect = document.querySelector('select[data-province-selector="true"]');
    if (provinceSelect) {
        provinceSelect.addEventListener('change', function () {
            const selectedProvinceId = this.value;
            const wardSelectName = this.dataset.targetWard;
            const wardSelect = document.querySelector(`select[name="${wardSelectName}"]`);

            if (!wardSelect) return;

            wardSelect.innerHTML = '<option value="">-- Vui lòng chờ --</option>';
            wardSelect.disabled = true;

            if (selectedProvinceId) {
                // Thay thế bằng URL API của bạn
                fetch(`/api/provinces/${selectedProvinceId}/wards`)
                    .then(response => response.json())
                    .then(data => {
                        wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
                        data.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.id;
                            option.textContent = ward.name;
                            wardSelect.appendChild(option);
                        });
                        wardSelect.disabled = false;
                    })
                    .catch(error => {
                        console.error('Lỗi khi tải danh sách phường/xã:', error);
                        wardSelect.innerHTML = '<option value="">-- Có lỗi xảy ra --</option>';
                    });
            } else {
                wardSelect.innerHTML = '<option value="">-- Vui lòng chọn Tỉnh/Thành phố --</option>';
                wardSelect.disabled = true;
            }
        });
    }

    // ========== TÍNH TOÁN LỆ PHÍ TỰ ĐỘNG ==========
    function tinhThanhTien() {
        const rows = document.querySelectorAll('#lePhiTable tbody tr');
        let tongTien = 0;

        rows.forEach(row => {
            const soLuongInput = row.querySelector('.so-luong');
            const mucLePhiSelect = row.querySelector('.muc-le-phi');
            const thanhTienCell = row.querySelector('.thanh-tien');

            if (soLuongInput && mucLePhiSelect && thanhTienCell) {
                const soLuong = parseInt(soLuongInput.value) || 0;
                const mucLePhi = parseInt(mucLePhiSelect.value) || 0;
                const thanhTien = soLuong * mucLePhi;

                // Format số tiền với dấu phẩy
                thanhTienCell.textContent = thanhTien.toLocaleString('vi-VN') + ' VNĐ';
                tongTien += thanhTien;
            }
        });

        // Cập nhật tổng
        const tongLePhiEl = document.getElementById('tongLePhi');
        if (tongLePhiEl) {
            tongLePhiEl.textContent = tongTien.toLocaleString('vi-VN') + ' VNĐ';
        }
    }

    // Lắng nghe sự kiện thay đổi số lượng và mức lệ phí
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('so-luong') || e.target.classList.contains('muc-le-phi')) {
            tinhThanhTien();
        }
    });

    // Lắng nghe sự kiện input để cập nhật real-time
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('so-luong')) {
            // Đảm bảo số lượng >= 1
            if (parseInt(e.target.value) < 1) {
                e.target.value = 1;
            }
            tinhThanhTien();
            // Nếu đang chọn thanh toán QR, cập nhật lại QR code
            if (hinhThucThanhToan && hinhThucThanhToan.value === 'Thanh toán QR') {
                generateQRCode();
            }
        }
    });

    // Khởi tạo tính toán lệ phí khi trang load
    tinhThanhTien();

    // Cập nhật QR code khi tổng tiền thay đổi
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('so-luong') || e.target.classList.contains('muc-le-phi')) {
            tinhThanhTien();
            // Nếu đang chọn thanh toán QR, cập nhật lại QR code
            if (hinhThucThanhToan && hinhThucThanhToan.value === 'Thanh toán QR') {
                generateQRCode();
            }
        }
    });

    // ========== KIỂM TRA THANH TOÁN VỚI CASSO API ==========
    const api_key = 'AK_CS.1f57ef80bd2d11f0a73fcb966f33aa53.ZbCZFwFUAE2cm31dPyfnRq9k3FVcCLTPPiYCrS4wNt8xQ9DeKu1v75GM5Q6MMQlnggRcZulM';
    const api_get_paid = 'https://oauth.casso.vn/v2/transactions';

    let paymentCheckInterval = null;
    let checkInterval = 10000;
    let consecutiveErrors = 0;
    const MAX_ERRORS = 3;

    /**
     * Hiển thị thông báo thanh toán thành công với animation đẹp
     */
    function showPaymentSuccessNotification(maGiaoDich, amount) {
        const paymentStatusEl = document.getElementById('paymentStatus');
        if (!paymentStatusEl) return;

        // Tạo HTML cho thông báo thành công với animation
        const successHTML = `
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="animation: slideInDown 0.5s ease-out;">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="font-size: 2rem;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="alert-heading mb-2">
                            <i class="fa fa-check-circle"></i> Thanh toán thành công!
                        </h5>
                        <div class="mb-2">
                            <strong>Mã giao dịch:</strong> <code>${maGiaoDich}</code><br>
                            <strong>Số tiền:</strong> <span class="text-success fw-bold">${amount.toLocaleString('vi-VN')} VNĐ</span><br>
                            <small class="text-muted">Thời gian: ${new Date().toLocaleString('vi-VN')}</small>
                        </div>

                    </div>
                </div>
            </div>
        `;

        paymentStatusEl.innerHTML = successHTML;

        // Thêm animation CSS nếu chưa có
        if (!document.getElementById('paymentSuccessStyles')) {
            const style = document.createElement('style');
            style.id = 'paymentSuccessStyles';
            style.textContent = `
                @keyframes slideInDown {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                @keyframes pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                }
                .payment-success-icon {
                    animation: pulse 2s infinite;
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Hàm kiểm tra thanh toán
     */
    async function checkPayment(maGiaoDich, amountToFind) {
        try {
            const toDate = new Date();
            const fromDate = new Date();
            fromDate.setDate(fromDate.getDate() - 7);

            const apiUrl = `${api_get_paid}?fromDate=${fromDate.toISOString().split('T')[0]}&toDate=${toDate.toISOString().split('T')[0]}&pageSize=100&page=1`;

            const response = await fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'Authorization': `apikey ${api_key}`,
                    "Content-Type": "application/json",
                },
            });

            if (!response.ok) {
                if (response.status === 429) {
                    consecutiveErrors++;
                    checkInterval = Math.min(checkInterval * 2, 60000);
                    const paymentStatusEl = document.getElementById('paymentStatus');
                    if (paymentStatusEl && paymentCheckInterval) {
                        paymentStatusEl.innerHTML = `<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Quá nhiều yêu cầu. Đang đợi ${checkInterval/1000} giây...</div>`;
                    }
                    if (paymentCheckInterval) {
                        clearInterval(paymentCheckInterval);
                        paymentCheckInterval = setInterval(() => checkPayment(maGiaoDich, amountToFind), checkInterval);
                    }
                    return;
                }
                consecutiveErrors++;
                if (consecutiveErrors >= MAX_ERRORS) {
                    stopPaymentCheck();
                }
                return;
            }

            consecutiveErrors = 0;
            checkInterval = 10000;

            const data = await response.json();

            if (!data || !data.data || !data.data.records) {
                return;
            }

            const successfulTransaction = data.data.records.find(transaction => {
                const description = transaction.description || transaction.content || '';
                const amount = transaction.amount || 0;

                // Kiểm tra số tiền khớp
                const amountMatch = amount === amountToFind;
                if (!amountMatch) return false;

                // Tìm kiếm linh hoạt: tìm mã giao dịch với hoặc không có dấu gạch dưới/khoảng trắng
                // Loại bỏ khoảng trắng, dấu gạch dưới và dấu gạch ngang để so sánh
                const normalizedDescription = description.replace(/[\s_\-]/g, '').toUpperCase();
                const normalizedMaGiaoDich = maGiaoDich.replace(/[\s_\-]/g, '').toUpperCase();

                // Tìm kiếm theo nhiều cách:
                // 1. Tìm mã đầy đủ (đã normalize)
                // 2. Tìm mã gốc
                // 3. Tìm phần đầu của mã (HS + timestamp - ít nhất 10 ký tự đầu)
                const codeMatch = normalizedDescription.includes(normalizedMaGiaoDich) ||
                                 description.includes(maGiaoDich) ||
                                 normalizedDescription.includes(maGiaoDich.replace(/[\s_\-]/g, '').toUpperCase()) ||
                                 (maGiaoDich.length >= 10 && (
                                     normalizedDescription.includes(maGiaoDich.substring(0, 10).replace(/[\s_\-]/g, '').toUpperCase()) ||
                                     description.includes(maGiaoDich.substring(0, 10))
                                 ));

                // Log để debug
                if (description.includes('HS') || description.toUpperCase().includes('HS')) {
                    console.log("Giao dịch khả nghi:", {
                        description,
                        normalizedDescription,
                        maGiaoDich,
                        normalizedMaGiaoDich,
                        amount,
                        amountToFind,
                        amountMatch,
                        codeMatch
                    });
                }

                return codeMatch;
            });

            if (successfulTransaction) {
                console.log("✅ Thanh toán thành công!", successfulTransaction);

                stopPaymentCheck();

                // Hiển thị thông báo thành công đẹp
                showPaymentSuccessNotification(maGiaoDich, amountToFind);

                isPaymentChecked = true;

                // Lưu mã giao dịch vào hidden input để submit cùng form
                // Lịch sử thanh toán sẽ được lưu khi submit form thành công
                if (maGiaoDichInput) {
                    maGiaoDichInput.value = maGiaoDich;
                }
            } else {
                const paymentStatusEl = document.getElementById('paymentStatus');
                if (paymentStatusEl && paymentCheckInterval) {
                    paymentStatusEl.innerHTML = `<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Đang kiểm tra thanh toán... (Mã: ${maGiaoDich})<br><small>Lần kiểm tra: ${new Date().toLocaleTimeString()}</small></div>`;
                }
            }
        } catch (error) {
            console.error("Lỗi kiểm tra thanh toán:", error);
            consecutiveErrors++;
            if (error.message.includes('Failed to fetch') || error.message.includes('ERR_INTERNET_DISCONNECTED')) {
                const paymentStatusEl = document.getElementById('paymentStatus');
                if (paymentStatusEl) {
                    paymentStatusEl.innerHTML = `<div class="alert alert-warning"><i class="fa fa-wifi"></i> Mất kết nối internet. Đang đợi kết nối lại...</div>`;
                }
                checkInterval = Math.min(checkInterval * 1.5, 30000);
                if (paymentCheckInterval) {
                    clearInterval(paymentCheckInterval);
                    paymentCheckInterval = setInterval(() => checkPayment(maGiaoDich, amountToFind), checkInterval);
                }
            } else if (consecutiveErrors >= MAX_ERRORS) {
                stopPaymentCheck();
            }
        }
    }

    /**
     * Bắt đầu kiểm tra thanh toán
     */
    function startPaymentCheck(maGiaoDich, amount) {
        console.log("Bắt đầu kiểm tra thanh toán:", { maGiaoDich, amount });

        stopPaymentCheck();
        checkInterval = 10000;
        consecutiveErrors = 0;

        if (!maGiaoDich || !amount) {
            console.error("Thiếu thông tin để kiểm tra thanh toán");
            return;
        }

        const paymentStatusEl = document.getElementById('paymentStatus');
        if (paymentStatusEl) {
            paymentStatusEl.innerHTML = `<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Đang kiểm tra thanh toán... (Mã: ${maGiaoDich})</div>`;
        }

        paymentCheckInterval = setInterval(() => {
            checkPayment(maGiaoDich, amount);
        }, checkInterval);

        setTimeout(() => {
            checkPayment(maGiaoDich, amount);
        }, 1000);
    }

    /**
     * Dừng kiểm tra thanh toán
     */
    function stopPaymentCheck() {
        if (paymentCheckInterval) {
            clearInterval(paymentCheckInterval);
            paymentCheckInterval = null;
        }
        checkInterval = 10000;
        consecutiveErrors = 0;
    }

    // ========== XỬ LÝ QR CODE PAYMENT ==========
    const hinhThucThanhToan = document.getElementById('hinhThucThanhToan');
    const qrPaymentSection = document.getElementById('qrPaymentSection');
    const qrCodeImage = document.getElementById('qrCodeImage');
    const qrAmount = document.getElementById('qrAmount');
    const maGiaoDichInput = document.getElementById('maGiaoDich');

    let isPaymentChecked = false;

    function generateQRCode() {
        // Kiểm tra xem có chọn "Thanh toán QR" không
        if (!hinhThucThanhToan || hinhThucThanhToan.value !== 'Thanh toán QR') {
            qrPaymentSection.style.display = 'none';
            stopPaymentCheck();
            return;
        }

        const tongLePhiEl = document.getElementById('tongLePhi');
        let tongTien = 0;
        if (tongLePhiEl) {
            const tongTienText = tongLePhiEl.textContent.replace(/[^\d]/g, '');
            tongTien = parseInt(tongTienText) || 0;
        }

        if (tongTien > 0) {
            // Reset trạng thái thanh toán
            isPaymentChecked = false;

            // Reset trạng thái hiển thị
            const paymentStatusEl = document.getElementById('paymentStatus');
            if (paymentStatusEl) {
                paymentStatusEl.innerHTML = '';
            }

            // Tạo mã giao dịch duy nhất (không có dấu gạch dưới để dễ nhập khi chuyển khoản)
            const timestamp = Date.now();
            const randomStr = Math.random().toString(36).substr(2, 6).toUpperCase();
            const maGiaoDich = 'HS' + timestamp + randomStr;
            maGiaoDichInput.value = maGiaoDich;

            // Lấy thông tin ngân hàng từ config
            const bankId = '{{ config("services.vietqr.bank_id", "MB") }}';
            const accountNo = '{{ config("services.vietqr.account_no", "914040399999") }}';

            // Tạo URL QR code
            const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-compact2.png?amount=${tongTien}&addInfo=${maGiaoDich}`;

            qrCodeImage.src = qrUrl;
            qrAmount.textContent = tongTien.toLocaleString('vi-VN') + ' VNĐ';
            qrPaymentSection.style.display = 'block';

            // Bắt đầu kiểm tra thanh toán tự động
            startPaymentCheck(maGiaoDich, tongTien);
        } else {
            qrPaymentSection.style.display = 'none';
            stopPaymentCheck();
            // Xóa trạng thái khi không có tiền
            const paymentStatusEl = document.getElementById('paymentStatus');
            if (paymentStatusEl) {
                paymentStatusEl.innerHTML = '';
            }
        }
    }

    // Xử lý khi thay đổi hình thức thanh toán
    if (hinhThucThanhToan) {
        hinhThucThanhToan.addEventListener('change', function() {
            generateQRCode();
        });

        // Kiểm tra và hiển thị QR code khi trang load (nếu đã chọn "Thanh toán QR")
        setTimeout(function() {
            generateQRCode();
        }, 500);
    }

    // Dừng kiểm tra khi rời khỏi trang
    window.addEventListener('beforeunload', function() {
        stopPaymentCheck();
    });

    // ========== XỬ LÝ SUBMIT FORM ==========
    const btnSubmitHoSo = document.getElementById('btnSubmitHoSo');
    if (btnSubmitHoSo) {
        btnSubmitHoSo.addEventListener('click', function(e) {
            e.preventDefault();

            if (!validateStep(currentStep)) {
                return;
            }

            const tongLePhiEl = document.getElementById('tongLePhi');
            let tongTien = 0;
            if (tongLePhiEl) {
                const tongTienText = tongLePhiEl.textContent.replace(/[^\d]/g, '');
                tongTien = parseInt(tongTienText) || 0;
            }

            const tongTienInput = document.getElementById('tongTienInput');
            if (tongTienInput) {
                tongTienInput.value = tongTien;
            }

            // Kiểm tra nếu có tiền nhưng chưa thanh toán
            if (tongTien > 0 && !isPaymentChecked) {
                alert('Vui lòng quét mã QR và thanh toán trước khi nộp hồ sơ. Hệ thống đang tự động kiểm tra thanh toán của bạn.');
                return;
            }

            // Luôn submit về route nop-ho-so.submit
            form.action = '{{ route("nop-ho-so.submit", ["maTTHC" => $tthc->maTTHC ?? ""]) }}';
            form.submit();
        });
    }

// ========== LOGIC ẨN/HIỆN FORM ĐỘNG (FIX LỖI TITLE TRIỆT ĐỂ) ==========
    function initDynamicForms() {
        const triggers = document.querySelectorAll('[data-trigger]');
        if (triggers.length === 0) return;

        // Hàm kiểm tra và ẩn/hiện cả khối bao ngoài (bao gồm Title)
        function checkAndToggleWrapper(inputContainer) {
            // 1. Tìm thẻ cha chứa các cột (class .row)
            const parentRow = inputContainer.closest('.row');
            if (!parentRow) return;

            // 2. Tìm thẻ bao ngoài cùng (class .col-12 trong Blade của bạn) chứa cả h6 và .row
            const mainWrapper = parentRow.closest('.col-12');
            // Lưu ý: Nếu trong HTML cấu trúc khác, bạn cần inspect xem thẻ chứa h6 là thẻ nào

            if (mainWrapper) {
                // 3. Kiểm tra xem trong dòng (.row) này còn cột nào hiển thị không?
                const allCols = parentRow.children;
                let hasVisibleCol = false;

                for (let col of allCols) {
                    // Nếu cột không có style display: none -> Tức là nó đang hiện
                    if (col.style.display !== 'none') {
                        hasVisibleCol = true;
                        break;
                    }
                }

                // 4. Ẩn/Hiện toàn bộ khối wrapper (Title + Row)
                if (hasVisibleCol) {
                    mainWrapper.style.display = 'block';
                } else {
                    mainWrapper.style.display = 'none';
                }
            }
        }

        function handleTriggerChange(triggerName, selectedValue) {
            // Tìm tất cả các input phụ thuộc
            const targets = document.querySelectorAll(`[data-group="${triggerName}"]`);

            targets.forEach(target => {
                const requiredValues = target.getAttribute('data-show').split(',');

                // Tìm container bao ngoài (col-md-*) để ẩn/hiện input
                const container = target.closest('.col-md-3, .col-md-4, .col-md-6, .col-md-8, .col-md-12, .col-12, .mb-3');

                if (container) {
                    if (requiredValues.includes(selectedValue)) {
                        // HIỆN INPUT
                        container.style.display = 'block';
                    } else {
                        // ẨN INPUT
                        container.style.display = 'none';

                        // Reset giá trị
                        if (target.tagName === 'INPUT' || target.tagName === 'SELECT' || target.tagName === 'TEXTAREA') {
                            target.value = '';
                            target.removeAttribute('required');
                            target.classList.remove('is-invalid');
                        }
                        target.querySelectorAll('input, select, textarea').forEach(input => {
                            if(input.type === 'checkbox' || input.type === 'radio') input.checked = false;
                            else input.value = '';
                            input.removeAttribute('required');
                        });
                    }

                    // QUAN TRỌNG: Kiểm tra xem có cần ẩn luôn Title không
                    checkAndToggleWrapper(container);
                }
            });
        }

        // Gán sự kiện (Giữ nguyên logic cũ)
        triggers.forEach(trigger => {
            const triggerName = trigger.getAttribute('data-trigger');

            if (trigger.type === 'radio' || trigger.type === 'checkbox') {
                const radios = document.querySelectorAll(`input[name="${trigger.name}"]`);
                radios.forEach(r => {
                    r.addEventListener('change', () => {
                        const checked = document.querySelector(`input[name="${trigger.name}"]:checked`);
                        handleTriggerChange(triggerName, checked ? checked.value : '');
                    });
                });
            } else {
                trigger.addEventListener('change', function() {
                    handleTriggerChange(triggerName, this.value);
                });
            }

            // Init state
            let initVal = trigger.value;
            if (trigger.type === 'radio' || trigger.type === 'checkbox') {
                const checked = document.querySelector(`input[name="${trigger.name}"]:checked`);
                initVal = checked ? checked.value : '';
            }
            handleTriggerChange(triggerName, initVal);
        });
    }

    initDynamicForms();
    // ========== TỰ ĐỘNG HIỂN THỊ STEP 4 NẾU THÀNH CÔNG ==========
    @if(isset($isSuccess) && $isSuccess)
        // Đánh dấu tất cả các step trước đó là completed
        document.querySelectorAll('.step').forEach((s, i) => {
            if (i < 3) {
                s.classList.add('completed');
                s.querySelector('.circle').innerHTML = '<i class="fa fa-check"></i>';
            }
        });
    @endif
});
</script>
@endsection
