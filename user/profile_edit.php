<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
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
            'points' => 1200
        ]
    ];
}

$success = '';
$error = '';
$user = null;

// Ambil data user yang login
foreach ($_SESSION['users'] as &$u) {
    if ($u['id'] == $_SESSION['user_id']) {
        $user = &$u;
        break;
    }
}

// Handle form update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $nama = trim($_POST['nama'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        
        if (empty($nama) || empty($telepon) || empty($alamat)) {
            $error = 'Semua field harus diisi!';
        } elseif (!preg_match('/^[0-9]{10,13}$/', $telepon)) {
            $error = 'Format nomor telepon tidak valid (10-13 digit)!';
        } else {
            $user['nama'] = $nama;
            $user['telepon'] = $telepon;
            $user['alamat'] = $alamat;
            $_SESSION['user_nama'] = $nama;
            $success = 'Profil berhasil diperbarui!';
        }
    }
    
    // Handle ubah password
    elseif (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $error = 'Semua field password harus diisi!';
        } elseif (!password_verify($old_password, $user['password'])) {
            $error = 'Password lama tidak sesuai!';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password baru minimal 6 karakter!';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Konfirmasi password tidak cocok!';
        } else {
            $user['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            $success = 'Password berhasil diubah!';
        }
    }
    
    // Handle ganti avatar
    elseif (isset($_POST['change_avatar'])) {
        $avatar_color = $_POST['avatar_color'] ?? '10b981';
        $user['foto'] = 'https://ui-avatars.com/api/?name=' . urlencode($user['nama']) . '&background=' . $avatar_color . '&color=fff&size=200';
        $_SESSION['user_foto'] = $user['foto'];
        $success = 'Avatar berhasil diubah!';
    }
    
    // Handle delete account
    elseif (isset($_POST['delete_account'])) {
        $password = $_POST['confirm_delete_password'] ?? '';
        
        if (!password_verify($password, $user['password'])) {
            $error = 'Password salah! Akun tidak dapat dihapus.';
        } else {
            // Hapus user dari session
            foreach ($_SESSION['users'] as $key => $u) {
                if ($u['id'] == $_SESSION['user_id']) {
                    unset($_SESSION['users'][$key]);
                    break;
                }
            }
            
            // Logout
            session_destroy();
            header('Location: ../index.php?deleted=1');
            exit;
        }
    }
}

$avatar_colors = [
    '10b981' => 'Green',
    '3b82f6' => 'Blue',
    'a855f7' => 'Purple',
    'f59e0b' => 'Orange',
    'ef4444' => 'Red',
    'ec4899' => 'Pink',
    '6366f1' => 'Indigo',
    '14b8a6' => 'Teal'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        .password-strength {
            height: 4px;
            transition: all 0.3s;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-green-600 to-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="hover:scale-110 transition">
                        <i class="fas fa-arrow-left text-2xl"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold">Edit Profil</h1>
                        <p class="text-green-100 text-sm">Kelola informasi akun Anda</p>
                    </div>
                </div>
                <a href="dashboard.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times mr-2"></i>Tutup
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4 py-8 max-w-4xl">
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sidebar - Avatar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-md p-6 fade-in">
                    <div class="text-center">
                        <img src="<?php echo htmlspecialchars($user['foto']); ?>" alt="Avatar" class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-green-500">
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($user['nama']); ?></h3>
                        <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($user['email']); ?></p>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Member sejak</p>
                            <p class="text-sm font-semibold text-green-600">
                                <?php echo date('d M Y', strtotime($user['member_since'])); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Ganti Avatar -->
                    <form method="POST" class="mt-6">
                        <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-palette mr-2 text-purple-600"></i>Ganti Warna Avatar
                        </h4>
                        <div class="grid grid-cols-4 gap-2 mb-4">
                            <?php foreach ($avatar_colors as $color => $name): ?>
                            <button type="submit" name="change_avatar" value="submit" 
                                onclick="document.querySelector('input[name=avatar_color]').value='<?php echo $color; ?>'"
                                class="w-12 h-12 rounded-full hover:scale-110 transition border-2 border-gray-300 hover:border-<?php echo $name; ?>-500"
                                style="background-color: #<?php echo $color; ?>" title="<?php echo $name; ?>">
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="avatar_color" value="10b981">
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Update Profil -->
                <div class="bg-white rounded-xl shadow-md p-6 fade-in">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user-edit mr-2 text-blue-600"></i>Informasi Pribadi
                    </h3>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user text-gray-400 mr-2"></i>Nama Lengkap
                            </label>
                            <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-envelope text-gray-400 mr-2"></i>Email
                            </label>
                            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Email tidak dapat diubah</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-phone text-gray-400 mr-2"></i>Nomor Telepon
                            </label>
                            <input type="tel" name="telepon" value="<?php echo htmlspecialchars($user['telepon']); ?>" required
                                pattern="[0-9]{10,13}"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-600 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>Alamat
                            </label>
                            <textarea name="alamat" rows="3" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-600 focus:outline-none"><?php echo htmlspecialchars($user['alamat']); ?></textarea>
                        </div>
                        <button type="submit" name="update_profile"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-blue-800 transition">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>

                <!-- Ubah Password -->
                <div class="bg-white rounded-xl shadow-md p-6 fade-in">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-lock mr-2 text-purple-600"></i>Keamanan Akun
                    </h3>
                    <form method="POST" id="passwordForm" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Password Lama</label>
                            <div class="relative">
                                <input type="password" name="old_password" id="old_password" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none pr-12">
                                <button type="button" onclick="togglePassword('old_password', 'icon1')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                    <i class="fas fa-eye" id="icon1"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Password Baru</label>
                            <div class="relative">
                                <input type="password" name="new_password" id="new_password" required
                                    oninput="checkPasswordStrength()"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none pr-12">
                                <button type="button" onclick="togglePassword('new_password', 'icon2')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                    <i class="fas fa-eye" id="icon2"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <div class="password-strength bg-gray-200 rounded-full overflow-hidden">
                                    <div id="strength-bar" class="h-full transition-all"></div>
                                </div>
                                <p id="strength-text" class="text-xs mt-1"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password Baru</label>
                            <div class="relative">
                                <input type="password" name="confirm_password" id="confirm_password" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none pr-12">
                                <button type="button" onclick="togglePassword('confirm_password', 'icon3')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                    <i class="fas fa-eye" id="icon3"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" name="change_password"
                            class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-purple-800 transition">
                            <i class="fas fa-key mr-2"></i>Ubah Password
                        </button>
                    </form>
                </div>

                <!-- Zona Bahaya -->
                <div class="bg-white rounded-xl shadow-md p-6 border-2 border-red-200 fade-in">
                    <h3 class="text-xl font-bold text-red-600 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Zona Bahaya
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Setelah menghapus akun, semua data Anda akan hilang permanen dan tidak dapat dipulihkan.
                    </p>
                    <button onclick="confirmDelete()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        <i class="fas fa-trash mr-2"></i>Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Delete -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6 fade-in">
            <div class="text-center mb-6">
                <div class="bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Hapus Akun?</h3>
                <p class="text-gray-600">Tindakan ini tidak dapat dibatalkan. Semua data Anda akan hilang permanen.</p>
            </div>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Masukkan password untuk konfirmasi:</label>
                    <input type="password" name="confirm_delete_password" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-lg font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" name="delete_account"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-semibold transition">
                        Ya, Hapus Akun
                    </button>
                </div>
            </form>
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

        function checkPasswordStrength() {
            const password = document.getElementById('new_password').value;
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-green-600'];
            const texts = ['Sangat Lemah', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
            const widths = ['20%', '40%', '60%', '80%', '100%'];
            
            strengthBar.className = 'h-full transition-all ' + (colors[strength] || 'bg-gray-300');
            strengthBar.style.width = widths[strength] || '0%';
            strengthText.textContent = password ? texts[strength] || '' : '';
            strengthText.className = 'text-xs mt-1 ' + (password ? 'text-gray-600' : '');
        }

        function confirmDelete() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>