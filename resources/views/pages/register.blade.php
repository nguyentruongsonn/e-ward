
@extends('layouts.app')
@section('content')


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 wow fadeInUp" data-wow-delay="0.3s" style="border-radius: 25px;">
                <div class="row g-0">
                    <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center bg-color text-white p-4" style="border-top-left-radius: 25px;border-bottom-left-radius:25px;">
                        <div class="text-center">
                            <h3 class="fw-bold text-white">CỔNG GIẢI QUYẾT CÁC THỦ TỤC HÀNH CHÍNH PHƯỜNG ABC</h3>
                            <i class="fa fa-user-circle fa-5x mt-3" aria-hidden="true"></i>
                        </div>
                    </div>

                    <div class="col-md-7 p-4">
                        <div class="card-body">
                            <h4 class="card-title text-center mb-3">Đăng ký</h4>

                            @if(session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <form action="{{ route('register.submit') }}" method="POST" novalidate>
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Họ và tên</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-color"><i class="fa fa-user text-white"></i></span>
                                        <input type="text" name="hovaten" id="hovaten" class="form-control border border-color" required>

                                    </div>
                                    <span id="tbhovaten"></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Mã CCCD</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-color"><i class="fa fa-id-card text-white"></i></span>
                                        <input type="text" name="cccd" id="cccd" class="form-control border border-color " required>

                                    </div>
                                    <span id="tbcccd"></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-color"><i class="fa fa-envelope text-white"></i></span>
                                        <input type="text" name="email" id="emaill" class="form-control border border-color " required>
                                    </div>
                                    <span id="tbemail"></span>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mật khẩu</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-color"><i class="fa fa-lock text-white"></i></span>
                                            <input id="passwordd" type="password" name="password" class="form-control border border-color" required>
                                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#passwordd" tabindex="-1">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        <span id="tbpassword"></span>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Xác nhận mật khẩu</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-color"><i class="fa fa-lock text-white"></i></span>
                                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control border border-color" required>
                                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="#password_confirmation" tabindex="-1">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                        <span id="tbpassword_confirmation"></span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-color"><i class="fa fa-phone text-white"></i></span>
                                        <input id="phone" type="text" name="phone" class="form-control border border-color" required>

                                    </div>
                                    <span id="tbphone"></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Quê quán</label>
                                    <input id="quequan" type="text" name="quequan"  class="form-control border border-color" required>
                                    <span id="tbquequan"></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nơi thường trú</label>
                                    <input type="text" name="thuongtru" id="thuongtru" class="form-control border border-color" required>
                                    <span id="tbthuongtru"></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nơi tạm trú</label>
                                    <input type="text" name="tamtru" id="tamtru" class="form-control border border-color" required>
                                    <span id="tbtamtru"></span>
                                </div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function () {
                                    const toggleButtons = document.querySelectorAll(".toggle-password");

                                    toggleButtons.forEach((button) => {
                                        button.addEventListener("click", function () {
                                            const targetSelector = this.getAttribute("data-target");
                                            const input = document.querySelector(targetSelector);
                                            const icon = this.querySelector("i");

                                            if (input) {
                                                if (input.type === "password") {
                                                    input.type = "text";
                                                    icon.classList.remove("fa-eye");
                                                    icon.classList.add("fa-eye-slash");
                                                } else {
                                                    input.type = "password";
                                                    icon.classList.remove("fa-eye-slash");
                                                    icon.classList.add("fa-eye");
                                                }
                                            }
                                        });
                                    });
                                });
                                </script>
                                <div class="mb-3">
                                    <label class="form-label d-block">Giới tính</label>
                                    <div class="btn-group" role="group" aria-label="Gender">
                                        <input type="radio" class="btn-check" name="gender" id="gender_male" value="Nam" required checked>
                                        <label class="btn btn-outline-secondary  border border-color" for="gender_male">Nam</label>

                                        <input type="radio" class="btn-check" name="gender" id="gender_female" value="Nữ"  required>
                                        <label class="btn btn-outline-secondary  border border-color" for="gender_female">Nữ</label>
                                    </div>
                                    <span id="tbgender"></span>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" id="submit_register" class="btn btn-color btn-lg">ĐĂNG KÝ</button>
                                </div>
                                <span id="tball"></span>
                                <p class="text-center small mt-3 mb-0">Đã có tài khoản? <a href="#">Đăng nhập</a></p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
