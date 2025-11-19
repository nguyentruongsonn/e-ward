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
        <td>{{ $appointment->maQuayLamViec ?? 'Chưa phân quầy' }}</td>
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
            @if($appointment->checkin_token)
                <a href="{{ route('appointment.checkin', $appointment->checkin_token) }}" 
                   class="btn btn-sm btn-outline-primary" 
                   target="_blank"
                   title="Xem QR code để cán bộ quét">
                    <i class="fas fa-qrcode me-1"></i>Xem QR Code
                </a>
            @endif
        </td>
    </tr>
@endforeach

