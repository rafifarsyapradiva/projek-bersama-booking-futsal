<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Generate laporan keuangan dari data booking
$bookings = $_SESSION['bookings'] ?? [];

// Filter by date range
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Today

$filtered_bookings = array_filter($bookings, function($b) use ($start_date, $end_date) {
    return $b['tanggal'] >= $start_date && $b['tanggal'] <= $end_date;
});

// Calculate statistics
$total_pendapatan = array_sum(array_column($filtered_bookings, 'total_harga'));
$total_transaksi = count($filtered_bookings);
$pendapatan_dikonfirmasi = array_sum(array_map(function($b) {
    return $b['status'] == 'Dikonfirmasi' || $b['status'] == 'Selesai' ? $b['total_harga'] : 0;
}, $filtered_bookings));
$pendapatan_pending = array_sum(array_map(function($b) {
    return $b['status'] == 'Menunggu Konfirmasi' ? $b['total_harga'] : 0;
}, $filtered_bookings));

// Group by payment method
$payment_methods = [];
foreach ($filtered_bookings as $booking) {
    $method = $booking['payment_method'] ?? 'unknown';
    if (!isset($payment_methods[$method])) {
        $payment_methods[$method] = ['count' => 0, 'total' => 0];
    }
    $payment_methods[$method]['count']++;
    $payment_methods[$method]['total'] += $booking['total_harga'];
}

// Group by date for chart
$daily_revenue = [];
foreach ($filtered_bookings as $booking) {
    $date = $booking['tanggal'];
    if (!isset($daily_revenue[$date])) {
        $daily_revenue[$date] = 0;
    }
    if ($booking['status'] == 'Dikonfirmasi' || $booking['status'] == 'Selesai') {
        $daily_revenue[$date] += $booking['total_harga'];
    }
}
ksort($daily_revenue);

// Top lapangan by revenue
$lapangan_revenue = [];
foreach ($filtered_bookings as $booking) {
    if ($booking['status'] == 'Dikonfirmasi' || $booking['status'] == 'Selesai') {
        $lap = $booking['lapangan'];
        if (!isset($lapangan_revenue[$lap])) {
            $lapangan_revenue[$lap] = 0;
        }
        $lapangan_revenue[$lap] += $booking['total_harga'];
    }
}
arsort($lapangan_revenue);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Admin Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-red-600 to-pink-600 text-white shadow-2xl z-50 no-print">
        <div class="p-6 border-b border-red-500">
            <div class="flex items-center space-x-3">
                <div class="bg-white w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-futbol text-red-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-lg">Reham Futsal</h2>
                    <p class="text-xs text-red-100">Admin Panel</p>
                </div>
            </div>
        </div>

        <nav class="p-4">
            <a href="dashboard.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="bookings.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-calendar-check w-5"></i>
                <span>Booking</span>
            </a>
            <a href="users.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-users w-5"></i>
                <span>Member</span>
            </a>
            <a href="lapangan.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-layer-group w-5"></i>
                <span>Lapangan</span>
            </a>
            <a href="keuangan.php" class="flex items-center space-x-3 bg-white/20 text-white px-4 py-3 rounded-lg mb-2">
                <i class="fas fa-chart-line w-5"></i>
                <span>Keuangan</span>
            </a>
            <a href="promo.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-tags w-5"></i>
                <span>Promo</span>
            </a>
            <a href="settings.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-cog w-5"></i>
                <span>Settings</span>
            </a>
        </nav>

        <div class="absolute bottom-0 w-64 p-4 border-t border-red-500">
            <a href="logout.php" class="flex items-center justify-center space-x-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <!-- Header -->
        <div class="mb-8 fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Laporan Keuangan</h1>
                    <p class="text-gray-600">Statistik dan analisis keuangan</p>
                </div>
                <div class="flex gap-2 no-print">
                    <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                        <i class="fas fa-print mr-2"></i>Cetak Laporan
                    </button>
                    <button onclick="exportCSV()" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition shadow-lg">
                        <i class="fas fa-file-excel mr-2"></i>Export CSV
                    </button>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 fade-in no-print">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Dari Tanggal</label>
                    <input type="date" name="start_date" value="<?php echo $start_date; ?>" 
                        class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="<?php echo $end_date; ?>" 
                        class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="keuangan.php" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </form>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl shadow-md p-6 fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 w-14 h-14 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mb-1">Total Pendapatan</h3>
                <p class="text-3xl font-bold">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
                <p class="text-sm text-green-100 mt-2"><?php echo $total_transaksi; ?> transaksi</p>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl shadow-md p-6 fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 w-14 h-14 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mb-1">Dikonfirmasi</h3>
                <p class="text-3xl font-bold">Rp <?php echo number_format($pendapatan_dikonfirmasi, 0, ',', '.'); ?></p>
                <p class="text-sm text-blue-100 mt-2">Pendapatan terkonfirmasi</p>
            </div>

            <div class="bg-gradient-to-br from-yellow-500 to-orange-500 text-white rounded-xl shadow-md p-6 fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 w-14 h-14 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mb-1">Pending</h3>
                <p class="text-3xl font-bold">Rp <?php echo number_format($pendapatan_pending, 0, ',', '.'); ?></p>
                <p class="text-sm text-yellow-100 mt-2">Menunggu konfirmasi</p>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl shadow-md p-6 fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 w-14 h-14 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-bar text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mb-1">Rata-rata</h3>
                <p class="text-3xl font-bold">Rp <?php echo $total_transaksi > 0 ? number_format($total_pendapatan / $total_transaksi, 0, ',', '.') : 0; ?></p>
                <p class="text-sm text-purple-100 mt-2">Per transaksi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-md p-6 fade-in">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>Pendapatan Harian
                </h3>
                <canvas id="revenueChart"></canvas>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white rounded-xl shadow-md p-6 fade-in">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-credit-card text-blue-600 mr-2"></i>Metode Pembayaran
                </h3>
                <canvas id="paymentChart"></canvas>
            </div>
        </div>

        <!-- Top Lapangan -->
        <div class="bg-white rounded-xl shadow-md p-6 fade-in">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-trophy text-yellow-600 mr-2"></i>Top Lapangan by Revenue
            </h3>
            <div class="space-y-3">
                <?php 
                $rank = 1;
                foreach (array_slice($lapangan_revenue, 0, 5, true) as $lap => $revenue): 
                ?>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold">
                            #<?php echo $rank++; ?>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800"><?php echo htmlspecialchars($lap); ?></p>
                            <p class="text-sm text-gray-600">
                                <?php 
                                $count = count(array_filter($filtered_bookings, function($b) use ($lap) {
                                    return $b['lapangan'] == $lap && ($b['status'] == 'Dikonfirmasi' || $b['status'] == 'Selesai');
                                }));
                                echo $count . ' booking';
                                ?>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-green-600">Rp <?php echo number_format($revenue, 0, ',', '.'); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($daily_revenue)); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?php echo json_encode(array_values($daily_revenue)); ?>,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Payment Methods Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        const paymentMethods = <?php echo json_encode($payment_methods); ?>;
        const methodNames = {
            'bank_transfer': 'Transfer Bank',
            'ewallet': 'E-Wallet',
            'qris': 'QRIS',
            'cash': 'Cash'
        };
        
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(paymentMethods).map(k => methodNames[k] || k),
                datasets: [{
                    data: Object.values(paymentMethods).map(v => v.total),
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(168, 85, 247)',
                        'rgb(251, 146, 60)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        function exportCSV() {
            let csv = 'Tanggal,Lapangan,Durasi,Harga,Status,Metode Pembayaran\n';
            <?php foreach ($filtered_bookings as $b): ?>
            csv += '<?php echo $b["tanggal"]; ?>,<?php echo $b["lapangan"]; ?>,<?php echo $b["durasi"]; ?> jam,<?php echo $b["total_harga"]; ?>,<?php echo $b["status"]; ?>,<?php echo $b["payment_method"]; ?>\n';
            <?php endforeach; ?>
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'laporan-keuangan-<?php echo date("Y-m-d"); ?>.csv';
            a.click();
        }
    </script>
</body>
</html>