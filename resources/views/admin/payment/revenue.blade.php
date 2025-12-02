@extends('admin.layout')

@section('title', 'Báo cáo Doanh thu')

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .stat-card.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-card.info {
        background: linear-gradient(135deg, #3494E6 0%, #EC6EAD 100%);
    }
    .stat-card.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-card h3 {
        margin: 0;
        font-size: 32px;
        font-weight: bold;
    }
    .stat-card p {
        margin: 5px 0 0 0;
        opacity: 0.9;
    }
    .chart-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .chart-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
    }
</style>
@endpush

@section('content')
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <div class="row">
                            <div class="col-md-6">
                                <h3><i class="fa fa-line-chart"></i> Báo cáo Doanh thu</h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('admin.payment.revenue.export') }}" class="btn btn-success">
                                    <i class="fa fa-file-excel-o"></i> Xuất Excel
                                </a>
                            </div>
                        </div>
                    </header>
                    <div class="panel-body">
                        <!-- Thống kê tổng quan -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="stat-card">
                                    <p>Tổng doanh thu</p>
                                    <h3>{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card success">
                                    <p>Hôm nay</p>
                                    <h3>{{ number_format($todayRevenue, 0, ',', '.') }} đ</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card info">
                                    <p>Tháng này</p>
                                    <h3>{{ number_format($thisMonthRevenue, 0, ',', '.') }} đ</h3>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card warning">
                                    <p>Năm này</p>
                                    <h3>{{ number_format($thisYearRevenue, 0, ',', '.') }} đ</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Biểu đồ doanh thu theo ngày (30 ngày) -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart-container">
                                    <div class="chart-title">Doanh thu 30 ngày gần nhất</div>
                                    <canvas id="dailyChart" height="80"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Biểu đồ doanh thu theo tháng và theo loại -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="chart-container">
                                    <div class="chart-title">Doanh thu 12 tháng gần nhất</div>
                                    <canvas id="monthlyChart" height="80"></canvas>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="chart-container">
                                    <div class="chart-title">Doanh thu theo loại thanh toán</div>
                                    <canvas id="paymentTypeChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Top 10 hồ sơ -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="chart-container">
                                    <div class="chart-title">Top 10 hồ sơ có giá trị cao nhất</div>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Mã hồ sơ</th>
                                                    <th>Chủ hồ sơ</th>
                                                    <th>Người nộp</th>
                                                    <th>Tổng tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($topHoSos as $index => $hoSo)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $hoSo->maHSXL ?? '-' }}</td>
                                                    <td>{{ $hoSo->tenChuHoSo ?? '-' }}</td>
                                                    <td>{{ $hoSo->hoTen ?? '-' }}</td>
                                                    <td class="text-right"><strong>{{ number_format($hoSo->total, 0, ',', '.') }} đ</strong></td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Không có dữ liệu</td>
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
            </div>
        </div>
    </section>
</section>
<!--main content end-->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dữ liệu từ server
    const dailyData = @json($dailyData);
    const monthlyData = @json($monthlyData);
    const byPaymentType = @json($byPaymentType);

    // Biểu đồ doanh thu theo ngày
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => d.date),
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: dailyData.map(d => d.revenue),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                }
            }
        }
    });

    // Biểu đồ doanh thu theo tháng
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => d.month),
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: monthlyData.map(d => d.revenue),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                }
            }
        }
    });

    // Biểu đồ tròn - Doanh thu theo loại thanh toán
    const paymentTypeCtx = document.getElementById('paymentTypeChart').getContext('2d');
    new Chart(paymentTypeCtx, {
        type: 'doughnut',
        data: {
            labels: byPaymentType.map(d => d.loaiGD || 'Khác'),
            datasets: [{
                data: byPaymentType.map(d => parseFloat(d.total)),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = new Intl.NumberFormat('vi-VN').format(context.parsed);
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return label + ': ' + value + ' đ (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

