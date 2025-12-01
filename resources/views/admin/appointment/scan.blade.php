@extends('admin.layout')

@section('title', 'Quét QR Code Lịch Hẹn')

@push('styles')
<style>
    .qr-input-section {
        border: 1px solid #eee;
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    
    .qr-input-section label {
        font-weight: 500;
        margin-top: 10px;
        color: #1A2A36;
    }
    
    .appointment-info {
        border: 1px solid #eee;
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
    }
    
    .appointment-info h4 {
        font-size: 18px;
        font-weight: 600;
        color: #32C36C;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }
    
    .appointment-info p {
        margin-bottom: 12px;
        color: #333;
    }
    
    .appointment-info strong {
        color: #1A2A36;
        font-weight: 500;
    }
    
    .so-thu-tu {
        font-size: 48px;
        font-weight: bold;
        color: #32C36C;
        text-align: center;
        margin: 20px 0;
    }
    
    .btn-checkin {
        font-size: 18px;
        padding: 15px 30px;
        background: #32C36C;
        border: none;
        color: white;
        border-radius: 5px;
    }
    
    .btn-checkin:hover {
        background: #28a745;
        color: white;
    }
    
    .qr-scanner-container {
        display: none;
        margin-top: 20px;
        text-align: center;
    }
    
    #qr-reader {
        width: 100%;
        max-width: 500px;
        margin: 0 auto;
    }
    
    #qr-reader__scan_region {
        border: 2px solid #32C36C;
        border-radius: 10px;
    }
    
    .btn-scan-qr {
        background: #32C36C;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        margin-left: 10px;
    }
    
    .btn-scan-qr:hover {
        background: #28a745;
        color: white;
    }
    
    .btn-stop-scan {
        background: #dc3545;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        margin-top: 10px;
    }
    
    .btn-stop-scan:hover {
        background: #c82333;
        color: white;
    }
</style>
@endpush

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-qrcode"></i> Quét QR Code Lịch Hẹn</h3>
                    </header>
                    <div class="panel-body">
                    <!-- Form nhập token hoặc quét QR -->
                    <div class="qr-input-section">
                        <form method="GET" action="{{ route('admin.appointment.scan') }}" id="scanForm">
                            <div class="row">
                                <div class="col-md-7">
                                    <label for="token" class="form-label"><strong>Nhập Token hoặc Quét QR Code:</strong></label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="token" 
                                           name="token" 
                                           value="{{ $token ?? '' }}"
                                           placeholder="Nhập checkin_token hoặc quét QR code"
                                           autofocus>
                                    <small class="form-text text-muted">Nhập token từ QR code của người dùng hoặc quét QR code</small>
                                </div>
                                <div class="col-md-5 d-flex align-items-end">
                                    <button type="button" class="btn btn-success btn-lg btn-scan-qr" id="btnScanQR">
                                        <i class="fa fa-camera"></i> Quét QR Code
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg" style="flex: 1; margin-left: 10px;">
                                        <i class="fa fa-search"></i> Tìm kiếm
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- QR Scanner Container -->
                        <div class="qr-scanner-container" id="qrScannerContainer">
                            <div id="qr-reader"></div>
                            <button type="button" class="btn btn-stop-scan" id="btnStopScan">
                                <i class="fa fa-stop"></i> Dừng quét
                            </button>
                        </div>
                    </div>

                    @if($token && !$lichHen)
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> Không tìm thấy lịch hẹn với token này.
                        </div>
                    @endif

                    @if($lichHen)
                        <div class="appointment-info">
                            <h4 class="mb-3"><i class="fa fa-calendar"></i> Thông tin lịch hẹn</h4>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Mã lịch hẹn:</strong> {{ $lichHen->maLichHen }}</p>
                                    <p><strong>Thủ tục hành chính:</strong> {{ $tthc->tenTTHC ?? '-' }}</p>
                                    <p><strong>Thời gian hẹn:</strong> {{ \Carbon\Carbon::parse($lichHen->thoiGianHen)->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    @if($nguoi)
                                        <p><strong>Họ tên:</strong> {{ $nguoi->hoTen ?? '-' }}</p>
                                        <p><strong>Email:</strong> {{ $nguoi->email ?? '-' }}</p>
                                        <p><strong>Số điện thoại:</strong> {{ $nguoi->soDienThoai ?? '-' }}</p>
                                    @endif
                                    <p><strong>Trạng thái:</strong> 
                                        <span class="badge 
                                            @if($lichHen->trangThai == 'Hoàn thành') bg-success
                                            @elseif($lichHen->trangThai == 'Đang xử lý') bg-info
                                            @elseif($lichHen->trangThai == 'Đã hủy' || $lichHen->trangThai == 'Không đến') bg-danger
                                            @else bg-warning
                                            @endif">
                                            {{ $lichHen->trangThai }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            @if($lichHen->checkin_time)
                                <div class="alert alert-success text-center">
                                    <h4><i class="fa fa-check-circle"></i> Đã check-in</h4>
                                    <div class="so-thu-tu">
                                        Số thứ tự: {{ $lichHen->soThuTu }}
                                    </div>
                                    <p class="mb-0"><strong>Quầy:</strong> {{ $quay->tenQuayLamViec ?? 'Chưa phân quầy' }}</p>
                                    <p class="mb-0"><strong>Thời gian check-in:</strong> {{ \Carbon\Carbon::parse($lichHen->checkin_time)->format('d/m/Y H:i') }}</p>
                                </div>
                            @else
                                <div class="text-center mt-4">
                                    <button type="button" 
                                            class="btn btn-success btn-checkin" 
                                            id="btnCheckIn"
                                            data-token="{{ $lichHen->checkin_token }}">
                                        <i class="fa fa-check-circle"></i> Xác nhận Check-in
                                    </button>
                                </div>
                                <div id="checkinResult" class="mt-3"></div>
                            @endif
                        </div>
                    @endif
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

@push('scripts')
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrcodeScanner = null;

document.addEventListener('DOMContentLoaded', function() {
    const btnCheckIn = document.getElementById('btnCheckIn');
    const checkinResult = document.getElementById('checkinResult');
    const btnScanQR = document.getElementById('btnScanQR');
    const btnStopScan = document.getElementById('btnStopScan');
    const qrScannerContainer = document.getElementById('qrScannerContainer');
    const tokenInput = document.getElementById('token');
    const scanForm = document.getElementById('scanForm');
    
    // Quét QR Code
    if (btnScanQR) {
        btnScanQR.addEventListener('click', function() {
            if (html5QrcodeScanner) {
                // Nếu đang quét thì dừng
                stopScanning();
                return;
            }
            
            qrScannerContainer.style.display = 'block';
            btnScanQR.innerHTML = '<i class="fa fa-stop"></i> Dừng quét';
            btnScanQR.classList.remove('btn-success');
            btnScanQR.classList.add('btn-danger');
            
            // Khởi tạo scanner
            html5QrcodeScanner = new Html5Qrcode("qr-reader");
            
            html5QrcodeScanner.start(
                { facingMode: "environment" }, // Sử dụng camera sau
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                function(decodedText, decodedResult) {
                    // Khi quét được QR code
                    console.log("QR Code detected:", decodedText);
                    
                    // Lấy token từ URL (nếu QR code là URL)
                    let token = decodedText;
                    
                    // Xử lý URL dạng /appointment/checkin/{token}
                    if (decodedText.includes('/appointment/checkin/')) {
                        const match = decodedText.match(/\/appointment\/checkin\/([a-f0-9\-]+)/i);
                        if (match && match[1]) {
                            token = match[1];
                        }
                    }
                    // Xử lý URL có token= query parameter
                    else if (decodedText.includes('token=')) {
                        const urlParams = new URLSearchParams(decodedText.split('?')[1]);
                        token = urlParams.get('token');
                    }
                    // Xử lý URL dạng /admin/appointment/scan?token=...
                    else if (decodedText.includes('/admin/appointment/scan')) {
                        const match = decodedText.match(/token=([^&]+)/);
                        if (match) {
                            token = match[1];
                        }
                    }
                    // Nếu là UUID thuần (không có URL), giữ nguyên
                    else if (/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i.test(decodedText.trim())) {
                        token = decodedText.trim();
                    }
                    
                    // Điền token vào input
                    tokenInput.value = token;
                    
                    // Dừng quét
                    stopScanning();
                    
                    // Tự động submit form
                    scanForm.submit();
                },
                function(errorMessage) {
                    // Lỗi khi quét (bỏ qua, chỉ log)
                    // console.log("QR Code scan error:", errorMessage);
                }
            ).catch(function(err) {
                console.error("Unable to start QR scanner:", err);
                alert("Không thể khởi động camera. Vui lòng kiểm tra quyền truy cập camera.");
                stopScanning();
            });
        });
    }
    
    // Dừng quét
    function stopScanning() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.stop().then(function() {
                html5QrcodeScanner.clear();
                html5QrcodeScanner = null;
                qrScannerContainer.style.display = 'none';
                btnScanQR.innerHTML = '<i class="fa fa-camera"></i> Quét QR Code';
                btnScanQR.classList.remove('btn-danger');
                btnScanQR.classList.add('btn-success');
            }).catch(function(err) {
                console.error("Error stopping scanner:", err);
            });
        }
    }
    
    if (btnStopScan) {
        btnStopScan.addEventListener('click', function() {
            stopScanning();
        });
    }
    
    // Check-in button

    if (btnCheckIn) {
        btnCheckIn.addEventListener('click', function() {
            const token = btnCheckIn.getAttribute('data-token');
            btnCheckIn.disabled = true;
            btnCheckIn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';

            fetch(`{{ url('/admin/appointment/checkin') }}/${token}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
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
                            <p class="mb-0"><strong>Quầy:</strong> ${data.tenQuayLamViec}</p>
                            <p class="mb-0"><strong>Thời gian check-in:</strong> ${data.thoiGianCheckIn}</p>
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
                            ${data.soThuTu ? '<p class="mb-0 mt-2">Số thứ tự: <strong>' + data.soThuTu + '</strong></p>' : ''}
                        </div>
                    `;
                    btnCheckIn.disabled = false;
                    btnCheckIn.innerHTML = '<i class="fa fa-check-circle"></i> Xác nhận Check-in';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                checkinResult.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi check-in</div>';
                btnCheckIn.disabled = false;
                btnCheckIn.innerHTML = '<i class="fa fa-check-circle"></i> Xác nhận Check-in';
            });
        });
    }
});
</script>
@endpush

