<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm_booking'])) {
        $booking_id = $_POST['booking_id'];
        foreach ($_SESSION['bookings'] as &$booking) {
            if ($booking['id'] == $booking_id) {
                $booking['status'] = 'Dikonfirmasi';
                $success = 'Booking berhasil dikonfirmasi!';
                break;
            }
        }
    } elseif (isset($_POST['reject_booking'])) {
        $booking_id = $_POST['booking_id'];
        foreach ($_SESSION['bookings'] as &$booking) {
            if ($booking['id'] == $booking_id) {
                $booking['status'] = 'Ditolak';
                $success = 'Booking berhasil ditolak!';
                break;
            }
        }
    } elseif (isset($_POST['delete_booking'])) {
        $booking_id = $_POST['booking_id'];
        foreach ($_SESSION['bookings'] as $key => $booking) {
            if ($booking['id'] == $booking_id) {
                unset($_SESSION['bookings'][$key]);
                $_SESSION['bookings'] = array_values($_SESSION['bookings']);
                $success = 'Booking berhasil dihapus!';
                break;
            }
        }
    }
}

// Filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

$filtered_bookings = $_SESSION['bookings'];

// Filter by status
if ($filter != 'all') {
    $filtered_bookings = array_filter($filtered_bookings, function($b) use ($filter) {
        if ($filter == 'pending') return $b['status'] == 'Menunggu Konfirmasi';
        if ($filter == 'confirmed') return $b['status'] == 'Dikonfirmasi';
        if ($filter == 'rejected') return $b['status'] == 'Ditolak';
        if ($filter == 'completed') return $b['status'] == 'Selesai';
        return true;
    });
}

// Search
if (!empty($search)) {
    $filtered_bookings = array_filter($filtered_bookings, function($b) use ($search) {
        $user = array_values(array_filter($_SESSION['users'], function($u) use ($b) {
            return $u['id'] == $b['user_id'];
        }))[0] ?? null;
        
        return stripos($b['lapangan'], $search) !== false ||
               stripos($user['nama'] ?? '', $search) !== false ||
               stripos($b['id'], $search) !== false;
    });
}

// Sort by date
usort($filtered_bookings, function($a, $b) {
    return strtotime($b['tanggal']) - strtotime($a['tanggal']);
});

// Statistics
$total_bookings = count($_SESSION['bookings']);
$pending = count(array_filter($_SESSION['bookings'], fn($b) => $b['status'] == 'Menunggu Konfirmasi'));
$confirmed = count(array_filter($_SESSION['bookings'], fn($b) => $b['status'] == 'Dikonfirmasi'));
$rejected = count(array_filter($_SESSION['bookings'], fn($b) => $b['status'] == 'Ditolak'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Booking - Admin Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
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
            <a href="dashboard.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="bookings.php" class="flex items-center space-x-3 bg-white/20 text-white px-4 py-3 rounded-lg mb-2">
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
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Manajemen Booking</h1>
            <p class="text-gray-600">Kelola semua booking lapangan futsal</p>
        </div>

        <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6 fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-xl"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Booking</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_bookings; ?></p>
                    </div>
                    <i class="fas fa-calendar-alt text-blue-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Pending</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $pending; ?></p>
                    </div>
                    <i class="fas fa-clock text-yellow-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Dikonfirmasi</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $confirmed; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Ditolak</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $rejected; ?></p>
                    </div>
                    <i class="fas fa-times-circle text-red-500 text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 fade-in">
            <div class="flex flex-wrap gap-4 items-center justify-between">
                <div class="flex flex-wrap gap-2">
                    <a href="?filter=all" class="px-4 py-2 rounded-lg <?php echo $filter == 'all' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                        Semua
                    </a>
                    <a href="?filter=pending" class="px-4 py-2 rounded-lg <?php echo $filter == 'pending' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                        Pending
                    </a>
                    <a href="?filter=confirmed" class="px-4 py-2 rounded-lg <?php echo $filter == 'confirmed' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                        Dikonfirmasi
                    </a>
                    <a href="?filter=rejected" class="px-4 py-2 rounded-lg <?php echo $filter == 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                        Ditolak
                    </a>
                </div>
                <form method="GET" class="flex gap-2">
                    <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                        placeholder="Cari booking..." 
                        class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Booking List -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden fade-in">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Member</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Lapangan</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tanggal & Waktu</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Total</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Pembayaran</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (count($filtered_bookings) > 0): ?>
                            <?php foreach ($filtered_bookings as $booking): ?>
                            <?php
                            $user = array_values(array_filter($_SESSION['users'], function($u) use ($booking) {
                                return $u['id'] == $booking['user_id'];
                            }))[0] ?? null;
                            
                            $status_colors = [
                                'Menunggu Konfirmasi' => 'bg-yellow-100 text-yellow-800',
                                'Dikonfirmasi' => 'bg-green-100 text-green-800',
                                'Ditolak' => 'bg-red-100 text-red-800',
                                'Selesai' => 'bg-blue-100 text-blue-800'
                            ];
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-mono text-gray-600">#<?php echo $booking['id']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="<?php echo $user['foto'] ?? ''; ?>" alt="" class="w-10 h-10 rounded-full">
                                        <div>
                                            <p class="font-semibold text-gray-800"><?php echo $user['nama'] ?? 'Unknown'; ?></p>
                                            <p class="text-xs text-gray-500"><?php echo $user['telepon'] ?? ''; ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800"><?php echo $booking['lapangan']; ?></td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-800"><?php echo date('d M Y', strtotime($booking['tanggal'])); ?></p>
                                    <p class="text-xs text-gray-600"><?php echo $booking['jam_mulai']; ?> - <?php echo $booking['jam_selesai']; ?></p>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-green-600">
                                    Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        <?php 
                                        $methods = [
                                            'bank_transfer' => 'Transfer Bank',
                                            'ewallet' => 'E-Wallet',
                                            'qris' => 'QRIS',
                                            'cash' => 'Cash'
                                        ];
                                        echo $methods[$booking['payment_method']] ?? $booking['payment_method'];
                                        ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full <?php echo $status_colors[$booking['status']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center space-x-2">
                                        <?php if ($booking['status'] == 'Menunggu Konfirmasi'): ?>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="confirm_booking" 
                                                class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600 transition text-sm"
                                                onclick="return confirm('Konfirmasi booking ini?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="reject_booking" 
                                                class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition text-sm"
                                                onclick="return confirm('Tolak booking ini?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <button onclick="viewDetail(<?php echo htmlspecialchars(json_encode($booking)); ?>)" 
                                            class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition text-sm">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <button type="submit" name="delete_booking" 
                                                class="bg-gray-500 text-white px-3 py-1 rounded-lg hover:bg-gray-600 transition text-sm"
                                                onclick="return confirm('Hapus booking ini?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-6xl mb-4 text-gray-300"></i>
                                <p>Tidak ada booking ditemukan</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Detail -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Detail Booking</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>

    <script>
        function viewDetail(booking) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('modalContent');
            
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Booking ID</p>
                            <p class="font-bold">#${booking.id}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-bold">${booking.status}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Lapangan</p>
                            <p class="font-bold">${booking.lapangan}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal</p>
                            <p class="font-bold">${new Date(booking.tanggal).toLocaleDateString('id-ID')}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Waktu</p>
                            <p class="font-bold">${booking.jam_mulai} - ${booking.jam_selesai}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Durasi</p>
                            <p class="font-bold">${booking.durasi} jam</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Harga per Jam</p>
                            <p class="font-bold">Rp ${booking.harga_per_jam.toLocaleString('id-ID')}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Harga</p>
                            <p class="font-bold text-green-600">Rp ${booking.total_harga.toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>