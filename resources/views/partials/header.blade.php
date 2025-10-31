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
<nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top shadow p-0">
    <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center border-end px-4 px-lg-5">
        <h2 class="m-0" style="color:#32C36C;">ABC</h2>
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
                    <a href="{{ route('admin.login') }}" class="dropdown-item">THỦ TỤC HÀNH CHÍNH</a>
                    <a href="{{ route('history') }}" class="dropdown-item">DỊCH VỤ CÔNG TRỰC TUYẾN</a>
                    <a href="{{ route('outstanding-service') }}" class="dropdown-item">DỊCH VỤ CÔNG NỔI BẬT</a>
                    <a href="{{ route('404') }}" class="dropdown-item">TRA CỨU HỒ SƠ</a>
                    <a href="{{ route('404') }}" class="dropdown-item">CÂU HỎI THƯỜNG GẶP</a>
                </div>
            </div>
            <a href="" class="nav-item nav-link">THANH TOÁN TRỰC TUYẾN</a>
            <a href="{{ route('contact') }}" class="nav-item nav-link">ĐÁNH GIÁ DỊCH VỤ</a>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">HỖ TRỢ</a>
                  <div class="dropdown-menu bg-light m-0">
                    <a href="{{ route('support.about') }}" class="dropdown-item">GIỚI THIỆU</a>
                    <a href="{{ route('support.terms') }}" class="dropdown-item">ĐIỀU KHOẢN SỬ DỤNG</a>
                    <a href="{{ route('support.guide') }}" class="dropdown-item">HƯỚNG DẪN SỬ DỤNG</a>
                    <a href="{{ route('support.notice') }}" class="dropdown-item">THÔNG BÁO</a>
                </div>
            </div>


        </div>

        @auth
            {{-- Nếu đã đăng nhập --}}
            <a href="{{ route('logout') }}" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block text-white">
                ĐĂNG XUẤT
            </a>
        @else
            {{-- Chưa đăng nhập --}}
            <button type="button" class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block " data-bs-toggle="modal" data-bs-target="#loginModal">
                ĐĂNG NHẬP
            </button>

            <button class="btn btn-primary rounded-0 py-4 px-lg-5 d-none d-lg-block" onclick="redirectToRegister()">ĐĂNG KÝ</button>





        @endauth
    </div>
</nav>

{{-- Modal đăng nhập --}}
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card-body">
                <div class="text-center mb-3">
                    <h3 class="card-title">ĐĂNG NHẬP</h3>
                </div>

                <form action="#" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label text-muted">Địa chỉ Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            placeholder="abc@gmail.com" required>
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
                    <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal"
                        class="text-decoration-none">
                        Đăng ký
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
