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
        color: #32C36C;
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

        @if($daCheckIn)
            <div class="alert alert-success text-center">
                <h4><i class="fa fa-check-circle"></i> Đã check-in thành công!</h4>
                <div class="so-thu-tu">
                    Số thứ tự: {{ $soThuTu }}
                </div>
                <p class="mb-0">Vui lòng chờ đến lượt của bạn</p>
            </div>
        @else
            <div class="text-center">
                <button type="button" class="btn btn-primary btn-lg" id="btnCheckIn">
                    <i class="fa fa-qrcode me-2"></i> Check-in ngay
                </button>
            </div>
            <div id="checkinResult" class="mt-3"></div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnCheckIn = document.getElementById('btnCheckIn');
    const checkinResult = document.getElementById('checkinResult');

    if (btnCheckIn) {
        btnCheckIn.addEventListener('click', function() {
            btnCheckIn.disabled = true;
            btnCheckIn.textContent = 'Đang xử lý...';

            fetch('{{ route("appointment.checkin.process", $lichHen->checkin_token) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    checkinResult.innerHTML = `
                        <div class="alert alert-success text-center">
                            <h4><i class="fa fa-check-circle"></i> Check-in thành công!</h4>
                            <div class="so-thu-tu">
                                Số thứ tự: ${data.soThuTu}
                            </div>
                            <p class="mb-0">Thời gian check-in: ${data.thoiGianCheckIn}</p>
                            <p class="mb-0"><strong>Quầy:</strong> ${data.tenQuayLamViec}</p>
                            <p class="mb-0 mt-2">Vui lòng chờ đến lượt của bạn</p>
                        </div>
                    `;
                    btnCheckIn.style.display = 'none';
                    // Reload sau 2 giây để cập nhật trạng thái
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    checkinResult.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-circle"></i> ${data.message}
                            ${data.soThuTu ? '<p class="mb-0 mt-2">Số thứ tự của bạn: <strong>' + data.soThuTu + '</strong></p>' : ''}
                        </div>
                    `;
                    btnCheckIn.disabled = false;
                    btnCheckIn.textContent = '<i class="fa fa-qrcode me-2"></i> Check-in ngay';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                checkinResult.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi check-in</div>';
                btnCheckIn.disabled = false;
                btnCheckIn.textContent = '<i class="fa fa-qrcode me-2"></i> Check-in ngay';
            });
        });
    }
});
</script>
@endsection

