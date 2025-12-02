@extends('layouts.app')

@section('content')
<style>
/* Clean modern design */
.tracking-container {
    min-height: 70vh;
    padding: 60px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.tracking-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.tracking-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    text-align: center;
}

.tracking-header h2 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
}

.tracking-header p {
    font-size: 16px;
    opacity: 0.9;
}

.search-section {
    padding: 40px;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 12px 20px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
}

.btn-search {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    padding: 12px 40px;
    font-size: 16px;
    font-weight: 600;
    color: white;
    transition: all 0.3s ease;
}

.btn-search:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.result-section {
    padding: 0 40px 40px 40px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.info-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s ease;
}

.info-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.info-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.info-value {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
}

.status-badge {
    display: inline-block;
    padding: 8px 20px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

.badge-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.badge-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.badge-primary {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.alert-custom {
    border-radius: 12px;
    border-left: 4px solid;
}
</style>

<div class="tracking-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="tracking-card">
                    <!-- Header -->
                    <div class="tracking-header">
                        <h2><i class="fa fa-search"></i> TRA CỨU HỒ SƠ</h2>
                        <p>Nhập mã hồ sơ để kiểm tra tiến độ xử lý</p>
                    </div>

                    <!-- Search Form -->
                    <div class="search-section">
                        @if(session('error'))
                            <div class="alert alert-danger alert-custom border-danger">
                                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('tracking.search') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <input type="text" 
                                           name="maHSXL" 
                                           class="form-control" 
                                           placeholder="Nhập mã hồ sơ (VD: HSXL2024001)" 
                                           value="{{ old('maHSXL') }}"
                                           required>
                                    @error('maHSXL')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-search w-100">
                                        <i class="fa fa-search"></i> Tra cứu
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Results -->
                    @if(isset($hoSo))
                        <div class="result-section">
                            <div class="alert alert-success alert-custom border-success">
                                <i class="fa fa-check-circle"></i> Tìm thấy hồ sơ!
                            </div>

                            <div class="info-grid">
                                <!-- Mã hồ sơ -->
                                <div class="info-item">
                                    <div class="info-label"><i class="fa fa-barcode"></i> Mã hồ sơ</div>
                                    <div class="info-value">{{ $hoSo->maHSXL }}</div>
                                </div>

                                <!-- Tên TTHC -->
                                <div class="info-item">
                                    <div class="info-label"><i class="fa fa-file-text"></i> Tên thủ tục</div>
                                    <div class="info-value">{{ $hoSo->tthc->tenTTHC ?? 'N/A' }}</div>
                                </div>

                                <!-- Tên chủ hồ sơ -->
                                <div class="info-item">
                                    <div class="info-label"><i class="fa fa-user"></i> Chủ hồ sơ</div>
                                    <div class="info-value">{{ $tenChuHoSo }}</div>
                                </div>

                                <!-- Ngày tiếp nhận -->
                                <div class="info-item">
                                    <div class="info-label"><i class="fa fa-calendar-check-o"></i> Ngày tiếp nhận</div>
                                    <div class="info-value">
                                        {{ $hoSo->ngayTiepNhan ? \Carbon\Carbon::parse($hoSo->ngayTiepNhan)->format('d/m/Y') : 'Chưa tiếp nhận' }}
                                    </div>
                                </div>

                                <!-- Ngày hẹn trả -->
                                <div class="info-item">
                                    <div class="info-label"><i class="fa fa-calendar"></i> Ngày hẹn trả</div>
                                    <div class="info-value">
                                        {{ $hoSo->ngayHenTra ? \Carbon\Carbon::parse($hoSo->ngayHenTra)->format('d/m/Y') : 'N/A' }}
                                    </div>
                                </div>

                                <!-- Ngày kết thúc xử lý -->
                                <div class="info-item">
                                    <div class="info-label"><i class="fa fa-calendar-check-o"></i> Ngày hoàn thành</div>
                                    <div class="info-value">
                                        {{ $hoSo->ngayKetThuc ? \Carbon\Carbon::parse($hoSo->ngayKetThuc)->format('d/m/Y') : 'Chưa hoàn thành' }}
                                    </div>
                                </div>

                                <!-- Trạng thái -->
                                <div class="info-item">
                                    <div class="info-label"><i class="fa fa-tasks"></i> Trạng thái</div>
                                    <div class="info-value">
                                        <span class="status-badge 
                                            @if($hoSo->maTrangThai == 10) badge-success
                                            @elseif(in_array($hoSo->maTrangThai, [1, 2, 3])) badge-info
                                            @elseif($hoSo->maTrangThai == 5) badge-warning
                                            @else badge-primary
                                            @endif">
                                            {{ $hoSo->trangThai->tenTrangThai ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Lệ phí -->
                                <div class="info-item">
                                    <div class="info-label"><i class="fa fa-money"></i> Lệ phí</div>
                                    <div class="info-value">{{ number_format($hoSo->lePhi ?? 0, 0, ',', '.') }} VNĐ</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
