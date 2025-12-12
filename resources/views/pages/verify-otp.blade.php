@extends('layouts.app')
@section('content')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h4 class="mb-3 text-center">Xác thực Email</h4>

                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <p class="text-muted">Chúng tôi đã gửi mã OTP tới email <strong>{{ $email }}</strong>. Vui lòng kiểm tra hộp thư và nhập mã gồm 6 chữ số để hoàn tất đăng ký.</p>

                    <form method="POST" action="{{ route('register.otp.verify') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Mã OTP</label>
                            <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" class="form-control" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-color" type="submit">Xác nhận</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('register.otp.resend') }}" class="mt-3">
                        @csrf
                        <button class="btn btn-link p-0">Gửi lại mã OTP</button>
                    </form>

                    <div class="mt-3">
                        <a href="{{ route('register') }}">Quay lại đăng ký</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


