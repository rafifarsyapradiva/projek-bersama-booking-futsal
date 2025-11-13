<?php
session_start();

// Inisialisasi data admin dummy
if (!isset($_SESSION['admins'])) {
    $_SESSION['admins'] = [
        [
            'id' => 1,
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'nama' => 'Administrator',
            'email' => 'admin@rehamfutsal.com',
            'role' => 'Super Admin',
            'foto' => 'https://ui-avatars.com/api/?name=Admin&background=ef4444&color=fff&size=200'
        ]
    ];
}

$error = '';

// Cek jika sudah login
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $loginSuccess = false;
        foreach ($_SESSION['admins'] as $admin) {
            if ($admin['username'] == $username && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama'] = $admin['nama'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['admin_foto'] = $admin['foto'];
                $_SESSION['admin_login_time'] = date('Y-m-d H:i:s');
                
                header('Location: dashboard.php');
                exit;
            }
        }
        
        if (!$loginSuccess) {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-red-500 via-pink-600 to-purple-700 min-h-screen flex items-center justify-center p-4">
    <div class="container max-w-md mx-auto">
        <div class="text-center mb-8 fade-in">
            <div class="bg-white w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 shadow-2xl float">
                <i class="fas fa-user-shield text-red-600 text-5xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-2">Admin Panel</h1>
            <p class="text-red-100">Reham Futsal Management System</p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden fade-in">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 p-6 text-center">
                <h2 class="text-2xl font-bold text-white">Login Administrator</h2>
                <p class="text-red-100 text-sm mt-1">Masuk ke dashboard admin</p>
            </div>

            <div class="p-8">
                <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Demo Credentials -->
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-red-800 font-semibold mb-3 flex items-center">
                        <i class="fas fa-key mr-2 text-lg"></i>Kredensial Demo Admin:
                    </p>
                    <div class="bg-white rounded p-3 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-red-700">Username:</span>
                            <code class="text-sm font-mono bg-red-50 px-2 py-1 rounded font-bold">admin</code>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-red-700">Password:</span>
                            <code class="text-sm font-mono bg-red-50 px-2 py-1 rounded font-bold">admin123</code>
                        </div>
                    </div>
                </div>

                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-user text-red-600 mr-2"></i>Username
                        </label>
                        <input type="text" name="username" required 
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none transition"
                            placeholder="Masukkan username admin">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-lock text-red-600 mr-2"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none transition pr-12"
                                placeholder="Masukkan password">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-700">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-4 rounded-lg font-bold text-lg hover:from-red-700 hover:to-pink-700 transition transform hover:scale-105 shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>Masuk ke Dashboard
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                    <a href="../../index.php" class="text-gray-500 hover:text-gray-700 text-sm inline-flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-6 text-white text-sm">
            <p><i class="fas fa-shield-alt mr-2"></i>Secure Admin Access</p>
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
    </script>
</body>

</html>

