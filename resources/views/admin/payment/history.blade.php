@extends('admin.layout')

@section('title', 'Lịch sử Thanh toán')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-money"></i> Lịch sử Thanh toán</h3>
                    </header>
                    <div class="panel-body">
                        <!-- Thống kê nhanh -->
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-3">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Tổng doanh thu</h4>
                                    </div>
                                    <div class="panel-body">
                                        <h3 class="text-primary">{{ number_format($stats['total'], 0, ',', '.') }} đ</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Hôm nay</h4>
                                    </div>
                                    <div class="panel-body">
                                        <h3 class="text-success">{{ number_format($stats['today'], 0, ',', '.') }} đ</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Tháng này</h4>
                                    </div>
                                    <div class="panel-body">
                                        <h3 class="text-info">{{ number_format($stats['this_month'], 0, ',', '.') }} đ</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="panel panel-warning">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Tổng giao dịch</h4>
                                    </div>
                                    <div class="panel-body">
                                        <h3 class="text-warning">{{ number_format($stats['count'], 0, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter form -->
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-12" style="margin-bottom: 10px;">
                                <a href="{{ route('admin.payment.history.export', request()->all()) }}" class="btn btn-success">
                                    <i class="fa fa-file-excel-o"></i> Xuất Excel
                                </a>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom: 20px;">
                            <form method="GET" action="{{ route('admin.payment.history') }}" class="form-inline">
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Mã GD, tên, email..." value="{{ request('search') }}">
                                </div>
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <select name="loaiGD" class="form-control">
                                        <option value="">Tất cả loại GD</option>
                                        <option value="Thanh toán QR" {{ request('loaiGD') == 'Thanh toán QR' ? 'selected' : '' }}>Thanh toán QR</option>
                                        <option value="Chuyển khoản" {{ request('loaiGD') == 'Chuyển khoản' ? 'selected' : '' }}>Chuyển khoản</option>
                                        <option value="Tiền mặt" {{ request('loaiGD') == 'Tiền mặt' ? 'selected' : '' }}>Tiền mặt</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <select name="trangThai" class="form-control">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="Thành công" {{ request('trangThai') == 'Thành công' ? 'selected' : '' }}>Thành công</option>
                                        <option value="Thất bại" {{ request('trangThai') == 'Thất bại' ? 'selected' : '' }}>Thất bại</option>
                                        <option value="Đang xử lý" {{ request('trangThai') == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <input type="date" name="from_date" class="form-control" 
                                           placeholder="Từ ngày" value="{{ request('from_date') }}">
                                </div>
                                <div class="form-group" style="margin-right: 10px; margin-bottom: 10px;">
                                    <input type="date" name="to_date" class="form-control" 
                                           placeholder="Đến ngày" value="{{ request('to_date') }}">
                                </div>
                                <button type="submit" class="btn btn-primary" style="margin-right: 10px; margin-bottom: 10px;">
                                    <i class="fa fa-search"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.payment.history') }}" class="btn btn-default" style="margin-bottom: 10px;">
                                    <i class="fa fa-refresh"></i> Xóa bộ lọc
                                </a>
                            </form>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã GD</th>
                                        <th>Người thanh toán</th>
                                        <th>Hồ sơ</th>
                                        <th>Loại GD</th>
                                        <th>Ngày GD</th>
                                        <th>Số tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Mô tả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->maGD }}</td>
                                        <td>
                                            <div>{{ $payment->hoTen ?? '-' }}</div>
                                            <small class="text-muted">{{ $payment->email ?? '-' }}</small>
                                        </td>
                                        <td>{{ $payment->tenChuHoSo ?? $payment->maHSXL ?? '-' }}</td>
                                        <td>{{ $payment->loaiGD ?? '-' }}</td>
                                        <td>{{ $payment->ngayGD ? \Carbon\Carbon::parse($payment->ngayGD)->format('d/m/Y H:i') : '-' }}</td>
                                        <td class="text-right"><strong>{{ number_format($payment->soTien ?? 0, 0, ',', '.') }} đ</strong></td>
                                        <td>
                                            @if($payment->trangThai == 'Thành công')
                                                <span class="badge bg-success">{{ $payment->trangThai }}</span>
                                            @elseif($payment->trangThai == 'Thất bại')
                                                <span class="badge bg-danger">{{ $payment->trangThai }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ $payment->trangThai ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($payment->moTa ?? '-', 50) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="text-center">
                            {{ $payments->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

