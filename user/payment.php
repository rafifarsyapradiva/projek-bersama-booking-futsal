<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Cek jika ada booking_id di parameter
$booking_id = $_GET['booking_id'] ?? null;
$booking = null;

if ($booking_id && isset($_SESSION['bookings'])) {
    foreach ($_SESSION['bookings'] as $b) {
        if ($b['id'] == $booking_id && $b['user_id'] == $_SESSION['user_id']) {
            $booking = $b;
            break;
        }
    }
}

// Jika tidak ada booking, redirect
if (!$booking) {
    header('Location: booking_history.php');
    exit;
}

// Ambil data user
$user = null;
if (isset($_SESSION['users'])) {
    foreach ($_SESSION['users'] as $u) {
        if ($u['id'] == $_SESSION['user_id']) {
            $user = $u;
            break;
        }
    }
}

$success = '';
$error = '';

// Handle form pembayaran
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    
    if (empty($payment_method)) {
        $error = 'Pilih metode pembayaran!';
    } else {
        // Update status booking menjadi "Menunggu Konfirmasi"
        foreach ($_SESSION['bookings'] as &$b) {
            if ($b['id'] == $booking_id) {
                $b['status'] = 'Menunggu Konfirmasi';
                $b['payment_method'] = $payment_method;
                $b['payment_date'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        $_SESSION['success_message'] = 'Pembayaran berhasil! Menunggu konfirmasi admin.';
        header('Location: booking_history.php');
        exit;
    }
}

// Data metode pembayaran
$payment_methods = [
    'bank_transfer' => [
        'name' => 'Transfer Bank',
        'icon' => 'university',
        'color' => 'blue',
        'banks' => [
            ['name' => 'BCA', 'account' => '1234567890', 'holder' => 'PT Reham Futsal'],
            ['name' => 'Mandiri', 'account' => '0987654321', 'holder' => 'PT Reham Futsal'],
            ['name' => 'BNI', 'account' => '5555666677', 'holder' => 'PT Reham Futsal']
        ]
    ],
    'ewallet' => [
        'name' => 'E-Wallet',
        'icon' => 'wallet',
        'color' => 'green',
        'options' => [
            ['name' => 'GoPay', 'number' => '081234567890'],
            ['name' => 'OVO', 'number' => '081234567890'],
            ['name' => 'DANA', 'number' => '081234567890'],
            ['name' => 'ShopeePay', 'number' => '081234567890']
        ]
    ],
    'qris' => [
        'name' => 'QRIS',
        'icon' => 'qrcode',
        'color' => 'purple',
        'description' => 'Scan QR Code untuk pembayaran'
    ],
    'cash' => [
        'name' => 'Bayar di Tempat',
        'icon' => 'money-bill-wave',
        'color' => 'orange',
        'description' => 'Bayar langsung di lapangan'
    ]
];
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
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        .slide-in { animation: slideIn 0.6s ease-out; }
        .payment-option {
            transition: all 0.3s;
        }
        .payment-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .payment-option.selected {
            border-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-green-600 to-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="booking_history.php" class="hover:scale-110 transition">
                        <i class="fas fa-arrow-left text-2xl"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold">Pembayaran</h1>
                        <p class="text-green-100 text-sm">Selesaikan pembayaran booking Anda</p>
                    </div>
                </div>
                <div class="bg-white/20 px-4 py-2 rounded-lg">
                    <i class="fas fa-clock mr-2"></i>
                    <span id="countdown">15:00</span>
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-6 fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Payment Methods -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-md p-6 fade-in">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-credit-card mr-2 text-blue-600"></i>Pilih Metode Pembayaran
                    </h3>

                    <form method="POST" id="paymentForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <?php foreach ($payment_methods as $key => $method): ?>
                            <div class="payment-option border-2 border-gray-300 rounded-xl p-4 cursor-pointer"
                                onclick="selectPayment('<?php echo $key; ?>')">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="bg-<?php echo $method['color']; ?>-100 w-12 h-12 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-<?php echo $method['icon']; ?> text-<?php echo $method['color']; ?>-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800"><?php echo $method['name']; ?></h4>
                                            <p class="text-xs text-gray-500">
                                                <?php if ($key == 'bank_transfer'): ?>
                                                    <?php echo count($method['banks']); ?> bank tersedia
                                                <?php elseif ($key == 'ewallet'): ?>
                                                    <?php echo count($method['options']); ?> opsi tersedia
                                                <?php else: ?>
                                                    <?php echo $method['description'] ?? 'Mudah & cepat'; ?>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <input type="radio" name="payment_method" value="<?php echo $key; ?>" class="w-5 h-5 text-green-600">
                                </div>
                                <div id="detail-<?php echo $key; ?>" class="hidden mt-3 pt-3 border-t border-gray-200">
                                    <!-- Detail akan muncul saat dipilih -->
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Detail Pembayaran -->
                        <div id="payment-details" class="hidden">
                            <!-- Bank Transfer Detail -->
                            <div id="bank-detail" class="hidden bg-blue-50 rounded-lg p-4 mb-4">
                                <h4 class="font-bold text-gray-800 mb-3">Pilih Bank:</h4>
                                <div class="space-y-3">
                                    <?php foreach ($payment_methods['bank_transfer']['banks'] as $bank): ?>
                                    <div class="bg-white p-3 rounded-lg border border-blue-200">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-bold text-gray-800"><?php echo $bank['name']; ?></p>
                                                <p class="text-sm text-gray-600">No. Rek: <?php echo $bank['account']; ?></p>
                                                <p class="text-xs text-gray-500">a.n. <?php echo $bank['holder']; ?></p>
                                            </div>
                                            <button type="button" onclick="copyText('<?php echo $bank['account']; ?>')" class="text-blue-600 hover:text-blue-700">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- E-Wallet Detail -->
                            <div id="ewallet-detail" class="hidden bg-green-50 rounded-lg p-4 mb-4">
                                <h4 class="font-bold text-gray-800 mb-3">Pilih E-Wallet:</h4>
                                <div class="grid grid-cols-2 gap-3">
                                    <?php foreach ($payment_methods['ewallet']['options'] as $wallet): ?>
                                    <div class="bg-white p-3 rounded-lg border border-green-200 text-center">
                                        <p class="font-bold text-gray-800 mb-1"><?php echo $wallet['name']; ?></p>
                                        <p class="text-sm text-gray-600"><?php echo $wallet['number']; ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- QRIS Detail -->
                            <div id="qris-detail" class="hidden bg-purple-50 rounded-lg p-4 mb-4 text-center">
                                <h4 class="font-bold text-gray-800 mb-3">Scan QR Code:</h4>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?php echo urlencode('REHAM-' . $booking['id']); ?>" 
                                    alt="QR Code" class="mx-auto mb-2 border-4 border-white shadow-lg rounded-lg">
                                <p class="text-sm text-gray-600">ID Transaksi: REHAM-<?php echo $booking['id']; ?></p>
                            </div>

                            <!-- Cash Detail -->
                            <div id="cash-detail" class="hidden bg-orange-50 rounded-lg p-4 mb-4">
                                <h4 class="font-bold text-gray-800 mb-3">Bayar di Tempat:</h4>
                                <div class="space-y-2 text-sm">
                                    <p><i class="fas fa-check text-green-600 mr-2"></i>Datang ke lokasi pada waktu booking</p>
                                    <p><i class="fas fa-check text-green-600 mr-2"></i>Bayar di kasir sebelum bermain</p>
                                    <p><i class="fas fa-check text-green-600 mr-2"></i>Tunjukkan kode booking Anda</p>
                                </div>
                            </div>

                            <!-- Upload Bukti -->
                            <div id="upload-section" class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="font-bold text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-upload mr-2 text-gray-600"></i>Upload Bukti Pembayaran (Opsional)
                                </h4>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-500 transition cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600 mb-2">Klik atau drag file ke sini</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG (Max 2MB)</p>
                                    <input type="file" accept="image/*" class="hidden" id="fileInput">
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" disabled
                            class="w-full bg-gray-400 text-white py-4 rounded-lg font-bold text-lg transition cursor-not-allowed">
                            <i class="fas fa-lock mr-2"></i>Pilih Metode Pembayaran
                        </button>
                    </form>
                </div>

                <!-- Info Penting -->
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg slide-in">
                    <h4 class="font-bold text-yellow-800 mb-2 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>Informasi Penting:
                    </h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Selesaikan pembayaran dalam 15 menit</li>
                        <li>• Upload bukti pembayaran untuk verifikasi lebih cepat</li>
                        <li>• Konfirmasi akan dikirim via email/WhatsApp</li>
                        <li>• Hubungi admin jika ada kendala</li>
                    </ul>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 fade-in sticky top-4">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-receipt mr-2 text-green-600"></i>Ringkasan Pesanan
                    </h3>

                    <div class="space-y-3 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Booking ID:</span>
                            <span class="font-semibold">#<?php echo $booking['id']; ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Lapangan:</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($booking['lapangan']); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tanggal:</span>
                            <span class="font-semibold"><?php echo date('d M Y', strtotime($booking['tanggal'])); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Waktu:</span>
                            <span class="font-semibold"><?php echo $booking['jam_mulai']; ?> - <?php echo $booking['jam_selesai']; ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Durasi:</span>
                            <span class="font-semibold"><?php echo $booking['durasi']; ?> jam</span>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Harga per Jam:</span>
                            <span>Rp <?php echo number_format($booking['harga_per_jam'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span>Rp <?php echo number_format($booking['harga_per_jam'] * $booking['durasi'], 0, ',', '.'); ?></span>
                        </div>
                        <?php if (isset($booking['diskon']) && $booking['diskon'] > 0): ?>
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon:</span>
                            <span>- Rp <?php echo number_format($booking['diskon'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex justify-between items-center mb-6">
                        <span class="text-lg font-bold text-gray-800">Total Bayar:</span>
                        <span class="text-2xl font-bold text-green-600">
                            Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?>
                        </span>
                    </div>

                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-green-700 mb-1">Pembayaran Aman & Terpercaya</p>
                        <div class="flex items-center justify-center space-x-2 text-green-600">
                            <i class="fas fa-shield-alt"></i>
                            <i class="fas fa-lock"></i>
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedMethod = null;

        function selectPayment(method) {
            // Remove previous selection
            document.querySelectorAll('.payment-option').forEach(el => {
                el.classList.remove('selected');
            });

            // Add selection
            event.currentTarget.classList.add('selected');
            document.querySelector(`input[value="${method}"]`).checked = true;
            selectedMethod = method;

            // Show payment details
            document.getElementById('payment-details').classList.remove('hidden');
            
            // Hide all details
            document.querySelectorAll('[id$="-detail"]').forEach(el => {
                el.classList.add('hidden');
            });

            // Show selected detail
            document.getElementById(`${method}-detail`).classList.remove('hidden');

            // Enable submit button
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitBtn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-blue-600', 'hover:from-green-700', 'hover:to-blue-700', 'transform', 'hover:scale-105');
            submitBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Konfirmasi Pembayaran';
        }

        function copyText(text) {
            navigator.clipboard.writeText(text);
            alert('Nomor rekening disalin: ' + text);
        }

        // Countdown timer
        let timeLeft = 900; // 15 minutes in seconds
        const countdownEl = document.getElementById('countdown');

        function updateCountdown() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                alert('Waktu pembayaran habis! Booking dibatalkan.');
                window.location.href = 'booking_history.php';
            } else if (timeLeft <= 60) {
                countdownEl.parentElement.classList.add('bg-red-500', 'animate-pulse');
            } else if (timeLeft <= 300) {
                countdownEl.parentElement.classList.add('bg-yellow-500');
            }
            
            timeLeft--;
        }

        setInterval(updateCountdown, 1000);

        // File upload handler
        document.querySelector('.border-dashed').addEventListener('click', () => {
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB!');
                    return;
                }
                alert('File berhasil dipilih: ' + file.name);
            }
        });
    </script>
</body>
</html>