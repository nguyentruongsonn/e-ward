@extends('layouts.app')

@section('content')
    <div class="container py-5" style="background-color: #f5f5f5; min-height: 80vh;">
        <div class="row">
            <!-- Sidebar (tái sử dụng giống trang profile để user quen giao diện) -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                    <div class="card-body text-center py-4">
                        <img src="{{ asset('img/header/user-avatar.png') }}" alt="Avatar" class="rounded-circle mb-3"
                             style="width: 80px; height: 80px; object-fit: cover;">
                        <h5 class="mb-3" style="color: #333;">{{ $nguoi->hoTen ?? $user->email }}</h5>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="profile-stats-card rounded p-3" style="border-left: 4px solid #007bff;">
                                    <h4 class="profile-stats-number mb-1" style="color:#007bff;">{{ $hoSoHoanThanh }}</h4>
                                    <small class="text-muted">Hồ sơ đã hoàn thành</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="profile-stats-card rounded p-3" style="border-left: 4px solid #007bff;">
                                    <h4 class="profile-stats-number mb-1" style="color:#007bff;">{{ $hoSoDangXuLy }}</h4>
                                    <small class="text-muted">Hồ sơ đang xử lý</small>
                                </div>
                            </div>
                        </div>

                        <div class="list-group list-group-flush text-start">
                            <a href="{{ route('profile') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-folder me-2"></i> Hồ sơ trực tuyến
                            </a>
                            <a href="{{ route('profile.appointments') }}" class="list-group-item list-group-item-action active">
                                <i class="fas fa-calendar-alt me-2"></i> Lịch hẹn
                            </a>
                            <a href="{{ route('profile.payments') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-history me-2"></i> Lịch sử thanh toán
                            </a>
                            <a href="{{ route('profile.notifications') }}" class="list-group-item list-group-item-action">
                                <i class="fas fa-bell me-2"></i> Thông báo
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-2">{{ $unreadCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nội dung chính: chi tiết lịch hẹn -->
            <div class="col-lg-9">
                <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            Chi tiết lịch hẹn
                        </h5>
                        <a href="{{ route('profile.appointments') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Mã lịch hẹn</p>
                                <h6>{{ $appointment->maLichHen }}</h6>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Thủ tục hành chính</p>
                                <h6>{{ $appointment->tthc->tenTTHC ?? '-' }}</h6>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Thời gian hẹn</p>
                                <h6>{{ $appointment->thoiGianHen ? $appointment->thoiGianHen->format('d/m/Y H:i') : '-' }}</h6>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Quầy làm việc</p>
                                <h6>
                                    @if($appointment->quaylamviec)
                                        {{ $appointment->quaylamviec->tenQuayLamViec }} ({{ $appointment->maQuayLamViec }})
                                    @else
                                        {{ $appointment->maQuayLamViec ?? 'Chưa phân quầy' }}
                                    @endif
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Số thứ tự</p>
                                <h6>{{ $appointment->soThuTu ?? '-' }}</h6>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Trạng thái</p>
                                <h6>
                                    <span class="badge
                                        @if($appointment->trangThai == 'Hoàn thành') bg-success
                                        @elseif($appointment->trangThai == 'Đang xử lý') bg-info
                                        @elseif($appointment->trangThai == 'Đã hủy' || $appointment->trangThai == 'Không đến') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ $appointment->trangThai }}
                                    </span>
                                </h6>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Thời gian check-in</p>
                                <h6>{{ $appointment->checkin_time ? $appointment->checkin_time->format('d/m/Y H:i') : '-' }}</h6>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Mã check-in (token)</p>
                                <h6>{{ $appointment->checkin_token ?? '-' }}</h6>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex flex-wrap gap-2">
                            @if($appointment->checkin_token)
                                <a href="{{ route('appointment.checkin', $appointment->checkin_token) }}"
                                   class="btn btn-outline-primary"
                                   target="_blank">
                                    <i class="fas fa-qrcode me-1"></i> Xem QR Check-in
                                </a>
                            @endif

                            @php
                                // Chỉ cho phép hủy khi: trạng thái đúng VÀ chưa tới giờ hẹn
                                $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                                $thoiGianHen = $appointment->thoiGianHen ? \Carbon\Carbon::parse($appointment->thoiGianHen)->setTimezone('Asia/Ho_Chi_Minh') : null;
                                $canCancel = in_array($appointment->trangThai, ['Đã đặt lịch', 'Chờ đến']) &&
                                             $thoiGianHen && $thoiGianHen->gt($now);
                            @endphp

                            @if($canCancel)
                                <form action="{{ route('profile.appointments.cancel', $appointment->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không?');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-times me-1"></i> Hủy lịch hẹn
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


