@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<!--main content start-->
<section id="main-content">
	<section class="wrapper">
		<!-- //market-->
		<div class="market-updates">
			<div class="col-md-3 market-update-gd">
				<div class="market-update-block clr-block-2">
					<div class="col-md-4 market-update-right">
						<i class="fa fa-file-text"> </i>
					</div>
					 <div class="col-md-8 market-update-left">
					 <h4>Tổng hồ sơ</h4>
					<h3>{{ number_format($stats['total_hoso']) }}</h3>
					<p>Tất cả hồ sơ xử lý</p>
				  </div>
				  <div class="clearfix"> </div>
				</div>
			</div>
			<div class="col-md-3 market-update-gd">
				<div class="market-update-block clr-block-1">
					<div class="col-md-4 market-update-right">
						<i class="fa fa-file-o" ></i>
					</div>
					<div class="col-md-8 market-update-left">
					<h4>Hồ sơ mới</h4>
						<h3>{{ number_format($stats['hoso_moi']) }}</h3>
						<p>Hôm nay</p>
					</div>
				  <div class="clearfix"> </div>
				</div>
			</div>
			<div class="col-md-3 market-update-gd">
				<div class="market-update-block clr-block-3">
					<div class="col-md-4 market-update-right">
						<i class="fa fa-users"></i>
					</div>
					<div class="col-md-8 market-update-left">
						<h4>Công dân</h4>
						<h3>{{ number_format($stats['total_congdan']) }}</h3>
						<p>Tổng số công dân</p>
					</div>
				  <div class="clearfix"> </div>
				</div>
			</div>
			<div class="col-md-3 market-update-gd">
				<div class="market-update-block clr-block-4">
					<div class="col-md-4 market-update-right">
						<i class="fa fa-calendar" aria-hidden="true"></i>
					</div>
					<div class="col-md-8 market-update-left">
						<h4>Lịch hẹn</h4>
						<h3>{{ number_format($stats['lichhen_hom_nay']) }}</h3>
						<p>Hôm nay</p>
					</div>
				  <div class="clearfix"> </div>
				</div>
			</div>
		   <div class="clearfix"> </div>
		</div>	
		<!-- //market-->
		
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Hồ sơ mới nhất</h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Mã HS</th>
										<th>Tên chủ hồ sơ</th>
										<th>Ngày tiếp nhận</th>
										<th>Trạng thái</th>
									</tr>
								</thead>
								<tbody>
									@forelse($hosos as $hoso)
									<tr>
										<td>
										{{ $hoso->maHSXL ?? '-' }}
									</td>
										<td>{{ $hoso->tenChuHoSo }}</td>
										<td>{{ $hoso->ngayTiepNhan ? $hoso->ngayTiepNhan->format('d/m/Y') : '-' }}</td>
										<td>
											@php
												$trangThai = \App\Models\TrangThaiHoSo::find($hoso->maTrangThai);
											@endphp
											{{ $trangThai ? $trangThai->tenTrangThai : '-' }}
										</td>
									</tr>
									@empty
									<tr>
										<td colspan="4" class="text-center">Chưa có hồ sơ nào</td>
									</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Lịch hẹn sắp tới</h3>
					</div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Mã lịch hẹn</th>
										<th>Thời gian</th>
										<th>Trạng thái</th>
									</tr>
								</thead>
								<tbody>
									@forelse($lichhens as $lichhen)
									<tr>
										<td>{{ $lichhen->maLichHen }}</td>
										<td>{{ $lichhen->thoiGianHen->format('d/m/Y H:i') }}</td>
										<td>{{ $lichhen->trangThai }}</td>
									</tr>
									@empty
									<tr>
										<td colspan="3" class="text-center">Chưa có lịch hẹn nào</td>
									</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
 <!-- footer -->
		  <div class="footer">
			<div class="wthree-copyright">
			  <p>© {{ date('Y') }} E-Ward Admin Panel. All rights reserved</p>
			</div>
		  </div>
  <!-- / footer -->
</section>
<!--main content end-->
@endsection

