@extends('admin.layout')

@section('title', 'Chi tiết Công dân')

@section('content')
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-user"></i> Chi tiết Công dân</h3>
                    </header>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px;">ID người dùng</th>
                                <td>{{ $congDan->IDnguoiDung }}</td>
                            </tr>
                            <tr>
                                <th>Mã công dân</th>
                                <td>{{ $congDan->IDCD ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Họ tên</th>
                                <td>{{ $congDan->hoTen ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $congDan->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td>{{ $congDan->soDienThoai ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Mã CCCD</th>
                                <td>{{ $congDan->maCCCD ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Giới tính</th>
                                <td>{{ $congDan->gioiTinh ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Ngày sinh</th>
                                <td>
                                    @if($congDan->ngaySinh)
                                        {{ \Carbon\Carbon::parse($congDan->ngaySinh)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Quê quán</th>
                                <td>{{ $congDan->queQuan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nơi thường trú</th>
                                <td>{{ $congDan->noiThuongTru ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nơi tạm trú</th>
                                <td>{{ $congDan->noiTamTru ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Vai trò</th>
                                <td>{{ $congDan->vaiTro ?? '-' }}</td>
                            </tr>
                        </table>

                        <a href="{{ route('admin.users.congdan') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
@endsection


