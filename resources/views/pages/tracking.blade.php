@extends('layouts.app')

@section('content')

    <div class="container-fluid page-header py-5 mb-5">
        <div class="container py-5">
            <form class="d-flex wow fadeInUp" data-wow-delay="0.3" role="search" action="{{ route('tracking.search') }}" method="POST">
                @csrf
                <input type="text" name="maHSXL" class="form-control" placeholder="Nhập mã hồ sơ (VD: HSXL2024001)" value="{{ old('maHSXL', request('maHSXL')) }}" required>
                @error('maHSXL')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
                <button class="btn btn-color" type="submit">TÌM KIẾM</button>
            </form>

            @if(session('error'))
                <div class="alert alert-danger mt-3">
                    {{ session('error') }}
                </div>
            @endif

            @if(isset($hoSo))
                <div class="mt-5 wow fadeInUp" data-wow-delay="0.5">
                    <h3 class="mb-4 text-center text-primary">Thông tin hồ sơ</h3>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Mã hồ sơ:</div>
                                <div class="col-md-8">{{ $hoSo->maHSXL }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Thủ tục:</div>
                                <div class="col-md-8">{{ $hoSo->tthc->tenTTHC ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Người nộp:</div>
                                <div class="col-md-8">{{ $tenChuHoSo ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Ngày nộp:</div>
                                <div class="col-md-8">{{ \Carbon\Carbon::parse($hoSo->thoiGianNop)->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Trạng thái:</div>
                                <div class="col-md-8">
                                    <span class="badge bg-{{ $hoSo->trangThai->tenTrangThai == 'Hoàn thành' ? 'success' : ($hoSo->trangThai->tenTrangThai == 'Đang xử lý' ? 'info' : 'secondary') }}">
                                        {{ $hoSo->trangThai->tenTrangThai ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                             @if($hoSo->ketQua)
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Kết quả:</div>
                                <div class="col-md-8">{{ $hoSo->ketQua }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

@endsection
