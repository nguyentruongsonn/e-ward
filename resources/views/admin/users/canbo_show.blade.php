@extends('admin.layout')

@section('title', 'Chi tiết Cán bộ')

@section('content')
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <h3><i class="fa fa-user-tie"></i> Chi tiết Cán bộ</h3>
                    </header>
                    <div class="panel-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px;">ID người dùng</th>
                                <td>{{ $canBo->IDnguoiDung }}</td>
                            </tr>
                            <tr>
                                <th>Mã cán bộ</th>
                                <td>{{ $canBo->IDCB ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Họ tên</th>
                                <td>{{ $canBo->hoTen ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $canBo->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Số điện thoại</th>
                                <td>{{ $canBo->soDienThoai ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Mã CCCD</th>
                                <td>{{ $canBo->maCCCD ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Giới tính</th>
                                <td>{{ $canBo->gioiTinh ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Ngày sinh</th>
                                <td>
                                    @if($canBo->ngaySinh)
                                        {{ \Carbon\Carbon::parse($canBo->ngaySinh)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Quê quán</th>
                                <td>{{ $canBo->queQuan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nơi thường trú</th>
                                <td>{{ $canBo->noiThuongTru ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nơi tạm trú</th>
                                <td>{{ $canBo->noiTamTru ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Vai trò</th>
                                <td>{{ $canBo->vaiTro ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Quầy làm việc</th>
                                <td>{{ $canBo->tenQuayLamViec ?? '-' }}</td>
                            </tr>
                        </table>

                        <a href="{{ route('admin.users.canbo') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </section>
</section>
@endsection


