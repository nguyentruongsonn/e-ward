@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<!--main content start-->
<section id="main-content">
	<section class="wrapper">
		<!-- //market-->

		<style>
			/* Nâng cấp giao diện dashboard */
			.market-update-block {
				border-radius: 12px;
				box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
				transition: transform 0.2s ease, box-shadow 0.2s ease;
			}
			.market-update-block:hover {
				transform: translateY(-3px);
				box-shadow: 0 8px 20px rgba(0, 0, 0, 0.10);
			}
			.market-update-left h3 {
				font-weight: 700;
			}
			.panel.panel-default {
				border-radius: 12px;
				border: none;
				box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
			}
			.panel-heading {
				border-radius: 12px 12px 0 0;
				background: linear-gradient(135deg, #4e73df, #1cc88a);
				color: #fff !important;
				border: none;
				padding: 12px 15px;
			}
			.panel-heading .panel-title {
				color: #fff;
				font-weight: 600;
				font-size: 14px;
			}
			.panel-body {
				background: #ffffff;
				border-radius: 0 0 12px 12px;
			}
		</style>
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

		{{-- Charts rows --}}
		<div class="row" style="margin-top: 20px;">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-line-chart"></i> Thống kê hồ sơ theo tháng (12 tháng gần nhất)</h3>
					</div>
					<div class="panel-body">
						<canvas id="hososByMonthChart" height="130"></canvas>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-calendar"></i> Lịch hẹn 7 ngày gần nhất</h3>
					</div>
					<div class="panel-body">
						<canvas id="appointmentsByDayChart" height="130"></canvas>
					</div>
				</div>
			</div>
		</div>

		<div class="row" style="margin-top: 15px;">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-pie-chart"></i> Tỷ lệ hồ sơ theo trạng thái</h3>
					</div>
					<div class="panel-body">
						<canvas id="hososByStatusChart" height="200"></canvas>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-line-chart"></i> Doanh thu 7 ngày gần nhất</h3>
					</div>
					<div class="panel-body">
						<canvas id="revenueByDayChart" height="200"></canvas>
					</div>
				</div>
			</div>
		</div>
		
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// ===== Hồ sơ theo tháng =====
		const monthlyLabels = @json($monthlyLabels);
		const monthlyValues = @json($monthlyValues);

		const hososByMonthCtx = document.getElementById('hososByMonthChart').getContext('2d');
		new Chart(hososByMonthCtx, {
			type: 'line',
			data: {
				labels: monthlyLabels,
				datasets: [{
					label: 'Số hồ sơ',
					data: monthlyValues,
					borderColor: 'rgba(75, 192, 192, 1)',
					backgroundColor: 'rgba(75, 192, 192, 0.2)',
					tension: 0.4,
					fill: true,
					pointRadius: 4,
					pointHoverRadius: 6,
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: { display: true },
					tooltip: {
						callbacks: {
							label: function(ctx) {
								return ' ' + ctx.parsed.y + ' hồ sơ';
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1
						}
					}
				}
			}
		});

		// ===== Hồ sơ theo trạng thái (pie chart) =====
		const hososByStatus = @json($hososByStatus);
		const statusLabels = hososByStatus.map(item => item.name || 'Không rõ');
		const statusValues = hososByStatus.map(item => parseInt(item.total));

		const statusColors = [
			'rgba(75, 192, 192, 0.8)',
			'rgba(54, 162, 235, 0.8)',
			'rgba(255, 206, 86, 0.8)',
			'rgba(255, 99, 132, 0.8)',
			'rgba(153, 102, 255, 0.8)',
			'rgba(255, 159, 64, 0.8)',
			'rgba(255, 99, 71, 0.8)',
			'rgba(0, 128, 128, 0.8)',
			'rgba(0, 204, 153, 0.8)',
			'rgba(128, 0, 255, 0.8)',
			'rgba(255, 140, 0, 0.8)',
		];

		const hososByStatusCtx = document.getElementById('hososByStatusChart').getContext('2d');
		new Chart(hososByStatusCtx, {
			type: 'doughnut',
			data: {
				labels: statusLabels,
				datasets: [{
					data: statusValues,
					backgroundColor: statusColors.slice(0, statusValues.length),
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: { position: 'bottom' },
					tooltip: {
						callbacks: {
							label: function(ctx) {
								const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
								const value = ctx.parsed;
								const percent = total ? ((value / total) * 100).toFixed(1) : 0;
								return ` ${ctx.label}: ${value} hồ sơ (${percent}%)`;
							}
						}
					}
				}
			}
		});

		// ===== Lịch hẹn 7 ngày gần nhất =====
		const appointmentLabels = @json($appointmentLabels);
		const appointmentValues = @json($appointmentValues);

		const appointmentsByDayCtx = document.getElementById('appointmentsByDayChart').getContext('2d');
		new Chart(appointmentsByDayCtx, {
			type: 'bar',
			data: {
				labels: appointmentLabels,
				datasets: [{
					label: 'Số lịch hẹn',
					data: appointmentValues,
					backgroundColor: 'rgba(54, 162, 235, 0.6)',
					borderColor: 'rgba(54, 162, 235, 1)',
					borderWidth: 1
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: { display: true },
					tooltip: {
						callbacks: {
							label: function(ctx) {
								return ' ' + ctx.parsed.y + ' lịch hẹn';
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1
						}
					}
				}
			}
		});

		// ===== Doanh thu 7 ngày gần nhất =====
		const revenueLabels = @json($revenueLabels);
		const revenueValues = @json($revenueValues);

		const revenueByDayCtx = document.getElementById('revenueByDayChart').getContext('2d');
		new Chart(revenueByDayCtx, {
			type: 'line',
			data: {
				labels: revenueLabels,
				datasets: [{
					label: 'Doanh thu (VNĐ)',
					data: revenueValues,
					borderColor: 'rgba(40, 167, 69, 1)',
					backgroundColor: 'rgba(40, 167, 69, 0.2)',
					tension: 0.4,
					fill: true,
					pointRadius: 3,
					pointHoverRadius: 5,
				}]
			},
			options: {
				responsive: true,
				plugins: {
					legend: { display: true },
					tooltip: {
						callbacks: {
							label: function(ctx) {
								return ' ' + new Intl.NumberFormat('vi-VN').format(ctx.parsed.y) + ' đ';
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							callback: function(value) {
								return new Intl.NumberFormat('vi-VN').format(value / 1000) + 'k';
							}
						}
					}
				}
			}
		});
	});
</script>
@endpush

