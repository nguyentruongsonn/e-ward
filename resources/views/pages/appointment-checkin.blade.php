@extends('layouts.app')

@section('title', 'Check-in lịch hẹn')

@section('content')
<style>
    .checkin-container {
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .qr-code-display {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 20px 0;
    }
    .so-thu-tu {
        font-size: 48px;
        font-weight: bold;
        color: #007bff;
        text-align: center;
        margin: 20px 0;
    }
</style>

<div class="container py-4">
    <div class="checkin-container">
        <div class="text-center mb-4">
            <h3 class="mb-3">Check-in lịch hẹn</h3>
            <p class="text-muted">Mã lịch hẹn: <strong>{{ $lichHen->maLichHen }}</strong></p>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Thông tin lịch hẹn</h5>
                <p><strong>Thủ tục:</strong> {{ $tthc->tenTTHC ?? '' }}</p>
                <p><strong>Thời gian hẹn:</strong> {{ \Carbon\Carbon::parse($lichHen->thoiGianHen)->format('d/m/Y H:i') }}</p>
                @if($lichHen->maQuayLamViec && $quay)
                    <p><strong>Quầy:</strong> {{ $quay->tenQuayLamViec }}</p>
                @else
                    <p><strong>Quầy:</strong> Sẽ được chọn khi check-in</p>
                @endif
            </div>
        </div>

        <!-- Hiển thị QR Code để cán bộ quét -->
        <div class="qr-code-display">
            <h5 class="text-center mb-3">Mã QR Code để cán bộ quét</h5>
            @if(isset($lichHen->checkin_token))
                <div class="text-center mb-3">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ route('admin.appointment.scan', ['token' => $lichHen->checkin_token]) }}" 
                         alt="QR Code" 
                         class="img-fluid"
                         style="max-width: 300px;">
                </div>
                <div class="text-center">
                    <p class="text-muted mb-2"><strong>Token:</strong></p>
                    <p class="bg-light p-2 rounded" style="word-break: break-all; font-family: monospace; font-size: 12px;">
                        {{ $lichHen->checkin_token }}
                    </p>
                    <small class="text-muted">Vui lòng đưa QR code này cho cán bộ để được check-in</small>
                </div>
            @endif
        </div>

        @if($daCheckIn)
            <div class="alert alert-success text-center mt-3">
                <h4><i class="fa fa-check-circle"></i> Đã check-in thành công!</h4>
                <div class="so-thu-tu">
                    Số thứ tự: {{ $soThuTu }}
                </div>
                @if($quay)
                    <p class="mb-0"><strong>Quầy:</strong> {{ $quay->tenQuayLamViec }}</p>
                @endif
                <p class="mb-0">Vui lòng chờ đến lượt của bạn</p>
            </div>
        @else
            <div class="alert alert-info text-center mt-3">
                <i class="fa fa-info-circle"></i> 
                <strong>Lưu ý:</strong> Vui lòng đưa QR code trên cho cán bộ để được check-in và nhận số thứ tự.
            </div>
        @endif
    </div>
</div>

@endsection

