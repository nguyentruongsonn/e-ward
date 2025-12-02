@extends('admin.layout')

@section('title', 'Danh sách tất cả hồ sơ')

@section('content')
<!--main content start-->
<section id="main-content">
	<section class="wrapper">
		<div class="row">
			<div class="col-lg-12">
				<section class="panel">
					<header class="panel-heading">
						<h3>Danh sách tất cả hồ sơ</h3>
					</header>
					<div class="panel-body">
						<!-- Filter form -->
						<div class="row" style="margin-bottom: 20px;">
							<form method="GET" action="{{ route('admin.hosoxuly.all') }}" class="form-inline">
								<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
									<label for="search" style="margin-right: 5px; font-weight: normal;">Tìm kiếm:</label>
									<input type="text" name="search" id="search" class="form-control" placeholder="Mã HS, tên, email, SĐT..." value="{{ request('search') }}" style="width: 250px;">
								</div>
								<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
									<label for="maTrangThai" style="margin-right: 5px; font-weight: normal;">Trạng thái:</label>
									<select name="maTrangThai" id="maTrangThai" class="form-control" style="width: 200px;">
										<option value="">Tất cả trạng thái</option>
										@foreach($trangThais as $tt)
											<option value="{{ $tt->maTrangThai }}" {{ request('maTrangThai') == $tt->maTrangThai ? 'selected' : '' }}>
												{{ $tt->tenTrangThai }}
											</option>
										@endforeach
									</select>
								</div>
								<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
									<label for="ngayTiepNhan_from" style="margin-right: 5px; font-weight: normal;">Từ ngày:</label>
									<input type="date" name="ngayTiepNhan_from" id="ngayTiepNhan_from" class="form-control" value="{{ request('ngayTiepNhan_from') }}" style="width: 150px;">
								</div>
								<div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
									<label for="ngayTiepNhan_to" style="margin-right: 5px; font-weight: normal;">Đến ngày:</label>
									<input type="date" name="ngayTiepNhan_to" id="ngayTiepNhan_to" class="form-control" value="{{ request('ngayTiepNhan_to') }}" style="width: 150px;">
								</div>
								<button type="submit" class="btn btn-primary" style="margin-right: 10px; margin-bottom: 10px;">Tìm kiếm</button>
								<a href="{{ route('admin.hosoxuly.all') }}" class="btn btn-default" style="margin-bottom: 10px;">Xóa bộ lọc</a>
							</form>
						</div>

						<!-- Table -->
						<div class="table-responsive" style="min-height: 300px;">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>Mã HS</th>
										<th>Tên chủ hồ sơ</th>
										<th>Thủ tục</th>
										<th>Email</th>
										<th>SĐT</th>
										<th>Ngày tiếp nhận</th>
										<th>Trạng thái</th>
										@if($user->vaiTro === 'Quản trị viên')
										<th>Cập nhật TT</th>
										@endif
										<th>Thao tác</th>
									</tr>
								</thead>
								<tbody>
									@forelse($hosos as $hoso)
									<tr>
										<td>{{ $hoso->maHSXL }}</td>
										<td>{{ is_array($hoso->tenChuHoSo) ? json_encode($hoso->tenChuHoSo, JSON_UNESCAPED_UNICODE) : ($hoso->tenChuHoSo ?? '-') }}</td>
										<td>{{ $hoso->tthc->tenTTHC ?? '-' }}</td>
										<td>{{ is_array($hoso->email) ? json_encode($hoso->email, JSON_UNESCAPED_UNICODE) : ($hoso->email ?? '-') }}</td>
										<td>{{ is_array($hoso->soDienThoai) ? json_encode($hoso->soDienThoai, JSON_UNESCAPED_UNICODE) : ($hoso->soDienThoai ?? '-') }}</td>
										<td>{{ $hoso->ngayTiepNhan ? $hoso->ngayTiepNhan->format('d/m/Y') : '-' }}</td>
										<td>
											@php
												$trangThai = $trangThais->firstWhere('maTrangThai', $hoso->maTrangThai);
												$badgeClass = match($hoso->maTrangThai) {
													1 => 'label-warning',
													2 => 'label-info',
													3 => 'label-danger',
													4 => 'label-primary',
													5 => 'label-warning',
													8 => 'label-danger',
													9 => 'label-success',
													10 => 'label-success',
													default => 'label-default'
												};
											@endphp
											<span class="label {{ $badgeClass }}">{{ $trangThai->tenTrangThai ?? '-' }}</span>
										</td>
										@if($user->vaiTro === 'Quản trị viên')
										<td>
											<select class="form-control form-control-sm trangthai-select" 
													data-mahoso="{{ $hoso->maHSXL }}" 
													data-original-value="{{ $hoso->maTrangThai }}"
													style="width: 150px; font-size: 12px;">
												@foreach($trangThais as $tt)
													<option value="{{ $tt->maTrangThai }}" {{ $hoso->maTrangThai == $tt->maTrangThai ? 'selected' : '' }}>
														{{ $tt->tenTrangThai }}
													</option>
												@endforeach
											</select>
										</td>
										@endif
										<td>
											<a href="{{ route('admin.hosoxuly.show', $hoso->maHSXL) }}" class="btn btn-sm btn-primary">
												<i class="fa fa-eye"></i> Xem
											</a>
										</td>
									</tr>
									@empty
									<tr>
										<td colspan="{{ $user->vaiTro === 'Quản trị viên' ? '9' : '8' }}" class="text-center">Không có hồ sơ nào</td>
									</tr>
									@endforelse
								</tbody>
							</table>
						</div>

						<!-- Pagination -->
						<div class="text-center" style="margin-top: 20px;">
							{{ $hosos->links() }}
						</div>
					</div>
				</section>
			</div>
		</div>
	</section>
</section>
<!--main content end-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.trangthai-select');

    selects.forEach(select => {
        select.addEventListener('change', function() {
            const maHSXL = this.getAttribute('data-mahoso');
            const maTrangThai = this.value;
            const originalValue = this.dataset.originalValue;

            if (confirm('Bạn có chắc chắn muốn cập nhật trạng thái hồ sơ này?')) {
                // Disable select while processing
                this.disabled = true;

                fetch(`{{ url('admin/hosoxuly') }}/${maHSXL}/trangthai`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        maTrangThai: parseInt(maTrangThai)
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'HTTP error! status: ' + response.status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.dataset.originalValue = maTrangThai;
                        alert('Đã cập nhật trạng thái thành công!');
                        location.reload();
                    } else {
                        alert(data.message || 'Có lỗi xảy ra');
                        this.value = originalValue;
                    }
                })
                .catch(error => {
                    alert('Có lỗi xảy ra khi cập nhật trạng thái: ' + error.message);
                    // Revert selection
                    this.value = originalValue;
                })
                .finally(() => {
                    this.disabled = false;
                });
            } else {
                // Revert selection if user cancels
                this.value = originalValue;
            }
        });

        // Store original value
        select.dataset.originalValue = select.value;
    });
});
</script>
@endsection
