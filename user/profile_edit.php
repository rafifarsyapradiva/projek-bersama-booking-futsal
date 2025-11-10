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

$success = '';
$error = '';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'update_profile') {
        $nama = trim($_POST['nama'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        
        // Validasi
        if (empty($nama)) $errors[] = 'Nama harus diisi';
        if (empty($telepon)) $errors[] = 'Telepon harus diisi';
        if (!preg_match('/^[0-9]{10,13}$/', $telepon)) $errors[] = 'Format telepon tidak valid';
        if (empty($alamat)) $errors[] = 'Alamat harus diisi';
        
        if (empty($errors)) {
            $userData['nama'] = $nama;
            $userData['telepon'] = $telepon;
            $userData['alamat'] = $alamat;
            
            // Update session
            $_SESSION['user_nama'] = $nama;
            
            $success = 'Profil berhasil diperbarui!';
        } else {
            $error = implode('<br>', $errors);
        }
    } 
    elseif ($action == 'change_password') {
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validasi
        if (!password_verify($old_password, $userData['password'])) {
            $errors[] = 'Password lama tidak sesuai';
        }
        if (strlen($new_password) < 6) {
            $errors[] = 'Password baru minimal 6 karakter';
        }
        if ($new_password !== $confirm_password) {
            $errors[] = 'Konfirmasi password tidak cocok';
        }
        
        if (empty($errors)) {
            $userData['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            $success = 'Password berhasil diubah!';
        } else {
            $error = implode('<br>', $errors);
        }
    }
    elseif ($action == 'change_avatar') {
        $avatar_style = $_POST['avatar_style'] ?? 'initials';
        $userData['foto'] = 'https://ui-avatars.com/api/?name=' . urlencode($userData['nama']) . '&background=' . substr(md5($avatar_style), 0, 6) . '&color=fff&size=200';
        $success = 'Avatar berhasil diubah!';
    }
}
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
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .avatar-option { transition: all 0.3s ease; }
        .avatar-option:hover { transform: scale(1.1); }
    </style>
</head>
<body class="bg-gradient-to-br from-purple-50 to-blue-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="../index.php" class="flex items-center space-x-2">
                    <i class="fas fa-futbol text-green-600 text-3xl"></i>
                    <span class="text-2xl font-bold text-gray-800">Reham Futsal</span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="dashboard.php" class="text-gray-700 hover:text-green-600 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
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
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-2xl mr-3 mt-0.5"></i>
                <div>
                    <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                    <div class="text-sm"><?php echo $error; ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Title -->
        <div class="text-center mb-8 fade-in">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-3">
                <i class="fas fa-user-edit text-purple-600 mr-2"></i>Edit Profil
            </h1>
            <p class="text-gray-600 text-lg">Kelola informasi dan keamanan akun Anda</p>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 fade-in sticky top-24">
                    <div class="text-center mb-6">
                        <div class="relative inline-block">
                            <img src="<?php echo $userData['foto']; ?>" alt="Profile" class="w-32 h-32 rounded-full border-4 border-purple-500 shadow-lg mx-auto mb-4">
                            <button onclick="document.getElementById('avatarModal').classList.remove('hidden')" class="absolute bottom-2 right-2 bg-purple-600 text-white w-10 h-10 rounded-full hover:bg-purple-700 transition shadow-lg">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($userData['nama']); ?></h3>
                        <p class="text-gray-500 text-sm mb-4"><?php echo htmlspecialchars($userData['email']); ?></p>
                        <div class="flex justify-center gap-2">
                            <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-full text-sm font-semibold">
                                <i class="fas fa-crown mr-1"></i>
                                <?php 
                                $level = 'Bronze';
                                if ($userData['total_booking'] >= 20) $level = 'Platinum';
                                elseif ($userData['total_booking'] >= 10) $level = 'Gold';
                                elseif ($userData['total_booking'] >= 5) $level = 'Silver';
                                echo $level;
                                ?> Member
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-t pt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Booking:</span>
                            <span class="font-bold text-gray-800"><?php echo $userData['total_booking']; ?>x</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Poin Reward:</span>
                            <span class="font-bold text-green-600"><?php echo $userData['points']; ?> pts</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Member Sejak:</span>
                            <span class="font-bold text-gray-800"><?php echo date('M Y', strtotime($userData['member_since'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Edit Profil Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8 fade-in">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-user text-blue-600 mr-2"></i>Informasi Pribadi
                    </h2>
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user text-purple-600 mr-2"></i>Nama Lengkap
                            </label>
                            <input type="text" name="nama" required 
                                value="<?php echo htmlspecialchars($userData['nama']); ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-envelope text-purple-600 mr-2"></i>Email
                            </label>
                            <input type="email" 
                                value="<?php echo htmlspecialchars($userData['email']); ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" disabled>
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i>Email tidak dapat diubah</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-phone text-purple-600 mr-2"></i>Nomor Telepon
                            </label>
                            <input type="tel" name="telepon" required pattern="[0-9]{10,13}"
                                value="<?php echo htmlspecialchars($userData['telepon']); ?>"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-map-marker-alt text-purple-600 mr-2"></i>Alamat Lengkap
                            </label>
                            <textarea name="alamat" required rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-purple-600 focus:outline-none transition"><?php echo htmlspecialchars($userData['alamat']); ?></textarea>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-4 rounded-lg font-bold text-lg hover:from-purple-700 hover:to-blue-700 transition shadow-lg">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8 fade-in">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-lock text-red-600 mr-2"></i>Keamanan Akun
                    </h2>
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-key text-red-600 mr-2"></i>Password Lama
                            </label>
                            <div class="relative">
                                <input type="password" name="old_password" id="old_password" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none transition pr-12"
                                    placeholder="Masukkan password lama">
                                <button type="button" onclick="togglePassword('old_password', 'old_icon')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                    <i class="fas fa-eye" id="old_icon"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-lock text-red-600 mr-2"></i>Password Baru
                            </label>
                            <div class="relative">
                                <input type="password" name="new_password" id="new_password" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none transition pr-12"
                                    placeholder="Minimal 6 karakter">
                                <button type="button" onclick="togglePassword('new_password', 'new_icon')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                    <i class="fas fa-eye" id="new_icon"></i>
                                </button>
                            </div>
                            <div id="password-strength" class="mt-2"></div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-lock text-red-600 mr-2"></i>Konfirmasi Password Baru
                            </label>
                            <div class="relative">
                                <input type="password" name="confirm_password" id="confirm_password" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none transition pr-12"
                                    placeholder="Ulangi password baru">
                                <button type="button" onclick="togglePassword('confirm_password', 'confirm_icon')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">
                                    <i class="fas fa-eye" id="confirm_icon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span class="font-semibold">Perhatian:</span> Setelah mengubah password, Anda akan tetap login di sesi ini.
                            </p>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-4 rounded-lg font-bold text-lg hover:from-red-700 hover:to-pink-700 transition shadow-lg">
                            <i class="fas fa-shield-alt mr-2"></i>Ubah Password
                        </button>
                    </form>
                </div>

                <!-- Delete Account -->
                <div class="bg-white rounded-2xl shadow-xl p-8 fade-in border-2 border-red-200">
                    <h2 class="text-2xl font-bold text-red-600 mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Zona Bahaya
                    </h2>
                    <p class="text-gray-600 mb-6">Tindakan di bawah ini bersifat permanen dan tidak dapat dibatalkan.</p>
                    <button onclick="alert('Fitur hapus akun akan tersedia segera!')" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Avatar Modal -->
    <div id="avatarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Pilih Avatar</h3>
                <button onclick="document.getElementById('avatarModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="change_avatar">
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <?php 
                    $colors = ['FF6B6B', '4ECDC4', '45B7D1', '96CEB4', 'FFEAA7', 'DFE6E9', 'A29BFE', 'FD79A8'];
                    foreach ($colors as $color): 
                    ?>
                    <label class="cursor-pointer avatar-option">
                        <input type="radio" name="avatar_style" value="<?php echo $color; ?>" class="sr-only peer">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($userData['nama']); ?>&background=<?php echo $color; ?>&color=fff&size=80" 
                            class="w-20 h-20 rounded-full border-4 border-transparent peer-checked:border-purple-600 hover:border-purple-300 transition">
                    </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white py-3 rounded-lg font-bold hover:bg-purple-700 transition">
                    Simpan Avatar
                </button>
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

        // Password strength indicator
        document.getElementById('new_password')?.addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');
            let strength = 0;
            let text = '';
            let color = '';
            
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;
            
            if (strength <= 2) {
                text = 'Lemah';
                color = 'bg-red-500';
            } else if (strength <= 3) {
                text = 'Sedang';
                color = 'bg-yellow-500';
            } else {
                text = 'Kuat';
                color = 'bg-green-500';
            }
            
            if (password.length > 0) {
                strengthDiv.innerHTML = `
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="${color} h-2 rounded-full" style="width: ${(strength/5)*100}%"></div>
                        </div>
                        <span class="text-sm font-semibold">${text}</span>
                    </div>
                `;
            } else {
                strengthDiv.innerHTML = '';
            }
        });
    </script>
</body>
</html>