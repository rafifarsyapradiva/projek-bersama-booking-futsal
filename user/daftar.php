<?php
session_start();

$success = '';
$error = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    
    // Validasi
    if (empty($nama)) $errors[] = 'Nama lengkap harus diisi';
    if (empty($email)) $errors[] = 'Email harus diisi';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid';
    if (empty($password)) $errors[] = 'Password harus diisi';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter';
    if ($password !== $confirm_password) $errors[] = 'Password dan konfirmasi password tidak cocok';
    if (empty($telepon)) $errors[] = 'Nomor telepon harus diisi';
    if (empty($alamat)) $errors[] = 'Alamat harus diisi';
    
    // Cek email sudah terdaftar
    if (empty($errors) && isset($_SESSION['users'])) {
        foreach ($_SESSION['users'] as $user) {
            if (strtolower($user['email']) == strtolower($email)) {
                $errors[] = 'Email sudah terdaftar! Gunakan email lain atau <a href="login.php" class="underline font-bold">login di sini</a>';
                break;
            }
        }
    }
    
    // Jika tidak ada error, daftar user
    if (empty($errors)) {
        $newId = count($_SESSION['users']) + 1;
        $newUser = [
            'id' => $newId,
            'nama' => $nama,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'telepon' => $telepon,
            'alamat' => $alamat,
            'foto' => 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=10b981&color=fff&size=200',
            'member_since' => date('Y-m-d'),
            'total_booking' => 0,
            'points' => 0
        ];
        
        $_SESSION['users'][] = $newUser;
        
        // Set success message untuk halaman login
        $_SESSION['success_message'] = 'Pendaftaran berhasil! Silakan login dengan akun Anda.';
        header('Location: login.php');
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
    <title>Daftar Akun - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        .slide-up { animation: slideUp 0.6s ease-out; }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-400 via-pink-500 to-red-500 min-h-screen flex items-center justify-center p-4">
    <div class="container max-w-2xl mx-auto my-8">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden fade-in">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-8 text-center">
                <a href="../index.php" class="inline-block">
                    <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 hover:scale-110 transition">
                        <i class="fas fa-user-plus text-purple-600 text-4xl"></i>
                    </div>
                </a>
                <h1 class="text-3xl font-bold text-white mb-2">Daftar Akun Baru</h1>
                <p class="text-purple-100">Bergabung dengan Reham Futsal - Gratis!</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-4 rounded mb-6 slide-up">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mr-3 text-xl mt-0.5"></i>
                        <div>
                            <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                            <div class="text-sm"><?php echo $error; ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Benefits -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-lg p-4 mb-6">
                    <p class="text-sm font-semibold text-purple-800 mb-3">
                        <i class="fas fa-gift mr-2"></i>Keuntungan Menjadi Member:
                    </p>
                    <ul class="space-y-2 text-sm text-purple-700">
                        <li><i class="fas fa-check text-green-600 mr-2"></i>Booking lebih mudah dan cepat</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i>Dapatkan poin reward setiap booking</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i>Akses promo eksklusif member</li>
                        <li><i class="fas fa-check text-green-600 mr-2"></i>Notifikasi jadwal dan reminder</li>
                    </ul>
                </div>

                <form method="POST" action="" class="space-y-5" id="registerForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="slide-up">
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user text-purple-600 mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" id="nama" required 
                                value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition"
                                placeholder="Masukkan nama lengkap">
                        </div>

                        <div class="slide-up">
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-envelope text-purple-600 mr-2"></i>Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required 
                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition"
                                placeholder="nama@email.com">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="slide-up">
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-lock text-purple-600 mr-2"></i>Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition pr-12"
                                    placeholder="Minimal 6 karakter">
                                <button type="button" onclick="togglePassword('password', 'icon1')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                    <i class="fas fa-eye" id="icon1"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Minimal 6 karakter</p>
                        </div>

                        <div class="slide-up">
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-lock text-purple-600 mr-2"></i>Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="confirm_password" id="confirm_password" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition pr-12"
                                    placeholder="Ulangi password">
                                <button type="button" onclick="togglePassword('confirm_password', 'icon2')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                    <i class="fas fa-eye" id="icon2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="slide-up">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-phone text-purple-600 mr-2"></i>Nomor Telepon <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="telepon" id="telepon" required 
                            value="<?php echo htmlspecialchars($_POST['telepon'] ?? ''); ?>"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition"
                            placeholder="081234567890" pattern="[0-9]{10,13}">
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Format: 081234567890</p>
                    </div>

                    <div class="slide-up">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alamat" id="alamat" required rows="3"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition"
                            placeholder="Masukkan alamat lengkap Anda"><?php echo htmlspecialchars($_POST['alamat'] ?? ''); ?></textarea>
                    </div>

                    <div class="slide-up">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" required class="w-5 h-5 text-purple-600 border-gray-300 rounded mt-1 focus:ring-purple-500">
                            <span class="ml-3 text-sm text-gray-700">
                                Saya menyetujui <a href="#" class="text-purple-600 font-semibold hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-purple-600 font-semibold hover:underline">Kebijakan Privasi</a> Reham Futsal
                            </span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-4 rounded-lg font-bold text-lg hover:from-purple-700 hover:to-pink-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-user-check mr-2"></i>Daftar Sekarang
                    </button>
                </form>

                <div class="mt-6 text-center slide-up">
                    <p class="text-gray-600">Sudah punya akun? 
                        <a href="login.php" class="text-purple-600 hover:text-purple-700 font-semibold">Login di sini</a>
                    </p>
                </div>

                <div class="mt-4 pt-6 border-t border-gray-200 text-center">
                    <a href="../index.php" class="text-gray-500 hover:text-gray-700 text-sm inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const telepon = document.getElementById('telepon').value;
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password minimal 6 karakter!');
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
                return false;
            }
            
            if (!/^[0-9]{10,13}$/.test(telepon)) {
                e.preventDefault();
                alert('Format nomor telepon tidak valid! Gunakan 10-13 digit angka.');
                return false;
            }
        });

        // Real-time password match indicator
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('border-red-500');
                this.classList.remove('border-green-500');
            } else if (confirmPassword && password === confirmPassword) {
                this.classList.add('border-green-500');
                this.classList.remove('border-red-500');
            } else {
                this.classList.remove('border-red-500', 'border-green-500');
            }
        });
    </script>
</body>
</html>