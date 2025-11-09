@foreach ($hoSoList as $hoSo)
    <tr data-detail-url="{{ route('profile.hoso.show', $hoSo->maHSXL) }}">
        <td>{{ $hoSo->maHSXL }}</td>
        <td>{{ $hoSo->tthc->tenTTHC ?? 'N/A' }}</td>
        <td>{{ $hoSo->ngayTiepNhan ? $hoSo->ngayTiepNhan->format('d/m/Y') : 'N/A' }}</td>
        <td>{{ $hoSo->ngayHenTra ? $hoSo->ngayHenTra->format('d/m/Y') : 'N/A' }}</td>
        <td>
            @if ($hoSo->ngayKetThucXuLy)
                <span class="badge bg-success">Đã hoàn thành</span>
            @else
                <span class="badge bg-warning">Đang xử lý</span>
            @endif
        </td>
        <td>
            <a href="#" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> Xem chi tiết
            </a>
        </td>
    </tr>
@endforeach

