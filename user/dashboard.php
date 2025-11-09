<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Silakan login terlebih dahulu!';
    header('Location: login.php');
    exit;
}

// Ambil data user
$userData = null;
foreach ($_SESSION['users'] as &$user) {
    if ($user['id'] == $_SESSION['user_id']) {
        $userData = &$user;
        break;
    }
}

if (!$userData) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Hitung statistik booking
$totalBooking = 0;
$activeBooking = 0;
$completedBooking = 0;
$cancelledBooking = 0;
$totalSpent = 0;
$recentBookings = [];

if (isset($_SESSION['bookings'])) {
    foreach ($_SESSION['bookings'] as $booking) {
        if ($booking['user_id'] == $_SESSION['user_id']) {
            $totalBooking++;
            $totalSpent += $booking['total_harga'];
            
            if ($booking['status'] == 'aktif') {
                $activeBooking++;
                $recentBookings[] = $booking;
            } elseif ($booking['status'] == 'selesai') {
                $completedBooking++;
            } elseif ($booking['status'] == 'dibatalkan') {
                $cancelledBooking++;
            }
        }
    }
}

// Sort recent bookings
usort($recentBookings, function($a, $b) {
    return strtotime($b['tanggal_booking']) - strtotime($a['tanggal_booking']);
});
$recentBookings = array_slice($recentBookings, 0, 3);

// Update total booking user
$userData['total_booking'] = $totalBooking;

// Success/Error messages
$success = $_SESSION['success_message'] ?? '';
$error = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../index.php');
    exit;
}

// Hitung member level
$memberLevel = 'Bronze';
$memberColor = 'orange';
if ($totalBooking >= 20) {
    $memberLevel = 'Platinum';
    $memberColor = 'purple';
} elseif ($totalBooking >= 10) {
    $memberLevel = 'Gold';
    $memberColor = 'yellow';
} elseif ($totalBooking >= 5) {
    $memberLevel = 'Silver';
    $memberColor = 'gray';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .slide-in { animation: slideIn 0.8s ease-out; }
        .stat-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="../index.php" class="flex items-center space-x-2">
                    <i class="fas fa-futbol text-green-600 text-3xl"></i>
                    <span class="text-2xl font-bold text-gray-800">Reham Futsal</span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="../index.php" class="text-gray-700 hover:text-green-600 transition">Beranda</a>
                    <a href="dashboard.php" class="text-green-600 font-semibold">Dashboard</a>
                    <a href="booking.php" class="text-gray-700 hover:text-green-600 transition">Booking</a>
                    <a href="booking_history.php" class="text-gray-700 hover:text-green-600 transition">Riwayat</a>
                    <a href="lapangan.php" class="text-gray-700 hover:text-green-600 transition">Lapangan</a>
                    <a href="jadwal.php" class="text-gray-700 hover:text-green-600 transition">Jadwal</a>
                    <a href="?logout=1" class="text-red-600 hover:text-red-700 transition" onclick="return confirm('Yakin ingin logout?')">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-6 py-8">
        <!-- Notifications -->
        <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg mb-6 fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <span class="font-semibold"><?php echo htmlspecialchars($success); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg mb-6 fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                <span class="font-semibold"><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-2xl p-8 mb-8 text-white fade-in shadow-xl">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-4">
                    <img src="<?php echo $userData['foto']; ?>" alt="Profile" class="w-20 h-20 rounded-full border-4 border-white shadow-lg">
                    <div>
                        <h1 class="text-4xl font-bold mb-2">Halo, <?php echo htmlspecialchars($userData['nama']); ?>! 👋</h1>
                        <p class="text-green-100 text-lg">Selamat datang di dashboard Anda</p>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="bg-white/20 backdrop-blur-lg px-3 py-1 rounded-full text-sm">
                                <i class="fas fa-calendar mr-1"></i>Member sejak <?php echo date('d M Y', strtotime($userData['member_since'])); ?>
                            </span>
                            <span class="bg-<?php echo $memberColor; ?>-400 px-3 py-1 rounded-full text-sm font-bold">
                                <i class="fas fa-crown mr-1"></i><?php echo $memberLevel; ?> Member
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="bg-white/20 backdrop-blur-lg rounded-xl p-4">
                        <div class="text-3xl font-bold"><?php echo $userData['points']; ?></div>
                        <div class="text-sm text-green-100">Poin Reward</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 stat-card slide-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <i class="fas fa-calendar-check text-blue-600 text-3xl"></i>
                    </div>
                    <span class="text-4xl font-bold text-blue-600"><?php echo $totalBooking; ?></span>
                </div>
                <h3 class="text-gray-600 font-semibold">Total Booking</h3>
                <p class="text-sm text-gray-500 mt-2">Semua waktu</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 stat-card slide-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-4 rounded-lg">
                        <i class="fas fa-clock text-green-600 text-3xl"></i>
                    </div>
                    <span class="text-4xl font-bold text-green-600"><?php echo $activeBooking; ?></span>
                </div>
                <h3 class="text-gray-600 font-semibold">Booking Aktif</h3>
                <p class="text-sm text-gray-500 mt-2">Sedang berjalan</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 stat-card slide-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-4 rounded-lg">
                        <i class="fas fa-check-circle text-purple-600 text-3xl"></i>
                    </div>
                    <span class="text-4xl font-bold text-purple-600"><?php echo $completedBooking; ?></span>
                </div>
                <h3 class="text-gray-600 font-semibold">Selesai</h3>
                <p class="text-sm text-gray-500 mt-2">Booking completed</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 stat-card slide-in">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-4 rounded-lg">
                        <i class="fas fa-money-bill-wave text-orange-600 text-3xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-orange-600">Rp <?php echo number_format($totalSpent, 0, ',', '.'); ?></div>
                    </div>
                </div>
                <h3 class="text-gray-600 font-semibold">Total Pengeluaran</h3>
                <p class="text-sm text-gray-500 mt-2">Semua waktu</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg p-8 fade-in">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-user-circle text-green-600 mr-2"></i>Profil Saya
                        </h2>
                        <button onclick="alert('Fitur edit profil coming soon!')" class="text-green-600 hover:text-green-700 font-semibold">
                            <i class="fas fa-edit mr-1"></i>Edit Profil
                        </button>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-start border-b pb-4">
                                <div class="bg-green-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-user text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Nama Lengkap</p>
                                    <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($userData['nama']); ?></p>
                                </div>
                            </div>

                            <div class="flex items-start border-b pb-4">
                                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email</p>
                                    <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($userData['email']); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-start border-b pb-4">
                                <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-phone text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Nomor Telepon</p>
                                    <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($userData['telepon']); ?></p>
                                </div>
                            </div>

                            <div class="flex items-start border-b pb-4">
                                <div class="bg-red-100 p-3 rounded-lg mr-4">
                                    <i class="fas fa-map-marker-alt text-red-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Alamat</p>
                                    <p class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($userData['alamat']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <?php if (!empty($recentBookings)): ?>
                <div class="bg-white rounded-xl shadow-lg p-8 mt-8 fade-in">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-history text-blue-600 mr-2"></i>Booking Terbaru
                    </h2>
                    <div class="space-y-4">
                        <?php foreach ($recentBookings as $booking): ?>
                        <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-green-400 transition">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-gray-800"><?php echo $booking['nama_lapangan']; ?></h4>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                                <div><i class="fas fa-calendar text-blue-600 mr-2"></i><?php echo date('d M Y', strtotime($booking['tanggal'])); ?></div>
                                <div><i class="fas fa-clock text-green-600 mr-2"></i><?php echo $booking['jam_mulai']; ?> - <?php echo $booking['jam_selesai']; ?></div>
                            </div>
                            <div class="mt-2 text-sm font-bold text-green-600">
                                Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="booking_history.php" class="block text-center mt-4 text-green-600 font-semibold hover:text-green-700">
                        Lihat Semua Riwayat <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Quick Actions & Promo -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-lg p-6 fade-in">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>Aksi Cepat
                    </h3>
                    <div class="space-y-3">
                        <a href="booking.php" class="block bg-gradient-to-r from-green-600 to-blue-600 text-white p-4 rounded-lg hover:from-green-700 hover:to-blue-700 transition text-center font-semibold">
                            <i class="fas fa-calendar-plus mr-2"></i>Booking Baru
                        </a>
                        <a href="booking_history.php" class="block bg-gradient-to-r from-purple-600 to-pink-600 text-white p-4 rounded-lg hover:from-purple-700 hover:to-pink-700 transition text-center font-semibold">
                            <i class="fas fa-history mr-2"></i>Lihat Riwayat
                        </a>
                        <a href="lapangan.php" class="block bg-gradient-to-r from-orange-600 to-red-600 text-white p-4 rounded-lg hover:from-orange-700 hover:to-red-700 transition text-center font-semibold">
                            <i class="fas fa-list mr-2"></i>Daftar Lapangan
                        </a>
                        <a href="jadwal.php" class="block bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-4 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition text-center font-semibold">
                            <i class="fas fa-calendar-alt mr-2"></i>Lihat Jadwal
                        </a>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl shadow-lg p-6 text-white">
                    <i class="fas fa-gift text-5xl mb-3"></i>
                    <h3 class="text-2xl font-bold mb-2">Promo Spesial!</h3>
                    <p class="text-sm mb-4">Booking 5 kali dapat diskon 20% untuk booking berikutnya</p>
                    <div class="bg-white/20 backdrop-blur-lg rounded-lg p-3 mb-4">
                        <div class="text-xs mb-1">Progress Anda:</div>
                        <div class="bg-white/30 rounded-full h-3 mb-1">
                            <div class="bg-white rounded-full h-3" style="width: <?php echo min(($totalBooking / 5) * 100, 100); ?>%"></div>
                        </div>
                        <div class="text-xs"><?php echo $totalBooking; ?> / 5 Booking</div>
                    </div>
                    <button onclick="alert('Lihat semua promo!')" class="bg-white text-orange-600 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition w-full">
                        Pelajari Lebih Lanjut
                    </button>
                </div>

                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                    <i class="fas fa-star text-5xl mb-3"></i>
                    <h3 class="text-xl font-bold mb-2">Member <?php echo $memberLevel; ?></h3>
                    <p class="text-sm mb-4">Tingkatkan level untuk benefit lebih banyak!</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Booking prioritas</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Diskon khusus member</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>Poin reward 2x lipat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>