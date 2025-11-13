<?php
session_start();

// Simpan nama admin sebelum dihapus
$admin_nama = $_SESSION['admin_nama'] ?? 'Administrator';

// Hapus semua session admin
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_nama']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_role']);
unset($_SESSION['admin_foto']);
unset($_SESSION['admin_login_time']);

// Set pesan logout berhasil
$_SESSION['logout_message'] = 'Logout berhasil. Silakan login kembali untuk mengakses admin panel.';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Admin - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="refresh" content="3;url=login.php">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes checkmark {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
        .checkmark {
            stroke-dasharray: 100;
            animation: checkmark 0.6s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-red-500 via-pink-600 to-purple-700 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center fade-in">
        <!-- Success Icon -->
        <div class="mb-6">
            <div class="bg-green-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-16 h-16" viewBox="0 0 52 52">
                    <circle class="checkmark" fill="none" stroke="#22c55e" stroke-width="4" cx="26" cy="26" r="20"/>
                    <path class="checkmark" fill="none" stroke="#22c55e" stroke-width="4" d="M14 27l7 7 16-16"/>
                </svg>
            </div>
        </div>

        <!-- Message -->
        <h1 class="text-3xl font-bold text-gray-800 mb-3">Logout Berhasil!</h1>
        <p class="text-gray-600 mb-6">
            Terima kasih, <strong><?php echo htmlspecialchars($admin_nama); ?></strong>!<br>
            Anda telah keluar dari admin panel dengan aman.
        </p>

        <!-- Info -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded mb-6 text-left">
            <p class="text-sm text-red-800">
                <i class="fas fa-info-circle mr-2"></i>
                Anda akan diarahkan ke halaman login dalam <span id="countdown" class="font-bold">3</span> detik...
            </p>
        </div>

        <!-- Security Info -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6 text-sm text-left">
            <p class="text-blue-800 mb-2 font-semibold">
                <i class="fas fa-shield-alt mr-2"></i>Sesi Berakhir dengan Aman
            </p>
            <ul class="text-blue-700 space-y-1 text-xs">
                <li><i class="fas fa-check mr-2"></i>Semua session telah dihapus</li>
                <li><i class="fas fa-check mr-2"></i>Data admin telah diamankan</li>
                <li><i class="fas fa-check mr-2"></i>Akses panel telah dicabut</li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <a href="login.php" class="block bg-gradient-to-r from-red-600 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-700 hover:to-pink-700 transition">
                <i class="fas fa-sign-in-alt mr-2"></i>Login Kembali
            </a>
            <a href="../index.php" class="block bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                <i class="fas fa-home mr-2"></i>Ke Halaman Utama
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500">
                <i class="fas fa-user-shield text-red-600 mr-1"></i>
                Reham Futsal Admin Panel
            </p>
        </div>
    </div>

    <script>
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = 'login.php';
            }
        }, 1000);
    </script>
</body>
</html>