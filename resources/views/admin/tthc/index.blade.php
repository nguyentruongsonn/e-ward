@extends('admin.layout')

@section('title', 'Danh sách Thủ tục Hành chính')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-file-text"></i> Danh sách Thủ tục Hành chính</h3>
                    </header>
                    <div class="panel-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-6">
                                <a href="{{ route('admin.tthc.create') }}" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Thêm TTHC mới
                                </a>
                            </div>
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.tthc.index') }}" class="form-inline pull-right">
                                    <div class="form-group" style="margin-right: 10px;">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Tìm kiếm..." value="{{ request('search') }}">
                                    </div>
                                    <div class="form-group" style="margin-right: 10px;">
                                        <select name="maLinhVuc" class="form-control">
                                            <option value="">Tất cả lĩnh vực</option>
                                            @foreach($linhVucs as $lv)
                                                <option value="{{ $lv->maLinhVuc }}" {{ request('maLinhVuc') == $lv->maLinhVuc ? 'selected' : '' }}>
                                                    {{ $lv->tenLinhVuc }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin-right: 10px;">
                                        <select name="trangThai" class="form-control">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="Công khai" {{ request('trangThai') == 'Công khai' ? 'selected' : '' }}>Công khai</option>
                                            <option value="Chờ công khai" {{ request('trangThai') == 'Chờ công khai' ? 'selected' : '' }}>Chờ công khai</option>
                                            <option value="Bãi bỏ" {{ request('trangThai') == 'Bãi bỏ' ? 'selected' : '' }}>Bãi bỏ</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-default">
                                        <i class="fa fa-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.tthc.index') }}" class="btn btn-default">
                                        <i class="fa fa-refresh"></i> Xóa bộ lọc
                                    </a>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã TTHC</th>
                                        <th>Tên TTHC</th>
                                        <th>Lĩnh vực</th>
                                        <th>Quầy làm việc</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tthcs as $tthc)
                                    <tr>
                                        <td>{{ $tthc->maTTHC }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($tthc->tenTTHC, 80) }}</td>
                                        <td>{{ $tthc->tenLinhVuc ?? '-' }}</td>
                                        <td>{{ $tthc->tenQuayLamViec ?? 'Chưa phân quầy' }}</td>
                                        <td>
                                            @if($tthc->trangThai == 'Công khai')
                                                <span class="badge bg-success">{{ $tthc->trangThai }}</span>
                                            @elseif($tthc->trangThai == 'Chờ công khai')
                                                <span class="badge bg-warning">{{ $tthc->trangThai }}</span>
                                            @elseif($tthc->trangThai == 'Bãi bỏ')
                                                <span class="badge bg-danger">{{ $tthc->trangThai }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $tthc->trangThai ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.tthc.edit', $tthc->maTTHC) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i> Sửa
                                            </a>
                                            <form action="{{ route('admin.tthc.destroy', $tthc->maTTHC) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thủ tục này?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center">
                            {{ $tthcs->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

