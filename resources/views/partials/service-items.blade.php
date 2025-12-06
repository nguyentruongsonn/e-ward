@foreach ($hoSoList as $hoSo)
    <tr data-detail-url="{{ route('profile.hoso.show', $hoSo->maHSXL) }}">
        <td>{{ $hoSo->maHSXL }}</td>
        <td>{{ $hoSo->tthc->tenTTHC ?? 'N/A' }}</td>
        <td>{{ $hoSo->ngayTiepNhan ? $hoSo->ngayTiepNhan->format('d/m/Y') : 'N/A' }}</td>
        <td>{{ $hoSo->ngayHenTra ? $hoSo->ngayHenTra->format('d/m/Y') : 'N/A' }}</td>
        <td>
            @if ($hoSo->trangThai)
                @php
                    // Áp dụng màu giống như admin dashboard
                    $badgeClass = match($hoSo->maTrangThai) {
                        1 => 'bg-warning',        // Chờ tiếp nhận - vàng
                        2 => 'bg-info',           // Được tiếp nhận - xanh dương
                        3 => 'bg-danger',         // Không được tiếp nhận - đỏ
                        4 => 'bg-primary',        // Đang xử lý - xanh dương đậm
                        5 => 'bg-warning',        // Yêu cầu bổ sung giấy tờ - vàng
                        6 => 'bg-info',           // Hồ sơ đã bổ sung giấy tờ - xanh dương
                        7 => 'bg-danger',         // Công dân yêu cầu rút hồ sơ - đỏ
                        8 => 'bg-danger',         // Dừng xử lý - đỏ
                        9 => 'bg-success',        // Đã xử lý xong - xanh lá
                        10 => 'bg-success',       // Đã trả kết quả - xanh lá
                        11 => 'bg-warning',       // Nhận trực tiếp - vàng
                        default => 'bg-secondary'
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">
                    {{ $hoSo->trangThai->tenTrangThai }}
                </span>
            @elseif ($hoSo->ngayKetThucXuLy)
                <span class="badge bg-success">Đã hoàn thành</span>
            @else
                <span class="badge bg-warning">Đang xử lý</span>
            @endif
        </td>
        <td>
            <a href="{{ route('profile.hoso.show', $hoSo->maHSXL) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> Xem chi tiết
            </a>
        </td>
    </tr>
@endforeach

