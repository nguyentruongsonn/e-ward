@foreach ($appointments as $appointment)
    @php
        $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        $thoiGianHen = $appointment->thoiGianHen ? \Carbon\Carbon::parse($appointment->thoiGianHen)->setTimezone('Asia/Ho_Chi_Minh') : null;
        $hoursUntil = $thoiGianHen ? $now->diffInHours($thoiGianHen, false) : null;
        $isWithin24Hours = $thoiGianHen && $hoursUntil !== null && $hoursUntil >= 0 && $hoursUntil <= 24;
    @endphp
    <tr style="background-color: {{ $isWithin24Hours ? '#fff3cd' : '#ffffff' }};">
        <td>{{ $appointment->maLichHen }}</td>
        <td>{{ $appointment->tthc->tenTTHC ?? '-' }}</td>
        <td>{{ $appointment->thoiGianHen ? $appointment->thoiGianHen->format('d/m/Y H:i') : '-' }}</td>
        <td>
            @if($appointment->quaylamviec)
                {{ $appointment->quaylamviec->tenQuayLamViec }} ({{ $appointment->maQuayLamViec }})
            @else
                {{ $appointment->maQuayLamViec ?? 'Chưa phân quầy' }}
            @endif
        </td>
        <td>{{ $appointment->soThuTu ?? '-' }}</td>
        <td>
            <span class="badge 
                @if($appointment->trangThai == 'Hoàn thành') bg-success
                @elseif($appointment->trangThai == 'Đang xử lý') bg-info
                @elseif($appointment->trangThai == 'Đã hủy' || $appointment->trangThai == 'Không đến') bg-danger
                @else bg-warning
                @endif">
                {{ $appointment->trangThai }}
            </span>
        </td>
        <td>
            <div class="d-flex flex-wrap gap-1">
                <a href="{{ route('profile.appointments.show', $appointment->id) }}"
                   class="btn btn-sm btn-outline-info">
                    <i class="fas fa-eye me-1"></i>Chi tiết
                </a>

                @if($appointment->checkin_token)
                    <a href="{{ route('appointment.checkin', $appointment->checkin_token) }}"
                       class="btn btn-sm btn-outline-primary"
                       target="_blank"
                       title="Xem QR code để cán bộ quét">
                        <i class="fas fa-qrcode me-1"></i>QR Check-in
                    </a>
                @endif

                @php
                    // Chỉ cho phép hủy khi: trạng thái đúng VÀ chưa tới giờ hẹn
                    $now = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
                    $thoiGianHen = $appointment->thoiGianHen ? \Carbon\Carbon::parse($appointment->thoiGianHen)->setTimezone('Asia/Ho_Chi_Minh') : null;
                    $canCancel = in_array($appointment->trangThai, ['Đã đặt lịch', 'Chờ đến']) &&
                                 $thoiGianHen && $thoiGianHen->gt($now);
                @endphp
                @if($canCancel)
                    <form action="{{ route('profile.appointments.cancel', $appointment->id) }}"
                          method="POST"
                          onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không?');"
                          style="display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-times me-1"></i>Hủy
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@endforeach

