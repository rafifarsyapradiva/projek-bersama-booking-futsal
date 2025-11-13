<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Inisialisasi data dummy jika belum ada
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        [
            'id' => 1,
            'nama' => 'Ahmad Rizki',
            'email' => 'ahmad.rizki@email.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'telepon' => '081234567890',
            'alamat' => 'Jl. Pemuda No. 123, Semarang',
            'foto' => 'https://ui-avatars.com/api/?name=Ahmad+Rizki&background=10b981&color=fff&size=200',
            'member_since' => '2024-01-15',
            'total_booking' => 12,
            'points' => 1200,
            'status' => 'active'
        ],
        [
            'id' => 2,
            'nama' => 'Budi Santoso',
            'email' => 'budi.santoso@email.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'telepon' => '082345678901',
            'alamat' => 'Jl. Pandanaran No. 456, Semarang',
            'foto' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=3b82f6&color=fff&size=200',
            'member_since' => '2024-02-20',
            'total_booking' => 8,
            'points' => 800,
            'status' => 'active'
        ]
    ];
}

if (!isset($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [
        [
            'id' => 1,
            'user_id' => 1,
            'lapangan' => 'Lapangan Futsal 1',
            'tanggal' => date('Y-m-d', strtotime('+1 day')),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'durasi' => 2,
            'harga_per_jam' => 50000,
            'total_harga' => 100000,
            'status' => 'Dikonfirmasi',
            'payment_method' => 'bank_transfer',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 2,
            'user_id' => 1,
            'lapangan' => 'Lapangan Rumput 1',
            'tanggal' => date('Y-m-d'),
            'jam_mulai' => '16:00',
            'jam_selesai' => '18:00',
            'durasi' => 2,
            'harga_per_jam' => 100000,
            'total_harga' => 200000,
            'status' => 'Menunggu Konfirmasi',
            'payment_method' => 'ewallet',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 3,
            'user_id' => 2,
            'lapangan' => 'Lapangan Futsal 2',
            'tanggal' => date('Y-m-d', strtotime('+2 days')),
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'durasi' => 2,
            'harga_per_jam' => 50000,
            'total_harga' => 100000,
            'status' => 'Dikonfirmasi',
            'payment_method' => 'cash',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]
    ];
}

// Statistik
$total_users = count($_SESSION['users']);
$total_bookings = count($_SESSION['bookings']);
$total_revenue = array_sum(array_column($_SESSION['bookings'], 'total_harga'));
$pending_bookings = count(array_filter($_SESSION['bookings'], function($b) {
    return $b['status'] == 'Menunggu Konfirmasi';
}));

// Booking hari ini
$today_bookings = array_filter($_SESSION['bookings'], function($b) {
    return $b['tanggal'] == date('Y-m-d');
});

// User terbaru
$recent_users = array_slice(array_reverse($_SESSION['users']), 0, 5);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        .slide-in { animation: slideIn 0.6s ease-out; }
        .stat-card {
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-red-600 to-pink-600 text-white shadow-2xl z-50">
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
            <a href="dashboard.php" class="flex items-center space-x-3 bg-white/20 text-white px-4 py-3 rounded-lg mb-2">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="bookings.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-calendar-check w-5"></i>
                <span>Booking</span>
                <?php if ($pending_bookings > 0): ?>
                <span class="ml-auto bg-yellow-500 text-white text-xs px-2 py-1 rounded-full"><?php echo $pending_bookings; ?></span>
                <?php endif; ?>
            </a>
            <a href="users.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-users w-5"></i>
                <span>Member</span>
            </a>
            <a href="lapangan.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-layer-group w-5"></i>
                <span>Lapangan</span>
            </a>
            <a href="keuangan.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
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
            <div class="flex items-center space-x-3 mb-3">
                <img src="<?php echo $_SESSION['admin_foto']; ?>" alt="Admin" class="w-10 h-10 rounded-full border-2 border-white">
                <div class="flex-1">
                    <p class="font-semibold text-sm"><?php echo $_SESSION['admin_nama']; ?></p>
                    <p class="text-xs text-red-100"><?php echo $_SESSION['admin_role']; ?></p>
                </div>
            </div>
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
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                    <p class="text-gray-600">Selamat datang kembali, <?php echo $_SESSION['admin_nama']; ?>!</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500"><?php echo date('l, d F Y'); ?></p>
                    <p class="text-lg font-semibold text-gray-800" id="clock"></p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 w-14 h-14 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold"><?php echo $total_users; ?></span>
                </div>
                <h3 class="text-lg font-semibold mb-1">Total Member</h3>
                <p class="text-sm text-blue-100">Terdaftar aktif</p>
            </div>

            <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl p-6 shadow-lg fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 w-14 h-14 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold"><?php echo $total_bookings; ?></span>
                </div>
                <h3 class="text-lg font-semibold mb-1">Total Booking</h3>
                <p class="text-sm text-green-100">Semua waktu</p>
            </div>

            <div class="stat-card bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-xl p-6 shadow-lg fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 w-14 h-14 rounded-full flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-2xl"></i>
                    </div>
                    <span class="text-2xl font-bold">Rp <?php echo number_format($total_revenue / 1000, 0); ?>K</span>
                </div>
                <h3 class="text-lg font-semibold mb-1">Total Pendapatan</h3>
                <p class="text-sm text-purple-100">Bulan ini</p>
            </div>

            <div class="stat-card bg-gradient-to-br from-yellow-500 to-orange-500 text-white rounded-xl p-6 shadow-lg fade-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-white/20 w-14 h-14 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold"><?php echo $pending_bookings; ?></span>
                </div>
                <h3 class="text-lg font-semibold mb-1">Pending</h3>
                <p class="text-sm text-yellow-100">Menunggu konfirmasi</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Booking Hari Ini -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 fade-in">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-calendar-day text-blue-600 mr-2"></i>Booking Hari Ini
                    </h3>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                        <?php echo count($today_bookings); ?> Booking
                    </span>
                </div>

                <?php if (count($today_bookings) > 0): ?>
                <div class="space-y-3">
                    <?php foreach ($today_bookings as $booking): ?>
                    <?php
                    $user = array_values(array_filter($_SESSION['users'], function($u) use ($booking) {
                        return $u['id'] == $booking['user_id'];
                    }))[0] ?? null;
                    ?>
                    <div class="border-l-4 border-green-500 bg-green-50 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <img src="<?php echo $user['foto'] ?? ''; ?>" alt="" class="w-10 h-10 rounded-full">
                                <div>
                                    <p class="font-semibold text-gray-800"><?php echo $user['nama'] ?? 'Unknown'; ?></p>
                                    <p class="text-sm text-gray-600"><?php echo $booking['lapangan']; ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-green-600"><?php echo $booking['jam_mulai']; ?> - <?php echo $booking['jam_selesai']; ?></p>
                                <span class="inline-block px-2 py-1 bg-green-500 text-white text-xs rounded-full">
                                    <?php echo $booking['status']; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-calendar-times text-6xl mb-4 text-gray-300"></i>
                    <p>Tidak ada booking hari ini</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
                <h3 class="text-xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="bookings.php?action=new" class="block bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transition">
                        <i class="fas fa-plus-circle mr-2"></i>Booking Baru
                    </a>
                    <a href="users.php?action=add" class="block bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition">
                        <i class="fas fa-user-plus mr-2"></i>Tambah Member
                    </a>
                    <a href="lapangan.php" class="block bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-3 rounded-lg hover:from-purple-600 hover:to-purple-700 transition">
                        <i class="fas fa-layer-group mr-2"></i>Kelola Lapangan
                    </a>
                    <a href="keuangan.php" class="block bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transition">
                        <i class="fas fa-file-invoice-dollar mr-2"></i>Laporan Keuangan
                    </a>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-800 mb-3">Member Terbaru</h4>
                    <div class="space-y-2">
                        <?php foreach (array_slice($recent_users, 0, 3) as $user): ?>
                        <div class="flex items-center space-x-2 text-sm">
                            <img src="<?php echo $user['foto']; ?>" alt="" class="w-8 h-8 rounded-full">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-700"><?php echo $user['nama']; ?></p>
                                <p class="text-xs text-gray-500"><?php echo date('d M Y', strtotime($user['member_since'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-bar text-green-600 mr-2"></i>Statistik Booking
                </h3>
                <canvas id="bookingChart"></canvas>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-pie text-purple-600 mr-2"></i>Status Pembayaran
                </h3>
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
    </main>

    <script>
        // Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Booking Chart
        const bookingCtx = document.getElementById('bookingChart').getContext('2d');
        new Chart(bookingCtx, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Booking',
                    data: [12, 19, 15, 25, 22, 30, 28],
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Payment Chart
        const paymentCtx = document.getElementById('paymentChart').getContext('2d');
        new Chart(paymentCtx, {
            type: 'doughnut',
            data: {
                labels: ['Transfer Bank', 'E-Wallet', 'QRIS', 'Cash'],
                datasets: [{
                    data: [40, 30, 20, 10],
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(168, 85, 247)',
                        'rgb(251, 146, 60)'
                    ]
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
</body>
</html>