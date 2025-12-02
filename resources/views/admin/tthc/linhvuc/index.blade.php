@extends('admin.layout')

@section('title', 'Quản lý Lĩnh vực')

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-book"></i> Quản lý Lĩnh vực</h3>
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
                                @if(auth()->user() && trim(auth()->user()->vaiTro) === 'Quản trị viên')
                                    <a href="{{ route('admin.tthc.linhvuc.create') }}" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Thêm lĩnh vực mới
                                    </a>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('admin.tthc.linhvuc.index') }}" class="form-inline pull-right">
                                    <div class="form-group" style="margin-right: 10px;">
                                        <input type="text" name="search" class="form-control" 
                                               placeholder="Tìm kiếm..." value="{{ request('search') }}">
                                    </div>
                                    <button type="submit" class="btn btn-default">
                                        <i class="fa fa-search"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.tthc.linhvuc.index') }}" class="btn btn-default">
                                        <i class="fa fa-refresh"></i> Xóa bộ lọc
                                    </a>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã lĩnh vực</th>
                                        <th>Tên lĩnh vực</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($linhVucs as $linhVuc)
                                    <tr>
                                        <td>{{ $linhVuc->maLinhVuc }}</td>
                                        <td>{{ $linhVuc->tenLinhVuc }}</td>
                                        <td>
                                            @if(auth()->user() && trim(auth()->user()->vaiTro) === 'Quản trị viên')
                                                <a href="{{ route('admin.tthc.linhvuc.edit', $linhVuc->maLinhVuc) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i> Sửa
                                                </a>
                                                <form action="{{ route('admin.tthc.linhvuc.destroy', $linhVuc->maLinhVuc) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lĩnh vực này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fa fa-trash"></i> Xóa
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">Chỉ xem</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center">
                            {{ $linhVucs->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

