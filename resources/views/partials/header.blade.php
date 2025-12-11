<div class="container-fluid bg-dark p-0">
    <div class="row gx-0 d-none d-lg-flex">
        <div class="col-lg-7 px-5 text-start">
            <div class="h-100 d-inline-flex align-items-center me-4">
                <small class="fa fa-map-marker-alt text-primary me-2"></small>
                <small>ĐẠI HỌC CÔNG NGHIỆP TP.HCM</small>
            </div>
            <div class="h-100 d-inline-flex align-items-center">
                <small class="far fa-clock text-primary me-2"></small>
                <small>T2 - CN : 06.00 AM - 12.00 PM</small>
            </div>
        </div>
        <div class="col-lg-5 px-5 text-end">
            <div class="h-100 d-inline-flex align-items-center me-4">
                <small class="fa fa-phone-alt text-primary me-2"></small>
                <small>+ 012 345 6789</small>
            </div>
            <div class="h-100 d-inline-flex align-items-center mx-n2">
                <a class="btn btn-square btn-link text-primary rounded-0 border-0 border-end border-secondary"
                    href="#"><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-square btn-link text-primary rounded-0 border-0 border-end border-secondary"
                    href="#"><i class="fab fa-twitter"></i></a>
                <a class="btn btn-square btn-link text-primary rounded-0 border-0 border-end border-secondary"
                    href="#"><i class="fab fa-linkedin-in"></i></a>
                <a class="btn btn-square btn-link text-primary rounded-0" href="#"><i
                        class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</div>
<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top shadow p-0">
    <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center border-end px-4 px-lg-5">
        <h2 class="headdername">ABC</h2>
    </a>

    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">TRANG
                CHỦ</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">THÔNG TIN & DỊCH VỤ</a>
                <div class="dropdown-menu bg-light m-0">
                    <a href="{{ route('services') }}" class="dropdown-item">DANH SÁCH DỊCH VỤ</a>
                    <a href="{{ route('outstanding-service') }}" class="dropdown-item">DỊCH VỤ CÔNG NỔI BẬT</a>
                    <a href="{{ route('tracking') }}" class="dropdown-item">TRA CỨU HỒ SƠ</a>
                    <a href="{{ route('support.faq') }}" class="dropdown-item">CÂU HỎI THƯỜNG GẶP</a>
                </div>
            </div>
            <a href="{{ route('service.ratings') }}" class="nav-item nav-link">ĐÁNH GIÁ DỊCH VỤ</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">HỖ TRỢ</a>
                <div class="dropdown-menu bg-light m-0">
                    <a href="{{ route('support.about') }}" class="dropdown-item">GIỚI THIỆU</a>
                    <a href="{{ route('support.terms') }}" class="dropdown-item">ĐIỀU KHOẢN SỬ DỤNG</a>
                    <a href="{{ route('support.guide') }}" class="dropdown-item">HƯỚNG DẪN SỬ DỤNG</a>
                    <a href="{{ route('support.notice') }}" class="dropdown-item">THÔNG BÁO</a>
                </div>
            </div>
            
            {{-- Notification Dropdown (Only for authenticated users) --}}
            @auth
                @php
                    // Lấy thông báo mới nhất (5 thông báo gần nhất)
                    $authUser = Auth::user();
                    $notifications = collect();
                    
                    // Định nghĩa tên trạng thái theo maTrangThai
                    $statusNames = [
                        1 => 'Chờ tiếp nhận',
                        2 => 'Được tiếp nhận',
                        3 => 'Không được tiếp nhận',
                        4 => 'Đang xử lý',
                        5 => 'Yêu cầu bổ sung',
                        6 => 'Đã bổ sung',
                        7 => 'Chờ lãnh đạo duyệt',
                        9 => 'Đã xử lý xong',
                        10 => 'Đã trả kết quả',
                        11 => 'Hủy'
                    ];
                    
                    if ($authUser && $authUser->IDnguoiDung) {
                        // Tìm IDCD của công dân
                        $congDan = DB::table('congdan')
                            ->where('IDnguoiDung', $authUser->IDnguoiDung)
                            ->first();
                        
                        if ($congDan) {
                            // Lấy các hồ sơ của công dân
                            $notifications = DB::table('hosoxuly as h')
                                ->join('tthc as t', 'h.maTTHC', '=', 't.maTTHC')
                                ->where('h.IDCD', $congDan->IDCD)
                                ->whereIn('h.maTrangThai', [2, 3, 4, 5, 6, 9, 10]) // Các trạng thái quan trọng
                                ->select(
                                    'h.maHSXL',
                                    't.tenTTHC',
                                    'h.maTrangThai',
                                    'h.ngayTiepNhan'
                                )
                                ->orderBy('h.ngayTiepNhan', 'desc')
                                ->limit(5)
                                ->get()
                                ->map(function($item) use ($statusNames) {
                                    $item->tenTrangThai = $statusNames[$item->maTrangThai] ?? 'Chưa xác định';
                                    return $item;
                                });
                        }
                    }
                    
                    // Lấy thời điểm đã xem thông báo lần cuối từ session
                    $lastViewedTime = session('notifications_last_viewed', null);
                    
                    // Đếm số thông báo chưa đọc (những thông báo sau lần xem cuối)
                    $unreadCount = 0;
                    if ($lastViewedTime) {
                        $unreadCount = $notifications->filter(function($notification) use ($lastViewedTime) {
                            return strtotime($notification->ngayTiepNhan) > strtotime($lastViewedTime);
                        })->count();
                    } else {
                        // Nếu chưa từng xem, tất cả đều là chưa đọc
                        $unreadCount = $notifications->count();
                    }
                    
                    $notificationCount = $notifications->count();
                @endphp
                
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle position-relative" data-bs-toggle="dropdown" 
                       id="notificationDropdown" onclick="markNotificationsAsRead()" 
                       >
                        <i class="fa fa-bell" style="font-size: 1.2rem;"></i>
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                  id="notificationBadge"
                                  style="font-size: 0.7rem; padding: 0.25rem 0.5rem;">
                                {{ $unreadCount }}
                                <span class="visually-hidden">thông báo mới</span>
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light m-0" style="min-width: 350px; max-height: 400px; position: absolute; right: -150px; ">
                        <div class="dropdown-header bg-color text-white" style="font-weight: bold;">
                            <i class="fa fa-bell"></i> Thông báo
                        </div>
                        
                        @if($notifications->count() > 0)
                            @foreach($notifications as $notification)
                                @php
                                    // Xác định màu badge theo trạng thái
                                    $badgeClass = match($notification->maTrangThai) {
                                        2 => 'bg-info',           // Được tiếp nhận
                                        3 => 'bg-danger',         // Không được tiếp nhận
                                        4 => 'bg-primary',        // Đang xử lý
                                        5 => 'bg-warning',        // Yêu cầu bổ sung
                                        6 => 'bg-info',           // Đã bổ sung
                                        9 => 'bg-success',        // Đã xử lý xong
                                        10 => 'bg-success',       // Đã trả kết quả
                                        default => 'bg-secondary'
                                    };
                                    
                                    $timeAgo = \Carbon\Carbon::parse($notification->ngayTiepNhan)->diffForHumans();
                                @endphp
                                
                                <a href="{{ route('profile.hoso.show', $notification->maHSXL) }}" 
                                   class="dropdown-item border-bottom" 
                                   style="padding: 0.75rem 1rem; white-space: normal;">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-grow-1">
                                            <strong style="font-size: 0.9rem;">{{ $notification->tenTTHC }}</strong>
                                            <br>
                                            <small class="text-muted">Mã HS: {{ $notification->maHSXL }}</small>
                                            <br>
                                            <span class="badge {{ $badgeClass }} mt-1" style="font-size: 0.75rem;">
                                                {{ $notification->tenTrangThai }}
                                            </span>
                                            <br>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                <i class="fa fa-clock-o"></i> {{ $timeAgo }}
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        
                        @endif
                    </div>
                </div>
            @endauth


        </div>

        @auth
            @php
                $authUser = Auth::user();
                // Kiểm tra xem Auth::user() trả về Nguoi hay User
                if ($authUser instanceof \App\Models\Nguoi) {
                    $nguoi = $authUser;
                    $user = $authUser->user ?? $authUser;
                } else {
                    $user = $authUser;
                    $nguoi = $user->nguoi;
                }
                $hoTen = $nguoi?->hoTen ?? ($user->email ?? 'Người dùng');

                // Kiểm tra quyền admin/cán bộ giống logic trong LoginController::isAdmin
                $isAdmin = false;
                if ($nguoi) {
                    $adminRoles = ['Quản trị viên', 'Cán bộ một cửa', 'Cán bộ thụ lý', 'Lãnh đạo'];
                    if (in_array(trim($nguoi->vaiTro ?? ''), $adminRoles, true)) {
                        $isAdmin = true;
                    } else {
                        $isAdmin = DB::table('quantrivien')
                            ->where('IDnguoiDung', $nguoi->IDnguoiDung ?? 0)
                            ->exists();
                    }
                }
            @endphp
            {{-- Nếu đã đăng nhập --}}
            <div class="nav-item dropdown d-none d-lg-block">
                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center text-primary fw-bold"
                    data-bs-toggle="dropdown" style="padding: 1rem 1.25rem !important;">
                    <img src="{{ asset('img/header/user-avatar.png') }}" alt="Avatar" class="rounded-circle me-2"
                        style="width: 32px; height: 32px; object-fit: cover;">
                    <span
                        style="overflow: hidden;max-width: 250px;white-space: nowrap;text-overflow: ellipsis; color: black">{{ $hoTen }}</span>
                    <i class="fas fa-chevron-down ms-2" style="font-size: 0.75rem;"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light m-0" style="min-width: 200px;">
                    <a href="{{ route('profile') }}" class="dropdown-item">
                        <i class="fas fa-user me-2"></i>
                        Thông tin cá nhân
                    </a>
                    @if($isAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item" style="color: #d9534f;">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Vào trang quản trị
                        </a>
                    @endif
                    <hr class="dropdown-divider">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"
                            style="border: none; background: none; width: 100%; text-align: left; padding: 0.5rem 1rem;">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
            {{-- Mobile version --}}
            <div class="d-lg-none">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle d-flex align-items-center text-primary fw-bold"
                        data-bs-toggle="dropdown">
                        <img src="{{ asset('img/header/user-avatar.png') }}" alt="Avatar" class="rounded-circle me-2"
                            style="width: 32px; height: 32px; object-fit: cover;">
                        <span>{{ $hoTen }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light m-0" style="min-width: 200px;">
                        <a href="{{ route('profile') }}" class="dropdown-item">
                            <i class="fas fa-user me-2"></i>
                            Thông tin cá nhân
                        </a>
                        @if($isAdmin)
                            <a href="{{ route('admin.dashboard') }}" class="dropdown-item" style="color: #d9534f;">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Vào trang quản trị
                            </a>
                        @endif
                        <hr class="dropdown-divider">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"
                                style="border: none; background: none; width: 100%; text-align: left; padding: 0.5rem 1rem;">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            {{-- Chưa đăng nhập --}}
            <button type="button" class="btn btn-color rounded-0 py-4 px-lg-5 d-none d-lg-block "
                data-bs-toggle="modal" data-bs-target="#loginModal">
                ĐĂNG NHẬP
            </button>

            <a href="{{ route('register') }}" class="btn btn-color rounded-0 py-4 px-lg-5 d-none d-lg-block">ĐĂNG
                KÝ</a>





        @endauth
    </div>
</nav>
</div>

{{-- Modal đăng nhập --}}
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="card-title">ĐĂNG NHẬP</h3>
                </div>

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="email" class="form-label text-muted">Địa chỉ Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="abc@gmail.com" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-muted">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="*********" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">ĐĂNG NHẬP</button>
                </form>

                <p class="text-center text-muted mt-3">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}"
                       class="text-decoration-none"
                       onclick="window.location.href='{{ route('register') }}'; return true;"
                       data-bs-dismiss="modal">
                        Đăng ký
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Script xử lý thông báo đăng ký thành công --}}
@if (session('register_success'))
    <script>
        // Đợi cho đến khi trang và các thư viện đã tải xong
        window.addEventListener('load', function() {
            // Hiển thị alert chúc mừng
            alert('{{ session('register_success') }}');

            // Mở popup đăng nhập sau khi hiển thị alert
            // Sử dụng setTimeout để đảm bảo Bootstrap đã được khởi tạo
            setTimeout(function() {
                var loginModalElement = document.getElementById('loginModal');
                if (loginModalElement && typeof bootstrap !== 'undefined') {
                    var loginModal = new bootstrap.Modal(loginModalElement);
                    loginModal.show();
                } else if (loginModalElement) {
                    // Fallback nếu Bootstrap chưa sẵn sàng, thử dùng jQuery
                    if (typeof $ !== 'undefined') {
                        $('#loginModal').modal('show');
                    }
                }
            }, 100);
        });
    </script>
@endif

{{-- Script xử lý mở modal đăng nhập khi đăng nhập thất bại --}}
@if (session('open_login_modal'))
    <script>
        // Đợi cho đến khi trang và các thư viện đã tải xong
        document.addEventListener('DOMContentLoaded', function() {
            // Sử dụng setTimeout để đảm bảo Bootstrap đã được khởi tạo
            setTimeout(function() {
                var loginModalElement = document.getElementById('loginModal');
                if (loginModalElement && typeof bootstrap !== 'undefined') {
                    var loginModal = new bootstrap.Modal(loginModalElement);
                    loginModal.show();
                } else if (loginModalElement) {
                    // Fallback nếu Bootstrap chưa sẵn sàng, thử dùng jQuery
                    if (typeof $ !== 'undefined') {
                        $('#loginModal').modal('show');
                    }
                }
            }, 100);
        });
    </script>
@endif

<script>
// Function to mark notifications as read
function markNotificationsAsRead() {
    // Send AJAX request to mark notifications as read
    fetch('/notifications/mark-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hide the badge
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                badge.style.display = 'none';
            }
        }
    })
    .catch(error => {
        console.error('Error marking notifications as read:', error);
    });
}
</script>
