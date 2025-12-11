@extends('layouts.app')

@section('content')
<style>
/* ====== STEP WIZARD ====== */
.step-wizard {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 35px;
    gap: 10px;
}
.step {
    flex: 1;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}
.step .circle {
    width: 100%;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #6c757d;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.step .circle::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}
.step:hover .circle::before {
    left: 100%;
}
.step.active .circle {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: #fff;
    border-color: #0056b3;
    box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
    transform: translateY(-2px);
}
.step:hover:not(.active) .circle {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    border-color: #adb5bd;
    transform: translateY(-1px);
}
.step p {
    display: none;
}

/* ====== CARDS ====== */
.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15) !important;
    transform: translateY(-2px);
}
.card-header {
    border-bottom: none;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    padding: 1.5rem;
}

/* ====== ALERTS ====== */
.alert {
    border-radius: 12px;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.alert-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    border-left: 4px solid #007bff !important;
    color: #0d47a1 !important;
}
.alert-warning {
    background: linear-gradient(135deg, #e0f7fa 0%, #b2ebf2 100%) !important;
    border-left: 4px solid #17a2b8 !important;
    color: #006064 !important;
}

/* ====== FORM SECTION ====== */
.form-section {
    border: 1px solid #e3f2fd;
    background: #ffffff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.08);
    margin-bottom: 20px;
    display: none;
    transition: all 0.3s ease;
}
.form-section.active {
    display: block;
    animation: fadeInUp 0.5s ease;
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.form-section h5 {
    font-size: 20px;
    font-weight: 700;
    color: #007bff;
    border-bottom: 3px solid #007bff;
    padding-bottom: 10px;
    margin-bottom: 25px;
    position: relative;
}
.form-section h5::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 60px;
    height: 3px;
    background: #17a2b8;
}

/* ====== FORM CONTROLS ====== */
.form-control, .form-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    padding: 0.6rem 1rem;
    transition: all 0.3s ease;
}
.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}
label {
    font-weight: 600;
    margin-top: 10px;
    margin-bottom: 8px;
    color: #495057;
    font-size: 14px;
}

/* ====== BUTTONS ====== */
.btn {
    border-radius: 8px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    border: none;
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
}
.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
}
.btn-light {
    background: #ffffff;
    border: 2px solid #e9ecef;
}
.btn-light:hover {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
}

/* ====== STAR RATING ====== */
.star-rating {
    direction: rtl;
    display: inline-flex;
    font-size: 2.5rem;
    gap: 5px;
}
.star-rating input[type="radio"] {
    display: none;
}
.star-rating label {
    color: #dee2e6;
    cursor: pointer;
    padding: 0 5px;
    transition: all 0.3s ease;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}
.star-rating input[type="radio"]:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label {
    color: #17a2b8;
    transform: scale(1.1);
}

/* ====== TABLE ====== */
.table {
    border-radius: 10px;
    overflow: hidden;
}
.table thead {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    color: white;
}
.table-hover tbody tr:hover {
    background-color: #e3f2fd;
    transform: scale(1.01);
    transition: all 0.2s ease;
}

/* ====== BADGES ====== */
.badge {
    padding: 0.5em 1em;
    border-radius: 8px;
    font-weight: 600;
}

/* ====== UTILITY ====== */
.shadow-sm {
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1) !important;
}
hr {
    opacity: 0.2;
}
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Header --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: #007bff;">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fa fa-file-text-o"></i> {{ $hoSo->maHSXL }}
                        </h4>
                        <div class="d-flex gap-2">
                            @php
                                // Chỉ cho phép dừng xử lý nếu trạng thái không phải "Đã xử lý xong" (9) hoặc "Đã trả kết quả" (10)
                                $canStop = !in_array($hoSo->maTrangThai, [9, 10]);
                            @endphp
                            @if($canStop)
                                <form action="{{ route('profile.hoso.stop', $hoSo->maHSXL) }}" method="POST" 
                                      onsubmit="return confirm('Bạn có chắc chắn muốn dừng xử lý hồ sơ này không?');"
                                      style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fa fa-stop"></i> Dừng xử lý
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('profile') }}" class="btn btn-light btn-sm">
                                <i class="fa fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Processing Info --}}
                    @if($hoSo->maTrangThai >= 2)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="alert alert-info" style="background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;">
                                <strong><i class="fa fa-clock-o"></i> Thời gian xử lý</strong>
                                <hr style="margin: 5px 0; border-top-color: #faebcc;">
                                <p><i class="fa fa-calendar-check-o"></i> Ngày tiếp nhận: <strong>{{ $hoSo->ngayTiepNhan ? \Carbon\Carbon::parse($hoSo->ngayTiepNhan)->format('d/m/Y H:i:s') : '-' }}</strong></p>
                                <p><i class="fa fa-calendar"></i> Ngày hẹn trả: <strong>{{ $hoSo->ngayHenTra ? \Carbon\Carbon::parse($hoSo->ngayHenTra)->format('d/m/Y H:i:s') : '-' }}</strong></p>
                                @if($hoSo->ngayHenTra)
                                    @php
                                        $now = \Carbon\Carbon::now();   
                                        $henTra = \Carbon\Carbon::parse($hoSo->ngayHenTra);
                                        $diff = $now->diff($henTra);
                                        $isLate = $now->gt($henTra);
                                    @endphp
                                    <p><i class="fa fa-hourglass-half"></i> Thời gian còn lại:
                                        <strong class="{{ $isLate ? 'text-primary' : 'text-info' }}">
                                            {{ $isLate ? 'Quá hạn ' : 'Còn lại ' }}
                                            {{ $diff->days }} ngày {{ $diff->h }} giờ {{ $diff->i }} phút
                                        </strong>
                                    </p>
                                @endif
                                <p><i class="fa fa-globe"></i> Hình thức: <strong>{{ $hoSo->hinhThuc }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning" style="background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;">
                                <strong><i class="fa fa-info-circle"></i> Trạng thái</strong>
                                <hr style="margin: 5px 0; border-top-color: #faebcc;">
                                <p><i class="fa fa-tasks"></i> Trạng thái hiện tại:
                                    @php
                                        // Áp dụng màu giống như admin dashboard
                                        $badgeClass = match($hoSo->maTrangThai) {
                                            1 => 'bg-warning',        // Chờ tiếp nhận - vàng
                                            2 => 'bg-info',           // Được tiếp nhận - xanh dương
                                            3 => 'bg-danger',         // Không được tiếp nhận - đỏ
                                            4 => 'bg-primary',        // Đang xử lý - xanh dương đậm
                                            5 => 'bg-warning',        // Yêu cầu bổ sung giấy tờ - vàng
                                            6 => 'bg-info',           // Hồ sơ đã bổ sung giấy tờ - xanh dương
                                            7 => 'bg-danger',         // Công dân yêu cầu rút hồ sơ - đỏ
                                            8 => 'bg-danger',         // Dừng xử lý - đỏ
                                            9 => 'bg-success',        // Đã xử lý xong - xanh lá
                                            10 => 'bg-success',       // Đã trả kết quả - xanh lá
                                            11 => 'bg-warning',       // Nhận trực tiếp - vàng
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $hoSo->trangThai->tenTrangThai ?? 'Không xác định' }}
                                    </span>
                                </p>
                                @if($hoSo->ngayTra)
                                    <p><i class="fa fa-check-circle"></i> Ngày trả kết quả: <strong>{{ \Carbon\Carbon::parse($hoSo->ngayTra)->format('d/m/Y') }}</strong></p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Result Files Alert --}}
                    @if($hoSo->maTrangThai == 10 && !empty($hoSo->duongdanfileketqua))
                        @php
                            $files = json_decode($hoSo->duongdanfileketqua, true);
                        @endphp
                        @if(is_array($files) && count($files) > 0)
                            <div class="alert alert-success mt-3 shadow-sm">
                                <h5 class="text-success border-bottom pb-2 mb-3">
                                    <i class="fa fa-check-circle"></i> KẾT QUẢ XỬ LÝ HỒ SƠ
                                </h5>
                                <p class="mb-2">Hồ sơ của bạn đã được xử lý xong. Vui lòng tải về kết quả tại đây:</p>
                                <div class="d-flex flex-wrap mt-2">
                                    @foreach($files as $file)
                                        @php
                                            $filename = basename($file);
                                            $displayName = $filename;
                                            if (preg_match('/^\d+_[a-zA-Z0-9]+_(.*)$/', $filename, $matches)) {
                                                $displayName = $matches[1];
                                            }
                                        @endphp
                                        <a href="{{ asset($file) }}" target="_blank" class="btn btn-success text-white mb-2 mr-2 shadow-sm">
                                            <i class="fa fa-download me-1"></i> Tải kết quả: {{ $displayName }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Supplement Request Alert --}}
                    @if($hoSo->maTrangThai == 5 && $yeuCauBoSung)
                        <div class="alert alert-warning">
                            <h5><i class="fa fa-exclamation-triangle"></i> Hồ sơ yêu cầu bổ sung giấy tờ</h5>
                            <p>Vui lòng tải lên các giấy tờ sau để hồ sơ được xử lý tiếp:</p>
                            <ul>
                                @foreach($yeuCauBoSung['giayto_names'] ?? [] as $tenGiayTo)
                                    <li>{{ $tenGiayTo }}</li>
                                @endforeach
                            </ul>
                            @if(!empty($yeuCauBoSung['ghi_chu']))
                                <p><strong>Ghi chú:</strong> {{ $yeuCauBoSung['ghi_chu'] }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Rating Alert --}}
                    @if($canRate)
                        <div class="alert alert-success">
                            <h5>Hồ sơ đã hoàn thành - Đánh giá dịch vụ</h5>
                            <p><i class="fa fa-info-circle"></i> Bạn còn <strong>{{ $daysRemaining }}</strong> ngày để đánh giá chất lượng dịch vụ.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Step Wizard --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="step-wizard">
                        <div class="step active" data-step="1" onclick="showStep(1)">
                            <div class="circle">
                                <i class="fa fa-info-circle mr-2"></i> Thông tin hồ sơ
                            </div>
                        </div>
                        <div class="step" data-step="2" onclick="showStep(2)">
                            <div class="circle">
                                <i class="fa fa-folder-open mr-2"></i> Tài liệu đã nộp
                            </div>
                        </div>
                        <div class="step" data-step="3" onclick="showStep(3)">
                            <div class="circle">
                                <i class="fa fa-money mr-2"></i> Phí, lệ phí
                            </div>
                        </div>
                        @if($hoSo->maTrangThai == 5 && $yeuCauBoSung)
                        <div class="step" data-step="4" onclick="showStep(4)">
                            <div class="circle">
                                <i class="fa fa-upload mr-2"></i> Bổ sung giấy tờ
                            </div>
                        </div>
                        @endif
                        @if($canRate)
                        <div class="step" data-step="5" onclick="showStep(5)">
                            <div class="circle">
                                <i class="fa fa-star mr-2"></i> Đánh giá
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Step 1: Application Info --}}
                    <div class="form-section active" data-step="1">
                        <h5><i class="fa fa-info-circle"></i> Thông tin hồ sơ</h5>
                        
                        @php
                            $getValueByName = function($name, $data) {
                                if (is_array($data) && isset($data[$name])) {
                                    return $data[$name];
                                }
                                return null;
                            };
                        @endphp

                        @if(!empty($cauHinhForm) && isset($cauHinhForm[0]))
                            @foreach($cauHinhForm as $group)
                                @if(isset($group['group']) && !empty($group['fields']))
                                    <h6 class="mt-4 mb-3 text-primary border-bottom pb-2">{{ $group['group'] }}</h6>
                                    
                                    @foreach($group['fields'] as $field)
                                        @if(isset($field['type']) && $field['type'] === 'row')
                                            @if(isset($field['title']) && !empty($field['title']))
                                                <p class="font-italic text-muted">{{ $field['title'] }}</p>
                                            @endif
                                            
                                            <div class="row mb-3">
                                                @foreach(($field['columns'] ?? []) as $column)
                                                    @php
                                                        $c_name = $column['name'] ?? '';
                                                        $c_label = $column['label'] ?? '';
                                                        $c_value = $getValueByName($c_name, $dulieu);
                                                    @endphp
                                                    
                                                    <div class="col-md-{{ $column['col'] ?? '6' }}">
                                                        <div class="form-group">
                                                            <label class="font-weight-bold">{{ $c_label }}</label>
                                                            <div class="form-control-plaintext bg-light p-2 rounded">
                                                                @if($c_value !== null && $c_value !== '')
                                                                    {{ is_array($c_value) ? json_encode($c_value, JSON_UNESCAPED_UNICODE) : $c_value }}
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        @elseif(!empty($dulieu) && (isset($dulieu['hoTen']) || isset($dulieu['cccd'])))
                            {{-- Hiển thị thông tin cơ bản cho hồ sơ nhận trực tiếp --}}
                            <h6 class="mt-4 mb-3 text-primary border-bottom pb-2">Thông tin chủ hồ sơ</h6>
                            <div class="row mb-3">
                                @if(isset($dulieu['hoTen']))
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Họ và tên</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['hoTen'] }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($dulieu['ngaySinh']))
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Ngày sinh</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['ngaySinh'] }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($dulieu['gioiTinh']))
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Giới tính</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['gioiTinh'] }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($dulieu['cccd']))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Số CCCD/CMND</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['cccd'] }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($dulieu['ngayCap']))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Ngày cấp</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['ngayCap'] }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($dulieu['noiCap']))
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Nơi cấp</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['noiCap'] }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <h6 class="mt-4 mb-3 text-primary border-bottom pb-2">Thông tin liên hệ</h6>
                            <div class="row mb-3">
                                @if(isset($dulieu['email']))
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Email</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['email'] }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($dulieu['soDienThoai']))
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Số điện thoại</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['soDienThoai'] }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(isset($dulieu['diaChi']))
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Địa chỉ (Thường trú / Tạm trú)</label>
                                            <div class="form-control-plaintext bg-light p-2 rounded">{{ $dulieu['diaChi'] }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <h6 class="mt-4 mb-3 text-primary border-bottom pb-2">Thông tin thủ tục</h6>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tên thủ tục hành chính</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">
                                            @php
                                                $tthc = \App\Models\TTHC::find($hoSo->maTTHC);
                                            @endphp
                                            {{ $tthc ? $tthc->ten : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Hình thức nộp</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">Nhận trực tiếp</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">Không có thông tin form để hiển thị.</p>
                        @endif
                    </div>

                    {{-- Step 2: Documents --}}
                    <div class="form-section" data-step="2">
                        <h5><i class="fa fa-folder-open"></i> Thành phần hồ sơ</h5>
                        
                        @if($thanhPhanHoSos && $thanhPhanHoSos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="5%">STT</th>
                                            <th width="40%">Tên giấy tờ</th>
                                            <th width="15%">Bản chính</th>
                                            <th width="15%">Bản sao</th>
                                            <th width="25%">File đính kèm</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $stt = 1; @endphp
                                        @foreach($thanhPhanHoSos as $tenThanhPhan => $giayTos)
                                            @php
                                                // Check if group has any uploaded files
                                                $hasUploadedInGroup = false;
                                                foreach($giayTos as $gt) {
                                                    if($taiLieu->where('maGiayTo', $gt->maGiayTo)->count() > 0) {
                                                        $hasUploadedInGroup = true;
                                                        break;
                                                    }
                                                }
                                            @endphp

                                            @if($hasUploadedInGroup)
                                                <tr class="bg-light">
                                                    <td colspan="5" class="font-weight-bold">{{ $tenThanhPhan }}</td>
                                                </tr>
                                                @foreach($giayTos as $gt)
                                                    @php
                                                        // Find uploaded files for this document type
                                                        $uploadedFiles = $taiLieu->where('maGiayTo', $gt->maGiayTo);
                                                        
                                                        // Get quantity requirements
                                                        $tpg = DB::table('thanhphangiayto')
                                                            ->where('maThanhPhan', DB::table('thanhphanhoso')->where('tenThanhPhan', $tenThanhPhan)->value('maThanhPhan'))
                                                            ->where('maGiayTo', $gt->maGiayTo)
                                                            ->first();
                                                    @endphp
                                                    
                                                    @if($uploadedFiles->count() > 0)
                                                        <tr>
                                                            <td class="text-center">{{ $stt++ }}</td>
                                                            <td>{{ $gt->tenGiayTo }}</td>
                                                            <td class="text-center">{{ $tpg->soLuongBanChinh ?? 0 }}</td>
                                                            <td class="text-center">{{ $tpg->soLuongBanSao ?? 0 }}</td>
                                                            <td>
                                                                @foreach($uploadedFiles as $file)
                                                                    <div class="mb-1">
                                                                        <a href="{{ asset('storage/' . $file->duongDan) }}" target="_blank" class="text-primary">
                                                                            <i class="fa fa-paperclip"></i> {{ $file->tenTep }}
                                                                        </a>
                                                                        <small class="text-muted">({{ number_format($file->kichThuoc / 1024, 2) }} KB)</small>
                                                                    </div>
                                                                @endforeach
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @elseif($taiLieu && $taiLieu->count() > 0)
                            {{-- Fallback if no structure but has files --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên file</th>
                                            <th>Kích thước</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($taiLieu as $idx => $tl)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $tl->tenTep }}</td>
                                                <td>{{ number_format($tl->kichThuoc / 1024, 2) }} KB</td>
                                                <td>
                                                    <a href="{{ asset('storage/' . $tl->duongDan) }}" target="_blank" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-download"></i> Tải xuống
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Chưa có tài liệu nào được nộp.</p>
                        @endif
                    </div>

                    {{-- Step 3: Fees --}}
                    <div class="form-section" data-step="3">
                        <h5><i class="fa fa-money"></i> Phí, lệ phí</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Tổng lệ phí</h6>
                                <div class="alert alert-success">
                                    <h4 class="mb-0">{{ number_format($hoSo->lePhi ?? 0, 0, ',', '.') }} VNĐ</h4>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Hình thức nhận kết quả</h6>
                                <p class="font-weight-bold">{{ $hoSo->hinhThuc ?? 'Chưa xác định' }}</p>
                                @if($hoSo->thongTinTra)
                                    <p><strong>Thông tin trả:</strong> {{ $hoSo->thongTinTra }}</p>
                                @endif
                            </div>
                        </div>

                        <h6>Lịch sử thanh toán</h6>
                        @if($lichSuThanhToan && $lichSuThanhToan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Thời gian</th>
                                            <th>Số tiền</th>
                                            <th>Nội dung</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lichSuThanhToan as $ls)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($ls->ngayGD)->format('d/m/Y H:i:s') }}</td>
                                                <td class="text-right font-weight-bold">{{ number_format($ls->soTien, 0, ',', '.') }} VNĐ</td>
                                                <td>{{ $ls->moTa }}</td>
                                                <td>
                                                    <span class=" badge-{{ $ls->trangThai == 'Thành công' ? 'success' : 'warning' }}">
                                                        {{ $ls->trangThai }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted font-italic">Chưa có lịch sử thanh toán.</p>
                        @endif
                    </div>

                    {{-- Step 4: Supplement Upload --}}
                    @if($hoSo->maTrangThai == 5 && $yeuCauBoSung)
                    <div class="form-section" data-step="4">
                        <h5><i class="fa fa-upload"></i> Nộp bổ sung giấy tờ</h5>
                        
                        <form action="{{ route('profile.application.upload-supplement', $hoSo->maHSXL) }}" method="POST" enctype="multipart/form-data" id="supplementForm">
                            @csrf
                            <div id="file-upload-container">
                                @foreach($yeuCauBoSung['giayto'] ?? [] as $index => $maGiayTo)
                                    <div class="file-upload-item mb-3 p-3 border rounded bg-light">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="font-weight-bold">{{ $yeuCauBoSung['giayto_names'][$index] ?? 'Giấy tờ' }}</label>
                                                <input type="hidden" name="maGiayTo[]" value="{{ $maGiayTo }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label>File (PDF, JPG, PNG - tối đa 10MB)</label>
                                                <input type="file" name="files[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="text-right mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa fa-upload"></i> Nộp bổ sung
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Step 5: Rating --}}
                    @if($canRate || $existingRating)
                    <div class="form-section" data-step="5">
                        <h5><i class="fa fa-star"></i> Đánh giá chất lượng dịch vụ</h5>
                        
                        @if($existingRating)
                            <div class="alert alert-success">
                                <h5><i class="fa fa-check-circle"></i> Bạn đã đánh giá dịch vụ này</h5>
                                <div class="mt-3">
                                    <div class="star-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star {{ $i <= $existingRating->soDiem ? 'text-info' : 'text-muted' }}" style="font-size: 24px;"></i>
                                        @endfor
                                    </div>
                                    <p class="mt-2"><strong>Ngày đánh giá:</strong> {{ \Carbon\Carbon::parse($existingRating->ngayDanhGia)->format('d/m/Y H:i') }}</p>
                                    @if($existingRating->nhanXet)
                                        <p><strong>Nhận xét:</strong> {{ $existingRating->nhanXet }}</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <p>Hồ sơ của bạn đã được xử lý xong. Vui lòng đánh giá chất lượng dịch vụ!</p>
                                <p><i class="fa fa-clock-o"></i> Bạn còn <strong>{{ $daysRemaining }}</strong> ngày để đánh giá.</p>
                            </div>
                            
                            <form id="ratingForm">
                                @csrf
                                <div class="form-group">
                                    <label class="font-weight-bold">Đánh giá của bạn:</label>
                                    <div class="star-rating">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" id="star{{ $i }}" name="soDiem" value="{{ $i }}" required>
                                            <label for="star{{ $i }}" title="{{ $i }} sao">
                                                <i class="fa fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="font-weight-bold">Nhận xét (tùy chọn):</label>
                                    <textarea name="nhanXet" class="form-control" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về dịch vụ..."></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-paper-plane"></i> Gửi đánh giá
                                </button>
                            </form>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Step navigation
function showStep(step) {
    // Hide all sections
    document.querySelectorAll('.form-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Remove active from all steps
    document.querySelectorAll('.step').forEach(s => {
        s.classList.remove('active');
    });
    
    // Show selected section
    const section = document.querySelector(`.form-section[data-step="${step}"]`);
    if (section) {
        section.classList.add('active');
    }
    
    // Add active to selected step
    const stepElement = document.querySelector(`.step[data-step="${step}"]`);
    if (stepElement) {
        stepElement.classList.add('active');
    }
}

// Rating form submission
document.addEventListener('DOMContentLoaded', function() {
    const ratingForm = document.getElementById('ratingForm');
    if (ratingForm) {
        ratingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const soDiem = document.querySelector('input[name="soDiem"]:checked');
            if (!soDiem) {
                alert('Vui lòng chọn số sao đánh giá');
                return;
            }
            
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('soDiem', soDiem.value);
            formData.append('nhanXet', document.querySelector('textarea[name="nhanXet"]').value);
            
            fetch('{{ route("profile.application.rate", $hoSo->maHSXL) }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            });
        });
    }
});
</script>
@endsection
