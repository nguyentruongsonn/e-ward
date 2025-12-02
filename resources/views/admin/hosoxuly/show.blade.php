@extends('admin.layout')

@section('title', 'Chi tiết hồ sơ')

@section('content')
@php
    // Define read-only status: 9 (Processed), 10 (Returned Result)
    $isReadOnly = in_array($hoSo->maTrangThai, [9, 10]);
@endphp
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3>{{ $hoSo->maHSXL }}</h3>

                    </header>


                    <div class="panel-body">
                        <div class="mt-2 ">
                            <a href="{{ route('admin.hosoxuly.index') }}" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Quay lại danh sách
                            </a>
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#sendMailModal">
                                <i class="fa fa-envelope"></i> Gửi mail cho chủ hồ sơ
                            </button>
                            @if($mailHistory && $mailHistory->count() > 0)
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#mailHistoryModal" style="margin-left: 10px;">
                                    <i class="fa fa-history"></i> Xem lịch sử mail ({{ $mailHistory->count() }})
                                </button>
                            @endif

                            @php
                                $user = Illuminate\Support\Facades\Auth::user();
                            @endphp

                            {{-- Cán bộ một cửa actions --}}
                            @if($user->vaiTro === 'Cán bộ một cửa' || $user->vaiTro === 'Quản trị viên')
                                {{-- Tiếp nhận hồ sơ (status = 1 hoặc 11) --}}
                                @if(in_array($hoSo->maTrangThai, [1, 11]))
                                    @if($hoSo->maTrangThai == 11)
                                        <button type="button" class="btn btn-primary btn-sm ml-2" onclick="$('#component-form').submit()">
                                            <i class="fa fa-check"></i> Xác nhận hoàn thành
                                        </button>
                                    @else
                                        <form action="{{ route('admin.hosoxuly.tiepnhan-action', $hoSo->maHSXL) }}" method="POST" class="d-inline ml-2">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-check"></i> Tiếp nhận
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                {{-- Chuyển thụ lý (status = 2: Đã tiếp nhận) --}}
                                @if($hoSo->maTrangThai == 2 && !$hoSo->nguoiDuyet)
                                    <button type="button" class="btn btn-info btn-sm ml-2" data-toggle="modal" data-target="#chuyenThuLyModal">
                                        <i class="fa fa-check-square-o"></i> Xác nhận hoàn thành
                                    </button>
                                @endif

                                {{-- Yêu cầu bổ sung giấy tờ --}}
                                @if(in_array($hoSo->maTrangThai, [1, 2]) && !in_array($hoSo->maTrangThai, [9, 10]))
                                    <button type="button" class="btn btn-info btn-sm ml-2" data-toggle="modal" data-target="#yeuCauBoSungModal">
                                        <i class="fa fa-plus-circle"></i> Yêu cầu bổ sung
                                    </button>
                                @endif

                                {{-- Trả kết quả (status = 9: Đã xử lý xong) --}}
                                @if($hoSo->maTrangThai == 9)
                                    <form action="{{ route('admin.hosoxuly.tra-ketqua', $hoSo->maHSXL) }}" method="POST" class="d-inline ml-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa fa-check-circle"></i> Trả kết quả
                                        </button>
                                    </form>
                                @endif

                                {{-- In giấy xác nhận tiếp nhận hồ sơ --}}
                                @if($hoSo->maTrangThai >= 1)
                                    <a href="{{ route('admin.hosoxuly.confirmation', $hoSo->maHSXL) }}"
                                       target="_blank"
                                       class="btn btn-default btn-sm ml-2">
                                        <i class="fa fa-print"></i> In giấy xác nhận
                                    </a>
                                @endif
                            @endif

                            {{-- Cán bộ thụ lý actions --}}
                            @if($user->vaiTro === 'Cán bộ thụ lý' || $user->vaiTro === 'Quản trị viên')
                                {{-- Chuyển lãnh đạo (status = 2: Đã tiếp nhận và đã qua một cửa) --}}
                                @if($hoSo->maTrangThai == 2 && $hoSo->nguoiTiepNhan)
                                    <button type="button" class="btn btn-warning btn-sm ml-2" data-toggle="modal" data-target="#chuyenLanhDaoModal">
                                        <i class="fa fa-check-square-o"></i> Chuyển lãnh đạo
                                    </button>
                                @endif

                                {{-- In giấy xác nhận chuyển lãnh đạo khi hồ sơ đã ở trạng thái chờ lãnh đạo duyệt (maTrangThai = 4) --}}
                                @if($hoSo->maTrangThai == 4)
                                    <a href="{{ route('admin.hosoxuly.leader-transfer', $hoSo->maHSXL) }}"
                                       target="_blank"
                                       class="btn btn-default btn-sm ml-2">
                                        <i class="fa fa-print"></i> In giấy chuyển lãnh đạo
                                    </a>
                                @endif

                                {{-- Yêu cầu bổ sung giấy tờ --}}
                                @if(in_array($hoSo->maTrangThai, [2, 4]) && !in_array($hoSo->maTrangThai, [9, 10]))
                                    <button type="button" class="btn btn-info btn-sm ml-2" data-toggle="modal" data-target="#yeuCauBoSungModal">
                                        <i class="fa fa-plus-circle"></i> Yêu cầu bổ sung
                                    </button>
                                @endif
                            @endif

                            {{-- Lãnh đạo actions --}}
                            @if($user->vaiTro === 'Lãnh đạo' || $user->vaiTro === 'Quản trị viên')
                                {{-- Phê duyệt (status = 4: Đang xử lý) --}}
                                @if($hoSo->maTrangThai == 4 && !$hoSo->nguoiDuyet)
                                    <button type="button" class="btn btn-success btn-sm ml-2" data-toggle="modal" data-target="#pheDuyetModal">
                                        <i class="fa fa-check-circle"></i> Phê duyệt
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm ml-1" data-toggle="modal" data-target="#yeuCauXuLyLaiModal">
                                        <i class="fa fa-undo"></i> Yêu cầu xử lý lại
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm ml-1" data-toggle="modal" data-target="#traLaiModal">
                                        <i class="fa fa-times"></i> Dừng xử lý
                                    </button>
                                @endif

                                {{-- In giấy xác nhận phê duyệt khi hồ sơ đã được xử lý xong (maTrangThai = 9) --}}
                                @if($hoSo->maTrangThai == 9)
                                    <a href="{{ route('admin.hosoxuly.leader-approval', $hoSo->maHSXL) }}"
                                       target="_blank"
                                       class="btn btn-default btn-sm ml-2">
                                        <i class="fa fa-print"></i> In giấy phê duyệt
                                    </a>
                                @endif
                            @endif
                        </div>
                        {{-- Hiển thị thông tin xử lý khi đã tiếp nhận --}}
                        @if($hoSo->maTrangThai >= 2)
                        <div class="row  mb-4" style="margin-top: 20px;">
                            <div class="col-md-6">
                                <div class="alert alert-info" style="background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;">
                                    <strong><i class="fa fa-clock-o"></i> Thời gian xử lý</strong>
                                    <hr style="margin: 5px 0; border-top-color: #faebcc;">
                                    <p><i class="fa fa-calendar-check-o"></i> Ngày tiếp nhận: <strong>{{ $hoSo->ngayTiepNhan ? \Carbon\Carbon::parse($hoSo->ngayTiepNhan)->format('d/m/Y H:i:s') : '-' }}</strong></p>
                                    <p><i class="fa fa-calendar"></i> Ngày hẹn trả: <strong>{{ $hoSo->ngayHenTra ? \Carbon\Carbon::parse($hoSo->ngayHenTra)->format('d/m/Y H:i:s') : '-' }}</strong></p>
                                    <p><i class="fa fa-hourglass-half"></i> Thời gian còn lại:
                                        @if($hoSo->ngayHenTra)
                                            @php
                                                $now = \Carbon\Carbon::now();
                                                $henTra = \Carbon\Carbon::parse($hoSo->ngayHenTra);
                                                $diff = $now->diff($henTra);
                                                $isLate = $now->gt($henTra);
                                            @endphp
                                            <strong class="{{ $isLate ? 'text-danger' : 'text-success' }}">
                                                {{ $isLate ? 'Quá hạn ' : 'Còn lại ' }}
                                                {{ $diff->days }} ngày {{ $diff->h }} giờ {{ $diff->i }} phút
                                            </strong>
                                        @else
                                            -
                                        @endif
                                    </p>
                                    <p><i class="fa fa-globe"></i> Hình thức tiếp nhận: <strong>{{ $hoSo->hinhThuc == 'Nhận trực tuyến' ? 'Trực tuyến' : 'Trực tiếp' }}</strong></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning" style="background-color: #fcf8e3; border-color: #faebcc; color: #8a6d3b;">
                                    <strong><i class="fa fa-user"></i> Người xử lý</strong>
                                    <hr style="margin: 5px 0; border-top-color: #faebcc;">
                                    <p><i class="fa fa-tasks"></i> Công việc: <strong>Tiếp nhận hồ sơ</strong></p>
                                    <p><i class="fa fa-user-circle"></i> Người tiếp nhận:
                                        <strong>
                                            @php
                                                $nguoiTiepNhan = \App\Models\Nguoi::find($hoSo->nguoiTiepNhan);
                                                echo $nguoiTiepNhan ? $nguoiTiepNhan->hoTen : '-';
                                            @endphp
                                        </strong>
                                    </p>
                                    <p><i class="fa fa-user-secret"></i> Người phê duyệt:
                                        <strong>
                                            @php
                                                $nguoiDuyet = \App\Models\Nguoi::find($hoSo->nguoiDuyet);
                                                echo $nguoiDuyet ? $nguoiDuyet->hoTen : 'Chưa có';
                                            @endphp
                                        </strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

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
                                height: 45px;
                                border-radius: 8px;
                                background-color: #f0f2f5;
                                color: #6c757d;
                                font-weight: 600;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 14px;
                                border: 1px solid #e9ecef;
                                transition: all 0.3s ease;
                            }
                            .step.active .circle {
                                background-color: #df7614;
                                color: #fff;
                                border-color: #df7614;
                                box-shadow: 0 4px 6px rgba(223, 118, 20, 0.2);
                            }
                            .step:hover:not(.active) .circle {
                                background-color: #e9ecef;
                                border-color: #dee2e6;
                            }
                            .step p {
                                display: none; /* Hide old labels as text is now inside the box */
                            }
                            /* ====== FORM SECTION ====== */
                            .form-section, .info-section {
                                border: 1px solid #eee;
                                background: #fff;
                                padding: 25px;
                                border-radius: 10px;
                                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                                margin-bottom: 20px;
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
                        </style>

                        {{-- STEP WIZARD --}}
                        <div class="step-wizard mb-4">
                            <div class="step active" data-step="1" onclick="showStep(1)">
                                <div class="circle">
                                    <i class="fa fa-info-circle mr-2"></i> Thông tin hồ sơ
                                </div>
                            </div>
                            <div class="step" data-step="2" onclick="showStep(2)">
                                <div class="circle">
                                    <i class="fa fa-folder-open mr-2"></i> Thành phần hồ sơ
                                </div>
                            </div>
                            <div class="step" data-step="3" onclick="showStep(3)">
                                <div class="circle">
                                    <i class="fa fa-money mr-2"></i> Phí, lệ phí
                                </div>
                            </div>
                            <div class="step" data-step="4" onclick="showStep(4)">
                                <div class="circle">
                                    <i class="fa fa-user mr-2"></i> Người nộp
                                </div>
                            </div>
                        </div>

                        @php
                            // Lấy cấu hình form từ database
                            $form = \Illuminate\Support\Facades\DB::table('formtructuyen')->where('maTTHC', $hoSo->maTTHC)->first();
                            $cauHinhForm = $form ? json_decode($form->cauHinhForm, true) : [];
                            $dulieuRaw = is_array($hoSo->dulieu) ? $hoSo->dulieu : json_decode($hoSo->dulieu ?? '{}', true);

                            // Xử lý cấu trúc dulieu mới (có thể là object với cauHinhForm và payload, hoặc là cauHinhForm đã merge, hoặc là payload gốc)
                            $cauHinhFormMerged = null;
                            $payload = null;

                            if (isset($dulieuRaw['cauHinhForm']) && isset($dulieuRaw['payload'])) {
                                // Cấu trúc mới: có cả cauHinhForm và payload
                                $cauHinhFormMerged = $dulieuRaw['cauHinhForm'];
                                $payload = $dulieuRaw['payload'];
                            } elseif (isset($dulieuRaw[0]) && isset($dulieuRaw[0]['group'])) {
                                // dulieu là cauHinhForm đã merge (cấu trúc cũ)
                                $cauHinhFormMerged = $dulieuRaw;
                                $payload = [];
                            } else {
                                // dulieu là payload gốc
                                $payload = $dulieuRaw;
                            }

                            // Sử dụng payload cho các thông tin lệ phí, thanh toán
                            $dulieu = $payload;

                            // Hàm đệ quy để lấy giá trị từ dulieu dựa trên name
                            $getValueByName = function($name, $dulieu) {
                                if (isset($dulieu[$name])) {
                                    return $dulieu[$name];
                                }
                                // Thử với các biến thể
                                foreach ($dulieu as $key => $value) {
                                    if (trim($key) === trim($name)) {
                                        return $value;
                                    }
                                }
                                // Thử với slug
                                $slugName = \Illuminate\Support\Str::slug($name, '_');
                                if (isset($dulieu[$slugName])) {
                                    return $dulieu[$slugName];
                                }
                                return null;
                            };

                            // Lấy thành phần hồ sơ để hiển thị
                            $thanhPhanHoSos = \Illuminate\Support\Facades\DB::table('thanhphanhoso as tph')
                                ->leftJoin('thanhphangiayto as tpg', 'tpg.maThanhPhan', '=', 'tph.maThanhPhan')
                                ->leftJoin('giayto as gt', 'gt.maGiayTo', '=', 'tpg.maGiayTo')
                                ->where('tph.maTTHC', $hoSo->maTTHC)
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
                        @endphp

                        {{-- ========================== STEP 1: THÔNG TIN HỒ SƠ ========================== --}}
                        <div class="form-section" data-step="1">
                            <h5>Thông tin hồ sơ</h5>
                            
                            {{-- FORM CHỈNH SỬA CHO TRẠNG THÁI 11 (NHẬN TRỰC TIẾP) --}}
                            @if($hoSo->maTrangThai == 11 && ($user->vaiTro === 'Cán bộ một cửa' || $user->vaiTro === 'Quản trị viên'))
                                <form action="{{ route('admin.hosoxuly.update-general-info', $hoSo->maHSXL) }}" method="POST">
                                    @csrf
                                    
                                    {{-- Group 1: Thông tin chủ hồ sơ --}}
                                    <div class="col-12">
                                        <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:10px;">Thông tin chủ hồ sơ</h5>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" name="hoTen" class="form-control" value="{{ $getValueByName('hoTen', $dulieu) ?? $hoSo->tenChuHoSo }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Ngày sinh <span class="text-danger">*</span></label>
                                            <input type="date" name="ngaySinh" class="form-control" value="{{ $getValueByName('ngaySinh', $dulieu) }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Giới tính</label>
                                            <select name="gioiTinh" class="form-control">
                                                <option value="Nam" {{ ($getValueByName('gioiTinh', $dulieu) == 'Nam') ? 'selected' : '' }}>Nam</option>
                                                <option value="Nữ" {{ ($getValueByName('gioiTinh', $dulieu) == 'Nữ') ? 'selected' : '' }}>Nữ</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Số CCCD/CMND <span class="text-danger">*</span></label>
                                            <input type="text" name="cccd" class="form-control" value="{{ $getValueByName('cccd', $dulieu) }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Ngày cấp</label>
                                            <input type="date" name="ngayCap" class="form-control" value="{{ $getValueByName('ngayCap', $dulieu) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Nơi cấp</label>
                                            <input type="text" name="noiCap" class="form-control" value="{{ $getValueByName('noiCap', $dulieu) }}" placeholder="Cục Cảnh sát ĐKQL cư trú...">
                                        </div>
                                    </div>

                                    {{-- Group 2: Thông tin liên hệ --}}
                                    <div class="col-12">
                                        <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:30px;">Thông tin liên hệ</h5>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $getValueByName('email', $dulieu) ?? $hoSo->email }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Số điện thoại <span class="text-danger">*</span></label>
                                            <input type="text" name="soDienThoai" class="form-control" value="{{ $getValueByName('soDienThoai', $dulieu) ?? $hoSo->soDienThoai }}" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Địa chỉ (Thường trú / Tạm trú) <span class="text-danger">*</span></label>
                                            <input type="text" name="diaChi" class="form-control" value="{{ $getValueByName('diaChi', $dulieu) }}" required>
                                        </div>
                                    </div>

                                    {{-- Group 3: Thông tin thủ tục --}}
                                    <div class="col-12">
                                        <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:30px;">Thông tin thủ tục</h5>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-6">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Tên thủ tục hành chính</label>
                                            <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                {{ $getValueByName('tenTTHC', $dulieu) }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Hình thức nộp</label>
                                            <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                Nhận trực tiếp
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Lưu thông tin
                                        </button>
                                    </div>
                                </form>
                                <hr>
                            @endif

                            <div class="row">
                                @php
                                    // Sử dụng cauHinhForm đã merge nếu có, nếu không thì dùng cauHinhForm gốc
                                    $displayForm = $cauHinhFormMerged ?? ($cauHinhForm ?? []);
                                @endphp

                                @if(!empty($displayForm) && isset($displayForm[0]))
                                    @foreach($displayForm as $group)
                                        @if(isset($group['group']) && !empty($group['fields']))
                                            {{-- Tiêu đề nhóm lớn (Ví dụ: Thông tin người nộp) --}}
                                            <div class="col-12">
                                                <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:30px;">{{ $group['group'] }}</h5>
                                            </div>

                                            @foreach($group['fields'] as $field)
                                                {{-- TRƯỜNG HỢP 1: LÀ DÒNG CHÚ THÍCH (CONTENT) ĐỨNG RIÊNG --}}
                                                @if(isset($field['type']) && $field['type'] === 'content')
                                                    <div class="col-12 {{ $field['class'] ?? '' }}">
                                                        <div class="alert alert-info mb-3">
                                                            {!! $field['content'] ?? '' !!}
                                                        </div>
                                                    </div>

                                                {{-- TRƯỜNG HỢP 2: LÀ DÒNG NHẬP LIỆU (ROW) --}}
                                                @elseif(isset($field['type']) && $field['type'] === 'row')
                                                    <div class="col-12">
                                                        {{-- Tiêu đề của Row --}}
                                                        @if(isset($field['title']) && !empty($field['title']))
                                                            <p class="mt-3 mb-2" style="color: black; font-size: 17px; font-style: italic;">{{ $field['title'] }}</p>
                                                        @endif

                                                        <div class="row g-3 mb-2">
                                                            @foreach(($field['columns'] ?? []) as $column)
                                                                @php
                                                                    $c_name = $column['name'] ?? \Illuminate\Support\Str::slug($column['label'] ?? 'field', '_');
                                                                    $c_label = $column['label'] ?? '';
                                                                    // Nếu là merged form, lấy value trực tiếp từ column
                                                                    if ($cauHinhFormMerged && isset($column['value'])) {
                                                                        $c_value = $column['value'];
                                                                    } else {
                                                                        $c_value = $getValueByName($c_name, $dulieu);
                                                                    }
                                                                @endphp

                                                                <div class="{{ 'col-md-' . ($column['col'] ?? '6') }}">
                                                                    @if(isset($column['type']) && $column['type'] === 'content')
                                                                        <div class="alert alert-secondary mb-0">
                                                                            {!! $column['content'] ?? $column['label'] ?? '' !!}
                                                                        </div>
                                                                    @else
                                                                        <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">{{ $c_label }}</label>
                                                                        <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                                            @if($c_value !== null && $c_value !== '')
                                                                                @if(is_array($c_value))
                                                                                    @foreach($c_value as $val)
                                                                                        @if(is_array($val))
                                                                                            {{ json_encode($val, JSON_UNESCAPED_UNICODE) }}
                                                                                        @else
                                                                                            {{ $val }}
                                                                                        @endif
                                                                                        @if(!$loop->last)<br>@endif
                                                                                    @endforeach
                                                                                @elseif(is_string($c_value) && (str_starts_with($c_value, 'http') || str_contains($c_value, '/storage/')))
                                                                                    <a href="{{ $c_value }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                                                        <i class="fa fa-download"></i> Xem/Tải file
                                                                                    </a>
                                                                                @else
                                                                                    {{ $c_value }}
                                                                                @endif
                                                                            @else
                                                                                <span class="text-muted">—</span>
                                                                            @endif
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                @elseif(!empty($dulieu) && (isset($dulieu['hoTen']) || isset($dulieu['cccd'])))
                                    {{-- Hiển thị thông tin cơ bản cho hồ sơ nhận trực tiếp --}}
                                    <div class="col-12">
                                        <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:10px;">Thông tin chủ hồ sơ</h5>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        @if(isset($dulieu['hoTen']))
                                            <div class="col-md-6">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Họ và tên</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['hoTen'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($dulieu['ngaySinh']))
                                            <div class="col-md-3">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Ngày sinh</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['ngaySinh'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($dulieu['gioiTinh']))
                                            <div class="col-md-3">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Giới tính</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['gioiTinh'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($dulieu['cccd']))
                                            <div class="col-md-4">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Số CCCD/CMND</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['cccd'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($dulieu['ngayCap']))
                                            <div class="col-md-4">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Ngày cấp</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['ngayCap'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($dulieu['noiCap']))
                                            <div class="col-md-4">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Nơi cấp</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['noiCap'] }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12">
                                        <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:30px;">Thông tin liên hệ</h5>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        @if(isset($dulieu['email']))
                                            <div class="col-md-6">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Email</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['email'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($dulieu['soDienThoai']))
                                            <div class="col-md-6">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Số điện thoại</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['soDienThoai'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($dulieu['diaChi']))
                                            <div class="col-md-12">
                                                <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Địa chỉ (Thường trú / Tạm trú)</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $dulieu['diaChi'] }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12">
                                        <h5 class="mt-4 mb-3" style="border-bottom: 1px solid #eee; padding-bottom: 5px; padding-top:30px;">Thông tin thủ tục</h5>
                                    </div>
                                    <div class="row g-3 mb-2">
                                        <div class="col-md-12">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Tên thủ tục hành chính</label>
                                            <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                @php
                                                    $tthc = \App\Models\TTHC::find($hoSo->maTTHC);
                                                @endphp
                                                {{ $tthc ? $tthc->ten : 'N/A' }}
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label" style="color: #1A2A36; margin-bottom: 5px;">Hình thức nộp</label>
                                            <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                Nhận trực tiếp
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-12">
                                        <p class="text-muted">Không có thông tin form để hiển thị.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ========================== STEP 2: THÀNH PHẦN HỒ SƠ ========================== --}}
                        <div class="form-section" data-step="2" style="display: none;">
                            <h5>Thành phần hồ sơ</h5>

                            @if($hoSo->maTrangThai == 11 && ($user->vaiTro === 'Cán bộ một cửa' || $user->vaiTro === 'Quản trị viên'))
                                <form id="component-form" action="{{ route('admin.hosoxuly.tiepnhan-action', $hoSo->maHSXL) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                            @endif

                            @if($thanhPhanHoSos && $thanhPhanHoSos->count() > 0)
                                <div class="mt-4">
                                    @php $componentIndex = 0; @endphp
                                    @foreach($thanhPhanHoSos as $tenThanhPhan => $giayTos)
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="fa fa-folder-open text-primary"></i> {{ $tenThanhPhan }}
                                                </h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th style="width: 50px;" class="text-center">STT</th>
                                                                <th>Tên giấy tờ</th>
                                                                <th style="width: 120px;" class="text-center">Số lượng</th>
                                                                <th style="width: 150px;" class="text-center">Trạng thái</th>
                                                                <th style="width: 250px;" class="text-center">File đính kèm</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($giayTos as $idx => $giayTo)
                                                                @php
                                                                    // Tìm file đã upload cho giấy tờ này
                                                                    $uploadedFile = $taiLieu ? $taiLieu->where('maGiayTo', $giayTo->maGiayTo)->first() : null;
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-center">{{ $idx + 1 }}</td>
                                                                    <td>
                                                                        <strong>{{ $giayTo->tenGiayTo }}</strong>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <small class="text-muted">
                                                                            Chính: {{ $giayTo->soLuongBanChinh ?? 0 }} | 
                                                                            Sao: {{ $giayTo->soLuongBanSao ?? 0 }}
                                                                        </small>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($uploadedFile)
                                                                            <span class="badge bg-success">
                                                                                <i class="fa fa-check"></i> Đã nộp
                                                                            </span>
                                                                        @else
                                                                            <span class="badge bg-warning text-dark">
                                                                                <i class="fa fa-clock-o"></i> Chưa nộp
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($hoSo->maTrangThai == 11 && ($user->vaiTro === 'Cán bộ một cửa' || $user->vaiTro === 'Quản trị viên'))
                                                                            <input type="file" name="files[{{ $giayTo->maGiayTo }}]" class="form-control form-control-sm mb-2">
                                                                        @endif

                                                                        @if($uploadedFile && $uploadedFile->duongDan)
                                                                            <div class="d-flex flex-column align-items-center">
                                                                                <a href="{{ asset('storage/' . $uploadedFile->duongDan) }}" 
                                                                                   target="_blank" 
                                                                                   class="btn btn-sm btn-outline-primary mb-1" 
                                                                                   title="{{ $uploadedFile->tenTep }}">
                                                                                    <i class="fa fa-download"></i> Tải xuống
                                                                                </a>
                                                                                <small class="text-muted text-truncate" style="max-width: 180px;">
                                                                                    {{ $uploadedFile->tenTep }}
                                                                                </small>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        @php $componentIndex++; @endphp
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted"><i class="fa fa-info-circle"></i> Không có thành phần hồ sơ nào được định nghĩa cho thủ tục này.</p>
                            @endif

                            @if($hoSo->maTrangThai == 11 && ($user->vaiTro === 'Cán bộ một cửa' || $user->vaiTro === 'Quản trị viên'))
                                </form>
                            @endif
                        </div>

                        {{-- ========================== STEP 3: THÔNG TIN PHÍ, LỆ PHÍ ========================== --}}
                        <div class="form-section" data-step="3" style="display: none;">
                            <h5>Thông tin phí, lệ phí</h5>

                            {{-- Hình thức nhận kết quả --}}
                            @if(!empty($payload) && isset($payload['hinh_thuc_nhan_ket_qua']))
                                <div class="mb-4">
                                    <h6>Hình thức nhận kết quả</h6>
                                    <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                        {{ $payload['hinh_thuc_nhan_ket_qua'] }}
                                    </div>
                                </div>
                            @elseif($hoSo->hinhThuc)
                                <div class="mb-4">
                                    <h6>Hình thức nhận kết quả</h6>
                                    <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                        {{ $hoSo->hinhThuc }}
                                    </div>
                                </div>
                            @endif

                            {{-- Thông tin bưu chính --}}
                            @if(!empty($payload) && (isset($payload['dang_ky_nop_ho_so_tai_nha']) || isset($payload['dang_ky_nhan_ket_qua_tai_nha'])))
                                <div class="mb-4">
                                    <h6>Thông tin bưu chính</h6>
                                    <div class="row g-3">
                                        @if(isset($payload['ten_buu_chinh']))
                                            <div class="col-md-4">
                                                <label class="form-label">Tên</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $payload['ten_buu_chinh'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($payload['sdt_buu_chinh']))
                                            <div class="col-md-4">
                                                <label class="form-label">Số điện thoại</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $payload['sdt_buu_chinh'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($payload['email_buu_chinh']))
                                            <div class="col-md-4">
                                                <label class="form-label">Email</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $payload['email_buu_chinh'] }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Thông tin lệ phí --}}
                            @if(!empty($payload) && isset($payload['le_phi_so_luong']) && isset($payload['muc_le_phi']))
                                <div class="mb-4">
                                    <h6>Chi tiết lệ phí</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Loại lệ phí</th>
                                                    <th>Số lượng</th>
                                                    <th>Mức lệ phí</th>
                                                    <th>Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($payload['le_phi_so_luong'] as $maLePhi => $soLuong)
                                                    @php
                                                        $lePhi = \Illuminate\Support\Facades\DB::table('lephi')->where('maLePhi', $maLePhi)->first();
                                                        $mucLePhi = $payload['muc_le_phi'][$maLePhi] ?? ($lePhi->soTien ?? 0);
                                                        $thanhTien = $soLuong * $mucLePhi;
                                                    @endphp
                                                    @if($lePhi)
                                                        <tr>
                                                            <td>{{ $lePhi->loaiLePhi }}</td>
                                                            <td>{{ $soLuong }}</td>
                                                            <td>{{ number_format($mucLePhi, 0, ',', '.') }} VNĐ</td>
                                                            <td>{{ number_format($thanhTien, 0, ',', '.') }} VNĐ</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>Tổng</strong></td>
                                                    <td><strong>{{ number_format($hoSo->lePhi, 0, ',', '.') }} VNĐ</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @elseif($hoSo->lePhi > 0)
                                <div class="mb-4">
                                    <h6>Tổng lệ phí</h6>
                                    <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; font-size: 18px; font-weight: bold; color: #32C36C;">
                                        {{ number_format($hoSo->lePhi, 0, ',', '.') }} VNĐ
                                    </div>
                                </div>
                            @endif

                            {{-- Hình thức thanh toán --}}
                            @if(!empty($payload) && isset($payload['hinh_thuc_thanh_toan']))
                                <div class="mb-4">
                                    <h6>Hình thức thanh toán</h6>
                                    <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                        {{ $payload['hinh_thuc_thanh_toan'] }}
                                    </div>
                                </div>
                            @endif

                            {{-- Thông tin hoàn tiền --}}
                            @if(!empty($payload) && (isset($payload['so_tai_khoan']) || isset($payload['chu_tai_khoan'])))
                                <div class="mb-4">
                                    <h6>Thông tin hoàn tiền</h6>
                                    <div class="row g-3">
                                        @if(isset($payload['so_tai_khoan']))
                                            <div class="col-md-4">
                                                <label class="form-label">Số tài khoản</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $payload['so_tai_khoan'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($payload['chu_tai_khoan']))
                                            <div class="col-md-4">
                                                <label class="form-label">Chủ tài khoản</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $payload['chu_tai_khoan'] }}
                                                </div>
                                            </div>
                                        @endif
                                        @if(isset($payload['ten_ngan_hang']))
                                            <div class="col-md-4">
                                                <label class="form-label">Tên ngân hàng</label>
                                                <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                                    {{ $payload['ten_ngan_hang'] }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- ========================== STEP 4: THÔNG TIN NGƯỜI NỘP ========================== --}}
                        <div class="form-section" data-step="4" style="display: none;">
                            <h5>Thông tin người nộp</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Họ tên:</strong></label>
                                    <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                        {{ $hoSo->tenChuHoSo }}
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Số điện thoại:</strong></label>
                                    <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                        {{ $hoSo->soDienThoai }}
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><strong>Email:</strong></label>
                                    <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                        {{ $hoSo->email }}
                                    </div>
                                </div>
                                @if($hoSo->congdan && $hoSo->congdan->nguoi)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><strong>Họ tên người dùng:</strong></label>
                                        <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                            {{ $hoSo->congdan->nguoi->hoTen ?? '-' }}
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($payload) && isset($payload['dia_chi']))
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label"><strong>Địa chỉ:</strong></label>
                                        <div class="form-control-plaintext" style="min-height: 38px; padding: 8px 12px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
                                            {{ $payload['dia_chi'] }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background-color: #f5f5f5;">
                                        <h4 class="panel-title">
                                            <i class="fa fa-file-text-o"></i> Ý kiến xử lý
                                            @if(!$isReadOnly)
                                                <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target="#yKienXuLyModal">
                                                    <i class="fa fa-upload"></i> Tải lên
                                                </button>
                                            @endif
                                        </h4>
                                    </div>
                                    <div class="panel-body" id="mainYKienContent">
                                        <p><strong>Nội dung:</strong> <span id="mainNoiDungYKien">{{ $hoSo->yKienXuLy ?? 'Chưa có ý kiến' }}</span></p>
                                        <div id="mainFileList">
                                            @php
                                                $files = json_decode($hoSo->duongdanfileykien, true) ?? [];
                                            @endphp
                                            @if(count($files) > 0)
                                                <ul class="list-group">
                                                    @foreach($files as $file)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span class="text-truncate d-inline-block" style="max-width: 250px; vertical-align: middle;" title="{{ basename($file) }}"><i class="fa fa-file"></i> {{ basename($file) }}</span>
                                                            @if($isReadOnly)
                                                                <a href="{{ asset($file) }}" download class="btn btn-sm btn-light">
                                                                    <i class="fa fa-download"></i> Tải xuống
                                                                </a>
                                                            @else
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown">
                                                                        <i class="fa fa-ellipsis-v"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <a class="dropdown-item" href="{{ asset($file) }}" download><i class="fa fa-download"></i> Tải xuống</a>
                                                                        <a class="dropdown-item" href="#" onclick="convertYKienFile('{{ $file }}'); return false;"><i class="fa fa-check-circle"></i> Chuyển thành kết quả</a>
                                                                        <div class="dropdown-divider"></div>
                                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteYKienFile('{{ $file }}'); return false;"><i class="fa fa-trash"></i> Xóa file</a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted">Chưa có file đính kèm</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background-color: #f5f5f5;">
                                        <h4 class="panel-title">
                                            <i class="fa fa-file-text"></i> Kết quả xử lý
                                            @if(!$isReadOnly)
                                                <button type="button" class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target="#ketQuaXuLyModal">
                                                    <i class="fa fa-upload"></i> Tải lên
                                                </button>
                                            @endif
                                        </h4>
                                    </div>
                                    <div class="panel-body" id="mainKetQuaContent">
                                        <div id="mainKetQuaList">
                                            @php
                                                $kqFiles = json_decode($hoSo->duongdanfileketqua, true) ?? [];
                                            @endphp
                                            @if(count($kqFiles) > 0)
                                                <ul class="list-group">
                                                    @foreach($kqFiles as $file)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span class="text-truncate d-inline-block" style="max-width: 250px; vertical-align: middle;" title="{{ basename($file) }}"><i class="fa fa-file-pdf-o text-danger"></i> {{ basename($file) }}</span>
                                                            @if($isReadOnly)
                                                                <a href="{{ asset($file) }}" download class="btn btn-sm btn-light">
                                                                    <i class="fa fa-download"></i> Tải xuống
                                                                </a>
                                                            @else
                                                                <div class="dropdown">
                                                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown">
                                                                        <i class="fa fa-ellipsis-v"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        <a class="dropdown-item" href="{{ asset($file) }}" download><i class="fa fa-download"></i> Tải xuống</a>
                                                                        <div class="dropdown-divider"></div>
                                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteKetQuaFile('{{ $file }}'); return false;"><i class="fa fa-trash"></i> Xóa file</a>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    <div class="panel-body">
                                        <style>
                                            /* File actions dropdown */
                                            .file-actions-dropdown {
                                                position: relative;
                                                display: inline-block;
                                            }
                                            .file-actions-btn {
                                                background: none;
                                                border: none;
                                                font-size: 18px;
                                                cursor: pointer;
                                                padding: 5px 10px;
                                                color: #666;
                                                transition: color 0.2s;
                                            }
                                            .file-actions-btn:hover {
                                                color: #333;
                                            }
                                            .file-actions-menu {
                                                display: none;
                                                position: absolute;
                                                right: 0;
                                                top: 100%;
                                                background: white;
                                                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                                                border-radius: 6px;
                                                min-width: 200px;
                                                z-index: 1000;
                                                margin-top: 5px;
                                                border: 1px solid #e0e0e0;
                                            }
                                            .file-actions-menu.show {
                                                display: block;
                                            }
                                            .file-actions-menu a,
                                            .file-actions-menu button {
                                                display: block;
                                                padding: 10px 15px;
                                                color: #333;
                                                text-decoration: none;
                                                border: none;
                                                background: none;
                                                width: 100%;
                                                text-align: left;
                                                cursor: pointer;
                                                transition: background 0.2s;
                                                font-size: 14px;
                                            }
                                            .file-actions-menu a:hover,
                                            .file-actions-menu button:hover {
                                                background: #f5f5f5;
                                            }
                                            .file-actions-menu a:first-child,
                                            .file-actions-menu button:first-child {
                                                border-radius: 6px 6px 0 0;
                                            }
                                            .file-actions-menu a:last-child,
                                            .file-actions-menu button:last-child {
                                                border-radius: 0 0 6px 6px;
                                            }
                                            .file-actions-menu i {
                                                margin-right: 8px;
                                                width: 16px;
                                                text-align: center;
                                            }
                                            .file-signed-badge {
                                                display: inline-block;
                                                background: #32C36C;
                                                color: white;
                                                padding: 2px 8px;
                                                border-radius: 3px;
                                                font-size: 11px;
                                                margin-left: 8px;
                                                font-weight: 500;
                                            }
                                        </style>

                                        <div id="ketQuaFilesList">
                                            @if($hoSo->duongdanfileketqua)
                                                @php
                                                    $filesKetQua = json_decode($hoSo->duongdanfileketqua, true) ?? [];
                                                    $fileSignatures = json_decode($hoSo->file_signatures ?? '{}', true);
                                                    $user = Auth::user();
                                                @endphp
                                                @if(count($filesKetQua) > 0)
                                                    @foreach($filesKetQua as $index => $file)
                                                        @php
                                                            $isSigned = isset($fileSignatures[$file]);
                                                            $signedBy = $isSigned ? $fileSignatures[$file]['signed_by'] ?? null : null;
                                                            $signedAt = $isSigned ? $fileSignatures[$file]['signed_at'] ?? null : null;
                                                        @endphp
                                                        <div class="file-item" style="padding: 10px; background: #f9f9f9; margin-bottom: 5px; border-radius: 3px; display: flex; align-items: center; justify-content: space-between;">
                                                            <div style="flex: 1;">
                                                                <i class="fa fa-file-pdf-o text-danger"></i>
                                                                <a href="{{ asset($file) }}" target="_blank">{{ basename($file) }}</a>
                                                                @if($isSigned)
                                                                    <span class="file-signed-badge">
                                                                        <i class="fa fa-check-circle"></i> Đã ký số
                                                                    </span>
                                                                    @if($signedAt)
                                                                        <small class="text-muted" style="margin-left: 8px;">
                                                                            <i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($signedAt)->format('d/m/Y H:i') }}
                                                                        </small>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                            <div class="file-actions-dropdown">
                                                                <button class="file-actions-btn" onclick="toggleFileMenu(event, {{ $index }})">
                                                                    <i class="fa fa-ellipsis-v"></i>
                                                                </button>
                                                                <div class="file-actions-menu" id="file-menu-{{ $index }}">
                                                                    <a href="{{ asset($file) }}" target="_blank">
                                                                        <i class="fa fa-eye"></i> Xem file
                                                                    </a>
                                                                    <a href="{{ asset($file) }}" download>
                                                                        <i class="fa fa-download"></i> Tải xuống
                                                                    </a>
                                                                    @if($user->vaiTro === 'Lãnh đạo' || $user->vaiTro === 'Quản trị viên')
                                                                        @if(!$isSigned)
                                                                            <button onclick="openSignModal('{{ $file }}', '{{ $hoSo->maHSXL }}')">
                                                                                <i class="fa fa-pencil-square-o"></i> Ký số điện tử
                                                                            </button>
                                                                        @else
                                                                            <button disabled style="opacity: 0.5; cursor: not-allowed;">
                                                                                <i class="fa fa-check-circle"></i> Đã ký số
                                                                            </button>
                                                                        @endif
                                                                    @endif
                                                                    <button onclick="removeKetQuaFile('{{ $file }}', '{{ $hoSo->maHSXL }}')" style="color: #d9534f;">
                                                                        <i class="fa fa-trash"></i> Xóa file
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else

                                                @endif
                                            @else
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lịch sử hoạt động -->
                        @if($hoSo->ghiChu)
                            <div class="info-section">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h4 class="mb-4" style="color: #2c3e50; font-weight: 600; border-left: 4px solid #32C36C; padding-left: 15px;">
                                            <i class="fa fa-history"></i> Lịch sử hoạt động
                                        </h4>

                                        @php
                                            // Parse the activity log
                                            $activities = [];
                                            $lines = explode("\n", $hoSo->ghiChu);
                                            foreach ($lines as $line) {
                                                $line = trim($line);
                                                if (empty($line)) continue;

                                                // Match pattern: [dd/mm/yyyy HH:ii] Activity description
                                                if (preg_match('/^\[(\d{2}\/\d{2}\/\d{4}\s+\d{2}:\d{2})\]\s*(.+)$/', $line, $matches)) {
                                                    $activities[] = [
                                                        'time' => $matches[1],
                                                        'description' => $matches[2]
                                                    ];
                                                } else {
                                                    // If no timestamp, add as a simple note
                                                    $activities[] = [
                                                        'time' => null,
                                                        'description' => $line
                                                    ];
                                                }
                                            }

                                            // Reverse to show newest first
                                            $activities = array_reverse($activities);
                                        @endphp

                                        <div class="activity-timeline" style="position: relative; padding: 20px 0 20px 30px;">
                                            <!-- Vertical Line -->
                                            <div style="position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #e9ecef;"></div>

                                            @foreach($activities as $index => $activity)
                                                <div class="activity-item" style="position: relative; margin-bottom: 30px;">
                                                    <!-- Dot -->
                                                    <div style="
                                                        position: absolute;
                                                        left: -21px;
                                                        top: 0;
                                                        width: 14px;
                                                        height: 14px;
                                                        border-radius: 50%;
                                                        background: {{ $index === 0 ? '#32C36C' : '#adb5bd' }};
                                                        border: 3px solid #fff;
                                                        box-shadow: 0 0 0 1px {{ $index === 0 ? '#32C36C' : '#adb5bd' }};
                                                        z-index: 1;
                                                    "></div>

                                                    <!-- Content -->
                                                    <div class="activity-content" style="
                                                        background: #fff;
                                                        border-radius: 8px;
                                                        padding: 10px 15px;
                                                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                                                        border: 1px solid #e9ecef;
                                                    ">
                                                        @if($activity['time'])
                                                            <div style="margin-bottom: 6px; display: flex; align-items: center; flex-wrap: wrap;">
                                                                <span style="
                                                                    font-size: 12px;
                                                                    font-weight: 600;
                                                                    color: {{ $index === 0 ? '#32C36C' : '#6c757d' }};
                                                                    background: {{ $index === 0 ? '#e8f5e9' : '#f8f9fa' }};
                                                                    padding: 2px 8px;
                                                                    border-radius: 4px;
                                                                    margin-right: 8px;
                                                                ">
                                                                    {{ $activity['time'] }}
                                                                </span>
                                                                @if($index === 0)
                                                                    <span class="badge badge-success" style="font-size: 10px;">Mới nhất</span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <div style="
                                                            color: #2c3e50;
                                                            font-size: 14px;
                                                            line-height: 1.5;
                                                            font-weight: {{ $index === 0 ? '500' : '400' }};
                                                            word-break: break-word;
                                                            overflow-wrap: break-word;
                                                        ">
                                                            {{ $activity['description'] }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Thông tin trả -->
                        @if($hoSo->thongTinTra)
                            <div class="info-section">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h4 class="mb-3" style="color: #32C36C; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                            <i class="fa fa-info"></i> Thông tin trả
                                        </h4>
                                        <div class="alert alert-warning">
                                            {{ $hoSo->thongTinTra }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->


<!-- Modal Gửi mail -->
<div class="modal fade" id="sendMailModal" tabindex="-1" aria-labelledby="sendMailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendMailModalLabel">
                    <i class="fa fa-envelope"></i> Gửi mail cho chủ hồ sơ
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="sendMailForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Người nhận:</strong></label>
                        <input type="text" class="form-control" value="{{ $hoSo->tenChuHoSo }} ({{ $hoSo->email }})" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Mã hồ sơ:</strong></label>
                        <input type="text" class="form-control" value="{{ $hoSo->maHSXL }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Loại mail:</strong></label>
                        <select name="loai_mail" id="loai_mail" class="form-control" required>
                            <option value="lien_lac">Liên lạc với chủ hồ sơ</option>
                            <option value="bo_sung">Yêu cầu bổ sung hồ sơ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Tiêu đề:</strong></label>
                        <input type="text" name="subject" id="mail_subject" class="form-control"
                               placeholder="Nhập tiêu đề email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Nội dung:</strong></label>
                        <textarea name="content" id="mail_content" class="form-control" rows="8"
                                  placeholder="Nhập nội dung email..." required></textarea>
                        <small class="text-muted">Bạn có thể sử dụng HTML để định dạng nội dung</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-paper-plane"></i> Gửi mail
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Lịch sử mail -->
<div class="modal fade" id="mailHistoryModal" tabindex="-1" aria-labelledby="mailHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 90%; width: 1200px;">
        <div class="modal-content" style="min-height: 600px;">
            <div class="modal-header" style="background: #32C36C; color: white; padding: 20px;">
                <h4 class="modal-title" id="mailHistoryModalLabel" style="font-size: 20px; font-weight: bold; margin: 0;">
                    <i class="fa fa-history"></i> Lịch sử mail đã gửi cho chủ hồ sơ
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 28px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
                @if($mailHistory && $mailHistory->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" style="font-size: 14px;">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="width: 12%; padding: 12px; font-weight: bold; font-size: 14px;">Thời gian</th>
                                    <th style="width: 15%; padding: 12px; font-weight: bold; font-size: 14px;">Loại mail</th>
                                    <th style="width: 28%; padding: 12px; font-weight: bold; font-size: 14px;">Tiêu đề</th>
                                    <th style="width: 20%; padding: 12px; font-weight: bold; font-size: 14px;">Email</th>
                                    <th style="width: 25%; padding: 12px; font-weight: bold; font-size: 14px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mailHistory as $mail)
                                    <tr style="background-color: {{ $mail->direction == 'incoming' ? '#e8f5e9' : '#ffffff' }}; border-bottom: 2px solid #e0e0e0;">
                                        <td style="padding: 15px; font-size: 14px; vertical-align: middle;">{{ $mail->sent_at->format('d/m/Y H:i') }}</td>
                                        <td style="padding: 15px; vertical-align: middle;">
                                            @if($mail->direction == 'incoming')
                                                <span class="badge badge-success" style="font-size: 13px; padding: 8px 12px;">
                                                    <i class="fa fa-arrow-left"></i> Công dân trả lời
                                                </span>
                                            @else
                                                <span class="badge {{ $mail->loai_mail == 'bo_sung' ? 'badge-warning' : 'badge-info' }}" style="font-size: 13px; padding: 8px 12px;">
                                                    <i class="fa fa-arrow-right"></i> {{ $mail->loai_mail == 'bo_sung' ? 'Bổ sung hồ sơ' : 'Liên lạc' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding: 15px; font-size: 14px; vertical-align: middle; font-weight: 500;">{{ strlen($mail->subject) > 60 ? substr($mail->subject, 0, 60) . '...' : $mail->subject }}</td>
                                        <td style="padding: 15px; font-size: 14px; vertical-align: middle;">{{ $mail->email }}</td>
                                        <td style="padding: 15px; vertical-align: middle;">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="viewMailContent({{ $mail->id }})" style="font-size: 13px; padding: 8px 15px;">
                                                <i class="fa fa-eye"></i> Xem nội dung
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- Mail content hidden -->
                                    <tr id="mail-content-{{ $mail->id }}" style="display: none;">
                                        <td colspan="5" style="padding: 20px; background: #fafafa;">
                                            <div class="card" style="border: 2px solid #32C36C; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                <div class="card-header" style="background: #32C36C; color: white; padding: 18px; border-radius: 6px 6px 0 0;">
                                                    @if($mail->direction == 'incoming')
                                                        <h5 style="margin: 0 0 12px 0; font-size: 16px; font-weight: bold;">
                                                            <i class="fa fa-arrow-left"></i> Email từ công dân
                                                        </h5>
                                                    @else
                                                        <h5 style="margin: 0 0 12px 0; font-size: 16px; font-weight: bold;">
                                                            <i class="fa fa-arrow-right"></i> Email gửi đi
                                                        </h5>
                                                    @endif
                                                    <div style="font-size: 14px; line-height: 1.8;">
                                                        <div><strong>Tiêu đề:</strong> {{ $mail->subject }}</div>
                                                        @if($mail->direction == 'outgoing')
                                                            <div><strong>Loại:</strong> {{ $mail->loai_mail == 'bo_sung' ? 'Yêu cầu bổ sung hồ sơ' : 'Liên lạc với chủ hồ sơ' }}</div>
                                                        @endif
                                                        <div><strong>{{ $mail->direction == 'incoming' ? 'Từ' : 'Gửi đến' }}:</strong> {{ $mail->email }}</div>
                                                        <div><strong>Thời gian:</strong> {{ $mail->sent_at->format('d/m/Y H:i:s') }}</div>
                                                    </div>
                                                </div>
                                                <div class="card-body" style="padding: 20px; max-height: 500px; overflow-y: auto; background: white;">
                                                    <strong style="font-size: 15px; color: #333; display: block; margin-bottom: 12px;">Nội dung:</strong>
                                                    <div style="padding: 18px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #32C36C; white-space: pre-wrap; font-size: 14px; line-height: 1.8; color: #333; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin-bottom: 15px;">{{ $mail->content }}</div>
                                                    @if($mail->direction == 'incoming')
                                                        <div style="text-align: right; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">
                                                            <button type="button" class="btn btn-success" onclick="replyToMail({{ $mail->id }}, '{{ addslashes($mail->subject) }}', '{{ addslashes($mail->email) }}')" style="font-size: 14px; padding: 10px 20px;">
                                                                <i class="fa fa-reply"></i> Trả lời mail
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info" style="font-size: 15px; padding: 20px; text-align: center;">
                        <i class="fa fa-info-circle" style="font-size: 18px;"></i> Chưa có lịch sử gửi mail nào.
                    </div>
                @endif
            </div>
            <div class="modal-footer" style="padding: 20px; background: #f8f9fa; border-top: 2px solid #e0e0e0;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="font-size: 14px; padding: 10px 25px;">
                    <i class="fa fa-times"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

<!-- Modal Chuyển thụ lý (Xác nhận hoàn thành - Một cửa) -->
<div class="modal fade" id="chuyenThuLyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #d9534f; font-weight: bold;">Xác nhận hoàn thành</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.hosoxuly.chuyen-thuly', $hoSo->maHSXL) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Chuyển hồ sơ sang <strong>Cán bộ thụ lý</strong> để thẩm định và soạn thảo.
                    </div>

                    <div class="form-group">
                        <label style="font-weight: bold;">Bình luận</label>
                        <textarea class="form-control" name="ghiChu" rows="4" placeholder="Nhập nội dung bình luận..."></textarea>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notify_next" value="1" checked> Gửi tin nhắn cho người xử lý kế tiếp
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="email_next" value="1" checked> Gửi email cho người xử lý kế tiếp
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notify_citizen" value="1" checked> Gửi tin nhắn cho người dân
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="email_citizen" value="1" checked> Gửi email cho người dân
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="zalo_citizen" value="1" checked> Gửi zalo cho người dân
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="submit" class="btn btn-warning" style="background-color: #e08e0b; border-color: #d58512; color: white; font-weight: bold; min-width: 120px;">Đồng ý</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Chuyển lãnh đạo (Xác nhận hoàn thành - Thụ lý) -->
<div class="modal fade" id="chuyenLanhDaoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #d9534f; font-weight: bold;">Xác nhận hoàn thành</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.hosoxuly.chuyen-lanhdao', $hoSo->maHSXL) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: bold;">Chọn đơn vị/người xử lý để chuyển đến</label>
                        <select class="form-control" name="nguoiDuyet" required>
                            <option value="">-- Chọn lãnh đạo phê duyệt --</option>
                            @if(isset($lanhDaos))
                                @foreach($lanhDaos as $ld)
                                    <option value="{{ $ld->IDnguoiDung }}">{{ $ld->hoTen }} ({{ $ld->vaiTro }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: bold;">Bình luận</label>
                        <textarea class="form-control" name="ghiChu" rows="4" placeholder="Nhập nội dung bình luận..."></textarea>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notify_next" value="1" checked> Gửi tin nhắn cho người xử lý kế tiếp
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="email_next" value="1" checked> Gửi email cho người xử lý kế tiếp
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="notify_citizen" value="1" checked> Gửi tin nhắn cho người dân
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="email_citizen" value="1" checked> Gửi email cho người dân
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="zalo_citizen" value="1" checked> Gửi zalo cho người dân
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="submit" class="btn btn-warning" style="background-color: #e08e0b; border-color: #d58512; color: white; font-weight: bold; min-width: 120px;">Đồng ý</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Phê duyệt -->
<div class="modal fade" id="pheDuyetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Phê duyệt hồ sơ</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="pheDuyetForm" action="{{ route('admin.hosoxuly.pheduyet', $hoSo->maHSXL) }}" method="POST">
                @csrf
                <input type="hidden" name="ketQuaFiles" id="ketQuaFilesInput_Duyet">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Ý kiến phê duyệt</label>
                        <textarea class="form-control" name="yKienDuyet" rows="4" placeholder="Nhập ý kiến phê duyệt...">Đồng ý giải quyết</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Xác nhận phê duyệt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Chuyển lãnh đạo -->
<div class="modal fade" id="chuyenLanhDaoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chuyển hồ sơ sang lãnh đạo</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="chuyenLanhDaoForm" action="{{ route('admin.hosoxuly.chuyen-lanhdao', $hoSo->maHSXL) }}" method="POST">
                @csrf
                <input type="hidden" name="ketQuaFiles" id="ketQuaFilesInput_Chuyen">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Chọn lãnh đạo (nếu cần chỉ định)</label>
                        <select class="form-control" name="nguoiDuyet">
                            <option value="">-- Chọn lãnh đạo --</option>
                            @foreach(\App\Models\Nguoi::where('vaiTro', 'Lãnh đạo')->get() as $lanhDao)
                                <option value="{{ $lanhDao->IDnguoiDung }}">{{ $lanhDao->hoTen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ghi chú</label>
                        <textarea class="form-control" name="ghiChu" rows="3" placeholder="Nhập ghi chú..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning">Chuyển lãnh đạo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Yêu cầu bổ sung -->
<div class="modal fade" id="yeuCauBoSungModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #d9534f; font-weight: bold;">Yêu cầu bổ sung giấy tờ</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.hosoxuly.yeu-cau-bo-sung', $hoSo->maHSXL) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Chọn các giấy tờ mà công dân cần bổ sung cho hồ sơ.
                    </div>
                    
                    @if($hoSo->tthc && $hoSo->tthc->thanhPhanHoSos->count() > 0)
                        @foreach($hoSo->tthc->thanhPhanHoSos as $tp)
                            <div class="panel panel-default mb-3">
                                <div class="panel-heading" style="background-color: #f5f5f5; padding: 10px;">
                                    <strong><i class="fa fa-folder-open"></i> {{ $tp->tenThanhPhan }}</strong>
                                </div>
                                <div class="panel-body" style="padding: 10px;">
                                    @if($tp->giayTos && $tp->giayTos->count() > 0)
                                        <div style="max-height: 150px; overflow-y: auto;">
                                            @foreach($tp->giayTos as $giayTo)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="giayto[]" value="{{ $giayTo->maGiayTo }}" id="gt_{{ $giayTo->maGiayTo }}">
                                                    <label class="form-check-label" for="gt_{{ $giayTo->maGiayTo }}">
                                                        {{ $giayTo->tenGiayTo }}
                                                        <small class="text-muted">
                                                            ({{ $giayTo->loaiGiayTo }} - 
                                                            Bản chính: {{ $giayTo->pivot->soLuongBanChinh }}, 
                                                            Bản sao: {{ $giayTo->pivot->soLuongBanSao }})
                                                        </small>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0"><i>Không có giấy tờ cụ thể nào.</i></p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted"><i class="fa fa-info-circle"></i> Không có thành phần hồ sơ nào được định nghĩa cho thủ tục này.</p>
                    @endif
                    
                    <div class="form-group mt-3">
                        <label style="font-weight: bold;">Ghi chú thêm:</label>
                        <textarea class="form-control" name="ghiChu" rows="3" placeholder="Nhập ghi chú chi tiết về yêu cầu bổ sung (nếu có)..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Đóng
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-paper-plane"></i> Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Ý kiến xử lý -->
<div class="modal fade" id="yKienXuLyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #d9534f; font-weight: bold;">Ý kiến xử lý của cán bộ</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="yKienXuLyForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label style="font-weight: bold;">Ý kiến xử lý</label>
                        <textarea class="form-control" name="yKienXuLy" rows="6" placeholder="Nhập ý kiến xử lý...">{{ $hoSo->yKienXuLy ?? '' }}</textarea>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: bold;">File đính kèm</label>
                        <div class="file-upload-area" style="border: 2px dashed #ccc; padding: 30px; text-align: center; background-color: #f9f9f9; border-radius: 5px;">
                            <i class="fa fa-cloud-upload" style="font-size: 48px; color: #999;"></i>
                            <p style="margin: 10px 0;">Kéo thả tệp tin hoặc <strong>Tải lên</strong></p>
                            <p style="font-size: 12px; color: #999;">Kích thước tối đa của tệp tin: 100MB</p>
                            <input type="file" name="fileYKien[]" id="fileYKien" multiple class="form-control-file" style="display: none;" onchange="uploadYKienFiles(this)">
                            <button type="button" class="btn btn-default" onclick="document.getElementById('fileYKien').click()">
                                <i class="fa fa-folder-open"></i> Chọn file
                            </button>
                        </div>
                        <div id="fileYKienList" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="submit" class="btn btn-warning" style="background-color: #e08e0b; border-color: #d58512; color: white; font-weight: bold; min-width: 120px;">
                        <i class="fa fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Kết quả xử lý -->
<div class="modal fade" id="ketQuaXuLyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="color: #d9534f; font-weight: bold;">Tải lên kết quả xử lý</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="ketQuaXuLyForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Tải lên file kết quả xử lý để trả cho công dân.
                    </div>

                    <div class="form-group">
                        <label style="font-weight: bold;">File kết quả</label>
                        <div class="file-upload-area" style="border: 2px dashed #ccc; padding: 30px; text-align: center; background-color: #f9f9f9; border-radius: 5px;">
                            <i class="fa fa-cloud-upload" style="font-size: 48px; color: #999;"></i>
                            <p style="margin: 10px 0;">Kéo thả tệp tin hoặc <strong>Tải lên</strong></p>
                            <p style="font-size: 12px; color: #999;">Kích thước tối đa của tệp tin: 100MB</p>
                            <input type="file" name="fileKetQua[]" id="fileKetQua" multiple class="form-control-file" style="display: none;" onchange="uploadKetQuaFiles(this)">
                            <button type="button" class="btn btn-default" onclick="document.getElementById('fileKetQua').click()">
                                <i class="fa fa-folder-open"></i> Chọn file
                            </button>
                        </div>
                        <div id="fileKetQuaList" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="submit" class="btn btn-success" style="font-weight: bold; min-width: 120px;">
                        <i class="fa fa-upload"></i> Tải lên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Trả lại -->
<div class="modal fade" id="traLaiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Trả lại hồ sơ</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.hosoxuly.tralai', $hoSo->maHSXL) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Lý do trả lại <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="lyDo" rows="4" required placeholder="Nhập lý do trả lại hồ sơ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận trả lại</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ký số điện tử -->
<div class="modal fade" id="signFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #32C36C; color: white;">
                <h5 class="modal-title">
                    <i class="fa fa-pencil-square-o"></i> Ký số điện tử
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                    <span>&times;</span>
                </button>
            </div>
            <form id="signFileForm">
                @csrf
                <input type="hidden" id="sign_file_path" name="file_path">
                <input type="hidden" id="sign_ma_hsxl" name="maHSXL">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Bạn đang ký số điện tử cho file: <strong id="sign_file_name"></strong>
                    </div>
                    <div class="form-group">
                        <label>Phương thức ký số</label>
                        <select class="form-control" name="signature_method">
                            <option value="digital_ca">Chữ ký số CA</option>
                            <option value="usb_token">USB Token</option>
                            <option value="sim_ca">SIM CA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ghi chú (tùy chọn)</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Nhập ghi chú nếu có..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> Sau khi ký số, file sẽ được đánh dấu là đã ký và không thể ký lại.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check"></i> Xác nhận ký số
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Yêu cầu xử lý lại -->
<div class="modal fade" id="yeuCauXuLyLaiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #f0ad4e; color: white;">
                <h5 class="modal-title">
                    <i class="fa fa-undo"></i> Yêu cầu xử lý lại
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.hosoxuly.yeu-cau-xu-ly-lai', $hoSo->maHSXL) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa fa-info-circle"></i> Hồ sơ sẽ được chuyển lại cho cán bộ thụ lý để xử lý lại.
                    </div>
                    <div class="form-group">
                        <label>Nội dung yêu cầu <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="noiDung" rows="5" required placeholder="Nhập nội dung yêu cầu xử lý lại..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-send"></i> Gửi yêu cầu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Dừng xử lý hồ sơ -->
<div class="modal fade" id="traLaiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #d9534f; color: white;">
                <h5 class="modal-title">
                    <i class="fa fa-times-circle"></i> Dừng xử lý hồ sơ
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.hosoxuly.tralai', $hoSo->maHSXL) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle"></i> Hồ sơ sẽ bị dừng xử lý. Hành động này sẽ cập nhật trạng thái hồ sơ thành "Dừng xử lý" (Trạng thái 8).
                    </div>
                    <div class="form-group">
                        <label>Lý do dừng xử lý <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="lyDo" rows="5" required placeholder="Nhập lý do dừng xử lý hồ sơ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-times"></i> Xác nhận dừng xử lý
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')

<script>
// ========== DEFINE showStep GLOBALLY FIRST ==========
// This must be defined BEFORE the page loads so onclick attributes can find it
let currentStep = 1;
const totalSteps = 4;

window.showStep = function(step) {
    step = parseInt(step);
    console.log('showStep called with:', step);

    // Hide all sections
    document.querySelectorAll(".form-section").forEach(el => {
        el.classList.add("hidden");
        el.style.display = 'none';
    });

    // Show the selected section
    const activeSection = document.querySelector(`.form-section[data-step='${step}']`);
    if (activeSection) {
        activeSection.classList.remove("hidden");
        activeSection.style.display = 'block';
        console.log('Showing section for step:', step);
    } else {
        console.error('Section not found for step:', step);
    }

    // Update step indicators
    document.querySelectorAll(".step").forEach((s, i) => {
        const stepNumber = i + 1;
        s.classList.remove("active", "completed");

        if (stepNumber < step) {
            s.classList.add("completed");
        } else if (stepNumber === step) {
            s.classList.add("active");
        }
    });

    // Update current step
    currentStep = step;
};

// ========== FILE MENU FUNCTIONS ==========
function toggleFileMenu(event, index) {
    event.stopPropagation();
    const menu = document.getElementById('file-menu-' + index);
    const allMenus = document.querySelectorAll('.file-actions-menu');

    // Close all other menus
    allMenus.forEach(m => {
        if (m !== menu) {
            m.classList.remove('show');
        }
    });

    // Toggle current menu
    menu.classList.toggle('show');
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.file-actions-dropdown')) {
        document.querySelectorAll('.file-actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// ========== DIGITAL SIGNATURE FUNCTIONS ==========
function openSignModal(filePath, maHSXL) {
    document.getElementById('sign_file_path').value = filePath;
    document.getElementById('sign_ma_hsxl').value = maHSXL;
    document.getElementById('sign_file_name').textContent = filePath.split('/').pop();
    $('#signFileModal').modal('show');
}

// Handle digital signature form submission
document.addEventListener('DOMContentLoaded', function() {
    const signForm = document.getElementById('signFileForm');
    if (signForm) {
        signForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(signForm);
            const submitBtn = signForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';

            fetch('{{ route("admin.hosoxuly.sign-file", $hoSo->maHSXL) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Ký số thành công!');
                    $('#signFileModal').modal('hide');
                    location.reload(); // Reload to show updated signature status
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể ký số'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi ký số');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});

// ========== OTHER FUNCTIONS ==========
function viewMailContent(mailId) {
    const contentRow = document.getElementById('mail-content-' + mailId);
    if (contentRow.style.display === 'none') {
        contentRow.style.display = 'table-row';
    } else {
        contentRow.style.display = 'none';
    }
}

function replyToMail(mailId, originalSubject, email) {
    // Đóng modal lịch sử mail trước
    $('#mailHistoryModal').modal('hide');

    // Đợi modal lịch sử mail đóng xong rồi mới mở modal gửi mail
    $('#mailHistoryModal').on('hidden.bs.modal', function() {
        // Mở modal gửi mail
        $('#sendMailModal').modal('show');

        // Điền thông tin
        const subjectInput = document.getElementById('mail_subject');
        const contentTextarea = document.getElementById('mail_content');
        const loaiMailSelect = document.getElementById('loai_mail');

        // Điền subject với "Re:" nếu chưa có
        let replySubject = originalSubject;
        if (!replySubject.toLowerCase().startsWith('re:')) {
            replySubject = 'Re: ' + replySubject;
        }
        subjectInput.value = replySubject;

        // Đặt loại mail là liên lạc
        loaiMailSelect.value = 'lien_lac';

        // Điền nội dung mẫu
        const currentDate = new Date().toLocaleDateString('vi-VN');
        contentTextarea.value = 'Kính gửi ' + email + ',\n\n' +
            'Cảm ơn bạn đã phản hồi.\n\n' +
            '[Vui lòng nhập nội dung trả lời]\n\n' +
            'Trân trọng!';

        // Focus vào textarea
        setTimeout(() => {
            contentTextarea.focus();
            // Đặt cursor ở vị trí bắt đầu nội dung cần nhập
            const startPos = contentTextarea.value.indexOf('[Vui lòng nhập nội dung trả lời]');
            if (startPos !== -1) {
                contentTextarea.setSelectionRange(startPos, startPos);
            }
        }, 300);

        // Xóa event listener để tránh lặp lại
        $('#mailHistoryModal').off('hidden.bs.modal');
    });
}


// File selection display for result files
document.getElementById('fileKetQua').addEventListener('change', function(e) {
    const fileList = document.getElementById('fileKetQuaList');
    fileList.innerHTML = '';
    for (let file of e.target.files) {
        const fileItem = document.createElement('div');
        fileItem.className = 'alert alert-success';
        fileItem.innerHTML = `<i class="fa fa-file"></i> ${file.name} (${(file.size / 1024).toFixed(2)} KB)`;
        fileList.appendChild(fileItem);
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize first step
    console.log('DOM loaded, initializing step 1');
    showStep(1);

    const sendMailForm = document.getElementById('sendMailForm');
    const loaiMailSelect = document.getElementById('loai_mail');
    const subjectInput = document.getElementById('mail_subject');
    const contentTextarea = document.getElementById('mail_content');

    // Tự động điền tiêu đề và nội dung mẫu khi chọn loại mail
    loaiMailSelect.addEventListener('change', function() {
        if (this.value === 'bo_sung') {
            subjectInput.value = 'Yêu cầu bổ sung hồ sơ - {{ $hoSo->maHSXL }}';
            contentTextarea.value = 'Kính gửi {{ $hoSo->tenChuHoSo }},\n\nHồ sơ của bạn (Mã: {{ $hoSo->maHSXL }}) cần được bổ sung thêm thông tin/tài liệu sau:\n\n[Vui lòng nhập chi tiết yêu cầu bổ sung]\n\nVui lòng bổ sung và gửi lại trong thời gian sớm nhất.\n\nTrân trọng!';
        } else {
            subjectInput.value = 'Thông báo về hồ sơ - {{ $hoSo->maHSXL }}';
            contentTextarea.value = 'Kính gửi {{ $hoSo->tenChuHoSo }},\n\nVề hồ sơ của bạn (Mã: {{ $hoSo->maHSXL }}):\n\n[Vui lòng nhập nội dung thông báo]\n\nTrân trọng!';
        }
    });

    // Gửi mail
    sendMailForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang gửi...';

        fetch('{{ route('admin.hosoxuly.send-mail', $hoSo->maHSXL) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Đã gửi mail thành công đến ' + data.email);
                // Đóng modal
                $('#sendMailModal').modal('hide');
                // Reset form
                sendMailForm.reset();
                // Reload trang để hiển thị badge "Đã gửi mail"
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể gửi mail'));
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi gửi mail');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });
});

@endpush

<!-- Modal Ý Kiến Xử Lý -->
<div class="modal fade" id="ykienXuLyModal" tabindex="-1" role="dialog" aria-labelledby="ykienXuLyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ykienXuLyModalLabel">Ý kiến xử lý</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ykienForm">
                    <div class="form-group">
                        <label>Nội dung ý kiến:</label>
                        <textarea class="form-control" id="noiDungYKien" rows="4">{{ $hoSo->yKienXuLy }}</textarea>
                        <button type="button" class="btn btn-primary mt-2" onclick="saveYKienComment()">Lưu nội dung</button>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>File đính kèm:</label>
                        <input type="file" id="fileYKienInput" multiple class="form-control" onchange="uploadYKienFiles(this)">
                    </div>
                    <div id="ykienFileListModal" class="mt-3">
                        <!-- File list will be rendered here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kết Quả Xử Lý -->
<div class="modal fade" id="ketQuaXuLyModal" tabindex="-1" role="dialog" aria-labelledby="ketQuaXuLyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ketQuaXuLyModalLabel">Kết quả xử lý</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="ketQuaForm">
                    <div class="form-group">
                        <label>File kết quả:</label>
                        <input type="file" id="fileKetQuaInput" multiple class="form-control" onchange="uploadKetQuaFiles(this)">
                    </div>
                    <div id="ketQuaFileListModal" class="mt-3">
                        <!-- File list will be rendered here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    /* Fix dropdown in modal */
    .modal-body {
        overflow: visible !important;
    }
    .list-group-item {
        overflow: visible !important;
    }
    /* Ensure dropdown menu is on top */
    .dropdown-menu {
        z-index: 10000 !important;
    }
</style>
<script>
    var yKienFiles = @json(json_decode($hoSo->duongdanfileykien, true) ?? []);
    var ketQuaFiles = @json(json_decode($hoSo->duongdanfileketqua, true) ?? []);
    const maHSXL_YKien = '{{ $hoSo->maHSXL }}';

    $(document).ready(function() {
        console.log('Document ready');

        // Ensure files are arrays
        if (!Array.isArray(yKienFiles)) {
            console.error('yKienFiles is not an array:', yKienFiles);
            yKienFiles = [];
        }
        if (!Array.isArray(ketQuaFiles)) {
            console.error('ketQuaFiles is not an array:', ketQuaFiles);
            ketQuaFiles = [];
        }

        console.log('yKienFiles:', yKienFiles);
        console.log('ketQuaFiles:', ketQuaFiles);

        // Initial render is now handled by Blade server-side
        // try {
        //     renderYKienFiles(yKienFiles);
        // } catch (e) {
        //     console.error('Error rendering yKienFiles:', e);
        // }

        // try {
        //     renderKetQuaFiles(ketQuaFiles);
        // } catch (e) {
        //     console.error('Error rendering ketQuaFiles:', e);
        // }

        // Initialize hidden inputs
        if (!Array.isArray(ketQuaFiles)) ketQuaFiles = [];
        const initialVal = JSON.stringify(ketQuaFiles);
        $('#ketQuaFilesInput_Duyet').val(initialVal);
        $('#ketQuaFilesInput_Chuyen').val(initialVal);

        // Add submit handlers to ensure data is synced right before submit
        $('#pheDuyetForm').on('submit', function() {
            console.log('Submitting pheDuyetForm with files:', ketQuaFiles);
            $('#ketQuaFilesInput_Duyet').val(JSON.stringify(ketQuaFiles));
        });

        $('#chuyenLanhDaoForm').on('submit', function() {
            console.log('Submitting chuyenLanhDaoForm with files:', ketQuaFiles);
            $('#ketQuaFilesInput_Chuyen').val(JSON.stringify(ketQuaFiles));
        });

        // Handle Ý kiến xử lý form submission
        $('#yKienXuLyForm').on('submit', function(e) {
            e.preventDefault();
            console.log('yKienXuLyForm submitted');

            // Get file input
            const fileInput = document.getElementById('fileYKien');

            // Check if files selected
            if (fileInput.files && fileInput.files.length > 0) {
                uploadYKienFiles(fileInput);
            }

            // Save comment if provided
            const yKienContent = $(this).find('[name="yKienXuLy"]').val();
            if (yKienContent && yKienContent.trim() !== '') {
                $.ajax({
                    url: '{{ route("admin.hosoxuly.save-ykien", $hoSo->maHSXL) }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        noiDung: yKienContent
                    },
                    success: function(res) {
                        if (res.success) {
                            $('#mainNoiDungYKien').text(yKienContent);
                            alert('Đã lưu ý kiến xử lý');
                            $('#yKienXuLyModal').modal('hide');
                        }
                    },
                    error: function() {
                        alert('Lỗi khi lưu ý kiến');
                    }
                });
            } else if (!fileInput.files || fileInput.files.length === 0) {
                alert('Vui lòng nhập ý kiến hoặc chọn file để tải lên');
            }
        });

        // Handle Kết quả xử lý form submission
        $('#ketQuaXuLyForm').on('submit', function(e) {
            e.preventDefault();
            console.log('ketQuaXuLyForm submitted');

            // Get file input
            const fileInput = document.getElementById('fileKetQua');

            // Check if files selected
            if (fileInput.files && fileInput.files.length > 0) {
                uploadKetQuaFiles(fileInput);
            } else {
                alert('Vui lòng chọn file để tải lên');
            }
        });
    });

    function renderYKienFiles(files) {
        console.log('Rendering yKienFiles...', files);
        // Update Modal List
        let htmlModal = '<ul class="list-group">';
        // Update Main Page List
        let htmlMain = '<ul class="list-group">';

        if (files.length === 0) {
            htmlModal = '<p class="text-muted">Chưa có file đính kèm</p>';
            htmlMain = '<p class="text-muted">Chưa có file đính kèm</p>';
        } else {
            files.forEach(file => {
                const fileName = file.split('/').pop();
                const fileUrl = '{{ asset('') }}' + file;

                // Modal Item (Simple - No dropdown)
                htmlModal += `
                    <li class="list-group-item">
                        <i class="fa fa-file"></i> ${fileName}
                    </li>
                `;

                // Main Page Item with Actions (Dropdown)
                htmlMain += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-file"></i> ${fileName}</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="${fileUrl}" download><i class="fa fa-download"></i> Tải xuống</a>
                                <a class="dropdown-item" href="#" onclick="convertYKienFile('${file}'); return false;"><i class="fa fa-check-circle"></i> Chuyển thành kết quả</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="deleteYKienFile('${file}'); return false;"><i class="fa fa-trash"></i> Xóa file</a>
                            </div>
                        </div>
                    </li>
                `;
            });
            htmlModal += '</ul>';
            htmlMain += '</ul>';
        }

        console.log('Updating DOM for yKienFiles');
        $('#ykienFileListModal').html(htmlModal);
        $('#mainFileList').html(htmlMain);
    }

    function renderKetQuaFiles(files) {
        console.log('Rendering ketQuaFiles...', files);
        if (!Array.isArray(files)) {
            console.error('renderKetQuaFiles called with non-array:', files);
            files = [];
        }

        // Update Modal List
        let htmlModal = '<ul class="list-group">';
        // Update Main Page List
        let htmlMain = '<ul class="list-group">';

        if (files.length === 0) {
            htmlModal = '<p class="text-muted">Chưa có file kết quả</p>';
            htmlMain = '<p class="text-muted">Chưa có file kết quả</p>';
        } else {
            files.forEach(file => {
                const fileName = file.split('/').pop();
                const fileUrl = '{{ asset('') }}' + file;

                // Modal Item (Simple - No dropdown)
                htmlModal += `
                    <li class="list-group-item">
                        <i class="fa fa-file-pdf-o text-danger"></i> ${fileName}
                    </li>
                `;

                // Main Page Item with Actions (Dropdown)
                htmlMain += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa fa-file-pdf-o text-danger"></i> ${fileName}</span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="${fileUrl}" download><i class="fa fa-download"></i> Tải xuống</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" onclick="deleteKetQuaFile('${file}'); return false;"><i class="fa fa-trash"></i> Xóa file</a>
                            </div>
                        </div>
                    </li>
                `;
            });
            htmlModal += '</ul>';
            htmlMain += '</ul>';
        }

        console.log('Updating DOM for ketQuaFiles');
        $('#ketQuaFileListModal').html(htmlModal);
        $('#mainKetQuaList').html(htmlMain);

        // Update hidden inputs immediately
        const jsonFiles = JSON.stringify(files);
        console.log('Updating hidden inputs with:', jsonFiles);
        $('#ketQuaFilesInput_Duyet').val(jsonFiles);
        $('#ketQuaFilesInput_Chuyen').val(jsonFiles);
    }

    function saveYKienComment() {
        const content = $('#noiDungYKien').val();
        $.ajax({
            url: '{{ route("admin.hosoxuly.save-ykien", $hoSo->maHSXL) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                noiDung: content
            },
            success: function(res) {
                if(res.success) {
                    alert('Đã lưu nội dung ý kiến');
                    $('#mainNoiDungYKien').text(content);
                } else {
                    alert('Lỗi khi lưu ý kiến');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra');
            }
        });
    }

    function uploadYKienFiles(input) {
        if (input.files && input.files.length > 0) {
            const formData = new FormData();
            for (let i = 0; i < input.files.length; i++) {
                formData.append('files[]', input.files[i]);
            }
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("admin.hosoxuly.upload-ykien", $hoSo->maHSXL) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if(res.success) {
                        yKienFiles = res.files;
                        renderYKienFiles(yKienFiles);
                        input.value = ''; // Reset input
                    } else {
                        alert('Lỗi upload file');
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi upload');
                }
            });
        }
    }

    function uploadKetQuaFiles(input) {
        if (input.files && input.files.length > 0) {
            const formData = new FormData();
            for (let i = 0; i < input.files.length; i++) {
                formData.append('fileKetQua[]', input.files[i]);
            }
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("admin.hosoxuly.ket-qua-xu-ly", $hoSo->maHSXL) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if(res.success) {
                        ketQuaFiles = res.files;
                        renderKetQuaFiles(ketQuaFiles);
                        input.value = ''; // Reset input
                    } else {
                        alert('Lỗi upload file');
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi upload');
                }
            });
        }
    }

    function deleteYKienFile(filePath) {
        if(!confirm('Bạn có chắc muốn xóa file này?')) return;

        $.ajax({
            url: '{{ route("admin.hosoxuly.remove-ykien-file", $hoSo->maHSXL) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                file: filePath
            },
            success: function(res) {
                if(res.success) {
                    // Remove from local list
                    yKienFiles = yKienFiles.filter(f => f !== filePath);
                    renderYKienFiles(yKienFiles);
                } else {
                    alert('Lỗi khi xóa file');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra');
            }
        });
    }

    function deleteKetQuaFile(filePath) {
        if(!confirm('Bạn có chắc muốn xóa file này?')) return;

        $.ajax({
            url: '{{ route("admin.hosoxuly.remove-ketqua-file", $hoSo->maHSXL) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                file: filePath
            },
            success: function(res) {
                if(res.success) {
                    // Remove from local list
                    ketQuaFiles = ketQuaFiles.filter(f => f !== filePath);
                    renderKetQuaFiles(ketQuaFiles);
                } else {
                    alert('Lỗi khi xóa file');
                }
            },
            error: function() {
                alert('Có lỗi xảy ra');
            }
        });
    }

    function convertYKienFile(filePath) {
        if(!confirm('Chuyển file này thành kết quả xử lý?')) return;

        // Ensure ketQuaFiles is array
        if (!Array.isArray(ketQuaFiles)) ketQuaFiles = [];

        // Check if exists
        if (ketQuaFiles.includes(filePath)) {
            alert('File này đã có trong danh sách kết quả');
            return;
        }

        // Add to local array and update UI
        ketQuaFiles.push(filePath);
        renderKetQuaFiles(ketQuaFiles);

        // Save to database immediately via AJAX
        $.ajax({
            url: '{{ route("admin.hosoxuly.ket-qua-xu-ly", $hoSo->maHSXL) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                converted_files: JSON.stringify(ketQuaFiles)
            },
            success: function(res) {
                if(res.success) {
                    console.log('File đã được lưu vào database');
                    alert('Đã chuyển file thành kết quả xử lý!');
                } else {
                    alert('Lỗi khi lưu: ' + (res.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.error('Save error:', xhr);
                alert('Có lỗi khi lưu vào database. File vẫn được thêm vào danh sách tạm thời.');
            }
        });
    }
</script>

