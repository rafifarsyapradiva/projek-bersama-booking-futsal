<?php
session_start();

// Simpan nama user sebelum dihapus
$user_nama = $_SESSION['user_nama'] ?? 'User';

// Hapus semua session user
unset($_SESSION['user_id']);
unset($_SESSION['user_nama']);
unset($_SESSION['user_email']);
unset($_SESSION['user_foto']);
unset($_SESSION['login_time']);

// Set pesan logout berhasil
$_SESSION['logout_message'] = 'Anda telah berhasil logout. Terima kasih!';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="refresh" content="3;url=../index.php">
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
<body class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
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
            Terima kasih telah menggunakan layanan kami, <strong><?php echo htmlspecialchars($user_nama); ?></strong>!
        </p>

        <!-- Info -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-6 text-left">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Anda akan diarahkan ke halaman utama dalam <span id="countdown" class="font-bold">3</span> detik...
            </p>
        </div>

        <!-- Actions -->
        <div class="space-y-3">
            <a href="../index.php" class="block bg-gradient-to-r from-green-600 to-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-blue-700 transition">
                <i class="fas fa-home mr-2"></i>Kembali ke Beranda
            </a>
            <a href="login.php" class="block bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                <i class="fas fa-sign-in-alt mr-2"></i>Login Lagi
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500">
                <i class="fas fa-futbol text-green-600 mr-1"></i>
                Reham Futsal - Sampai jumpa lagi!
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
                window.location.href = '../index.php';
            }
        }, 1000);
    </script>
</body>
</html>