<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Inisialisasi settings
if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [
        'site_name' => 'Reham Futsal',
        'site_tagline' => 'Lapangan Futsal Terbaik di Semarang',
        'email' => 'info@rehamfutsal.com',
        'phone' => '+62 812-3456-7890',
        'address' => 'Jl. Ulin Utara 2 No. 320, Semarang, Jawa Tengah',
        'open_time' => '06:00',
        'close_time' => '22:00',
        'booking_advance_days' => 7,
        'min_booking_hours' => 1,
        'max_booking_hours' => 8,
        'currency' => 'IDR',
        'timezone' => 'Asia/Jakarta',
        'allow_online_payment' => true,
        'require_verification' => false,
        'maintenance_mode' => false
    ];
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_general'])) {
        $_SESSION['settings']['site_name'] = trim($_POST['site_name']);
        $_SESSION['settings']['site_tagline'] = trim($_POST['site_tagline']);
        $_SESSION['settings']['email'] = trim($_POST['email']);
        $_SESSION['settings']['phone'] = trim($_POST['phone']);
        $_SESSION['settings']['address'] = trim($_POST['address']);
        $success = 'Pengaturan umum berhasil diperbarui!';
        
    } elseif (isset($_POST['update_operational'])) {
        $_SESSION['settings']['open_time'] = $_POST['open_time'];
        $_SESSION['settings']['close_time'] = $_POST['close_time'];
        $_SESSION['settings']['booking_advance_days'] = intval($_POST['booking_advance_days']);
        $_SESSION['settings']['min_booking_hours'] = intval($_POST['min_booking_hours']);
        $_SESSION['settings']['max_booking_hours'] = intval($_POST['max_booking_hours']);
        $success = 'Pengaturan operasional berhasil diperbarui!';
        
    } elseif (isset($_POST['update_system'])) {
        $_SESSION['settings']['currency'] = $_POST['currency'];
        $_SESSION['settings']['timezone'] = $_POST['timezone'];
        $_SESSION['settings']['allow_online_payment'] = isset($_POST['allow_online_payment']);
        $_SESSION['settings']['require_verification'] = isset($_POST['require_verification']);
        $_SESSION['settings']['maintenance_mode'] = isset($_POST['maintenance_mode']);
        $success = 'Pengaturan sistem berhasil diperbarui!';
        
    } elseif (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Get current admin
        $current_admin = null;
        foreach ($_SESSION['admins'] as &$admin) {
            if ($admin['id'] == $_SESSION['admin_id']) {
                $current_admin = &$admin;
                break;
            }
        }
        
        if (!password_verify($old_password, $current_admin['password'])) {
            $error = 'Password lama tidak sesuai!';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password baru minimal 6 karakter!';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Konfirmasi password tidak cocok!';
        } else {
            $current_admin['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            $success = 'Password berhasil diubah!';
        }
    }
}

$settings = $_SESSION['settings'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
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
            <a href="keuangan.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-chart-line w-5"></i>
                <span>Keuangan</span>
            </a>
            <a href="promo.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-tags w-5"></i>
                <span>Promo</span>
            </a>
            <a href="settings.php" class="flex items-center space-x-3 bg-white/20 text-white px-4 py-3 rounded-lg mb-2">
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
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Pengaturan Sistem</h1>
            <p class="text-gray-600">Kelola konfigurasi dan preferensi sistem</p>
        </div>

        <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6 fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-xl"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-6 fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow-md mb-6 fade-in">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-4 px-6">
                    <button onclick="switchTab('general')" class="tab-btn py-4 px-4 border-b-2 border-red-600 text-red-600 font-semibold">
                        <i class="fas fa-info-circle mr-2"></i>Umum
                    </button>
                    <button onclick="switchTab('operational')" class="tab-btn py-4 px-4 border-b-2 border-transparent text-gray-600 hover:text-red-600 hover:border-red-600 transition">
                        <i class="fas fa-clock mr-2"></i>Operasional
                    </button>
                    <button onclick="switchTab('system')" class="tab-btn py-4 px-4 border-b-2 border-transparent text-gray-600 hover:text-red-600 hover:border-red-600 transition">
                        <i class="fas fa-cogs mr-2"></i>Sistem
                    </button>
                    <button onclick="switchTab('security')" class="tab-btn py-4 px-4 border-b-2 border-transparent text-gray-600 hover:text-red-600 hover:border-red-600 transition">
                        <i class="fas fa-shield-alt mr-2"></i>Keamanan
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content: General -->
        <div id="general" class="tab-content active fade-in">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Pengaturan Umum</h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nama Situs</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tagline</label>
                        <input type="text" name="site_tagline" value="<?php echo htmlspecialchars($settings['site_tagline']); ?>" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($settings['email']); ?>" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Telepon</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($settings['phone']); ?>" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                        <textarea name="address" rows="3" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none"><?php echo htmlspecialchars($settings['address']); ?></textarea>
                    </div>
                    <button type="submit" name="update_general"
                        class="bg-gradient-to-r from-red-600 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-700 hover:to-pink-700 transition">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab Content: Operational -->
        <div id="operational" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Pengaturan Operasional</h3>
                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Jam Buka</label>
                            <input type="time" name="open_time" value="<?php echo $settings['open_time']; ?>" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Jam Tutup</label>
                            <input type="time" name="close_time" value="<?php echo $settings['close_time']; ?>" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Booking Maks (Hari)</label>
                            <input type="number" name="booking_advance_days" value="<?php echo $settings['booking_advance_days']; ?>" min="1" max="30" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                            <p class="text-xs text-gray-500 mt-1">Berapa hari ke depan user bisa booking</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Min Booking (Jam)</label>
                            <input type="number" name="min_booking_hours" value="<?php echo $settings['min_booking_hours']; ?>" min="1" max="12" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Max Booking (Jam)</label>
                            <input type="number" name="max_booking_hours" value="<?php echo $settings['max_booking_hours']; ?>" min="1" max="12" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                        </div>
                    </div>
                    <button type="submit" name="update_operational"
                        class="bg-gradient-to-r from-red-600 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-700 hover:to-pink-700 transition">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab Content: System -->
        <div id="system" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Pengaturan Sistem</h3>
                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Mata Uang</label>
                            <select name="currency" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                                <option value="IDR" <?php echo $settings['currency'] == 'IDR' ? 'selected' : ''; ?>>IDR (Rupiah)</option>
                                <option value="USD" <?php echo $settings['currency'] == 'USD' ? 'selected' : ''; ?>>USD (Dollar)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Timezone</label>
                            <select name="timezone" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                                <option value="Asia/Jakarta" <?php echo $settings['timezone'] == 'Asia/Jakarta' ? 'selected' : ''; ?>>Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar" <?php echo $settings['timezone'] == 'Asia/Makassar' ? 'selected' : ''; ?>>Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura" <?php echo $settings['timezone'] == 'Asia/Jayapura' ? 'selected' : ''; ?>>Asia/Jayapura (WIT)</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_online_payment" <?php echo $settings['allow_online_payment'] ? 'checked' : ''; ?>
                                class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="ml-3 text-gray-700">Izinkan Pembayaran Online</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="require_verification" <?php echo $settings['require_verification'] ? 'checked' : ''; ?>
                                class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="ml-3 text-gray-700">Wajib Verifikasi Email</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>
                                class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="ml-3 text-gray-700">Mode Maintenance</span>
                        </label>
                    </div>
                    <button type="submit" name="update_system"
                        class="bg-gradient-to-r from-red-600 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-700 hover:to-pink-700 transition">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <!-- Tab Content: Security -->
        <div id="security" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Keamanan</h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Password Lama</label>
                        <input type="password" name="old_password" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Password Baru</label>
                        <input type="password" name="new_password" required minlength="6"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                        <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required minlength="6"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <button type="submit" name="change_password"
                        class="bg-gradient-to-r from-red-600 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-700 hover:to-pink-700 transition">
                        <i class="fas fa-key mr-2"></i>Ubah Password
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-gray-200">
                    <h4 class="font-bold text-gray-800 mb-4">Informasi Sesi</h4>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Login sebagai:</span>
                            <span class="font-semibold"><?php echo $_SESSION['admin_username']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Waktu login:</span>
                            <span class="font-semibold"><?php echo date('d M Y H:i:s', strtotime($_SESSION['admin_login_time'])); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Session ID:</span>
                            <span class="font-mono text-xs"><?php echo substr(session_id(), 0, 20); ?>...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('border-red-600', 'text-red-600');
                btn.classList.add('border-transparent', 'text-gray-600');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Activate button
            event.currentTarget.classList.remove('border-transparent', 'text-gray-600');
            event.currentTarget.classList.add('border-red-600', 'text-red-600');
        }
    </script>
</body>

</html>

