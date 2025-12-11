@extends('layouts.app')

@section('title', 'Đặt lịch nộp hồ sơ')

@section('content')
<style>
    .form-section {
        border: 1px solid #eee;
        background: #fff;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    .form-section h5 {
        font-size: 18px;
        font-weight: 600;
        color: #007bff;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
        margin-bottom: 20px;
    }
    .time-slot {
        display: inline-block;
        padding: 8px 16px;
        margin: 5px;
        border: 2px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
        background: #fff;
    }
    .time-slot:hover:not(.disabled) {
        border-color: #007bff;
        background: #f0f9f4;
    }
    .time-slot.selected {
        border-color: #007bff;
        background: #007bff;
        color: #fff;
    }
    .time-slot.disabled {
        background: #f5f5f5;
        color: #999;
        cursor: not-allowed;
        opacity: 0.5;
    }
    .quay-item {
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }
    .quay-item:hover {
        border-color: #007bff;
        background: #f0f9f4;
    }
    .quay-item.selected {
        border-color: #007bff;
        background: #007bff;
        color: #fff;
    }
</style>

<!---Page header-->
<div class="container-fluid page-header pt-5">
    <div class="container py-5">
        <form class="d-flex wow fadeInUp" data-wow-delay="0.3s" role="search">
            <input class="form-control me-2" type="search" placeholder="Nhập từ khóa tìm kiếm" aria-label="Search">
            <button class="btn btn-color" type="submit">TÌM KIẾM</button>
        </form>
    </div>
</div>
<!--Page header-->
<div class="container py-4">
    {{-- BREADCRUMB --}}


    <div class="row">
        <div class="col-md-8">
            <div class="form-section">
                <h5>Thông tin người nộp</h5>
                <form id="appointmentForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" name="ho_ten" value="{{ $nguoi->hoTen ?? '' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" class="form-control" name="ngay_sinh" value="{{ $nguoi->ngaySinh ?? '' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số CCCD</label>
                            <input type="text" class="form-control" name="cccd" value="{{ $nguoi->maCCCD ?? '' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ $nguoi->email ?? '' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" name="so_dien_thoai" value="{{ $nguoi->soDienThoai ?? '' }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" name="dia_chi" value="{{ $nguoi->noiThuongTru ?? $nguoi->noiTamTru ?? '' }}" readonly>
                        </div>
                    </div>
                </form>
            </div>

            <div class="form-section">
                <h5>Thông tin đặt lịch</h5>
                <div class="mb-3">
                    <label class="form-label required">Thủ tục hành chính</label>
                    <input type="text" class="form-control" value="{{ $tthc->tenTTHC ?? '' }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Ngày hẹn</label>
                    <input type="date" class="form-control" id="ngayHen" name="ngay_hen" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quầy làm việc</label>
                    <p class="text-muted"><small>Hệ thống sẽ tự động chọn quầy khi bạn check-in</small></p>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Giờ hẹn</label>
                    <div id="timeSlotsContainer">
                        <p class="text-muted">Vui lòng chọn ngày hẹn trước</p>
                    </div>
                    <input type="hidden" id="gioHen" name="gio_hen" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('outstanding-service.show', $tthc->maTTHC) }}" class="btn btn-secondary">Hủy</a>
                <button type="button" class="btn btn-color" id="btnDatLich">Đặt lịch</button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-section">
                <h5>Thông tin lịch hẹn</h5>
                <div id="appointmentInfo" class="text-muted">
                    <p>Vui lòng điền đầy đủ thông tin để xem chi tiết</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal thành công với QR code --}}
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">
                    <i class="fa fa-check-circle me-2"></i>Đặt lịch thành công!
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <p class="mb-2"><strong>Mã lịch hẹn:</strong> <span id="modalMaLichHen" class="text-color fw-bold"></span></p>
                    <p class="mb-2"><strong>Thời gian:</strong> <span id="modalThoiGian"></span></p>
                </div>
                <div class="mb-3">
                    <p class="text-muted mb-2">Quét mã QR để check-in và lấy số thứ tự:</p>
                    <div id="qrCodeContainer" class="d-flex justify-content-center">
                        <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                    </div>
                </div>
                <div class="alert alert-info">
                    <small><i class="fa fa-info-circle"></i> Vui lòng lưu lại mã QR này để check-in khi đến nơi</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <a href="{{ route('outstanding-service.show', $tthc->maTTHC) }}" class="btn btn-color">Về trang chi tiết</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ngayHenInput = document.getElementById('ngayHen');
    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
    const gioHenInput = document.getElementById('gioHen');
    const btnDatLich = document.getElementById('btnDatLich');
    const appointmentInfo = document.getElementById('appointmentInfo');

    let selectedTime = null;
    let gioDaDay = [];
    let gioKhongChoDat = [];
    let gioLamViec = [];

    // Xử lý thay đổi ngày hẹn
    ngayHenInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const dayOfWeek = selectedDate.getDay();
        
        // Check if weekend (0 = Sunday, 6 = Saturday)
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            alert('Không thể đặt lịch vào thứ 7 và chủ nhật. Vui lòng chọn ngày làm việc (Thứ 2 - Thứ 6).');
            this.value = '';
            timeSlotsContainer.innerHTML = '<p class="text-muted">Vui lòng chọn ngày hẹn trước</p>';
            selectedTime = null;
            gioHenInput.value = '';
            return;
        }
        
        selectedTime = null;
        gioHenInput.value = '';
        updateTimeSlots();
    });

    // Cập nhật danh sách giờ hẹn
    function updateTimeSlots() {
        const ngayHen = ngayHenInput.value;

        if (!ngayHen) {
            timeSlotsContainer.innerHTML = '<p class="text-muted">Vui lòng chọn ngày hẹn trước</p>';
            return;
        }

        timeSlotsContainer.innerHTML = '<p class="text-muted">Đang tải...</p>';

        // Gọi API để lấy danh sách giờ đã đầy
        fetch(`{{ route('appointment.available-slots', $tthc->maTTHC) }}?ngay_hen=${ngayHen}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    gioDaDay = data.gioDaDay || [];
                    gioKhongChoDat = data.gioKhongChoDat || [];
                    gioLamViec = data.gioLamViec || [];
                    renderTimeSlots(gioDaDay, gioKhongChoDat, gioLamViec);
                } else {
                    timeSlotsContainer.innerHTML = '<p class="text-danger">Có lỗi xảy ra: ' + data.message + '</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                timeSlotsContainer.innerHTML = '<p class="text-danger">Có lỗi xảy ra khi tải dữ liệu</p>';
            });
    }

    // Render danh sách giờ hẹn
    function renderTimeSlots(gioDaDay, gioKhongChoDat, gioLamViec) {
        // Sử dụng danh sách giờ làm việc từ server (cách nhau 1 tiếng, không bao gồm 17:30)
        const hours = gioLamViec.length > 0 ? gioLamViec : [
            '07:30', '08:30', '09:30', '10:30', '11:30',
            '13:30', '14:30', '15:30', '16:30'  // Removed 17:30
        ];

        let html = '<div class="d-flex flex-wrap">';
        hours.forEach(gio => {
            const isDisabled = gioDaDay.includes(gio) || gioKhongChoDat.includes(gio);
            const isSelected = selectedTime === gio;
            let className = 'time-slot';
            if (isDisabled) className += ' disabled';
            if (isSelected) className += ' selected';

            html += `<div class="${className}" data-time="${gio}" ${isDisabled ? '' : 'onclick="selectTime(\'' + gio + '\')"'}>${gio}</div>`;
        });
        html += '</div>';

        timeSlotsContainer.innerHTML = html;
        updateAppointmentInfo();
    }

    // Chọn giờ hẹn
    window.selectTime = function(gio) {
        if (gioKhongChoDat.includes(gio) || gioDaDay.includes(gio)) return;

        // Bỏ chọn giờ cũ
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });

        // Chọn giờ mới
        const slot = document.querySelector(`[data-time="${gio}"]`);
        if (slot) {
            slot.classList.add('selected');
            selectedTime = gio;
            gioHenInput.value = gio;
            updateAppointmentInfo();
        }
    };

    // Cập nhật thông tin lịch hẹn
    function updateAppointmentInfo() {
        if (!ngayHenInput.value || !selectedTime) {
            appointmentInfo.innerHTML = '<p class="text-muted">Vui lòng điền đầy đủ thông tin</p>';
            return;
        }

        const ngayHen = new Date(ngayHenInput.value).toLocaleDateString('vi-VN');

        appointmentInfo.innerHTML = `
            <p><strong>Thủ tục:</strong> {{ $tthc->tenTTHC ?? '' }}</p>
            <p><strong>Ngày hẹn:</strong> ${ngayHen}</p>
            <p><strong>Giờ hẹn:</strong> ${selectedTime}</p>
            <p><strong>Quầy:</strong> Sẽ được chọn khi check-in</p>
        `;
    }

    // Xử lý đặt lịch
    btnDatLich.addEventListener('click', function() {
        if (!ngayHenInput.value || !selectedTime) {
            alert('Vui lòng điền đầy đủ thông tin!');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('ngay_hen', ngayHenInput.value);
        formData.append('gio_hen', selectedTime);

        btnDatLich.disabled = true;
        btnDatLich.textContent = 'Đang xử lý...';

        fetch('{{ route("appointment.store", $tthc->maTTHC) }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hiển thị modal thành công với QR code
                showSuccessModal(data);
            } else {
                alert('Lỗi: ' + data.message);
                btnDatLich.disabled = false;
                btnDatLich.textContent = 'Đặt lịch';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi đặt lịch');
            btnDatLich.disabled = false;
            btnDatLich.textContent = 'Đặt lịch';
        });
    });

    // Hiển thị modal thành công với QR code
    function showSuccessModal(data) {
        document.getElementById('modalMaLichHen').textContent = data.maLichHen;
        document.getElementById('modalThoiGian').textContent = data.thoiGianHen;
        document.getElementById('qrCodeImage').src = data.qr_code_image;

        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();

        btnDatLich.disabled = false;
        btnDatLich.textContent = 'Đặt lịch';
    }

    // Khởi tạo
    const today = new Date().toISOString().split('T')[0];
    ngayHenInput.value = today;
});
</script>
@endsection
