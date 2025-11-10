@extends('layouts.app')

@section('content')
<style>
    /* ====== BREADCRUMB ====== */

    .breadcrumb a {
        color: #32C36C;
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
        background-color: #32C36C;
        box-shadow: 0 0 15px rgba(12, 215, 39, 0.5);
    }
    .step.completed .circle {
        background-color: #28a745;
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
        color: #32C36C;
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

        {{-- ========================== STEP 1 ========================== --}}
        <div class="form-section" data-step="1">
            <h5>Thông tin hồ sơ</h5>
            <div class="row">
                @if(isset($config) && is_array($config))
                    @foreach($config as $group)
                        @if(isset($group['group']) && !empty($group['fields']))
                            <div class="col-12">
                                <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:30px;">{{ $group['group'] }}</h5>
                            </div>

                            @foreach($group['fields'] as $field)
                            @if(isset($field['type']) && $field['type'] === 'row')
                                <div class="col-12">
                                    <div class="row g-3 mb-2">
                                        @foreach(($field['columns'] ?? []) as $column)
                                            @php
                                                $c_name = $column['name'] ?? \Illuminate\Support\Str::slug($column['label'] ?? 'field', '_');
                                                $c_label = $column['label'] ?? '';
                                                $c_required = isset($column['required']) && $column['required'];
                                                $c_attributes = $column['attributes'] ?? [];
                                            @endphp
                                            <div class="{{ 'col-md-' . ($column['col'] ?? '6') }}">
                                                {{-- Thêm class 'required' nếu trường là bắt buộc --}}
                                                <label for="{{ $c_name }}" class="form-label @if($c_required) required @endif">{{ $c_label }}</label>

                                                @switch($column['type'] ?? 'text')
                                                    @case('select')
                                                        <select
                                                            class="form-select"
                                                            name="{{ $c_name }}"
                                                            id="{{ $c_name }}"
                                                            @if($c_required) required @endif
                                                            {{-- Thêm các attributes động như data-* --}}
                                                            @foreach($c_attributes as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                                                        >
                                                            <option value="">-- Vui lòng chọn --</option>
                                                            {{-- Sửa lại để dùng key-value --}}
                                                            @foreach($column['options'] ?? [] as $key => $value)
                                                                <option value="{{ $key }}">{{ $value }}</option>
                                                            @endforeach
                                                        </select>
                                                        @break
                                                    @default
                                                        <input
                                                            type="{{ $column['type'] ?? 'text' }}"
                                                            class="form-control"
                                                            name="{{ $c_name }}"
                                                            id="{{ $c_name }}"
                                                            @if($c_required) required @endif
                                                            {{-- Thêm các attributes động --}}
                                                            @foreach($c_attributes as $attr => $val) {{ $attr }}="{{ $val }}" @endforeach
                                                        >
                                                @endswitch
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                @php
                                    $f_name = $field['name'] ?? \Illuminate\Support\Str::slug($field['label'] ?? 'field', '_');
                                    $f_label = $field['label'] ?? '';
                                    $f_required = isset($field['required']) && $field['required'];
                                @endphp
                                <div class="col-12 mb-3">
                                    <label class="form-label @if($f_required) required @endif">{{ $f_label }}</label>
                                    @switch($field['type'] ?? 'text')
                                        @case('radio')
                                            <div class="d-flex flex-wrap gap-4 mt-2">
                                                @foreach($field['options'] ?? [] as $index => $opt)
                                                    <div class="form-check">
                                                        <input class="" type="radio" name="{{ $f_name }}" id="{{ $f_name }}_{{ $index }}" value="{{ $opt }}" @if($f_required && $loop->first) required @endif>
                                                        <label class="" for="{{ $f_name }}_{{ $index }}">{{ $opt }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @break
                                        @default
                                            <input type="{{ $field['type'] ?? 'text' }}" class="form-control" name="{{ $f_name }}" id="{{ $f_name }}" @if($f_required) required @endif>
                                    @endswitch
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
                <button type="button" class="btn btn-primary next-step">Tiếp tục</button>
            </div>
        </div>

        {{-- ========================== STEP 2 ========================== --}}
        <div class="form-section hidden" data-step="2">
            <h5>Thành phần hồ sơ</h5>

            <div class="accordion mt-3" id="thanhPhanHoSoAccordion">
                @php
                    // Biến này để kiểm tra xem có bất kỳ hồ sơ nào cần nộp không
                    $hasRequiredFiles = false;
                @endphp

                @forelse($thanhPhanHoSos as $tenThanhPhan => $giayTos)
                    @php
                        // Lọc ra danh sách giấy tờ thực sự cần nộp (có số lượng > 0)
                        $requiredGiayTos = $giayTos->filter(function ($tp) {
                            return ($tp->soLuongBanChinh ?? 0) >= 1 || ($tp->soLuongBanSao ?? 0) >= 1;
                        });
                    @endphp

                    {{-- Chỉ hiển thị accordion item nếu có ít nhất 1 giấy tờ cần nộp trong nhóm đó --}}
                    @if($requiredGiayTos->isNotEmpty())
                        @php $hasRequiredFiles = true; @endphp
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $loop->index }}">
                                <button class="accordion-button @if(!$loop->first) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $loop->index }}">
                                    <strong>{{ $tenThanhPhan }}</strong>
                                    <span class="badge bg-primary rounded-pill ms-2">{{ $requiredGiayTos->count() }} giấy tờ cần nộp</span>
                                </button>
                            </h2>
                            <div id="collapse-{{ $loop->index }}" class="accordion-collapse collapse @if($loop->first) show @endif" aria-labelledby="heading-{{ $loop->index }}" data-bs-parent="#thanhPhanHoSoAccordion">
                                <div class="accordion-body p-0">
                                    {{-- Bảng chứa các giấy tờ bên trong accordion --}}
                                    <div class="table-responsive">
                                        <table class="table-service-cth table-bordered table-sm align-middle text-dark mb-0">
                                            <thead class="dark">
                                                <tr class="text-center">
                                                    <th style="width: 50px;">STT</th>
                                                    <th>Tên giấy tờ</th>
                                                    <th style="width: 100px;">Bản chính</th>
                                                    <th style="width: 100px;">Bản sao</th>
                                                    <th style="width: 30%;">Nộp file</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($requiredGiayTos as $index => $tp)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $tp->tenGiayTo }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-success">{{ $tp->soLuongBanChinh }}</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($tp->soLuongBanSao)
                                                                <span class="badge bg-info">{{ $tp->soLuongBanSao }}</span>
                                                            @else
                                                                <span class="text-muted">—</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{-- Thêm thuộc tính required cho input file --}}
                                                            <input type="file" name="taiLieu[{{ $tp->maGiayTo }}][]" class="form-control form-control-sm" multiple >
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    {{-- Xử lý trường hợp không có dữ liệu $thanhPhanHoSos --}}
                    @php $hasRequiredFiles = false; @endphp
                @endforelse

                {{-- Nếu sau khi lọc, không có bất kỳ giấy tờ nào cần nộp --}}
                @if(!$hasRequiredFiles)
                    <div class="alert alert-success text-center">
                        ✅ Không có giấy tờ nào cần nộp trực tuyến cho thủ tục này.
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary prev-step">Quay lại</button>
                <button type="button" class="btn btn-primary next-step">Tiếp tục</button>
            </div>

        </div>

        {{-- ========================== STEP 3 ========================== --}}
        <div class="form-section hidden" data-step="3">
            <h5>Thông tin phí, lệ phí</h5>

            {{-- Hình thức nhận kết quả --}}
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

            {{-- Phần hiển thị khi chọn "Nhận dịch vụ bưu chính" --}}
            <div id="buuChinhSection" class="mb-5" style="display: none;">
                {{-- Checkbox đăng ký --}}
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



                {{-- Thông tin liên hệ --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Tên</label>
                        <input type="text" name="ten_buu_chinh" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="sdt_buu_chinh" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email_buu_chinh" class="form-control">
                    </div>
                </div>

                {{-- Địa chỉ bưu cục đến nhận hồ sơ (hiển thị khi chọn checkbox nộp hồ sơ tại nhà) --}}
                <div id="diaChiNopHoSo" class="mb-3" style="display: none;">
                    <p class="mt-5 mb-1" style="color: #007bff;">Địa chỉ bưu cục đến nhận hồ sơ của người dân</p>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label required">Tỉnh/TP</label>
                            <select name="tinh_tp_nop_ho_so" class="form-select">
                                <option value="">-- Chọn Tỉnh/TP --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phường/Xã</label>
                            <input type="text" name="phuong_xa_nop_ho_so" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Địa chỉ chi tiết</label>
                            <input type="text" name="dia_chi_chi_tiet_nop_ho_so" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- Địa chỉ bưu cục gửi kết quả (hiển thị khi chọn checkbox nhận kết quả tại nhà) --}}
                <div id="diaChiNhanKetQua" class="mb-3" style="display: none;">
                    <p class="mt-5 mb-1" style="color:#007bff;">Địa chỉ bưu cục gửi kết quả đến người dân</p>
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label required">Tỉnh/TP nhận kết quả</label>
                            <select name="tinh_tp_nhan_ket_qua" class="form-select">
                                <option value="">-- Chọn Tỉnh/TP --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Phường/Xã nhận kết quả</label>
                            <input type="text" name="phuong_xa_nhan_ket_qua" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Địa chỉ chi tiết nơi nhận kết quả</label>
                            <input type="text" name="dia_chi_chi_tiet_nhan_ket_qua" class="form-control">
                        </div>
                    </div>
                </div>
                                {{-- Thông tin thu phí tại nhà --}}
                <div class="mt-4 mb-1">
                    <h6 class="mt-5">Thông tin thu phí hồ sơ/ trả về kết quả tại nhà</h6>
                    <p class="text-danger">(Nhân viên bưu chính sẽ thu phí trực tiếp tại nhà khi thu hồ sơ/trả kết quả)</p>
                </div>
            </div>

            {{-- Bảng lệ phí --}}
            <div class="mt-5 mb-1 ">
                <h5>Thông tin phí, lệ phí</h5>
                <div class="table-responsive rounded">
                    <table class="table table-bordered align-middle rounded" id="lePhiTable">
                        <thead class="bg-primary">
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
                                    <td>
                                        <input type="number" name="le_phi_so_luong[{{ $lePhi->maLePhi }}]" class="form-control form-control-sm so-luong" value="1" min="1" data-muc-le-phi="{{ $lePhi->soTien }}">
                                    </td>
                                    <td>
                                        <select name="muc_le_phi[{{ $lePhi->maLePhi }}]" class="form-select form-select-sm muc-le-phi">
                                            <option value="{{ $lePhi->soTien }}">{{ number_format($lePhi->soTien, 0, ',', '.') }} VNĐ</option>
                                        </select>
                                    </td>
                                    <td class="thanh-tien">{{ number_format($lePhi->soTien, 0, ',', '.') }} VNĐ</td>
                                    <td class="text-center">{{ $lePhi->batBuoc ?? 'Không' }}</td>
                                    <td class="small">{{ $lePhi->moTa ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        Chưa có thông tin lệ phí cho thủ tục này
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-primary rounded-bottom">
                            <tr>
                                <td colspan="3" class="text-end text-dark"><strong>Tổng</strong></td>
                                <td class="text-dark fw-bold" id="tongLePhi">
                                    @if($lePhis->isNotEmpty())
                                        {{ number_format($lePhis->sum('soTien'), 0, ',', '.') }} VNĐ
                                    @else
                                        0 VNĐ
                                    @endif
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Hình thức thanh toán --}}
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

            {{-- Hiển thị QR Code khi có số tiền > 0 --}}
{{-- File: .../nop-ho-so.blade.php --}}

<div id="qrPaymentSection" class="mt-4 mb-4" style="display: none;">
    <div class="card border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fa fa-qrcode"></i> Thanh toán qua QR Code</h5>
        </div>
        <div class="card-body text-center">
            <p class="text-muted mb-3">Vui lòng quét mã QR bên dưới để thanh toán</p>
            <div id="qrCodeContainer" class="mb-3">
                <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 300px;">
            </div>
            <div class="alert alert-info">
                <strong>Số tiền cần thanh toán:</strong>
                <span id="qrAmount" class="fw-bold text-primary"></span>
            </div>

            {{-- THÊM DÒNG NÀY VÀO --}}
            <div id="paymentStatus" class="mt-3 small text-center"></div>
            {{-- KẾT THÚC DÒNG CẦN THÊM --}}

            <input type="hidden" name="ma_giao_dich" id="maGiaoDich" value="">
        </div>
    </div>
</div>

            {{-- Checkbox xác nhận --}}
            <div class="mb-4">
                <div class="form-check">
                    <input class="" type="checkbox" name="xac_nhan_thong_tin" id="xacNhanThongTin" required>
                    <label class="" for="xacNhanThongTin">
                        Tôi chắc chắn rằng các thông tin khai báo trên là đúng sự thật và đồng ý chịu trách nhiệm trước pháp luật về lời khai trên.
                    </label>
                </div>
            </div>

            {{-- Đăng ký thông tin hoàn tiền --}}
            <div class="mt-5 mb-1">
                <h5>Đăng ký thông tin hoàn tiền</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Số tài khoản</label>
                        <input type="text" name="so_tai_khoan" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Chủ tài khoản</label>
                        <input type="text" name="chu_tai_khoan" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tên ngân hàng</label>
                        <input type="text" name="ten_ngan_hang" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Địa chỉ đơn vị hưởng thụ</label>
                        <input type="text" name="dia_chi_don_vi_huong_thu" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Chi nhánh ngân hàng</label>
                        <input type="text" name="chi_nhanh_ngan_hang" class="form-control">
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary prev-step">Quay lại</button>
                <div>
                    <button type="button" class="btn btn-primary" id="btnSubmitHoSo">Nộp hồ sơ</button>
                </div>
            </div>
        </div>

        {{-- ========================== STEP 4 ========================== --}}
        <div class="form-section hidden" data-step="4">
            @if(isset($isSuccess) && $isSuccess)
                <div class="text-center mb-4">
                    <div class="mb-3">
                        <i class="fa fa-check-circle text-success" style="font-size: 64px;"></i>
                    </div>
                    <h3 class="text-success mb-3">Nộp hồ sơ thành công!</h3>
                    <p class="text-muted">Mã hồ sơ của bạn: <strong class="text-dark">{{ $maHSXL ?? '' }}</strong></p>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">Thông tin người nộp</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Họ tên:</strong> {{ $hoSo->tenChuHoSo ?? ($dulieu['ho_ten'] ?? ($nguoiInfo->hoTen ?? '—')) }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Ngày sinh:</strong> {{ $dulieu['ngay_sinh'] ?? ($nguoiInfo->ngaySinh ? \Carbon\Carbon::parse($nguoiInfo->ngaySinh)->format('d/m/Y') : '—') }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Số CCCD:</strong> {{ $dulieu['so_cccd'] ?? ($dulieu['cccd'] ?? ($nguoiInfo->maCCCD ?? '—')) }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Nơi cấp:</strong> {{ $dulieu['noi_cap'] ?? ($dulieu['noi_cap_cccd'] ?? '—') }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Số điện thoại:</strong> {{ $hoSo->soDienThoai ?? ($dulieu['so_dien_thoai'] ?? ($nguoiInfo->soDienThoai ?? '—')) }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong> {{ $hoSo->email ?? ($dulieu['email'] ?? ($nguoiInfo->email ?? '—')) }}
                            </div>
                            <div class="col-md-12 mb-3">
                                <strong>Địa chỉ:</strong>
                                @php
                                    $diaChiParts = [];
                                    if (!empty($dulieu['dia_chi_chi_tiet'])) {
                                        $diaChiParts[] = $dulieu['dia_chi_chi_tiet'];
                                    }
                                    if (!empty($dulieu['phuong_xa'])) {
                                        $diaChiParts[] = $dulieu['phuong_xa'];
                                    }
                                    if (!empty($dulieu['tinh_thanh'])) {
                                        $diaChiParts[] = $dulieu['tinh_thanh'];
                                    }
                                    if (!empty($dulieu['quoc_gia'])) {
                                        $diaChiParts[] = $dulieu['quoc_gia'];
                                    }
                                    $diaChi = !empty($diaChiParts) ? implode(', ', $diaChiParts) : ($dulieu['dia_chi'] ?? ($nguoiInfo->noiThuongTru ?? '—'));
                                @endphp
                                {{ $diaChi }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">Thành phần hồ sơ đã nộp</h5>
                    </div>
                    <div class="card-body">
                        @php
                            // Tổng hợp tất cả giấy tờ đã nộp từ tất cả thành phần
                            $allGiayTosCoFile = collect();
                            $stt = 1;
                            $hasAnyFiles = false;
                        @endphp
                        @foreach($thanhPhanHoSos ?? [] as $tenThanhPhan => $giayTos)
                            @php
                                // Lọc chỉ những giấy tờ đã có file đã nộp
                                foreach ($giayTos as $giayTo) {
                                    // Đảm bảo maGiayTo được cast về int để so sánh
                                    $maGiayToInt = (int)$giayTo->maGiayTo;
                                    $files = $tailieuNop[$maGiayToInt] ?? collect();
                                    if ($files->isNotEmpty()) {
                                        $allGiayTosCoFile->push((object)[
                                            'stt' => $stt++,
                                            'maGiayTo' => $giayTo->maGiayTo,
                                            'tenGiayTo' => $giayTo->tenGiayTo,
                                            'soLuongBanChinh' => $giayTo->soLuongBanChinh,
                                            'soLuongBanSao' => $giayTo->soLuongBanSao,
                                            'files' => $files
                                        ]);
                                        $hasAnyFiles = true;
                                    }
                                }
                            @endphp
                        @endforeach
                        @if($allGiayTosCoFile->isNotEmpty())
                            <div class="table-responsive rounded">
                                <table class="table table-sm table-bordered table-hover">
                                    <thead class="table-dark" style="height: 40px;">
                                        <tr>
                                            <th style="width: 5%;" class="text-white">STT</th>
                                            <th style="width: 60%;"class="text-white">Tên giấy tờ</th>
                                            <th style="width: 35%;"class="text-white">File đã nộp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allGiayTosCoFile as $giayTo)
                                            <tr>
                                                <td class="text-center">{{ $giayTo->stt }}</td>
                                                <td>
                                                    <p style="text-align:justify;">{{ $giayTo->tenGiayTo }}</p>
                                                </td>
                                                <td>
                                                    @foreach($giayTo->files as $file)
                                                        <div class="mb-2 p-2 bg-light rounded border">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div class="d-flex align-items-center">
                                                                    @php
                                                                        $fileExtension = strtolower(pathinfo($file->tenTep, PATHINFO_EXTENSION));
                                                                        $fileIcon = 'fa-file';
                                                                        if (in_array($fileExtension, ['pdf'])) {
                                                                            $fileIcon = 'fa-file-pdf text-danger';
                                                                        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                                                            $fileIcon = 'fa-file-word text-primary';
                                                                        } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                                                            $fileIcon = 'fa-file-excel text-success';
                                                                        } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                                            $fileIcon = 'fa-file-image text-info';
                                                                        }
                                                                        $fileSize = $file->kichThuoc ?? 0;
                                                                        $fileSizeFormatted = $fileSize > 0 ? number_format($fileSize / 1024, 2) . ' KB' : '—';
                                                                    @endphp
                                                                    <i class="fa {{ $fileIcon }} me-2" style="font-size: 18px;"></i>
                                                                    <div>
                                                                        <a href="{{ asset('storage/' . $file->duongDan) }}" target="_blank" class="text-decoration-none fw-bold">
                                                                            {{ $file->tenTep }}
                                                                        </a>
                                                                        <div class="small text-muted">
                                                                            <i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($file->ngayTai)->format('d/m/Y H:i') }}
                                                                            <span class="ms-2">
                                                                                <i class="fa fa-hdd-o"></i> {{ $fileSizeFormatted }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <a href="{{ asset('storage/' . $file->duongDan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fa fa-download"></i> Tải xuống
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @if(!$hasAnyFiles)
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle"></i> Chưa có giấy tờ nào được nộp kèm file.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0 text-white">Thông tin lệ phí</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Loại lệ phí</th>
                                        <th>Số lượng</th>
                                        <th>Mức lệ phí</th>
                                        <th>Thành tiền</th>
                                        <th>Mô tả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($lePhiChiTiet ?? [] as $lePhi)
                                        <tr>
                                            <td>{{ $lePhi['loaiLePhi'] }}</td>
                                            <td>{{ $lePhi['soLuong'] }}</td>
                                            <td>{{ number_format($lePhi['mucLePhi'], 0, ',', '.') }} VNĐ</td>
                                            <td><strong>{{ number_format($lePhi['thanhTien'], 0, ',', '.') }} VNĐ</strong></td>
                                            <td class="small">{{ $lePhi['moTa'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Không có lệ phí</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-primary">
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tổng:</strong></td>
                                        <td colspan="2"><strong>{{ number_format($hoSo->lePhi ?? 0, 0, ',', '.') }} VNĐ</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mb-4">
                    <button type="button" class="btn btn-info" onclick="window.print()">
                        <i class="fa fa-print"></i> In phiếu nộp hồ sơ
                    </button>
                    <button type="button" class="btn btn-warning" id="btnBienLai">
                        <i class="fa fa-receipt"></i> Thông tin biên lai thanh toán
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-success">
                        <i class="fa fa-check"></i> Đồng ý
                    </a>
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

        requiredInputs.forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            // Hiển thị thông báo lỗi mặc định của trình duyệt cho trường không hợp lệ đầu tiên
            form.reportValidity();
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
                // Xóa class lỗi khi quay lại
                const currentSection = document.querySelector(`.form-section[data-step='${currentStep}']`);
                currentSection.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

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
            // if (tongTien > 0 && !isPaymentChecked) {
            //     alert('Vui lòng quét mã QR và thanh toán trước khi nộp hồ sơ. Hệ thống đang tự động kiểm tra thanh toán của bạn.');
            //     return;
            // }

            // Luôn submit về route nop-ho-so.submit
            form.action = '{{ route("nop-ho-so.submit", ["maTTHC" => $tthc->maTTHC ?? ""]) }}';
            form.submit();
        });
    }

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
