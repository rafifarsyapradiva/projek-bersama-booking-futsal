<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - Reham Futsal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-blue: #3B82F6;
      --cyan-bright: #06B6D4;
      --orange-warm: #F59E0B;
    }
    body {
      background: linear-gradient(135deg, #1E3A8A, #3B82F6);
      font-family: 'Segoe UI', sans-serif;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Daftar Akun</h1>
      <p class="text-gray-600 mt-2">Booking Lapangan Futsal Reham</p>
    </div>

    <!-- Pesan Sukses -->
    <div id="success" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
      <strong>Berhasil!</strong> Akun telah dibuat. 
      <a href="login.php" class="underline font-semibold">Login sekarang</a>
    </div>

    <!-- Pesan Error -->
    <div id="error" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
      <strong>Error:</strong> <span id="error-msg"></span>
    </div>

    <!-- Form Daftar -->
    <form id="form-daftar" class="space-y-5">
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          <i class="fas fa-envelope text-cyan-bright"></i> Email
        </label>
        <input type="email" id="email" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent"
               placeholder="contoh@email.com">
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          <i class="fas fa-lock text-orange-warm"></i> Password
        </label>
        <input type="password" id="password" required minlength="6"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent"
               placeholder="Minimal 6 karakter">
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          <i class="fas fa-lock text-teal-accent"></i> Konfirmasi Password
        </label>
        <input type="password" id="confirm" required minlength="6"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-blue focus:border-transparent"
               placeholder="Ulangi password">
      </div>

      <!-- TOMBOL DAFTAR (PASTIKAN ADA!) -->
      <button type="submit" id="btn-daftar"
              class="w-full bg-gradient-to-r from-primary-blue to-cyan-bright text-blackface font-bold py-3 rounded-lg hover:shadow-lg transform hover:scale-105 transition duration-200 flex items-center justify-center gap-2">
        <i class="fas fa-user-plus"></i>
        <span>Daftar Sekarang</span>
      </button>
    </form>

    <p class="text-center mt-6 text-gray-600">
      Sudah punya akun? 
      <a href="login.php" class="text-primary-blue font-semibold hover:underline">Login di sini</a>
    </p>

    <p class="text-center mt-4 text-xs text-gray-500">
      <i class="fas fa-info-circle"></i> Hanya tampilan demo – tidak ada penyimpanan.
    </p>
  </div>

  <!-- JavaScript -->
  <script>
    document.getElementById('form-daftar').addEventListener('submit', function(e) {
      e.preventDefault();

      const email = document.getElementById('email').value.trim();
      const pass = document.getElementById('password').value;
      const confirm = document.getElementById('confirm').value;

      const errorDiv = document.getElementById('error');
      const successDiv = document.getElementById('success');
      const errorMsg = document.getElementById('error-msg');

      errorDiv.classList.add('hidden');
      successDiv.classList.add('hidden');

      if (!email || !pass || !confirm) {
        errorMsg.textContent = "Semua kolom wajib diisi!";
        errorDiv.classList.remove('hidden');
        return;
      }
      if (!email.includes('@') || !email.includes('.')) {
        errorMsg.textContent = "Email tidak valid!";
        errorDiv.classList.remove('hidden');
        return;
      }
      if (pass.length < 6) {
        errorMsg.textContent = "Password minimal 6 karakter!";
        errorDiv.classList.remove('hidden');
        return;
      }
      if (pass !== confirm) {
        errorMsg.textContent = "Password tidak cocok!";
        errorDiv.classList.remove('hidden');
        return;
      }

      successDiv.classList.remove('hidden');
      setTimeout(() => {
        window.location.href = 'login.php';
      }, 1500);
    });
  </script>

</body>
</html>
