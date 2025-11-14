<?php
session_start();

// Redirect jika belum login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Redirect jika tidak ada data booking
if (!isset($_SESSION['temp_booking'])) {
    header('Location: booking.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$temp_booking = $_SESSION['temp_booking'];

// Cari data user
$user = null;
foreach ($_SESSION['users'] as $u) {
    if ($u['id'] === $user_id) {
        $user = $u;
        break;
    }
}

// Cari data lapangan
$lapangan = null;
foreach ($_SESSION['lapangan'] as $lap) {
    if ($lap['id'] === $temp_booking['lapangan_id']) {
        $lapangan = $lap;
        break;
    }
}

// Process payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode_pembayaran = $_POST['metode_pembayaran'] ?? '';
    
    if ($metode_pembayaran) {
        // Buat booking ID unik
        $booking_id = 'BK' . date('Ymd') . str_pad(count($_SESSION['bookings']) + 1, 4, '0', STR_PAD_LEFT);
        
        // Simpan booking
        $_SESSION['bookings'][] = [
            'id' => $booking_id,
            'user_id' => $user_id,
            'lapangan_id' => $temp_booking['lapangan_id'],
            'lapangan_nama' => $lapangan['nama'],
            'tanggal' => $temp_booking['tanggal'],
            'jam_mulai' => $temp_booking['jam_mulai'],
            'jam_selesai' => $temp_booking['jam_selesai'],
            'durasi' => $temp_booking['durasi'],
            'harga_per_jam' => $temp_booking['harga_per_jam'],
            'subtotal' => $temp_booking['subtotal'],
            'diskon_member' => $temp_booking['diskon_member'],
            'diskon_promo' => $temp_booking['diskon_promo'],
            'total_harga' => $temp_booking['total_harga'],
            'kode_promo' => $temp_booking['kode_promo'] ?? '',
            'metode_pembayaran' => $metode_pembayaran,
            'status' => 'confirmed',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Update total booking user untuk level member
        foreach ($_SESSION['users'] as &$u) {
            if ($u['id'] === $user_id) {
                $u['total_booking'] = ($u['total_booking'] ?? 0) + 1;
                break;
            }
        }
        
        // Tambah notifikasi
        $_SESSION['notifications'][] = [
            'id' => 'notif_' . time() . '_' . rand(1000, 9999),
            'user_id' => $user_id,
            'type' => 'info',
            'title' => 'Booking Berhasil!',
            'message' => "Booking {$booking_id} untuk {$lapangan['nama']} pada {$temp_booking['tanggal']} berhasil dikonfirmasi.",
            'read' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Hapus temp booking
        unset($_SESSION['temp_booking']);
        
        // Redirect ke booking history dengan success message
        $_SESSION['success_message'] = 'Pembayaran berhasil! Booking Anda telah dikonfirmasi.';
        header('Location: booking_history.php');
        exit();
    }
}

// Format tanggal Indonesia
function formatTanggalIndo($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    
    $timestamp = strtotime($tanggal);
    $hari_nama = $hari[date('w', $timestamp)];
    $tanggal_num = date('d', $timestamp);
    $bulan_nama = $bulan[date('n', $timestamp)];
    $tahun = date('Y', $timestamp);
    
    return "$hari_nama, $tanggal_num $bulan_nama $tahun";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .payment-card {
            transition: all 0.3s ease;
        }
        
        .payment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.2);
        }
        
        .payment-card.selected {
            border-color: #10b981;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(5, 150, 105, 0.05) 100%);
        }
        
        .bank-logo {
            width: 80px;
            height: 40px;
            object-fit: contain;
        }
        
        .qr-code-container {
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .price-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .copy-btn {
            transition: all 0.2s ease;
        }
        
        .copy-btn:active {
            transform: scale(0.95);
        }
        
        .file-input-label {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-input-label:hover {
            background-color: #f9fafb;
            border-color: #10b981;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <nav class="gradient-bg text-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-white hover:text-gray-200 transition">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Pembayaran</h1>
                        <p class="text-xs text-green-100">Pilih metode pembayaran</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($user['nama']); ?></p>
                        <p class="text-xs text-green-100">
                            <i class="fas fa-star mr-1"></i>
                            <?php 
                            $total_booking = $user['total_booking'] ?? 0;
                            if ($total_booking >= 20) {
                                echo 'Platinum';
                            } elseif ($total_booking >= 10) {
                                echo 'Gold';
                            } elseif ($total_booking >= 5) {
                                echo 'Silver';
                            } else {
                                echo 'Bronze';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Summary (Sticky on Desktop) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-receipt text-green-600 mr-2"></i>
                        Ringkasan Pesanan
                    </h2>
                    
                    <!-- Booking Details -->
                    <div class="space-y-3 mb-4 pb-4 border-b">
                        <div class="flex items-start">
                            <i class="fas fa-futbol text-gray-400 mt-1 mr-3"></i>
                            <div>
                                <p class="text-xs text-gray-500">Lapangan</p>
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($lapangan['nama']); ?></p>
                                <p class="text-xs text-gray-600"><?php echo htmlspecialchars($lapangan['jenis']); ?></p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <i class="fas fa-calendar text-gray-400 mt-1 mr-3"></i>
                            <div>
                                <p class="text-xs text-gray-500">Tanggal & Waktu</p>
                                <p class="font-semibold text-gray-800"><?php echo formatTanggalIndo($temp_booking['tanggal']); ?></p>
                                <p class="text-sm text-gray-600">
                                    <?php echo $temp_booking['jam_mulai']; ?> - <?php echo $temp_booking['jam_selesai']; ?>
                                    <span class="text-xs text-gray-500">(<?php echo $temp_booking['durasi']; ?> jam)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Price Breakdown -->
                    <div class="space-y-2 mb-4 pb-4 border-b">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Harga per jam</span>
                            <span class="font-medium">Rp <?php echo number_format($temp_booking['harga_per_jam'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Durasi (<?php echo $temp_booking['durasi']; ?> jam)</span>
                            <span class="font-medium">Rp <?php echo number_format($temp_booking['subtotal'], 0, ',', '.'); ?></span>
                        </div>
                        
                        <?php if ($temp_booking['diskon_member'] > 0): ?>
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon Member</span>
                            <span class="font-medium">-Rp <?php echo number_format($temp_booking['diskon_member'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($temp_booking['diskon_promo'] > 0): ?>
                        <div class="flex justify-between text-sm text-blue-600">
                            <span>Diskon Promo (<?php echo $temp_booking['kode_promo']; ?>)</span>
                            <span class="font-medium">-Rp <?php echo number_format($temp_booking['diskon_promo'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Total -->
                    <div class="price-badge text-white rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-green-100 mb-1">Total Pembayaran</p>
                                <p class="text-2xl font-bold">Rp <?php echo number_format($temp_booking['total_harga'], 0, ',', '.'); ?></p>
                            </div>
                            <i class="fas fa-check-circle text-3xl text-white opacity-50"></i>
                        </div>
                    </div>
                    
                    <!-- Important Notes -->
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-xs font-semibold text-yellow-800 mb-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Penting!
                        </p>
                        <ul class="text-xs text-yellow-700 space-y-1">
                            <li>• Selesaikan pembayaran dalam 24 jam</li>
                            <li>• Simpan bukti pembayaran</li>
                            <li>• Hubungi admin jika ada kendala</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="lg:col-span-2">
                <form method="POST" class="space-y-6" id="paymentForm">
                    <!-- Transfer Bank -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center mb-4">
                            <input type="radio" name="metode_pembayaran" id="transfer_bank" value="Transfer Bank" class="w-5 h-5 text-green-600 focus:ring-green-500" required>
                            <label for="transfer_bank" class="ml-3 flex items-center cursor-pointer flex-1">
                                <i class="fas fa-university text-2xl text-blue-600 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-800">Transfer Bank</p>
                                    <p class="text-xs text-gray-500">BCA, Mandiri, BNI</p>
                                </div>
                            </label>
                        </div>
                        
                        <div id="transfer_bank_details" class="hidden pl-8 mt-4 space-y-4">
                            <!-- BCA -->
                            <div class="payment-card border-2 border-gray-200 rounded-lg p-4 hover:border-blue-400">
                                <div class="flex items-center justify-between mb-3">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="bank-logo">
                                    <button type="button" onclick="copyToClipboard('8471526394')" class="copy-btn bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-xs font-medium hover:bg-blue-100">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">Nomor Rekening</p>
                                <p class="text-xl font-bold text-gray-800 mb-1">8471 5263 94</p>
                                <p class="text-sm text-gray-600">a.n. <span class="font-semibold">Reham Futsal</span></p>
                            </div>
                            
                            <!-- Mandiri -->
                            <div class="payment-card border-2 border-gray-200 rounded-lg p-4 hover:border-yellow-400">
                                <div class="flex items-center justify-between mb-3">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" alt="Mandiri" class="bank-logo">
                                    <button type="button" onclick="copyToClipboard('1370009284756')" class="copy-btn bg-yellow-50 text-yellow-600 px-3 py-1 rounded-lg text-xs font-medium hover:bg-yellow-100">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">Nomor Rekening</p>
                                <p class="text-xl font-bold text-gray-800 mb-1">1370 0092 8475 6</p>
                                <p class="text-sm text-gray-600">a.n. <span class="font-semibold">Reham Futsal</span></p>
                            </div>
                            
                            <!-- BNI -->
                            <div class="payment-card border-2 border-gray-200 rounded-lg p-4 hover:border-orange-400">
                                <div class="flex items-center justify-between mb-3">
                                    <img src="https://upload.wikimedia.org/wikipedia/id/5/55/BNI_logo.svg" alt="BNI" class="bank-logo">
                                    <button type="button" onclick="copyToClipboard('0928374651')" class="copy-btn bg-orange-50 text-orange-600 px-3 py-1 rounded-lg text-xs font-medium hover:bg-orange-100">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">Nomor Rekening</p>
                                <p class="text-xl font-bold text-gray-800 mb-1">0928 3746 51</p>
                                <p class="text-sm text-gray-600">a.n. <span class="font-semibold">Reham Futsal</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- E-Wallet -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center mb-4">
                            <input type="radio" name="metode_pembayaran" id="e_wallet" value="E-Wallet" class="w-5 h-5 text-green-600 focus:ring-green-500">
                            <label for="e_wallet" class="ml-3 flex items-center cursor-pointer flex-1">
                                <i class="fas fa-wallet text-2xl text-purple-600 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-800">E-Wallet</p>
                                    <p class="text-xs text-gray-500">OVO, GoPay, DANA, ShopeePay</p>
                                </div>
                            </label>
                        </div>
                        
                        <div id="e_wallet_details" class="hidden pl-8 mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- OVO -->
                            <div class="payment-card border-2 border-gray-200 rounded-lg p-4 hover:border-purple-400">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="bg-purple-600 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold">
                                            OVO
                                        </div>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('081234567890')" class="copy-btn bg-purple-50 text-purple-600 px-3 py-1 rounded-lg text-xs font-medium hover:bg-purple-100">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">Nomor</p>
                                <p class="text-lg font-bold text-gray-800">0812-3456-7890</p>
                            </div>
                            
                            <!-- GoPay -->
                            <div class="payment-card border-2 border-gray-200 rounded-lg p-4 hover:border-green-400">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="bg-green-600 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold text-xs">
                                            GoPay
                                        </div>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('081234567890')" class="copy-btn bg-green-50 text-green-600 px-3 py-1 rounded-lg text-xs font-medium hover:bg-green-100">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">Nomor</p>
                                <p class="text-lg font-bold text-gray-800">0812-3456-7890</p>
                            </div>
                            
                            <!-- DANA -->
                            <div class="payment-card border-2 border-gray-200 rounded-lg p-4 hover:border-blue-400">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="bg-blue-600 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold text-xs">
                                            DANA
                                        </div>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('081234567890')" class="copy-btn bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-xs font-medium hover:bg-blue-100">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">Nomor</p>
                                <p class="text-lg font-bold text-gray-800">0812-3456-7890</p>
                            </div>
                            
                            <!-- ShopeePay -->
                            <div class="payment-card border-2 border-gray-200 rounded-lg p-4 hover:border-orange-400">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="bg-orange-600 text-white w-10 h-10 rounded-lg flex items-center justify-center font-bold text-[10px]">
                                            SPay
                                        </div>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('081234567890')" class="copy-btn bg-orange-50 text-orange-600 px-3 py-1 rounded-lg text-xs font-medium hover:bg-orange-100">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </div>
                                <p class="text-sm text-gray-600 mb-1">Nomor</p>
                                <p class="text-lg font-bold text-gray-800">0812-3456-7890</p>
                            </div>
                        </div>
                    </div>

                    <!-- QRIS -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center mb-4">
                            <input type="radio" name="metode_pembayaran" id="qris" value="QRIS" class="w-5 h-5 text-green-600 focus:ring-green-500">
                            <label for="qris" class="ml-3 flex items-center cursor-pointer flex-1">
                                <i class="fas fa-qrcode text-2xl text-red-600 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-800">QRIS</p>
                                    <p class="text-xs text-gray-500">Scan QR Code untuk pembayaran</p>
                                </div>
                            </label>
                        </div>
                        
                        <div id="qris_details" class="hidden pl-8 mt-4">
                            <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-lg p-6 text-center">
                                <div class="qr-code-container inline-block bg-white p-4 rounded-lg shadow-md">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode('REHAM_FUTSAL_' . $temp_booking['total_harga']); ?>" 
                                         alt="QRIS" 
                                         class="w-48 h-48 mx-auto">
                                </div>
                                <p class="text-sm text-gray-600 mt-4">Scan QR Code dengan aplikasi pembayaran favorit Anda</p>
                                <div class="flex justify-center items-center space-x-3 mt-3">
                                    <i class="fab fa-cc-visa text-2xl text-gray-400"></i>
                                    <i class="fab fa-cc-mastercard text-2xl text-gray-400"></i>
                                    <i class="fas fa-mobile-alt text-2xl text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex items-center mb-4">
                            <input type="radio" name="metode_pembayaran" id="cash" value="Cash" class="w-5 h-5 text-green-600 focus:ring-green-500">
                            <label for="cash" class="ml-3 flex items-center cursor-pointer flex-1">
                                <i class="fas fa-money-bill-wave text-2xl text-green-600 mr-3"></i>
                                <div>
                                    <p class="font-semibold text-gray-800">Bayar di Tempat (Cash)</p>
                                    <p class="text-xs text-gray-500">Bayar langsung saat datang</p>
                                </div>
                            </label>
                        </div>
                        
                        <div id="cash_details" class="hidden pl-8 mt-4">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex items-start">
                                    <i class="fas fa-info-circle text-green-600 mt-1 mr-3"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-green-800 mb-2">Informasi Pembayaran Cash</p>
                                        <ul class="text-sm text-green-700 space-y-1">
                                            <li>• Bayar langsung di kasir Reham Futsal</li>
                                            <li>• Datang minimal 15 menit sebelum jam booking</li>
                                            <li>• Tunjukkan kode booking: <span class="font-bold">BK<?php echo date('Ymd'); ?></span></li>
                                            <li>• Siapkan uang pas untuk mempercepat transaksi</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Bukti Transfer (Optional) -->
                    <div class="bg-white rounded-xl shadow-lg p-6" id="upload_section" style="display: none;">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-upload text-blue-600 mr-2"></i>
                            Upload Bukti Transfer (Opsional)
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Upload bukti transfer untuk mempercepat proses verifikasi</p>
                        
                        <label for="bukti_transfer" class="file-input-label block border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600">
                                <span class="text-blue-600 font-semibold">Klik untuk upload</span> atau drag & drop
                            </p>
                            <p class="text-xs text-gray-500 mt-2">PNG, JPG, JPEG, PDF (Max. 2MB)</p>
                            <input type="file" id="bukti_transfer" name="bukti_transfer" accept="image/*,.pdf" class="hidden">
                        </label>
                        
                        <div id="file_preview" class="hidden mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-file-image text-2xl text-blue-600"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800" id="file_name"></p>
                                        <p class="text-xs text-gray-500" id="file_size"></p>
                                    </div>
                                </div>
                                <button type="button" onclick="removeFile()" class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-times-circle text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <button type="submit" class="w-full gradient-bg text-white py-4 rounded-lg font-semibold text-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center justify-center space-x-2">
                            <i class="fas fa-check-circle"></i>
                            <span>Konfirmasi Pembayaran</span>
                        </button>
                        
                        <p class="text-xs text-center text-gray-500 mt-3">
                            Dengan mengklik tombol di atas, Anda menyetujui 
                            <a href="#" class="text-green-600 hover:underline">Syarat & Ketentuan</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center space-x-6 mb-4">
                <a href="#" class="hover:text-green-400 transition"><i class="fab fa-facebook text-xl"></i></a>
                <a href="#" class="hover:text-green-400 transition"><i class="fab fa-instagram text-xl"></i></a>
                <a href="#" class="hover:text-green-400 transition"><i class="fab fa-twitter text-xl"></i></a>
                <a href="#" class="hover:text-green-400 transition"><i class="fab fa-whatsapp text-xl"></i></a>
            </div>
            <p class="text-sm text-gray-400">© 2025 Reham Futsal. All rights reserved.</p>
            <p class="text-xs text-gray-500 mt-2">Jl. Ulin Utara 2 No. 320, Semarang, Jawa Tengah 50263</p>
        </div>
    </footer>

    <script>
        // Toggle payment details based on selection
        document.querySelectorAll('input[name="metode_pembayaran"]').forEach(radio => {
            radio.addEventListener('change', function() {
                // Hide all details
                document.getElementById('transfer_bank_details').classList.add('hidden');
                document.getElementById('e_wallet_details').classList.add('hidden');
                document.getElementById('qris_details').classList.add('hidden');
                document.getElementById('cash_details').classList.add('hidden');
                
                // Show upload section for non-cash payments
                const uploadSection = document.getElementById('upload_section');
                if (this.value !== 'Cash') {
                    uploadSection.style.display = 'block';
                } else {
                    uploadSection.style.display = 'none';
                }
                
                // Show selected payment details
                if (this.id === 'transfer_bank') {
                    document.getElementById('transfer_bank_details').classList.remove('hidden');
                } else if (this.id === 'e_wallet') {
                    document.getElementById('e_wallet_details').classList.remove('hidden');
                } else if (this.id === 'qris') {
                    document.getElementById('qris_details').classList.remove('hidden');
                } else if (this.id === 'cash') {
                    document.getElementById('cash_details').classList.remove('hidden');
                }
            });
        });

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                // Show success notification
                const notification = document.createElement('div');
                notification.className = 'fixed top-24 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce';
                notification.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Nomor berhasil disalin!';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 2000);
            });
        }

        // File upload handling
        document.getElementById('bukti_transfer').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const filePreview = document.getElementById('file_preview');
                const fileName = document.getElementById('file_name');
                const fileSize = document.getElementById('file_size');
                
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                
                filePreview.classList.remove('hidden');
            }
        });

        function removeFile() {
            document.getElementById('bukti_transfer').value = '';
            document.getElementById('file_preview').classList.add('hidden');
        }

        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const selectedPayment = document.querySelector('input[name="metode_pembayaran"]:checked');
            
            if (!selectedPayment) {
                e.preventDefault();
                alert('Silakan pilih metode pembayaran terlebih dahulu!');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitBtn.disabled = true;
        });

        // Auto scroll to form on mobile
        if (window.innerWidth < 1024) {
            window.scrollTo({
                top: 300,
                behavior: 'smooth'
            });
        }
    </script>
</body>
</html>