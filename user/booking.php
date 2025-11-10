<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Silakan login terlebih dahulu untuk melakukan booking!';
    header('Location: login.php?redirect=booking.php');
    exit;
}

$success = '';
$error = '';
$errors = [];

// Pre-select lapangan dari URL
$selectedLapangan = $_GET['lapangan'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lapangan_id = $_POST['lapangan_id'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $jam_mulai = $_POST['jam_mulai'] ?? '';
    $jam_selesai = $_POST['jam_selesai'] ?? '';
    $catatan = trim($_POST['catatan'] ?? '');
    $kode_promo = trim($_POST['kode_promo'] ?? '');
    
    // Validasi
    if (empty($lapangan_id)) $errors[] = 'Pilih lapangan terlebih dahulu';
    if (empty($tanggal)) $errors[] = 'Tanggal harus diisi';
    if (empty($jam_mulai)) $errors[] = 'Jam mulai harus diisi';
    if (empty($jam_selesai)) $errors[] = 'Jam selesai harus diisi';
    
    // Validasi tanggal tidak boleh di masa lalu
    if ($tanggal < date('Y-m-d')) {
        $errors[] = 'Tanggal booking tidak boleh di masa lalu';
    }
    
    // Validasi jam
    if (empty($errors)) {
        $time1 = strtotime($jam_mulai);
        $time2 = strtotime($jam_selesai);
        
        if ($time2 <= $time1) {
            $errors[] = 'Jam selesai harus lebih besar dari jam mulai';
        }
        
        $durasi = ($time2 - $time1) / 3600;
        if ($durasi > 8) {
            $errors[] = 'Durasi maksimal 8 jam per booking';
        }
        if ($durasi < 1) {
            $errors[] = 'Durasi minimal 1 jam';
        }
        
        // Cek jam operasional
        if ($time1 < strtotime('06:00') || $time2 > strtotime('22:00')) {
            $errors[] = 'Jam operasional: 06:00 - 22:00 WIB';
        }
    }
    
    // Cek bentrok jadwal
    if (empty($errors) && isset($_SESSION['bookings'])) {
        foreach ($_SESSION['bookings'] as $booking) {
            if ($booking['lapangan_id'] == $lapangan_id && 
                $booking['tanggal'] == $tanggal && 
                $booking['status'] == 'aktif') {
                
                $existStart = strtotime($booking['jam_mulai']);
                $existEnd = strtotime($booking['jam_selesai']);
                $newStart = strtotime($jam_mulai);
                $newEnd = strtotime($jam_selesai);
                
                if (($newStart >= $existStart && $newStart < $existEnd) ||
                    ($newEnd > $existStart && $newEnd <= $existEnd) ||
                    ($newStart <= $existStart && $newEnd >= $existEnd)) {
                    $errors[] = 'Jadwal bentrok dengan booking lain. Pilih waktu yang berbeda.';
                    break;
                }
            }
        }
    }
    
    // Proses booking
    if (empty($errors)) {
        // Ambil data lapangan
        $harga_per_jam = 0;
        $nama_lapangan = '';
        $jenis_lapangan = '';
        foreach ($_SESSION['lapangan'] as $lap) {
            if ($lap['id'] == $lapangan_id) {
                $harga_per_jam = $lap['harga'];
                $nama_lapangan = $lap['nama'];
                $jenis_lapangan = $lap['jenis'];
                break;
            }
        }
        
        $total_harga = $durasi * $harga_per_jam;
        $diskon = 0;
        $diskon_nominal = 0;
        
        // Cek kode promo
        if (!empty($kode_promo) && isset($_SESSION['promo'])) {
            foreach ($_SESSION['promo'] as $promo) {
                if (strtoupper($promo['kode']) == strtoupper($kode_promo) && $promo['aktif']) {
                    if ($total_harga >= $promo['min_booking']) {
                        $diskon = $promo['diskon'];
                        $diskon_nominal = min(($total_harga * $diskon / 100), $promo['max_diskon']);
                        $total_harga -= $diskon_nominal;
                        break;
                    }
                }
            }
        }
        
        // Simpan booking
        if (!isset($_SESSION['bookings'])) {
            $_SESSION['bookings'] = [];
        }
        
        $bookingId = count($_SESSION['bookings']) + 1;
        $newBooking = [
            'id' => $bookingId,
            'user_id' => $_SESSION['user_id'],
            'lapangan_id' => $lapangan_id,
            'nama_lapangan' => $nama_lapangan,
            'jenis_lapangan' => $jenis_lapangan,
            'tanggal' => $tanggal,
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'durasi' => $durasi,
            'harga_per_jam' => $harga_per_jam,
            'subtotal' => $durasi * $harga_per_jam,
            'diskon' => $diskon_nominal,
            'total_harga' => $total_harga,
            'catatan' => $catatan,
            'status' => 'aktif',
            'tanggal_booking' => date('Y-m-d H:i:s'),
            'kode_promo' => $kode_promo
        ];
        
        $_SESSION['bookings'][] = $newBooking;
        
        // Update poin user
        foreach ($_SESSION['users'] as &$user) {
            if ($user['id'] == $_SESSION['user_id']) {
                $user['points'] += floor($total_harga / 10000); // 1 poin per 10rb
                $user['total_booking']++;
                break;
            }
        }
        
        $_SESSION['success_message'] = 'Booking berhasil! Kode booking: #' . str_pad($bookingId, 4, '0', STR_PAD_LEFT);
        header('Location: booking_history.php');
        exit;
    } else {
        $error = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Lapangan - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .slide-down { animation: slideDown 0.6s ease-out; }
        .fade-in { animation: fadeIn 0.8s ease-out; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
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
                    <a href="booking.php" class="text-green-600 font-semibold">Booking</a>
                    <a href="booking_history.php" class="text-gray-700 hover:text-green-600 transition">Riwayat</a>
                    <a href="lapangan.php" class="text-gray-700 hover:text-green-600 transition">Lapangan</a>
                    <a href="jadwal.php" class="text-gray-700 hover:text-green-600 transition">Jadwal</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-6 py-8">
        <div class="max-w-5xl mx-auto">
            <!-- Title -->
            <div class="text-center mb-8 slide-down">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-3">
                    <i class="fas fa-calendar-plus text-green-600 mr-2"></i>Booking Lapangan
                </h1>
                <p class="text-gray-600 text-lg">Isi formulir dengan lengkap untuk melakukan booking</p>
            </div>

            <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg mb-6 slide-down">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle text-2xl mr-3 mt-0.5"></i>
                    <div>
                        <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                        <div><?php echo $error; ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 fade-in">
                <form method="POST" action="" id="bookingForm" class="space-y-8">
                    <!-- Pilih Lapangan -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-4 text-xl">
                            <i class="fas fa-futbol text-green-600 mr-2"></i>Pilih Lapangan <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($_SESSION['lapangan'] as $lap): ?>
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="lapangan_id" value="<?php echo $lap['id']; ?>" 
                                    <?php echo ($selectedLapangan == $lap['id']) ? 'checked' : ''; ?>
                                    required class="peer sr-only" onchange="updatePrice()">
                                <div class="border-3 border-gray-300 rounded-xl p-5 peer-checked:border-green-600 peer-checked:bg-green-50 hover:border-green-400 transition group-hover:shadow-lg">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="font-bold text-gray-800 text-lg"><?php echo $lap['nama']; ?></h3>
                                        <span class="bg-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-100 text-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-800 px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-<?php echo $lap['jenis'] == 'rumput' ? 'crown' : 'certificate'; ?> mr-1"></i><?php echo ucfirst($lap['jenis']); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3"><?php echo $lap['deskripsi']; ?></p>
                                    <div class="flex items-baseline">
                                        <span class="text-3xl font-bold text-green-600">Rp <?php echo number_format($lap['harga'], 0, ',', '.'); ?></span>
                                        <span class="text-sm text-gray-500 ml-2">/ jam</span>
                                    </div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-3 text-xl">
                            <i class="fas fa-calendar text-green-600 mr-2"></i>Tanggal Booking <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal" id="tanggal" required min="<?php echo date('Y-m-d'); ?>"
                            value="<?php echo $_POST['tanggal'] ?? date('Y-m-d'); ?>"
                            class="w-full px-5 py-4 border-2 border-gray-300 rounded-lg focus:border-green-600 focus:outline-none transition text-lg"
                            onchange="checkAvailability()">
                        <p class="text-sm text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Pilih tanggal yang Anda inginkan</p>
                    </div>

                    <!-- Jam -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-800 font-bold mb-3 text-xl">
                                <i class="fas fa-clock text-green-600 mr-2"></i>Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="jam_mulai" id="jam_mulai" required min="06:00" max="22:00"
                                value="<?php echo $_POST['jam_mulai'] ?? ''; ?>"
                                class="w-full px-5 py-4 border-2 border-gray-300 rounded-lg focus:border-green-600 focus:outline-none transition text-lg"
                                onchange="calculateDuration()">
                            <p class="text-sm text-gray-500 mt-2">Jam operasional: 06:00 - 22:00</p>
                        </div>
                        <div>
                            <label class="block text-gray-800 font-bold mb-3 text-xl">
                                <i class="fas fa-clock text-green-600 mr-2"></i>Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="jam_selesai" id="jam_selesai" required min="06:00" max="22:00"
                                value="<?php echo $_POST['jam_selesai'] ?? ''; ?>"
                                class="w-full px-5 py-4 border-2 border-gray-300 rounded-lg focus:border-green-600 focus:outline-none transition text-lg"
                                onchange="calculateDuration()">
                            <p class="text-sm text-gray-500 mt-2">Maksimal 8 jam per booking</p>
                        </div>
                    </div>

                    <!-- Kode Promo -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-3 text-xl">
                            <i class="fas fa-ticket text-green-600 mr-2"></i>Kode Promo (Opsional)
                        </label>
                        <div class="flex gap-3">
                            <input type="text" name="kode_promo" id="kode_promo" 
                                value="<?php echo $_POST['kode_promo'] ?? ''; ?>"
                                class="flex-1 px-5 py-4 border-2 border-gray-300 rounded-lg focus:border-green-600 focus:outline-none transition text-lg uppercase"
                                placeholder="Masukkan kode promo">
                            <button type="button" onclick="checkPromo()" class="bg-blue-600 text-white px-6 py-4 rounded-lg font-semibold hover:bg-blue-700 transition">
                                <i class="fas fa-check mr-2"></i>Cek Promo
                            </button>
                        </div>
                        <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800 font-semibold mb-2"><i class="fas fa-tag mr-2"></i>Promo Tersedia:</p>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <?php foreach ($_SESSION['promo'] as $promo): ?>
                                    <?php if ($promo['aktif']): ?>
                                    <li><code class="bg-yellow-100 px-2 py-1 rounded font-mono"><?php echo $promo['kode']; ?></code> - <?php echo $promo['nama']; ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-3 text-xl">
                            <i class="fas fa-note-sticky text-green-600 mr-2"></i>Catatan (Opsional)
                        </label>
                        <textarea name="catatan" rows="4" 
                            class="w-full px-5 py-4 border-2 border-gray-300 rounded-lg focus:border-green-600 focus:outline-none transition"
                            placeholder="Tambahkan catatan khusus, misalnya: request rompi, bola ekstra, dll."><?php echo $_POST['catatan'] ?? ''; ?></textarea>
                    </div>

                    <!-- Summary -->
                    <div id="bookingSummary" class="bg-gradient-to-r from-blue-50 to-green-50 border-2 border-blue-200 rounded-xl p-6 hidden">
                        <h3 class="font-bold text-xl text-gray-800 mb-4">
                            <i class="fas fa-calculator text-blue-600 mr-2"></i>Ringkasan Booking
                        </h3>
                        <div class="space-y-3 text-gray-700">
                            <div class="flex justify-between">
                                <span>Durasi:</span>
                                <span class="font-bold" id="summary-durasi">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Harga per jam:</span>
                                <span class="font-bold" id="summary-harga">-</span>
                            </div>
                            <div class="flex justify-between border-t pt-3">
                                <span class="text-lg font-bold">Total Pembayaran:</span>
                                <span class="text-2xl font-bold text-green-600" id="summary-total">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                        <h4 class="font-bold text-blue-800 mb-4 flex items-center text-lg">
                            <i class="fas fa-info-circle mr-2 text-2xl"></i>Informasi Penting
                        </h4>
                        <ul class="space-y-3 text-sm text-blue-700">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                                <span>Pembayaran dilakukan saat kedatangan (cash/transfer)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                                <span>Konfirmasi booking akan tersimpan di riwayat Anda</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                                <span>Harap datang 10-15 menit sebelum waktu booking</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                                <span>Pembatalan dapat dilakukan maksimal 2 jam sebelum waktu booking</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                                <span>Dapatkan poin reward setiap booking untuk ditukar dengan diskon</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-green-600 to-blue-600 text-white py-5 rounded-lg font-bold text-xl hover:from-green-700 hover:to-blue-700 transition transform hover:scale-105 shadow-lg">
                            <i class="fas fa-check-circle mr-2"></i>Konfirmasi Booking
                        </button>
                        <a href="dashboard.php" 
                            class="flex-1 bg-gray-200 text-gray-700 py-5 rounded-lg font-bold text-xl hover:bg-gray-300 transition text-center">
                            <i class="fas fa-times-circle mr-2"></i>Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const lapanganData = <?php echo json_encode($_SESSION['lapangan']); ?>;
        
        function updatePrice() {
            calculateDuration();
        }
        
        function calculateDuration() {
            const jamMulai = document.getElementById('jam_mulai').value;
            const jamSelesai = document.getElementById('jam_selesai').value;
            const selectedLapangan = document.querySelector('input[name="lapangan_id"]:checked');
            
            if (jamMulai && jamSelesai && selectedLapangan) {
                const start = new Date('2000-01-01 ' + jamMulai);
                const end = new Date('2000-01-01 ' + jamSelesai);
                const diff = (end - start) / (1000 * 60 * 60);
                
                if (diff > 0 && diff <= 8) {
                    const lapanganId = parseInt(selectedLapangan.value);
                    const lapangan = lapanganData.find(l => l.id === lapanganId);
                    const hargaPerJam = lapangan.harga;
                    const total = diff * hargaPerJam;
                    
                    document.getElementById('summary-durasi').textContent = diff + ' jam';
                    document.getElementById('summary-harga').textContent = 'Rp ' + hargaPerJam.toLocaleString('id-ID');
                    document.getElementById('summary-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
                    document.getElementById('bookingSummary').classList.remove('hidden');
                } else {
                    document.getElementById('bookingSummary').classList.add('hidden');
                    if (diff > 8) {
                        alert('Durasi maksimal 8 jam per booking!');
                    }
                }
            }
        }
        
        function checkPromo() {
            const kodePromo = document.getElementById('kode_promo').value.toUpperCase();
            if (kodePromo) {
                alert('Kode promo "' + kodePromo + '" akan divalidasi saat konfirmasi booking.');
            } else {
                alert('Masukkan kode promo terlebih dahulu!');
            }
        }
        
        function checkAvailability() {
            // Bisa dikembangkan untuk cek realtime availability
            console.log('Checking availability...');
        }
        
        // Auto calculate on page load
        window.onload = function() {
            calculateDuration();
        };
    </script>
</body>
</html>