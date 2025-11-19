@extends('layouts.app')

@push('styles')
    <style>
        :root {
            --accent-color: #CE7A58;
        }

        .profile-stats-card {
            border-left: 4px solid var(--accent-color) !important;
            border-top: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
            border-bottom: 1px solid #dee2e6;
            background: white !important;
        }

        .profile-stats-number {
            color: var(--accent-color);
            font-size: 2rem;
            font-weight: bold;
        }

        .profile-menu-item {
            border: none;
            background: white;
            color: #333;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .profile-menu-item:hover {
            background-color: var(--accent-color) !important;
            color: white !important;
        }

        .profile-menu-item.active {
            background-color: #f5e6d3 !important;
            color: #333 !important;
            border-left: 4px solid var(--accent-color) !important;
        }

        .profile-menu-item.active-parent {
            background-color: #f5e6d3 !important;
            color: #333 !important;
            border-left: 4px solid var(--accent-color) !important;
        }

        /* Rotate chevron only when expanded */
        .profile-menu-item[aria-expanded="true"] .fa-chevron-down {
            transform: rotate(180deg);
        }

        .collapse .profile-menu-item {
            padding-left: 3rem !important;
        }

        .profile-form-control {
            border-color: var(--accent-color) !important;
        }

        .profile-form-control:focus {
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(206, 122, 88, 0.25) !important;
        }

        .profile-search-btn {
            background-color: var(--accent-color) !important;
            border-color: var(--accent-color) !important;
            color: white !important;
        }

        .profile-search-btn:hover {
            background-color: #b86947 !important;
            border-color: #b86947 !important;
        }

        .profile-header {
            background: linear-gradient(135deg, #f5e6d3 0%, #f0d4b8 100%);
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }

        .profile-header-icon {
            color: var(--accent-color);
            font-size: 2rem;
        }

        .profile-no-results {
            color: var(--accent-color);
            text-align: center;
            padding: 40px 20px;
            font-size: 1.1rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5" style="background-color: #f5f5f5; min-height: 80vh;">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                    <div class="card-body text-center py-4">
                        <img src="{{ asset('img/header/user-avatar.png') }}" alt="Avatar" class="rounded-circle mb-3"
                            style="width: 80px; height: 80px; object-fit: cover;">
                        <h5 class="mb-3" style="color: #333;">{{ $nguoi->hoTen ?? $user->email }}</h5>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="profile-stats-card rounded p-3">
                                    <h4 class="profile-stats-number mb-1">{{ $hoSoHoanThanh }}</h4>
                                    <small class="text-muted">Hồ sơ đã hoàn thành</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="profile-stats-card rounded p-3">
                                    <h4 class="profile-stats-number mb-1">{{ $hoSoDangXuLy }}</h4>
                                    <small class="text-muted">Hồ sơ đang xử lý</small>
                                </div>
                            </div>
                        </div>

                        <div class="list-group list-group-flush text-start"
                            style="border: 1px solid var(--accent-color); border-radius: 4px;">
                            <!-- Thông tin tài khoản (Collapsible) -->
                            <a href="javascript:void(0)"
                                class="list-group-item profile-menu-item d-flex justify-content-between align-items-center {{ ($activePage ?? 'services') == 'identity' ? 'active-parent' : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#accountInfoCollapse"
                                aria-controls="accountInfoCollapse"
                                aria-expanded="{{ ($activePage ?? 'services') == 'identity' ? 'true' : 'false' }}">
                                <span><i class="fas fa-user me-2"></i> Thông tin tài khoản</span>
                                <i class="fas fa-chevron-down fa-sm"></i>
                            </a>
                            <div class="collapse {{ ($activePage ?? 'services') == 'identity' ? 'show' : '' }}"
                                id="accountInfoCollapse">
                                <a href="{{ route('profile.identity') }}"
                                    class="list-group-item profile-menu-item ps-5 {{ ($activePage ?? 'services') == 'identity' ? 'active' : '' }}">
                                    <i class="fas fa-id-card me-2"></i> Thông tin định danh
                                </a>
                                {{-- <a href="#" class="list-group-item profile-menu-item ps-5">
                                    <i class="fas fa-info-circle me-2"></i> Thông tin mở rộng
                                </a> --}}
                            </div>

                            <a href="javascript:void(0)"
                                class="list-group-item profile-menu-item {{ ($activePage ?? 'services') == 'services' ? 'active-parent' : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#serviceManageCollapse"
                                aria-controls="serviceManageCollapse"
                                aria-expanded="{{ ($activePage ?? 'services') == 'services' ? 'true' : 'false' }}">
                                <span class="float-end"><i class="fas fa-chevron-down fa-sm"></i></span>
                                <i class="fas fa-cog me-2"></i> Quản lý dịch vụ công
                            </a>
                            <div class="collapse {{ ($activePage ?? 'services') == 'services' ? 'show' : '' }}"
                                id="serviceManageCollapse">
                                <a href="{{ route('profile') }}"
                                    class="list-group-item profile-menu-item ps-5 {{ ($activePage ?? 'services') == 'services' ? 'active' : '' }}">
                                    <i class="fas fa-folder me-2"></i> Dịch vụ công của tôi
                                </a>
                            </div>
                            <a href="{{ route('profile.password-change') }}"
                                class="list-group-item profile-menu-item {{ ($activePage ?? 'services') == 'password-change' || ($activePage ?? 'services') == 'password-change-verify' ? 'active' : '' }}">
                                <i class="fas fa-file-alt me-2"></i> Đổi mật khẩu
                            </a>
                            {{-- <a href="#" class="list-group-item profile-menu-item">
                                <i class="fas fa-tools me-2"></i> Tiện ích
                            </a>
                            <a href="#" class="list-group-item profile-menu-item">
                                <i class="fas fa-link me-2"></i> Liên kết tài khoản
                            </a> --}}
                            <a href="{{ route('profile.payments') }}"
                                class="list-group-item profile-menu-item {{ ($activePage ?? 'services') == 'payments' ? 'active' : '' }}">
                                <i class="fas fa-history me-2"></i> Lịch sử thanh toán
                            </a>
                            <a href="{{ route('profile.notifications') }}"
                                class="list-group-item profile-menu-item {{ ($activePage ?? 'services') == 'notifications' ? 'active' : '' }}">
                                <i class="fas fa-bell me-2"></i> Thông báo
                                @if (isset($unreadCount) && $unreadCount > 0)
                                    <span class="badge bg-danger rounded-pill ms-2 notification-badge"
                                        id="notificationBadge">{{ $unreadCount }}</span>
                                @else
                                    <span class="badge bg-danger rounded-pill ms-2 notification-badge"
                                        id="notificationBadge" style="display: none;">0</span>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                @if (($activePage ?? 'services') == 'identity')
                    <!-- Thông tin định danh -->
                    <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-id-card me-2 text-primary"></i>
                                Thông tin định danh
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Họ tên:</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span class="flex-grow-1">{{ $nguoi->hoTen ?? 'Chưa có dữ liệu' }}</span>
                                        @if ($nguoi->hoTen)
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Số CMT (9 số):</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span class="flex-grow-1">Chưa có dữ liệu</span>
                                        <i class="fas fa-minus-circle text-danger ms-2"
                                            title="Trường thông tin không có dữ liệu"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Số CMT/CCCD (12 số):</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span class="flex-grow-1">{{ $nguoi->maCCCD ?? 'Chưa có dữ liệu' }}</span>
                                        @if ($nguoi->maCCCD)
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ngày sinh:</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span
                                            class="flex-grow-1">{{ $nguoi->ngaySinh ? \Carbon\Carbon::parse($nguoi->ngaySinh)->format('d/m/Y') : 'Chưa có dữ liệu' }}</span>
                                        @if ($nguoi->ngaySinh)
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Số điện thoại:</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span class="flex-grow-1">{{ $nguoi->soDienThoai ?? 'Chưa có dữ liệu' }}</span>
                                        @if ($nguoi->soDienThoai)
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Giới tính:</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span class="flex-grow-1">{{ $nguoi->gioiTinh ?? 'Chưa có dữ liệu' }}</span>
                                        @if ($nguoi->gioiTinh)
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email:</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span
                                            class="flex-grow-1">{{ $nguoi->email ?? ($user->email ?? 'Chưa có dữ liệu') }}</span>
                                        @if ($nguoi->email || ($user->email ?? false))
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Quê Quán:</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span class="flex-grow-1">{{ $nguoi->queQuan ?? 'Chưa có dữ liệu' }}</span>
                                        @if ($nguoi->queQuan)
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Địa chỉ thường trú:</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span class="flex-grow-1">{{ $nguoi->noiThuongTru ?? 'Chưa có dữ liệu' }}</span>
                                        @if ($nguoi->noiThuongTru)
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nơi tạm trú:</label>
                                    <div class="d-flex align-items-center border rounded p-2 bg-light">
                                        <span class="flex-grow-1">{{ $nguoi->noiTamTru ?? 'Chưa có dữ liệu' }}</span>
                                        @if ($nguoi->noiTamTru)
                                            <i class="fas fa-exclamation-circle text-warning ms-2"
                                                title="Thông tin do Người dùng tự nhập và chưa được xác minh"></i>
                                        @else
                                            <i class="fas fa-minus-circle text-danger ms-2"
                                                title="Trường thông tin không có dữ liệu"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-3">
                                <p class="fw-bold">Ghi chú về các biểu tượng dữ liệu:</p>
                                <p class="mb-1"><i class="fas fa-check-circle text-success me-2"></i> Biểu tượng Xanh là
                                    các thông tin đã được xác minh với Cơ sở dữ liệu Dân cư Quốc gia hoặc CSDL tin cậy khác
                                </p>
                                <p class="mb-1"><i class="fas fa-exclamation-circle text-warning me-2"></i> Biểu tượng
                                    Vàng là các thông tin do Người dùng tự nhập và chưa được xác minh với 1 CSDL tin cậy</p>
                                <p class="mb-1"><i class="fas fa-minus-circle text-danger me-2"></i> Biểu tượng Đỏ là
                                    các trường thông tin không có dữ liệu</p>
                            </div>
                        </div>
                    </div>
                @elseif (($activePage ?? 'services') == 'services')
                    <!-- Dịch vụ công của tôi -->
                    <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                        <div class="profile-header">
                            <h4 class="mb-0" style="color: #333;">
                                <i class="fas fa-folder profile-header-icon me-2"></i>
                                Dịch vụ công của tôi
                            </h4>
                        </div>
                        <div class="card-body">
                            <!-- Search Form -->
                            <form method="GET" action="{{ route('profile') }}" class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="ten_dich_vu" class="form-label">Tên dịch vụ công</label>
                                        <input type="text" class="form-control profile-form-control" id="ten_dich_vu"
                                            name="ten_dich_vu" value="{{ request('ten_dich_vu') }}"
                                            placeholder="Nhập tên dịch vụ công">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="ma_ho_so" class="form-label">Mã hồ sơ</label>
                                        <input type="text" class="form-control profile-form-control" id="ma_ho_so"
                                            name="ma_ho_so" value="{{ request('ma_ho_so') }}"
                                            placeholder="Nhập mã hồ sơ">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="trang_thai" class="form-label">Trạng thái hồ sơ</label>
                                        <select class="form-select profile-form-control" id="trang_thai"
                                            name="trang_thai">
                                            <option value="">-- Chọn trạng thái hồ sơ --</option>
                                            <option value="dang_xu_ly"
                                                {{ request('trang_thai') == 'dang_xu_ly' ? 'selected' : '' }}>Đang xử lý
                                            </option>
                                            <option value="da_hoan_thanh"
                                                {{ request('trang_thai') == 'da_hoan_thanh' ? 'selected' : '' }}>Đã hoàn
                                                thành</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn profile-search-btn">
                                            <i class="fas fa-search me-2"></i>Tìm kiếm
                                        </button>
                                        <a href="{{ route('profile') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo me-2"></i>Làm mới
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <!-- Results -->
                            @if (isset($hoSoList) && $hoSoList->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Mã hồ sơ</th>
                                                <th>Tên dịch vụ</th>
                                                <th>Ngày tiếp nhận</th>
                                                <th>Ngày hẹn trả</th>
                                                <th>Ngày hẹn gần nhất</th>
                                                <th>Trạng thái</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody id="servicesList">
                                            @include('partials.service-items', ['hoSoList' => $hoSoList])
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Load More Button & Pagination -->
                                <div class="mt-4">
                                    @if ($hoSoList->hasMorePages())
                                        <!-- Load More Button (AJAX) -->
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn profile-search-btn px-4 py-2"
                                                id="loadMoreServicesBtn" data-page="2"
                                                data-ten-dich-vu="{{ request('ten_dich_vu') ?? '' }}"
                                                data-ma-ho-so="{{ request('ma_ho_so') ?? '' }}"
                                                data-trang-thai="{{ request('trang_thai') ?? '' }}">
                                                <i class="fas fa-chevron-down me-2"></i>
                                                Xem thêm hồ sơ ({{ $hoSoList->total() - $hoSoList->count() }} còn lại)
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Pagination Info -->
                                    <div class="text-center text-muted mb-2">
                                        <small id="servicesPaginationInfo">
                                            Hiển thị {{ $hoSoList->firstItem() ?? 0 }}-{{ $hoSoList->lastItem() ?? 0 }}
                                            trong tổng số {{ $hoSoList->total() }} hồ sơ
                                        </small>
                                    </div>

                                    <!-- Traditional Pagination (Fallback) -->
                                    <div class="d-flex justify-content-center">
                                        {{ $hoSoList->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @else
                                <div class="profile-no-results">
                                    Không tìm thấy hồ sơ nào thoả mãn điều kiện tìm kiếm
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (($activePage ?? 'services') == 'payments')
                    <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">
                                <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                                Lịch sử thanh toán
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('profile.payments') }}" class="mb-4">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Loại giao dịch</label>
                                        <select name="loai_gd" class="form-select profile-form-control">
                                            <option value="all"
                                                {{ request('loai_gd', 'all') == 'all' ? 'selected' : '' }}>-- Chọn loại
                                                giao
                                                dịch --</option>
                                            <option value="nop_le_phi"
                                                {{ request('loai_gd') == 'nop_le_phi' ? 'selected' : '' }}>Nộp lệ phí
                                            </option>
                                            <option value="hoan_tien"
                                                {{ request('loai_gd') == 'hoan_tien' ? 'selected' : '' }}>Hoàn tiền
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Từ ngày</label>
                                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                                            class="form-control profile-form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Đến ngày</label>
                                        <input type="date" name="to_date" value="{{ request('to_date') }}"
                                            class="form-control profile-form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn profile-search-btn w-100"><i
                                                class="fas fa-search me-2"></i>Tìm</button>
                                    </div>
                                </div>
                            </form>

                            @if (isset($payments) && $payments->count() > 0)
                                <h6 class="mb-3">Danh sách giao dịch thanh toán</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Mã GD</th>
                                                <th>Số GD</th>
                                                <th>Loại GD</th>
                                                <th>Ngày GD</th>
                                                <th>Số tiền</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payments as $gd)
                                                <tr>
                                                    <td>{{ $gd->maGD }}</td>
                                                    <td>{{ $gd->soGD ?? '-' }}</td>
                                                    <td>{{ $gd->loaiGD ?? '-' }}</td>
                                                    <td>{{ $gd->ngayGD ? $gd->ngayGD->format('d/m/Y H:i') : '-' }}</td>
                                                    <td>{{ $gd->soTien !== null ? number_format($gd->soTien, 0, ',', '.') . ' đ' : '-' }}
                                                    </td>
                                                    <td>{{ $gd->trangThai ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $payments->links() }}
                                </div>
                            @else
                                <div class="profile-no-results">
                                    Không tìm thấy lịch sử thanh toán theo điều kiện tìm kiếm
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (($activePage ?? 'services') == 'notifications')
                    <!-- Thông báo đặt lịch hẹn -->
                    <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                        <div class="profile-header">
                            <h4 class="mb-0" style="color: #333;">
                                <i class="fas fa-bell profile-header-icon me-2"></i>
                                Thông báo đặt lịch hẹn
                            </h4>
                        </div>
                        <div class="card-body">
                            <!-- Filter Form -->
                            <form method="GET" action="{{ route('profile.notifications') }}" class="mb-4">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="only_unread"
                                                id="only_unread" value="1"
                                                {{ request('only_unread') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="only_unread">
                                                Chỉ hiển thị thông báo chưa đọc
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="submit" class="btn profile-search-btn">
                                            <i class="fas fa-filter me-2"></i>Lọc
                                        </button>
                                        <a href="{{ route('profile.notifications') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo me-2"></i>Làm mới
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <!-- Notifications List -->
                            @if (isset($notifications) && $notifications->count() > 0)
                                <div class="list-group" id="notificationsList">
                                    @include('partials.notification-items', [
                                        'notifications' => $notifications,
                                    ])
                                </div>

                                <!-- Load More Button & Pagination -->
                                <div class="mt-4">
                                    @if ($notifications->hasMorePages())
                                        <!-- Load More Button (AJAX) -->
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn profile-search-btn px-4 py-2"
                                                id="loadMoreBtn" data-page="2"
                                                data-only-unread="{{ request('only_unread') ? '1' : '0' }}">
                                                <i class="fas fa-chevron-down me-2"></i>
                                                Xem thêm thông báo ({{ $notifications->total() - $notifications->count() }}
                                                còn lại)
                                            </button>
                                        </div>
                                    @endif

                                    <!-- Pagination Info -->
                                    <div class="text-center text-muted mb-2">
                                        <small>
                                            Hiển thị
                                            {{ $notifications->firstItem() ?? 0 }}-{{ $notifications->lastItem() ?? 0 }}
                                            trong tổng số {{ $notifications->total() }} thông báo
                                        </small>
                                    </div>

                                    <!-- Traditional Pagination (Fallback) -->
                                    <div class="d-flex justify-content-center">
                                        {{ $notifications->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            @else
                                <div class="profile-no-results">
                                    <i class="fas fa-bell-slash fa-3x mb-3" style="opacity: 0.3;"></i>
                                    <p class="mb-0">Bạn không có thông báo nào!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (($activePage ?? 'services') == 'password-change')
                    <!-- Đổi mật khẩu - Form nhập mật khẩu hiện tại -->
                    <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                        <div class="profile-header">
                            <h4 class="mb-0" style="color: #333;">
                                <i class="fas fa-key profile-header-icon me-2"></i>
                                Đổi mật khẩu
                            </h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('profile.password-change.request') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="current_password" class="form-label fw-bold">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control profile-form-control @error('current_password') is-invalid @enderror" 
                                        id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn profile-search-btn">
                                        <i class="fas fa-paper-plane me-2"></i>Gửi mã OTP
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if (($activePage ?? 'services') == 'password-change-verify')
                    <!-- Đổi mật khẩu - Form nhập OTP và mật khẩu mới -->
                    <div class="card shadow-sm" style="border: 1px solid #dee2e6;">
                        <div class="profile-header">
                            <h4 class="mb-0" style="color: #333;">
                                <i class="fas fa-key profile-header-icon me-2"></i>
                                Xác thực đổi mật khẩu
                            </h4>
                        </div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <p class="text-muted mb-4">
                                Mã OTP đã được gửi tới email: <strong>{{ $email ?? '' }}</strong>
                            </p>

                            <form method="POST" action="{{ route('profile.password-change.verify.submit') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="code" class="form-label fw-bold">Mã OTP</label>
                                    <input type="text" class="form-control profile-form-control @error('code') is-invalid @enderror" 
                                        id="code" name="code" required maxlength="6" pattern="[0-9]{6}" 
                                        placeholder="Nhập 6 chữ số">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label fw-bold">Mật khẩu mới</label>
                                    <input type="password" class="form-control profile-form-control @error('new_password') is-invalid @enderror" 
                                        id="new_password" name="new_password" required minlength="6">
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label fw-bold">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control profile-form-control" 
                                        id="new_password_confirmation" name="new_password_confirmation" required minlength="6">
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                    <button type="submit" class="btn profile-search-btn">
                                        <i class="fas fa-check me-2"></i>Xác nhận đổi mật khẩu
                                    </button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('profile.password-change.resend-otp') }}" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Gửi lại mã OTP
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show success message if exists
            @if (session('success'))
                alert('{{ session('success') }}');
            @endif

            const modalEl = document.getElementById('hosoDetailModal');
            if (modalEl) {
                const modalTitle = modalEl.querySelector('.modal-title');
                const modalBody = modalEl.querySelector('.modal-body');

                // Wire sidebar "Lịch sử thanh toán" link if static
                try {
                    const historyIcon = document.querySelector('.list-group .profile-menu-item i.fas.fa-history');
                    if (historyIcon) {
                        const historyA = historyIcon.closest('a');
                        if (historyA) historyA.setAttribute('href', "{{ route('profile.payments') }}");
                    }
                } catch (e) {}

                document.body.addEventListener('click', async function(e) {
                    const btn = e.target.closest('.btn-info');
                    if (!btn) return;
                    if (btn.tagName === 'A') {
                        e.preventDefault();
                    }
                    const row = btn.closest('tr');
                    const url = row ? row.getAttribute('data-detail-url') : null;
                    
                    // Debug: log URL and check if it contains '0'
                    console.log('URL from data-detail-url:', url);
                    console.log('Row element:', row);
                    console.log('Button element:', btn);
                    
                    // Also try to get maHSXL from button data attribute
                    const maHSXLFromButton = btn.getAttribute('data-ma-hsxl');
                    console.log('maHSXL from button:', maHSXLFromButton);
                    
                    if (!url || url.includes('/0') || url.endsWith('/0')) {
                        console.error('URL không hợp lệ:', url);
                        alert('Không tìm thấy thông tin chi tiết hồ sơ. Vui lòng làm mới trang và thử lại.');
                        return;
                    }
                    
                    // Validate URL doesn't end with /0
                    if (url.match(/\/0(\?|$)/)) {
                        console.error('URL có mã hồ sơ = 0:', url);
                        alert('Mã hồ sơ không hợp lệ. Vui lòng liên hệ bộ phận hỗ trợ.');
                        return;
                    }
                    
                    // Show loading state in modal
                    if (!modalEl) {
                        console.error('Không tìm thấy modal element');
                        alert('Không thể hiển thị modal. Vui lòng làm mới trang.');
                        return;
                    }
                    
                    // Set loading content and show modal immediately
                    modalTitle.textContent = 'Đang tải...';
                    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div><p class="mt-2 text-muted">Vui lòng chờ...</p></div>';
                    
                    // Show modal first with loading state
                    const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    bsModal.show();
                    
                    try {
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        // Parse JSON response (only once)
                        let data;
                        try {
                            data = await res.json();
                        } catch (jsonError) {
                            console.error('Lỗi parse JSON:', jsonError);
                            throw new Error(`Lỗi ${res.status}: Không thể đọc dữ liệu từ server`);
                        }
                        
                        // Check if response is an error
                        if (!res.ok || data.error) {
                            const errorMessage = data.message || data.error || `Lỗi ${res.status}: Không thể tải chi tiết hồ sơ`;
                            console.error('Lỗi từ server:', res.status, errorMessage);
                            throw new Error(errorMessage);
                        }
                        
                        console.log('Dữ liệu nhận được:', data);
                        
                        if (!data || !data.type) {
                            console.error('Dữ liệu không hợp lệ:', data);
                            throw new Error('Dữ liệu không hợp lệ. Vui lòng thử lại.');
                        }
                        
                        // Kiểm tra loại modal: 'appointment' (đặt lịch) hoặc 'service' (nộp hồ sơ)
                        if (data.type === 'appointment') {
                            // Modal cho đặt lịch hẹn
                            modalTitle.textContent = `Chi tiết lịch hẹn - ${data.tenTTHC || ''}`;
                            
                            const lichHenHtml = (data.lichHenList && data.lichHenList.length)
                                ? data.lichHenList.map(item => {
                                    const statusBadge = item.trangThai === 'Đã đặt lịch' ? 'bg-info' 
                                        : item.trangThai === 'Chờ đến' ? 'bg-warning' 
                                        : item.trangThai === 'Đang xử lý' ? 'bg-primary' 
                                        : item.trangThai === 'Hoàn thành' ? 'bg-success' 
                                        : 'bg-secondary';
                                    
                                    return `
                                        <div class="card mb-3 border-primary">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Lịch hẹn: ${item.maLichHen ?? 'N/A'}</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-clock me-2"></i>Thời gian hẹn:</strong>
                                                        <div class="mt-1">${item.thoiGianHen ?? 'N/A'}</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-info-circle me-2"></i>Trạng thái:</strong>
                                                        <div class="mt-1">
                                                            <span class="badge ${statusBadge}">${item.trangThai ?? 'N/A'}</span>
                                                        </div>
                                                    </div>
                                                    ${item.soThuTu ? `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-sort-numeric-up me-2"></i>Số thứ tự:</strong>
                                                        <div class="mt-1"><span class="badge bg-dark">${item.soThuTu}</span></div>
                                                    </div>
                                                    ` : ''}
                                                    ${item.tenQuayLamViec ? `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-door-open me-2"></i>Quầy làm việc:</strong>
                                                        <div class="mt-1">${item.tenQuayLamViec} <small class="text-muted">(Mã: ${item.maQuayLamViec})</small></div>
                                                    </div>
                                                    ` : item.maQuayLamViec ? `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-door-open me-2"></i>Mã quầy:</strong>
                                                        <div class="mt-1">${item.maQuayLamViec}</div>
                                                    </div>
                                                    ` : `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-door-open me-2"></i>Quầy làm việc:</strong>
                                                        <div class="mt-1"><span class="text-muted">Chưa được chọn (sẽ chọn khi check-in)</span></div>
                                                    </div>
                                                    `}
                                                    ${item.checkin_time ? `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-check-circle me-2 text-success"></i>Thời gian check-in:</strong>
                                                        <div class="mt-1">${item.checkin_time}</div>
                                                    </div>
                                                    ` : ''}
                                                    ${item.checkin_token ? `
                                                    <div class="col-12">
                                                        <strong><i class="fas fa-key me-2"></i>Mã check-in:</strong>
                                                        <div class="mt-1">
                                                            <code class="small bg-light p-2 rounded d-inline-block">${item.checkin_token}</code>
                                                        </div>
                                                    </div>
                                                    ` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }).join('')
                                : '<p class="mb-0 text-muted text-center py-3">Chưa có lịch hẹn.</p>';
                            
                            modalBody.innerHTML = `
                                <div>
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Thông tin:</strong> Đây là hồ sơ được tạo tự động khi đặt lịch hẹn.
                                    </div>
                                    <h5 class="mb-3"><i class="fas fa-list me-2"></i>Danh sách lịch hẹn</h5>
                                    ${lichHenHtml}
                                </div>
                            `;
                        } else {
                            // Modal cho nộp hồ sơ trực tuyến
                            const lichHenHtml = (data.lichHenList && data.lichHenList.length)
                                ? data.lichHenList.map(item => {
                                    const statusBadge = item.trangThai === 'Đã đặt lịch' ? 'bg-info' 
                                        : item.trangThai === 'Chờ đến' ? 'bg-warning' 
                                        : item.trangThai === 'Đang xử lý' ? 'bg-primary' 
                                        : item.trangThai === 'Hoàn thành' ? 'bg-success' 
                                        : 'bg-secondary';
                                    
                                    return `
                                        <div class="card mb-2 border-secondary">
                                            <div class="card-body">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-calendar-alt me-2"></i>Mã lịch hẹn:</strong>
                                                        <div class="mt-1">${item.maLichHen ?? 'N/A'}</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-clock me-2"></i>Thời gian hẹn:</strong>
                                                        <div class="mt-1">${item.thoiGianHen ?? 'N/A'}</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-info-circle me-2"></i>Trạng thái:</strong>
                                                        <div class="mt-1">
                                                            <span class="badge ${statusBadge}">${item.trangThai ?? 'N/A'}</span>
                                                        </div>
                                                    </div>
                                                    ${item.soThuTu ? `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-sort-numeric-up me-2"></i>Số thứ tự:</strong>
                                                        <div class="mt-1"><span class="badge bg-dark">${item.soThuTu}</span></div>
                                                    </div>
                                                    ` : ''}
                                                    ${item.tenQuayLamViec ? `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-door-open me-2"></i>Quầy làm việc:</strong>
                                                        <div class="mt-1">${item.tenQuayLamViec} <small class="text-muted">(Mã: ${item.maQuayLamViec})</small></div>
                                                    </div>
                                                    ` : item.maQuayLamViec ? `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-door-open me-2"></i>Mã quầy:</strong>
                                                        <div class="mt-1">${item.maQuayLamViec}</div>
                                                    </div>
                                                    ` : ''}
                                                    ${item.checkin_time ? `
                                                    <div class="col-md-6">
                                                        <strong><i class="fas fa-check-circle me-2 text-success"></i>Thời gian check-in:</strong>
                                                        <div class="mt-1">${item.checkin_time}</div>
                                                    </div>
                                                    ` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }).join('')
                                : '<p class="mb-0 text-muted text-center py-3">Chưa có lịch hẹn.</p>';
                            
                            modalTitle.textContent = `Hồ sơ ${data.maHSXL} - ${data.tenTTHC || ''}`;
                            modalBody.innerHTML = `
                                <div class="row g-3">
                                    <div class="col-md-6"><strong>Tên chủ hồ sơ:</strong> ${data.tenChuHoSo ?? ''}</div>
                                    <div class="col-md-6"><strong>SĐT:</strong> ${data.soDienThoai ?? ''}</div>
                                    <div class="col-md-6"><strong>Email:</strong> ${data.email ?? ''}</div>
                                    <div class="col-md-6"><strong>Đơn vị xử lý:</strong> ${data.donViXuLy ?? ''}</div>
                                    <div class="col-md-4"><strong>Ngày tiếp nhận:</strong> ${data.ngayTiepNhan ?? ''}</div>
                                    <div class="col-md-4"><strong>Ngày hẹn trả:</strong> ${data.ngayHenTra ?? ''}</div>
                                    <div class="col-md-4"><strong>Ngày kết thúc:</strong> ${data.ngayKetThucXuLy ?? ''}</div>
                                    <div class="col-md-4"><strong>Trạng thái:</strong> ${data.maTrangThai ?? ''}</div>
                                    <div class="col-md-4"><strong>Lệ phí:</strong> ${data.lePhi ?? ''}</div>
                                    <div class="col-md-4"><strong>Hình thức:</strong> ${data.hinhThuc ?? ''}</div>
                                    ${(() => {
                                        // Nếu có "Danh sách đã nộp", ẩn 3 trường này ở ngoài (chỉ hiển thị trong danh sách)
                                        const hasDanhSachNop = data.danhSachNop && data.danhSachNop.length > 0;
                                        if (hasDanhSachNop) {
                                            return '';
                                        }
                                        // Nếu không có danh sách, hiển thị bình thường
                                        return `
                                            ${data.thongTinTra ? `<div class="col-12"><strong>Thông tin trả:</strong><div class="border rounded p-2 bg-light">${(data.thongTinTra ?? '').toString()}</div></div>` : ''}
                                            ${data.ghiChu ? `<div class="col-12"><strong>Ghi chú:</strong><div class="border rounded p-2 bg-light">${(data.ghiChu ?? '').toString()}</div></div>` : ''}
                                            ${(Array.isArray(data.dulieu) || (data.dulieu && typeof data.dulieu === 'object')) ? `
                                                <div class="col-12"><strong>Dữ liệu hồ sơ:</strong>
                                                    <pre id="dulieu-json" class="bg-light border rounded p-2" style="white-space: pre-wrap;"></pre>
                                                </div>
                                            ` : ''}
                                        `;
                                    })()}
                                    ${(data.lichHenList && data.lichHenList.length > 0) ? `
                                    <div class="col-12"><strong>Lịch hẹn liên quan:</strong>
                                        <div class="mt-2">
                                            ${lichHenHtml}
                                        </div>
                                    </div>
                                    ` : ''}
                                    ${(data.danhSachNop && data.danhSachNop.length > 0) ? `
                                    <div class="col-12"><strong>Danh sách đã nộp:</strong>
                                        <div class="mt-2">
                                            ${data.danhSachNop.map((item, index) => {
                                                const statusBadge = item.trangThai === 'Đã hoàn thành' ? 'bg-success' : 'bg-warning';
                                                return `
                                                    <div class="card mb-3 border-secondary">
                                                        <div class="card-body">
                                                            <div class="row g-3">
                                                                <div class="col-md-6"><strong>Tên chủ hồ sơ:</strong> ${item.tenChuHoSo ?? ''}</div>
                                                                <div class="col-md-6"><strong>SĐT:</strong> ${item.soDienThoai ?? ''}</div>
                                                                <div class="col-md-6"><strong>Email:</strong> ${item.email ?? ''}</div>
                                                                <div class="col-md-6"><strong>Đơn vị xử lý:</strong> ${item.donViXuLy ?? ''}</div>
                                                                <div class="col-md-4"><strong>Ngày tiếp nhận:</strong> ${item.ngayTiepNhan ?? ''}</div>
                                                                <div class="col-md-4"><strong>Ngày hẹn trả:</strong> ${item.ngayHenTra ?? ''}</div>
                                                                <div class="col-md-4"><strong>Ngày kết thúc:</strong> ${item.ngayKetThucXuLy ?? ''}</div>
                                                                <div class="col-md-4"><strong>Trạng thái:</strong> ${item.maTrangThai ?? ''}</div>
                                                                <div class="col-md-4"><strong>Lệ phí:</strong> ${item.lePhi ? new Intl.NumberFormat('vi-VN').format(item.lePhi) : '0'}</div>
                                                                <div class="col-md-4"><strong>Hình thức:</strong> ${item.hinhThuc ?? ''}</div>
                                                                ${item.thongTinTra ? `
                                                                <div class="col-12"><strong>Thông tin trả:</strong><div class="border rounded p-2 bg-light">${item.thongTinTra}</div></div>
                                                                ` : ''}
                                                                ${item.ghiChu ? `
                                                                <div class="col-12"><strong>Ghi chú:</strong><div class="border rounded p-2 bg-light">${item.ghiChu}</div></div>
                                                                ` : ''}
                                                                ${(Array.isArray(item.dulieu) || (item.dulieu && typeof item.dulieu === 'object')) ? `
                                                                <div class="col-12"><strong>Dữ liệu hồ sơ:</strong>
                                                                    <pre class="bg-light border rounded p-2" style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;">${JSON.stringify(item.dulieu, null, 2)}</pre>
                                                                </div>
                                                                ` : ''}
                                                            </div>
                                                        </div>
                                                    </div>
                                                `;
                                            }).join('')}
                                        </div>
                                    </div>
                                    ` : ''}
                                </div>
                            `;
                            
                            // Fill JSON safely as text (chỉ cho modal service khi không có danh sách đã nộp)
                            try {
                                const hasDanhSachNop = data.danhSachNop && data.danhSachNop.length > 0;
                                if (!hasDanhSachNop && data.dulieu && (Array.isArray(data.dulieu) || typeof data.dulieu === 'object')) {
                                    const pre = modalBody.querySelector('#dulieu-json');
                                    if (pre) pre.textContent = JSON.stringify(data.dulieu, null, 2);
                                }
                            } catch (e) {
                                /* no-op */
                            }
                        }
                        
                        // Modal is already shown, content is updated
                    } catch (err) {
                        console.error('Lỗi chi tiết:', err);
                        
                        // Update modal content with error message
                        modalTitle.textContent = 'Lỗi';
                        modalBody.innerHTML = `
                            <div class="alert alert-danger">
                                <h6 class="alert-heading">Không thể tải chi tiết hồ sơ</h6>
                                <p class="mb-0">${err.message || 'Đã xảy ra lỗi không xác định. Vui lòng thử lại sau.'}</p>
                                <hr>
                                <small class="text-muted">Nếu lỗi vẫn tiếp tục, vui lòng liên hệ bộ phận hỗ trợ.</small>
                                <br><br>
                                <button class="btn btn-sm btn-secondary" onclick="location.reload()">Tải lại trang</button>
                            </div>
                        `;
                    }
                });
            } else {
                console.error('Không tìm thấy modal element với id hosoDetailModal');
            }

            // Function to open notification modal
            window.openNotificationModal = async function(notificationId, isRead) {
                const modalEl = document.getElementById('notificationDetailModal');
                const modalTitle = document.getElementById('notificationModalTitle');
                const modalBody = document.getElementById('notificationModalBody');

                if (!modalEl) return;

                // Show loading
                modalTitle.textContent = 'Chi tiết thông báo';
                modalBody.innerHTML = '<div class="text-muted">Đang tải...</div>';

                // Open modal
                const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                bsModal.show();

                try {
                    // Fetch notification details and mark as read
                    const response = await fetch(
                        `{{ url('/profile/notifications') }}/${notificationId}/detail`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute('content') || '',
                                'Accept': 'application/json'
                            }
                        });

                    if (!response.ok) {
                        throw new Error('Network error');
                    }

                    const data = await response.json();

                    // Update modal content
                    modalTitle.textContent = data.tieuDe || 'Chi tiết thông báo';
                    modalBody.innerHTML = `
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                ${data.created_at || ''}
                                ${data.loai ? `<span class="ms-2"><i class="fas fa-tag me-1"></i>${data.loai}</span>` : ''}
                            </small>
                        </div>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0" style="white-space: pre-wrap;">${(data.noiDung || 'Không có nội dung')}</p>
                        </div>
                    `;

                    // If notification was unread, update UI
                    if (!isRead) {
                        // Find the notification item
                        const notificationItem = document.querySelector(
                            `[data-notification-id="${notificationId}"]`);
                        if (notificationItem) {
                            // Remove "Mới" badge
                            const badge = notificationItem.querySelector('.badge');
                            if (badge) badge.remove();

                            // Remove unread styling
                            notificationItem.classList.remove('bg-light', 'border-start', 'border-3',
                                'border-warning');
                            notificationItem.setAttribute('data-notification-read', '1');

                            // Remove bold from title
                            const title = notificationItem.querySelector('h6');
                            if (title) title.classList.remove('fw-bold');
                        }

                        // Update unread count in sidebar immediately
                        // Use requestAnimationFrame to ensure DOM update happens before next paint
                        requestAnimationFrame(() => {
                            const unreadBadge = document.getElementById('notificationBadge');
                            if (unreadBadge) {
                                const currentCount = parseInt(unreadBadge.textContent.trim()) || 0;
                                const newCount = Math.max(0, currentCount - 1);

                                if (newCount > 0) {
                                    unreadBadge.textContent = newCount;
                                    unreadBadge.style.display = 'inline-block';
                                } else {
                                    unreadBadge.textContent = '0';
                                    unreadBadge.style.display = 'none';
                                }

                                // Force reflow to ensure update is visible
                                void unreadBadge.offsetHeight;
                            }

                            // Also try to find badge by class as fallback
                            const fallbackBadge = document.querySelector(
                                'a[href*="notifications"].profile-menu-item .notification-badge, a[href*="notifications"].profile-menu-item .badge'
                            );
                            if (fallbackBadge && (!unreadBadge || fallbackBadge !== unreadBadge)) {
                                const currentCount = parseInt(fallbackBadge.textContent.trim()) ||
                                    0;
                                const newCount = Math.max(0, currentCount - 1);

                                if (newCount > 0) {
                                    fallbackBadge.textContent = newCount;
                                    fallbackBadge.style.display = 'inline-block';
                                } else {
                                    fallbackBadge.textContent = '0';
                                    fallbackBadge.style.display = 'none';
                                }
                            }
                        });
                    }
                } catch (err) {
                    console.error(err);
                    modalBody.innerHTML =
                        '<div class="alert alert-danger">Không tải được chi tiết thông báo.</div>';
                }
            };

            // Load More Notifications
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', async function() {
                    const btn = this;
                    const currentPage = parseInt(btn.getAttribute('data-page')) || 2;
                    const onlyUnread = btn.getAttribute('data-only-unread') === '1';
                    const notificationsList = document.getElementById('notificationsList');

                    // Disable button and show loading
                    btn.disabled = true;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tải...';

                    try {
                        const url = new URL('{{ route('profile.notifications.load-more') }}', window
                            .location.origin);
                        url.searchParams.append('page', currentPage);
                        if (onlyUnread) {
                            url.searchParams.append('only_unread', '1');
                        }

                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Network error');
                        }

                        const data = await response.json();

                        if (data.html) {
                            // Append new notifications
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            const newItems = [];
                            while (tempDiv.firstChild) {
                                newItems.push(tempDiv.firstChild);
                                notificationsList.appendChild(tempDiv.firstChild);
                            }

                            // Update button state
                            if (data.hasMore && data.nextPage) {
                                btn.setAttribute('data-page', data.nextPage);
                                btn.disabled = false;

                                // Calculate remaining count (approximate)
                                const currentCount = notificationsList.querySelectorAll(
                                    '.notification-item').length;
                                const remaining = Math.max(0, (currentCount + 5) -
                                    currentCount); // Rough estimate
                                btn.innerHTML =
                                    `<i class="fas fa-chevron-down me-2"></i>Xem thêm thông báo`;
                            } else {
                                btn.style.display = 'none';
                            }

                            // Update pagination info if exists
                            const paginationInfo = document.querySelector(
                                '.text-center.text-muted.mb-2 small');
                            if (paginationInfo) {
                                const totalItems = notificationsList.querySelectorAll(
                                    '.notification-item').length;
                                paginationInfo.textContent = `Hiển thị 1-${totalItems} thông báo`;
                            }
                        } else {
                            throw new Error('No data received');
                        }
                    } catch (err) {
                        console.error(err);
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        alert('Không thể tải thêm thông báo. Vui lòng thử lại.');
                    }
                });
            }

            // Load More Services
            const loadMoreServicesBtn = document.getElementById('loadMoreServicesBtn');
            if (loadMoreServicesBtn) {
                loadMoreServicesBtn.addEventListener('click', async function() {
                    const btn = this;
                    const currentPage = parseInt(btn.getAttribute('data-page')) || 2;
                    const tenDichVu = btn.getAttribute('data-ten-dich-vu') || '';
                    const maHoSo = btn.getAttribute('data-ma-ho-so') || '';
                    const trangThai = btn.getAttribute('data-trang-thai') || '';
                    const servicesList = document.getElementById('servicesList');

                    // Disable button and show loading
                    btn.disabled = true;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang tải...';

                    try {
                        const url = new URL('{{ route('profile.services.load-more') }}', window
                            .location.origin);
                        url.searchParams.append('page', currentPage);
                        if (tenDichVu) {
                            url.searchParams.append('ten_dich_vu', tenDichVu);
                        }
                        if (maHoSo) {
                            url.searchParams.append('ma_ho_so', maHoSo);
                        }
                        if (trangThai) {
                            url.searchParams.append('trang_thai', trangThai);
                        }

                        const response = await fetch(url, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Network error');
                        }

                        const data = await response.json();

                        if (data.html) {
                            // Append new rows to table
                            const tempDiv = document.createElement('tbody');
                            tempDiv.innerHTML = data.html;
                            while (tempDiv.firstChild) {
                                servicesList.appendChild(tempDiv.firstChild);
                            }

                            // Update button state
                            if (data.hasMore && data.nextPage) {
                                btn.setAttribute('data-page', data.nextPage);
                                btn.disabled = false;
                                btn.innerHTML =
                                    `<i class="fas fa-chevron-down me-2"></i>Xem thêm hồ sơ`;
                            } else {
                                btn.style.display = 'none';
                            }

                            // Update pagination info if exists
                            const paginationInfo = document.getElementById('servicesPaginationInfo');
                            if (paginationInfo) {
                                const totalItems = servicesList.querySelectorAll('tr').length;
                                paginationInfo.textContent =
                                    `Hiển thị 1-${totalItems} trong tổng số ${totalItems} hồ sơ`;
                            }
                        } else {
                            throw new Error('No data received');
                        }
                    } catch (err) {
                        console.error(err);
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        alert('Không thể tải thêm hồ sơ. Vui lòng thử lại.');
                    }
                });
            }
        });
    </script>

    <!-- Modal chi tiết hồ sơ -->
    <div class="modal fade" id="hosoDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết hồ sơ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-muted">Đang tải...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chi tiết thông báo -->
    <div class="modal fade" id="notificationDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalTitle">Chi tiết thông báo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="notificationModalBody">
                    <div class="text-muted">Đang tải...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
@endpush
