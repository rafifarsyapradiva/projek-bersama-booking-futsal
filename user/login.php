<?php
session_start();

$error = '';
$success = '';

// Cek jika sudah login
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Cek pesan dari halaman lain
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $error = 'Email dan password harus diisi!';
    } else {
        // Cek kredensial
        $loginSuccess = false;
        if (isset($_SESSION['users'])) {
            foreach ($_SESSION['users'] as $user) {
                if (strtolower($user['email']) == strtolower($email) && password_verify($password, $user['password'])) {
                    // Login berhasil
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_nama'] = $user['nama'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_foto'] = $user['foto'];
                    $_SESSION['login_time'] = date('Y-m-d H:i:s');
                    
                    // Set notification
                    $_SESSION['success_message'] = 'Selamat datang kembali, ' . $user['nama'] . '!';
                    
                    // Redirect
                    $redirect = $_GET['redirect'] ?? 'dashboard.php';
                    header('Location: ' . $redirect);
                    exit;
                }
            }
        }
        
        if (!$loginSuccess) {
            $error = 'Email atau password salah! Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .slide-in { animation: slideIn 0.8s ease-out; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-400 via-blue-500 to-purple-600 min-h-screen flex items-center justify-center p-4">
    <div class="container max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden fade-in">
            <!-- Header -->
            <div class="bg-gradient-to-r from-green-600 to-blue-600 p-8 text-center">
                <a href="../index.php" class="inline-block">
                    <div class="bg-white w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 hover:scale-110 transition">
                        <i class="fas fa-futbol text-green-600 text-4xl"></i>
                    </div>
                </a>
                <h1 class="text-3xl font-bold text-white mb-2">Reham Futsal</h1>
                <p class="text-green-100">Masuk ke Akun Anda</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6 slide-in">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2 text-xl"></i>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-6 slide-in">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Demo Info -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800 font-semibold mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-lg"></i>Akun Demo untuk Testing:
                    </p>
                    <div class="bg-white rounded p-3 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-blue-700">Email:</span>
                            <code class="text-sm font-mono bg-blue-50 px-2 py-1 rounded">ahmad.rizki@email.com</code>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-blue-700">Password:</span>
                            <code class="text-sm font-mono bg-blue-50 px-2 py-1 rounded">password123</code>
                        </div>
                    </div>
                </div>

                <form method="POST" action="" class="space-y-6" id="loginForm">
                    <div class="slide-in">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-envelope text-green-600 mr-2"></i>Email
                        </label>
                        <input type="email" name="email" id="email" required 
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-600 focus:outline-none transition"
                            placeholder="nama@email.com">
                    </div>

                    <div class="slide-in">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock text-green-600 mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-600 focus:outline-none transition pr-12"
                                placeholder="Masukkan password">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between slide-in">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700">Ingat saya</span>
                        </label>
                        <a href="#" class="text-sm text-green-600 hover:text-green-700 font-semibold">Lupa password?</a>
                    </div>

                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-blue-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk Sekarang
                    </button>
                </form>

                <div class="mt-6 text-center slide-in">
                    <p class="text-gray-600">Belum punya akun? 
                        <a href="daftar.php" class="text-green-600 hover:text-green-700 font-semibold">Daftar sekarang</a>
                    </p>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <a href="../index.php" class="text-gray-500 hover:text-gray-700 text-sm inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Mohon isi semua field!');
            }
        });
    </script>
</body>
</html>