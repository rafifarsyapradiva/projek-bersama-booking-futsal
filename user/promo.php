<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Silakan login terlebih dahulu!';
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// User vouchers (claimed vouchers)
if (!isset($_SESSION['user_vouchers'])) {
    $_SESSION['user_vouchers'] = [];
}

// Handle claim voucher
if (isset($_GET['claim']) && isset($_GET['code'])) {
    $kode = strtoupper($_GET['code']);
    $alreadyClaimed = false;
    
    // Cek apakah sudah claim
    foreach ($_SESSION['user_vouchers'] as $voucher) {
        if ($voucher['user_id'] == $_SESSION['user_id'] && $voucher['kode'] == $kode) {
            $alreadyClaimed = true;
            break;
        }
    }
    
    if ($alreadyClaimed) {
        $error = 'Anda sudah mengklaim voucher ini sebelumnya!';
    } else {
        // Find promo
        foreach ($_SESSION['promo'] as $promo) {
            if ($promo['kode'] == $kode && $promo['aktif']) {
                $_SESSION['user_vouchers'][] = [
                    'user_id' => $_SESSION['user_id'],
                    'kode' => $kode,
                    'nama' => $promo['nama'],
                    'diskon' => $promo['diskon'],
                    'min_booking' => $promo['min_booking'],
                    'berlaku_sampai' => $promo['berlaku_sampai'],
                    'claimed_date' => date('Y-m-d H:i:s'),
                    'used' => false
                ];
                $success = 'Voucher berhasil diklaim! Gunakan saat booking.';
                break;
            }
        }
    }
}

// Get user vouchers
$myVouchers = [];
foreach ($_SESSION['user_vouchers'] as $voucher) {
    if ($voucher['user_id'] == $_SESSION['user_id']) {
        $myVouchers[] = $voucher;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo & Voucher - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .bounce-animation { animation: bounce 2s infinite; }
        .promo-card {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .promo-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shine 3s infinite;
        }
        .promo-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-pink-50 via-purple-50 to-blue-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="../index.php" class="flex items-center space-x-2">
                    <i class="fas fa-futbol text-green-600 text-3xl"></i>
                    <span class="text-2xl font-bold text-gray-800">Reham Futsal</span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="dashboard.php" class="text-gray-700 hover:text-green-600 transition">Dashboard</a>
                    <a href="booking.php" class="text-gray-700 hover:text-green-600 transition">Booking</a>
                    <a href="promo.php" class="text-green-600 font-semibold">Promo</a>
                    <a href="member_card.php" class="text-gray-700 hover:text-green-600 transition">Member Card</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-6 py-12">
        <!-- Notifications -->
        <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-6 py-4 rounded-lg mb-6 fade-in max-w-4xl mx-auto">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <span class="font-semibold"><?php echo htmlspecialchars($success); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-6 py-4 rounded-lg mb-6 fade-in max-w-4xl mx-auto">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                <span class="font-semibold"><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Title -->
        <div class="text-center mb-12 fade-in">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-4 bounce-animation">
                <i class="fas fa-gift text-pink-500 mr-3"></i>Promo & Voucher
            </h1>
            <p class="text-xl text-gray-600">Dapatkan diskon dan penawaran spesial untuk booking Anda!</p>
        </div>

        <!-- Hero Promo Banner -->
        <div class="bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 rounded-3xl p-12 mb-12 text-white text-center fade-in shadow-2xl">
            <div class="max-w-3xl mx-auto">
                <i class="fas fa-fire text-7xl mb-6 bounce-animation"></i>
                <h2 class="text-4xl md:text-5xl font-bold mb-4">PROMO SPESIAL HARI INI!</h2>
                <p class="text-2xl mb-6">Hemat hingga <span class="text-yellow-300 font-bold text-5xl">20%</span></p>
                <p class="text-lg mb-8 opacity-90">Claim voucher sekarang dan gunakan saat booking!</p>
                <div class="flex justify-center gap-4 flex-wrap">
                    <a href="#available-promos" class="bg-white text-red-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-300 hover:text-red-700 transition shadow-lg">
                        <i class="fas fa-ticket mr-2"></i>Lihat Semua Promo
                    </a>
                    <a href="booking.php" class="bg-yellow-400 text-red-700 px-8 py-4 rounded-full font-bold text-lg hover:bg-yellow-300 transition shadow-lg">
                        <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- My Vouchers Section -->
        <?php if (!empty($myVouchers)): ?>
        <div class="mb-12 fade-in">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-wallet text-blue-600 mr-2"></i>Voucher Saya (<?php echo count($myVouchers); ?>)
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($myVouchers as $voucher): 
                    $isExpired = strtotime($voucher['berlaku_sampai']) < time();
                    $isUsed = $voucher['used'];
                ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden <?php echo ($isExpired || $isUsed) ? 'opacity-60' : ''; ?>">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-6 text-white">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-white/20 backdrop-blur-lg px-4 py-2 rounded-full">
                                <span class="font-bold text-2xl"><?php echo $voucher['diskon']; ?>%</span>
                            </div>
                            <?php if ($isUsed): ?>
                            <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-check mr-1"></i>TERPAKAI
                            </span>
                            <?php elseif ($isExpired): ?>
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-times mr-1"></i>EXPIRED
                            </span>
                            <?php else: ?>
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-check mr-1"></i>AKTIF
                            </span>
                            <?php endif; ?>
                        </div>
                        <h3 class="text-xl font-bold mb-2"><?php echo $voucher['nama']; ?></h3>
                        <div class="bg-white/20 backdrop-blur-lg rounded-lg p-3">
                            <p class="text-xs mb-1">Kode Voucher:</p>
                            <p class="font-mono font-bold text-2xl tracking-wider"><?php echo $voucher['kode']; ?></p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-2 mb-4 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-tag text-purple-600 mr-2"></i>
                                <span>Min. booking: Rp <?php echo number_format($voucher['min_booking'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar text-purple-600 mr-2"></i>
                                <span>Berlaku s/d: <?php echo date('d M Y', strtotime($voucher['berlaku_sampai'])); ?></span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-purple-600 mr-2"></i>
                                <span>Diklaim: <?php echo date('d M Y', strtotime($voucher['claimed_date'])); ?></span>
                            </div>
                        </div>
                        <?php if (!$isExpired && !$isUsed): ?>
                        <a href="booking.php" class="block text-center bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition">
                            <i class="fas fa-shopping-cart mr-2"></i>Gunakan Voucher
                        </a>
                        <?php else: ?>
                        <button disabled class="block w-full text-center bg-gray-300 text-gray-500 py-3 rounded-lg font-semibold cursor-not-allowed">
                            <i class="fas fa-ban mr-2"></i>Tidak Tersedia
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Available Promos Section -->
        <div id="available-promos" class="mb-12 fade-in">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">
                <i class="fas fa-tags text-red-600 mr-2"></i>Promo Tersedia
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($_SESSION['promo'] as $promo): 
                    if (!$promo['aktif']) continue;
                    
                    // Cek apakah sudah diklaim
                    $claimed = false;
                    foreach ($myVouchers as $voucher) {
                        if ($voucher['kode'] == $promo['kode']) {
                            $claimed = true;
                            break;
                        }
                    }
                ?>
                <div class="promo-card bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-br from-pink-500 to-purple-600 p-8 text-white text-center relative">
                        <div class="absolute top-4 right-4">
                            <?php if ($claimed): ?>
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-check mr-1"></i>DIKLAIM
                            </span>
                            <?php else: ?>
                            <span class="bg-yellow-400 text-purple-900 px-3 py-1 rounded-full text-xs font-bold">
                                <i class="fas fa-fire mr-1"></i>HOT
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="bg-white/20 backdrop-blur-lg w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-5xl font-bold"><?php echo $promo['diskon']; ?>%</span>
                        </div>
                        <h3 class="text-2xl font-bold mb-2"><?php echo $promo['nama']; ?></h3>
                        <div class="bg-white/20 backdrop-blur-lg rounded-lg p-4 mt-4">
                            <p class="text-sm mb-2">Kode Promo:</p>
                            <p class="font-mono font-bold text-3xl tracking-wider"><?php echo $promo['kode']; ?></p>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-3 mb-6 text-sm">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 text-lg mr-3 mt-1"></i>
                                <span class="text-gray-700">Diskon <span class="font-bold text-pink-600"><?php echo $promo['diskon']; ?>%</span> setiap booking</span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 text-lg mr-3 mt-1"></i>
                                <span class="text-gray-700">Min. transaksi <span class="font-bold">Rp <?php echo number_format($promo['min_booking'], 0, ',', '.'); ?></span></span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 text-lg mr-3 mt-1"></i>
                                <span class="text-gray-700">Max. diskon <span class="font-bold">Rp <?php echo number_format($promo['max_diskon'], 0, ',', '.'); ?></span></span>
                            </div>
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 text-lg mr-3 mt-1"></i>
                                <span class="text-gray-700">Berlaku s/d <span class="font-bold"><?php echo date('d M Y', strtotime($promo['berlaku_sampai'])); ?></span></span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <?php if (!$claimed): ?>
                            <a href="?claim=1&code=<?php echo $promo['kode']; ?>" 
                                onclick="return confirm('Klaim voucher <?php echo $promo['kode']; ?>?')"
                                class="block text-center bg-gradient-to-r from-pink-600 to-purple-600 text-white py-4 rounded-lg font-bold text-lg hover:from-pink-700 hover:to-purple-700 transition shadow-lg">
                                <i class="fas fa-gift mr-2"></i>Klaim Voucher
                            </a>
                            <?php else: ?>
                            <a href="booking.php"
                                class="block text-center bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-blue-700 transition shadow-lg">
                                <i class="fas fa-shopping-cart mr-2"></i>Gunakan Sekarang
                            </a>
                            <?php endif; ?>
                            
                            <button onclick="copyCode('<?php echo $promo['kode']; ?>')" 
                                class="w-full bg-gray-100 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-200 transition">
                                <i class="fas fa-copy mr-2"></i>Salin Kode
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- How to Use Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-12 fade-in">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">
                <i class="fas fa-question-circle text-blue-600 mr-2"></i>Cara Menggunakan Voucher
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-blue-600">1</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Klaim Voucher</h3>
                    <p class="text-sm text-gray-600">Pilih promo yang Anda inginkan dan klik tombol "Klaim Voucher"</p>
                </div>
                <div class="text-center">
                    <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-green-600">2</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Pilih Lapangan</h3>
                    <p class="text-sm text-gray-600">Kunjungi halaman booking dan pilih lapangan yang Anda inginkan</p>
                </div>
                <div class="text-center">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-purple-600">3</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Masukkan Kode</h3>
                    <p class="text-sm text-gray-600">Masukkan kode voucher di form booking saat checkout</p>
                </div>
                <div class="text-center">
                    <div class="bg-orange-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-3xl font-bold text-orange-600">4</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-2">Nikmati Diskon</h3>
                    <p class="text-sm text-gray-600">Diskon akan otomatis teraplikasi pada total pembayaran Anda</p>
                </div>
            </div>
        </div>

        <!-- Terms & Conditions -->
        <div class="bg-gradient-to-r from-gray-800 to-gray-900 rounded-2xl p-8 text-white fade-in">
            <h3 class="text-2xl font-bold mb-4">
                <i class="fas fa-file-contract mr-2"></i>Syarat & Ketentuan
            </h3>
            <ul class="space-y-2 text-sm text-gray-300">
                <li><i class="fas fa-check text-green-400 mr-2"></i>Voucher hanya berlaku untuk user yang sudah terdaftar</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Satu voucher hanya dapat digunakan sekali per transaksi</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Voucher tidak dapat digabungkan dengan promo lainnya</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Voucher tidak dapat diuangkan atau dipindahtangankan</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Berlaku untuk semua jenis lapangan kecuali disebutkan lain</li>
                <li><i class="fas fa-check text-green-400 mr-2"></i>Reham Futsal berhak mengubah atau membatalkan promo sewaktu-waktu</li>
            </ul>
        </div>
    </div>

    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Kode "' + code + '" berhasil di-copy!');
            });
        }
    </script>
</body>
</html>