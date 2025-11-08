@extends('layouts.app')

@section('content')
<style>
    /* ====== BREADCRUMB ====== */
    .breadcrumb {
        font-size: 14px;
        margin-bottom: 15px;
        color: #666;
    }
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
        <a href="/">Trang chủ</a> →
        <a href="#">Dịch vụ công trực tuyến</a> →
        <strong>{{ $tthc->tenTTHC ?? 'Đăng ký kết hôn' }}</strong>
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
    <form id="nopHoSoForm" method="POST" action="#" enctype="multipart/form-data" novalidate>
        @csrf

        {{-- ========================== STEP 1 ========================== --}}
        <div class="form-section" data-step="1">
            <h5>Thông tin hồ sơ</h5>
            <div class="row">
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
                    <div class="col-4">
                        <label class="form-label required"><h5>Hình thức nhận kết quả</h5></label>
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
                <div class="mt-4 mb-4">
                    <h6 class="mt-5">Thông tin thu phí hồ sơ/ trả về kết quả tại nhà</h6>
                    <p class="text-danger">(Nhân viên bưu chính sẽ thu phí trực tiếp tại nhà khi thu hồ sơ/trả kết quả)</p>
                </div>
            </div>

            {{-- Bảng lệ phí --}}
            <div class="my-5 py-5 ">
                <label class="mb-3"><h5>Thông tin phí, lệ phí</h5></label>
                <div class="table-responsive rounded">
                    <table class="table table-bordered align-middle rounded" id="lePhiTable">
                        <thead class="bg-primary">
                            <tr>
                                <th class="text-dark">Số lượng>Loại lệ phí</th>
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
            <div class="my-5 pt-5 border-top">
                <div class="row">
                    <div class="col-8">
                        <label class="form-label required" ><h5>Chọn hình thức thanh toán</h5></label>
                        <select name="hinh_thuc_thanh_toan" class="form-select mt-2" required>
                            <option value="">-- Chọn hình thức --</option>
                            <option value="Thanh toán trực tuyến payment">Thanh toán trực tuyến payment</option>
                            <option value="Thanh toán trực tiếp">Thanh toán trực tiếp</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Đề nghị xuất biên lai --}}
            <div class="mb-4">
                <label class="form-label"><strong>Đề nghị cá nhân/doanh nghiệp/cơ sở lựa chọn:</strong></label>
                <div class="mt-2">
                    <div class="form-check">
                        <input class="" type="radio" name="xuat_bien_lai" id="bien_lai_ca_nhan" value="Xuất biên lai cho cá nhân" checked>
                        <label class="" for="bien_lai_ca_nhan">Xuất biên lai cho cá nhân</label>
                    </div>
                    <div class="form-check">
                        <input class="" type="radio" name="xuat_bien_lai" id="bien_lai_doanh_nghiep" value="Xuất biên lai cho doanh nghiệp/cơ sở">
                        <label class="" for="bien_lai_doanh_nghiep">Xuất biên lai cho doanh nghiệp/cơ sở</label>
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
            <div class="my-5 py-5 border-top">
                <label class="mb-3"><strong>Đăng ký thông tin hoàn tiền</strong></label>
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
                <button type="button" class="btn btn-primary next-step">Tiếp tục</button>
                <input type="submit">
            </div>
        </div>

        {{-- ========================== STEP 4 ========================== --}}
        <div class="form-section hidden" data-step="4">
            <h5>Xác nhận và nộp hồ sơ</h5>
            {{-- ... Nội dung Step 4 không đổi ... --}}
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
        }
    });

    // Khởi tạo tính toán lệ phí khi trang load
    tinhThanhTien();
});
</script>
@endsection
