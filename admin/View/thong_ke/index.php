<style>
    .filter-section {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .filter-form {
        display: flex;
        gap: 15px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .form-group label {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
    }
    
    .form-group select,
    .form-group input {
        padding: 8px 12px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        font-size: 14px;
        min-width: 150px;
    }
    
    .btn-filter {
        padding: 8px 20px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.3s;
    }
    
    .btn-filter:hover {
        background: #0056b3;
    }
    
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid;
    }
    
    .stat-card.blue { border-left-color: #007bff; }
    .stat-card.green { border-left-color: #28a745; }
    .stat-card.orange { border-left-color: #ffc107; }
    .stat-card.purple { border-left-color: #6f42c1; }
    
    .stat-card-title {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 10px;
        text-transform: uppercase;
        font-weight: 600;
    }
    
    .stat-card-value {
        font-size: 32px;
        font-weight: 700;
        color: #333;
    }
    
    .chart-section {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .chart-header {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .chart-header h2 {
        font-size: 20px;
        color: #333;
        margin: 0;
    }
    
    .chart-container {
        position: relative;
        height: 400px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    
    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>

<div class="filter-section">
    <h2 style="margin-top: 0; margin-bottom: 20px; color: #333;">🔍 Lọc Thống Kê</h2>
    <form method="GET" action="?act=admin-thong-ke" class="filter-form">
        <input type="hidden" name="act" value="admin-thong-ke">
        <div class="form-group">
            <label for="nam">Năm:</label>
            <select name="nam" id="nam">
                <?php
                $currentYear = date('Y');
                for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                    $selected = ($i == $nam) ? 'selected' : '';
                    echo "<option value='$i' $selected>$i</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="thang">Tháng (tùy chọn):</label>
            <select name="thang" id="thang">
                <option value="">Tất cả các tháng</option>
                <?php
                $months = [
                    1 => 'Tháng 1', 2 => 'Tháng 2', 3 => 'Tháng 3', 4 => 'Tháng 4',
                    5 => 'Tháng 5', 6 => 'Tháng 6', 7 => 'Tháng 7', 8 => 'Tháng 8',
                    9 => 'Tháng 9', 10 => 'Tháng 10', 11 => 'Tháng 11', 12 => 'Tháng 12'
                ];
                foreach ($months as $num => $name) {
                    $selected = ($thang == $num) ? 'selected' : '';
                    echo "<option value='$num' $selected>$name</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn-filter">Lọc</button>
    </form>
</div>

<!-- Tổng quan thống kê -->
<div class="stats-overview">
    <div class="stat-card blue">
        <div class="stat-card-title">Tổng Doanh Thu</div>
        <div class="stat-card-value"><?= number_format($tongHop['tong_doanh_thu'] ?? 0, 0, ',', '.') ?> đ</div>
    </div>
    
    <div class="stat-card green">
        <div class="stat-card-title">Tổng Đăng Ký</div>
        <div class="stat-card-value"><?= number_format($tongHop['tong_dang_ky'] ?? 0) ?></div>
        <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">
            Đã xác nhận: <?= number_format($tongHop['tong_dang_ky_da_xac_nhan'] ?? 0) ?>
        </div>
    </div>
    
    <div class="stat-card orange">
        <div class="stat-card-title">Tổng Thanh Toán</div>
        <div class="stat-card-value"><?= number_format($tongHop['tong_thanh_toan'] ?? 0) ?></div>
    </div>
    
    <div class="stat-card purple">
        <div class="stat-card-title">Tổng Hoàn Tiền</div>
        <div class="stat-card-value"><?= number_format($tongHop['tong_hoan_tien'] ?? 0, 0, ',', '.') ?> đ</div>
        <div style="font-size: 12px; color: #6c757d; margin-top: 5px;">
            Số lượng: <?= number_format($tongHop['so_luong_hoan_tien'] ?? 0) ?>
        </div>
    </div>
</div>

<!-- Biểu đồ thống kê -->
<div class="chart-section">
    <div class="chart-header">
        <h2>📊 Biểu Đồ Thống Kê Theo Thời Gian</h2>
        <?php if ($thang && $nam): ?>
            <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 14px;">Tháng <?= $thang ?>/<?= $nam ?></p>
        <?php elseif ($nam): ?>
            <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 14px;">Năm <?= $nam ?></p>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($chartData['labels'])): ?>
        <div class="chart-container">
            <canvas id="statisticsChart"></canvas>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-state-icon">📊</div>
            <p>Không có dữ liệu thống kê cho khoảng thời gian đã chọn</p>
        </div>
    <?php endif; ?>
</div>

<?php if (!empty($chartData['labels'])): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('statisticsChart').getContext('2d');
    
    const chartData = {
        labels: <?= json_encode($chartData['labels']) ?>,
        datasets: [
            {
                label: 'Doanh Thu (VNĐ)',
                data: <?= json_encode($chartData['doanhThu']) ?>,
                borderColor: 'rgb(0, 123, 255)',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                yAxisID: 'y',
                fill: true
            },
            {
                label: 'Số Đăng Ký',
                data: <?= json_encode($chartData['dangKy']) ?>,
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                yAxisID: 'y1',
                fill: true
            },
            {
                label: 'Số Thanh Toán',
                data: <?= json_encode($chartData['thanhToan']) ?>,
                borderColor: 'rgb(255, 193, 7)',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                yAxisID: 'y1',
                fill: true
            }
        ]
    };
    
    const config = {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label === 'Doanh Thu (VNĐ)') {
                                label += new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                            } else {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Ngày'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Doanh Thu (VNĐ)'
                    },
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Số Lượng'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    };
    
    new Chart(ctx, config);
</script>
<?php endif; ?>

<!-- Thống kê theo khóa học -->
<?php if (!empty($theoKhoaHoc)): ?>
<div class="chart-section">
    <div class="chart-header">
        <h2>📚 Thống Kê Theo Khóa Học</h2>
    </div>
    <div class="table-responsive" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left;">Khóa Học</th>
                    <th style="padding: 12px; text-align: left;">Danh Mục</th>
                    <th style="padding: 12px; text-align: right;">Số Đăng Ký</th>
                    <th style="padding: 12px; text-align: right;">Đã Xác Nhận</th>
                    <th style="padding: 12px; text-align: right;">Doanh Thu</th>
                    <th style="padding: 12px; text-align: right;">Thanh Toán</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($theoKhoaHoc as $item): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;"><?= htmlspecialchars($item['ten_khoa_hoc']) ?></td>
                    <td style="padding: 12px;"><?= htmlspecialchars($item['ten_danh_muc'] ?? 'N/A') ?></td>
                    <td style="padding: 12px; text-align: right;"><?= number_format($item['so_dang_ky']) ?></td>
                    <td style="padding: 12px; text-align: right;"><?= number_format($item['so_da_xac_nhan']) ?></td>
                    <td style="padding: 12px; text-align: right; font-weight: 600; color: #28a745;">
                        <?= number_format($item['doanh_thu'], 0, ',', '.') ?> đ
                    </td>
                    <td style="padding: 12px; text-align: right;"><?= number_format($item['so_thanh_toan']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<!-- Thống kê theo phương thức thanh toán -->
<?php if (!empty($theoPhuongThuc)): ?>
<div class="chart-section">
    <div class="chart-header">
        <h2>💳 Thống Kê Theo Phương Thức Thanh Toán</h2>
    </div>
    <div class="table-responsive" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left;">Phương Thức</th>
                    <th style="padding: 12px; text-align: right;">Tổng Số Lượng</th>
                    <th style="padding: 12px; text-align: right;">Thành Công</th>
                    <th style="padding: 12px; text-align: right;">Thất Bại</th>
                    <th style="padding: 12px; text-align: right;">Tổng Tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($theoPhuongThuc as $item): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px; font-weight: 600;"><?= htmlspecialchars($item['phuong_thuc']) ?></td>
                    <td style="padding: 12px; text-align: right;"><?= number_format($item['so_luong']) ?></td>
                    <td style="padding: 12px; text-align: right; color: #28a745;">
                        <?= number_format($item['thanh_cong']) ?>
                    </td>
                    <td style="padding: 12px; text-align: right; color: #dc3545;">
                        <?= number_format($item['that_bai']) ?>
                    </td>
                    <td style="padding: 12px; text-align: right; font-weight: 600; color: #007bff;">
                        <?= number_format($item['tong_tien'], 0, ',', '.') ?> đ
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

