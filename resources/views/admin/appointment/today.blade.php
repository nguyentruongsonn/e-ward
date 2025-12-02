@extends('admin.layout')

@section('title', 'Lịch hẹn hôm nay')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-calendar"></i> Lịch hẹn hôm nay ({{ \Carbon\Carbon::parse($today)->format('d/m/Y') }})</h3>
                    </header>
                    <div class="panel-body">
                        <!-- Filter form -->
                        <div class="row" style="margin-bottom: 20px;">
                            <form method="GET" action="{{ route('admin.appointment.today') }}" class="form-inline">
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <label for="search" style="margin-right: 5px; font-weight: normal;">Tìm kiếm:</label>
                                    <input type="text" name="search" id="search" class="form-control" 
                                           placeholder="Mã lịch hẹn, tên, email, SĐT..." 
                                           value="{{ request('search') }}" style="width: 250px;">
                                </div>
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <label for="trangThai" style="margin-right: 5px; font-weight: normal;">Trạng thái:</label>
                                    <select name="trangThai" id="trangThai" class="form-control" style="width: 180px;">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="Đã đặt lịch" {{ request('trangThai') == 'Đã đặt lịch' ? 'selected' : '' }}>Đã đặt lịch</option>
                                        <option value="Chờ đến" {{ request('trangThai') == 'Chờ đến' ? 'selected' : '' }}>Chờ đến</option>
                                        <option value="Đang xử lý" {{ request('trangThai') == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                                        <option value="Hoàn thành" {{ request('trangThai') == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                                        <option value="Đã hủy" {{ request('trangThai') == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                                        <option value="Không đến" {{ request('trangThai') == 'Không đến' ? 'selected' : '' }}>Không đến</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-right: 10px; margin-bottom: 10px;">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.appointment.today') }}" class="btn btn-default" style="margin-bottom: 10px;">
                                    <i class="fa fa-refresh"></i> Xóa bộ lọc
                                </a>
                            </form>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã lịch hẹn</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Thủ tục</th>
                                        <th>Thời gian hẹn</th>
                                        <th>Quầy</th>
                                        <th>Số thứ tự</th>
                                        <th>Trạng thái</th>
                                        @if(in_array($user->vaiTro, ['Cán bộ một cửa', 'Quản trị viên']))
                                        <th>Cập nhật TT</th>
                                        @endif
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($appointments as $appointment)
                                    @php
                                        $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                                        $thoiGianHen = $appointment->thoiGianHen ? \Carbon\Carbon::parse($appointment->thoiGianHen)->setTimezone('Asia/Ho_Chi_Minh') : null;
                                        
                                        // Tính số giờ còn lại đến lịch hẹn
                                        $hoursUntil = null;
                                        if ($thoiGianHen) {
                                            if ($thoiGianHen->isFuture()) {
                                                // Nếu lịch hẹn trong tương lai, tính số giờ còn lại
                                                $hoursUntil = $now->diffInHours($thoiGianHen, false);
                                            } else {
                                                // Nếu lịch hẹn đã qua, set -1
                                                $hoursUntil = -1;
                                            }
                                        }
                                        
                                        // Nền cam nếu còn từ 0 đến 24 giờ (chưa đến giờ hẹn)
                                        $isWithin24Hours = $thoiGianHen && $hoursUntil !== null && $hoursUntil >= 0 && $hoursUntil <= 24;
                                        
                                        $canSendReminder = $isWithin24Hours && !$appointment->reminder_sent_at && 
                                                          $appointment->trangThai != 'Hoàn thành' && 
                                                          $appointment->trangThai != 'Đã hủy' && 
                                                          $appointment->trangThai != 'Không đến';
                                    @endphp
                                    <tr style="background-color: {{ $isWithin24Hours ? '#fff3cd' : '#ffffff' }};">
                                        <td>{{ $appointment->maLichHen }}</td>
                                        <td>{{ $appointment->congdan->nguoi->hoTen ?? '-' }}</td>
                                        <td>{{ $appointment->congdan->nguoi->email ?? '-' }}</td>
                                        <td>{{ $appointment->congdan->nguoi->soDienThoai ?? '-' }}</td>
                                        <td>{{ $appointment->tthc->tenTTHC ?? '-' }}</td>
                                        <td>{{ $appointment->thoiGianHen ? $appointment->thoiGianHen->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            @if($appointment->maQuayLamViec)
                                                @php
                                                    $quay = DB::table('quaylamviec')->where('maQuayLamViec', $appointment->maQuayLamViec)->first();
                                                @endphp
                                                {{ $quay->tenQuayLamViec ?? 'Quầy ' . $appointment->maQuayLamViec }}
                                            @else
                                                Chưa phân quầy
                                            @endif
                                        </td>
                                        <td>{{ $appointment->soThuTu ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $appointment->trangThai == 'Đã đặt lịch' ? 'primary' : ($appointment->trangThai == 'Hoàn thành' ? 'success' : ($appointment->trangThai == 'Đã hủy' ? 'danger' : 'warning')) }}">
                                                {{ $appointment->trangThai }}
                                            </span>
                                        </td>
                                        @if(in_array($user->vaiTro, ['Cán bộ một cửa', 'Quản trị viên']))
                                        <td>
                                            <select class="form-control form-control-sm status-update-select" 
                                                    data-appointment-id="{{ $appointment->id }}"
                                                    data-current-status="{{ $appointment->trangThai }}"
                                                    style="width: 160px; font-size: 11px;">
                                                <option value="Đã đặt lịch" {{ $appointment->trangThai == 'Đã đặt lịch' ? 'selected' : '' }}>Đã đặt lịch</option>
                                                <option value="Chờ đến" {{ $appointment->trangThai == 'Chờ đến' ? 'selected' : '' }}>Chờ đến</option>
                                                <option value="Đang xử lý" {{ $appointment->trangThai == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                                                <option value="Hoàn thành" {{ $appointment->trangThai == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                                                <option value="Yêu cầu bổ sung giấy tờ" {{ $appointment->trangThai == 'Yêu cầu bổ sung giấy tờ' ? 'selected' : '' }}>Yêu cầu bổ sung</option>
                                                <option value="Đã hủy" {{ $appointment->trangThai == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                                                <option value="Không đến" {{ $appointment->trangThai == 'Không đến' ? 'selected' : '' }}>Không đến</option>
                                            </select>
                                        </td>
                                        @endif
                                        <td>
                                            @if($appointment->checkin_token)
                                                <a href="{{ route('admin.appointment.scan', ['token' => $appointment->checkin_token]) }}" 
                                                   class="btn btn-info btn-sm" title="Xem chi tiết">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endif
                                            @if($canSendReminder)
                                                <button type="button" 
                                                        class="btn btn-warning btn-sm btn-send-reminder" 
                                                        data-lichhen-id="{{ $appointment->id }}"
                                                        data-email="{{ $appointment->congdan->nguoi->email ?? '' }}"
                                                        title="Gửi mail nhắc hẹn">
                                                    <i class="fa fa-envelope"></i> Gửi mail
                                                </button>
                                            @elseif($appointment->reminder_sent_at)
                                                <span class="badge bg-success" title="Đã gửi mail nhắc: {{ \Carbon\Carbon::parse($appointment->reminder_sent_at)->format('d/m/Y H:i') }}">
                                                    <i class="fa fa-check"></i> Đã gửi mail nhắc hẹn
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="{{ in_array($user->vaiTro, ['Cán bộ một cửa', 'Quản trị viên']) ? '11' : '10' }}" class="text-center">Không có lịch hẹn nào hôm nay</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="text-center" style="margin-top: 20px;">
                            {{ $appointments->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cập nhật trạng thái lịch hẹn
    document.querySelectorAll('.status-update-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const appointmentId = this.getAttribute('data-appointment-id');
            const currentStatus = this.getAttribute('data-current-status');
            const newStatus = this.value;
            
            if (newStatus === currentStatus) {
                return; // No change
            }
            
            let confirmMsg = `Bạn có chắc muốn cập nhật trạng thái từ "${currentStatus}" sang "${newStatus}"?`;
            if (newStatus === 'Hoàn thành') {
                confirmMsg += '\n\nLưu ý: Hệ thống sẽ tự động tạo hồ sơ xử lý (trạng thái Nhận trực tiếp) sau khi cập nhật.';
            }
            
            if (!confirm(confirmMsg)) {
                this.value = currentStatus; // Revert to original
                return;
            }
            
            const select = this;
            select.disabled = true;
            
            fetch(`/admin/appointment/${appointmentId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    trangThai: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    select.setAttribute('data-current-status', newStatus);
                    let alertMsg = data.message;
                    if (data.hoSoCreated) {
                        alertMsg += `\n\nBạn có muốn xem hồ sơ vừa tạo (${data.maHSXL})?`;
                        if (confirm(alertMsg)) {
                            window.location.href = data.hoSoUrl;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert(alertMsg);
                        window.location.reload();
                    }
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể cập nhật trạng thái'));
                    select.value = currentStatus;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi cập nhật trạng thái');
                select.value = currentStatus;
            })
            .finally(() => {
                select.disabled = false;
            });
        });
    });

    // Gửi mail nhắc hẹn
    document.querySelectorAll('.btn-send-reminder').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const lichHenId = this.getAttribute('data-lichhen-id');
            const email = this.getAttribute('data-email');
            const btn = this;
            
            if (!confirm('Bạn có chắc muốn gửi mail nhắc hẹn đến ' + email + '?')) {
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang gửi...';
            
            fetch('{{ route('admin.appointment.send-reminder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    lichhen_id: lichHenId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '<i class="fa fa-check"></i> Đã gửi';
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-success');
                    btn.disabled = true;
                    alert('Đã gửi mail nhắc hẹn thành công!');
                    // Reload sau 1 giây
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể gửi mail'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-envelope"></i> Gửi mail';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi mail');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-envelope"></i> Gửi mail';
            });
        });
    });
});
</script>
@endpush

