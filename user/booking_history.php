<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Silakan login terlebih dahulu!';
    header('Location: login.php');
    exit;
}

// Handle batalkan booking
if (isset($_GET['cancel']) && isset($_GET['id'])) {
    $bookingId = intval($_GET['id']);
    foreach ($_SESSION['bookings'] as &$booking) {
        if ($booking['id'] == $bookingId && $booking['user_id'] == $_SESSION['user_id']) {
            // Cek apakah masih bisa dibatalkan (minimal 2 jam sebelum)
            $bookingDateTime = strtotime($booking['tanggal'] . ' ' . $booking['jam_mulai']);
            $now = time();
            $diff = ($bookingDateTime - $now) / 3600;
            
            if ($diff >= 2) {
                $booking['status'] = 'dibatalkan';
                $_SESSION['success_message'] = 'Booking berhasil dibatalkan!';
            } else {
                $_SESSION['error_message'] = 'Pembatalan hanya bisa dilakukan minimal 2 jam sebelum waktu booking!';
            }
            break;
        }
    }
    header('Location: booking_history.php');
    exit;
}

// Ambil booking user
$userBookings = [];
if (isset($_SESSION['bookings'])) {
    foreach ($_SESSION['bookings'] as $booking) {
        if ($booking['user_id'] == $_SESSION['user_id']) {
            $userBookings[] = $booking;
        }
    }
}

// Urutkan dari yang terbaru
usort($userBookings, function($a, $b) {
    return strtotime($b['tanggal_booking']) - strtotime($a['tanggal_booking']);
});

// Filter
$filter = $_GET['filter'] ?? 'all';
if ($filter !== 'all') {
    $userBookings = array_filter($userBookings, function($booking) use ($filter) {
        return $booking['status'] == $filter;
    });
}

// Notifications
$success = $_SESSION['success_message'] ?? '';
$error = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .booking-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .booking-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15); }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 to-pink-50 min-h-screen">
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
                    <a href="dashboard.php" class="text-gray-700 hover:text-green-600 transition">Dashboard</a>
                    <a href="booking.php" class="text-gray-700 hover:text-green-600 transition">Booking</a>
                    <a href="booking_history.php" class="text-green-600 font-semibold">Riwayat</a>
                    <a href="lapangan.php" class="text-gray-700 hover:text-green-600 transition">Lapangan</a>
                    <a href="jadwal.php" class="text-gray-700 hover:text-green-600 transition">Jadwal</a>
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

        <!-- Title & Filter -->
        <div class="mb-8 fade-in">
            <div class="text-center mb-6">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-3">
                    <i class="fas fa-history text-purple-600 mr-2"></i>Riwayat Booking
                </h1>
                <p class="text-gray-600 text-lg">Kelola dan pantau semua riwayat booking Anda</p>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl shadow-lg p-4 flex flex-wrap gap-3 justify-center">
                <a href="?filter=all" class="px-6 py-3 rounded-lg font-semibold transition <?php echo $filter == 'all' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <i class="fas fa-list mr-2"></i>Semua (<?php echo count($userBookings); ?>)
                </a>
                <a href="?filter=aktif" class="px-6 py-3 rounded-lg font-semibold transition <?php echo $filter == 'aktif' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <i class="fas fa-clock mr-2"></i>Aktif
                </a>
                <a href="?filter=selesai" class="px-6 py-3 rounded-lg font-semibold transition <?php echo $filter == 'selesai' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <i class="fas fa-check-circle mr-2"></i>Selesai
                </a>
                <a href="?filter=dibatalkan" class="px-6 py-3 rounded-lg font-semibold transition <?php echo $filter == 'dibatalkan' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                    <i class="fas fa-times-circle mr-2"></i>Dibatalkan
                </a>
            </div>
        </div>

        <?php if (empty($userBookings)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center fade-in">
            <div class="mb-6">
                <i class="fas fa-calendar-times text-gray-300 text-9xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">
                <?php if ($filter == 'all'): ?>
                    Belum Ada Riwayat Booking
                <?php else: ?>
                    Tidak Ada Booking <?php echo ucfirst($filter); ?>
                <?php endif; ?>
            </h2>
            <p class="text-gray-600 mb-8 text-lg">
                <?php if ($filter == 'all'): ?>
                    Anda belum pernah melakukan booking. Ayo booking lapangan sekarang!
                <?php else: ?>
                    Filter <?php echo ucfirst($filter); ?> tidak memiliki data. Coba filter lain.
                <?php endif; ?>
            </p>
            <div class="flex justify-center gap-4">
                <a href="booking.php" class="inline-block bg-gradient-to-r from-green-600 to-blue-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:from-green-700 hover:to-blue-700 transition">
                    <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                </a>
                <?php if ($filter != 'all'): ?>
                <a href="booking_history.php" class="inline-block bg-gray-200 text-gray-700 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-300 transition">
                    <i class="fas fa-list mr-2"></i>Lihat Semua
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <!-- Booking List -->
        <div class="space-y-6">
            <?php foreach ($userBookings as $booking): ?>
            <?php
                $statusColor = 'gray';
                $statusIcon = 'clock';
                if ($booking['status'] == 'aktif') {
                    $statusColor = 'green';
                    $statusIcon = 'check-circle';
                } elseif ($booking['status'] == 'selesai') {
                    $statusColor = 'blue';
                    $statusIcon = 'check-double';
                } elseif ($booking['status'] == 'dibatalkan') {
                    $statusColor = 'red';
                    $statusIcon = 'times-circle';
                }
                
                // Cek apakah bisa dibatalkan
                $bookingDateTime = strtotime($booking['tanggal'] . ' ' . $booking['jam_mulai']);
                $now = time();
                $diff = ($bookingDateTime - $now) / 3600;
                $canCancel = ($booking['status'] == 'aktif' && $diff >= 2);
            ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden booking-card fade-in">
                <div class="md:flex">
                    <!-- Left Section -->
                    <div class="md:w-1/3 bg-gradient-to-br from-<?php echo $booking['jenis_lapangan'] == 'rumput' ? 'green' : 'blue'; ?>-500 to-<?php echo $booking['jenis_lapangan'] == 'rumput' ? 'green' : 'blue'; ?>-700 p-8 text-white flex flex-col justify-center items-center">
                        <i class="fas fa-futbol text-7xl mb-4 opacity-80"></i>
                        <h3 class="text-2xl font-bold mb-2 text-center"><?php echo $booking['nama_lapangan']; ?></h3>
                        <span class="bg-white/20 backdrop-blur-lg px-4 py-2 rounded-full text-sm font-semibold">
                            <?php echo ucfirst($booking['jenis_lapangan']); ?>
                        </span>
                        <div class="bg-white/20 rounded-lg p-4 mt-6 w-full text-center">
                            <p class="text-sm opacity-90 mb-1">Booking ID</p>
                            <p class="text-3xl font-bold">#<?php echo str_pad($booking['id'], 4, '0', STR_PAD_LEFT); ?></p>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="md:w-2/3 p-6">
                        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                            <span class="bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-800 px-5 py-2 rounded-full text-sm font-bold">
                                <i class="fas fa-<?php echo $statusIcon; ?> mr-2"></i><?php echo ucfirst($booking['status']); ?>
                            </span>
                            <span class="text-gray-500 text-sm">
                                <i class="fas fa-calendar mr-2"></i>Dibooking: <?php echo date('d M Y, H:i', strtotime($booking['tanggal_booking'])); ?>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-500 text-sm mb-2">
                                    <i class="fas fa-calendar-day text-blue-600 mr-2"></i>Tanggal Main
                                </p>
                                <p class="font-bold text-gray-800 text-lg"><?php echo date('d F Y', strtotime($booking['tanggal'])); ?></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-500 text-sm mb-2">
                                    <i class="fas fa-clock text-green-600 mr-2"></i>Waktu
                                </p>
                                <p class="font-bold text-gray-800 text-lg"><?php echo substr($booking['jam_mulai'], 0, 5); ?> - <?php echo substr($booking['jam_selesai'], 0, 5); ?> WIB</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500 text-xs mb-1">Durasi</p>
                                <p class="font-bold text-gray-800"><?php echo $booking['durasi']; ?> Jam</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500 text-xs mb-1">Harga/Jam</p>
                                <p class="font-bold text-gray-800">Rp <?php echo number_format($booking['harga_per_jam'], 0, ',', '.'); ?></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <p class="text-gray-500 text-xs mb-1">Subtotal</p>
                                <p class="font-bold text-gray-800">Rp <?php echo number_format($booking['subtotal'], 0, ',', '.'); ?></p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <p class="text-green-600 text-xs mb-1">Total Bayar</p>
                                <p class="font-bold text-green-600 text-lg">Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></p>
                            </div>
                        </div>

                        <?php if ($booking['diskon'] > 0): ?>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded mb-4">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-tag text-yellow-600 mr-2"></i>
                                <span class="font-semibold">Diskon:</span> Rp <?php echo number_format($booking['diskon'], 0, ',', '.'); ?>
                                <?php if (!empty($booking['kode_promo'])): ?>
                                    - Kode: <code class="bg-yellow-100 px-2 py-1 rounded"><?php echo $booking['kode_promo']; ?></code>
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($booking['catatan'])): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mb-4">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-sticky-note text-blue-600 mr-2"></i>
                                <span class="font-semibold">Catatan:</span> <?php echo htmlspecialchars($booking['catatan']); ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <div class="flex flex-wrap gap-3">
                            <button onclick="printBooking(<?php echo $booking['id']; ?>)" class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition font-semibold">
                                <i class="fas fa-print mr-2"></i>Cetak Bukti
                            </button>
                            <button onclick="shareBooking(<?php echo $booking['id']; ?>)" class="flex-1 bg-purple-600 text-white py-3 px-4 rounded-lg hover:bg-purple-700 transition font-semibold">
                                <i class="fas fa-share mr-2"></i>Share
                            </button>
                            <?php if ($canCancel): ?>
                            <a href="?cancel=1&id=<?php echo $booking['id']; ?>" 
                               onclick="return confirm('Yakin ingin membatalkan booking ini?')"
                               class="flex-1 bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition text-center font-semibold">
                                <i class="fas fa-times-circle mr-2"></i>Batalkan
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary -->
        <div class="mt-8 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl p-8 text-white text-center fade-in">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-4xl font-bold mb-2"><?php echo count($userBookings); ?></div>
                    <div class="opacity-90">Total Booking</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">
                        <?php 
                        $totalHours = 0;
                        foreach ($userBookings as $b) {
                            if ($b['status'] != 'dibatalkan') $totalHours += $b['durasi'];
                        }
                        echo $totalHours;
                        ?>
                    </div>
                    <div class="opacity-90">Total Jam Main</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2">
                        Rp <?php 
                        $totalSpent = 0;
                        foreach ($userBookings as $b) {
                            if ($b['status'] != 'dibatalkan') $totalSpent += $b['total_harga'];
                        }
                        echo number_format($totalSpent, 0, ',', '.');
                        ?>
                    </div>
                    <div class="opacity-90">Total Pengeluaran</div>
                </div>
            </div>
            <p class="mt-6 text-lg opacity-90">Terima kasih telah mempercayai Reham Futsal! 🎉</p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function printBooking(id) {
            alert('Fitur cetak bukti booking #' + id + ' akan segera tersedia!');
            // Implementasi print bisa ditambahkan di sini
        }
        
        function shareBooking(id) {
            const text = 'Saya booking lapangan di Reham Futsal! Booking ID: #' + String(id).padStart(4, '0');
            if (navigator.share) {
                navigator.share({
                    title: 'Booking Reham Futsal',
                    text: text
                });
            } else {
                alert(text);
            }
        }
    </script>
</body>
</html>