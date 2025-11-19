@foreach ($hoSoList as $hoSo)
    @if($hoSo->maHSXL)
    <tr data-detail-url="{{ route('profile.hoso.show', ['maHSXL' => $hoSo->maHSXL]) }}">
        <td>{{ $hoSo->maHSXL }}</td>
        <td>{{ $hoSo->tthc->tenTTHC ?? 'N/A' }}</td>
        <td>{{ $hoSo->ngayTiepNhan ? $hoSo->ngayTiepNhan->format('d/m/Y') : 'N/A' }}</td>
        <td>{{ $hoSo->ngayHenTra ? $hoSo->ngayHenTra->format('d/m/Y') : 'N/A' }}</td>
        <td>
            @if ($hoSo->lichHenGanNhat)
                {{-- Hồ sơ từ lịch hẹn: Hiển thị thông tin lịch hẹn --}}
                <div>{{ $hoSo->lichHenGanNhat->thoiGianHen ? $hoSo->lichHenGanNhat->thoiGianHen->format('d/m/Y H:i') : '' }}</div>
                <small class="text-muted">{{ $hoSo->lichHenGanNhat->trangThai }}</small>
            @elseif ($hoSo->hinhThuc === 'Nhận trực tuyến')
                {{-- Hồ sơ nộp trực tuyến: Hiển thị "Đã nộp hồ sơ" --}}
                <span class="text-success">
                    <i class="fas fa-check-circle"></i> Đã nộp hồ sơ
                </span>
                <br>
                <small class="text-muted">Nộp trực tuyến</small>
            @else
                {{-- Hồ sơ khác: Hiển thị "Chưa đặt lịch" --}}
                <span class="text-muted">Chưa đặt lịch</span>
            @endif
        </td>
        <td>
            @if ($hoSo->ngayKetThucXuLy)
                <span class="badge bg-success">Đã hoàn thành</span>
            @else
                <span class="badge bg-warning">Đang xử lý</span>
            @endif
        </td>
        <td>
            <a href="#" class="btn btn-sm btn-info" data-ma-hsxl="{{ $hoSo->maHSXL }}">
                <i class="fas fa-eye"></i> Xem chi tiết
            </a>
        </td>
    </tr>
    @else
    <tr>
        <td colspan="7" class="text-center text-muted">Hồ sơ không có mã (maHSXL: null)</td>
    </tr>
    @endif
@endforeach

