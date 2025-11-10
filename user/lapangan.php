<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Lapangan & Harga - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .fade-in-up { animation: fadeInUp 0.8s ease-out; }
        .price-card { transition: all 0.3s ease; }
        .price-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .badge-pulse { animation: pulse 2s infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-green-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="../index.php" class="flex items-center space-x-2">
                    <i class="fas fa-futbol text-green-600 text-3xl"></i>
                    <span class="text-2xl font-bold text-gray-800">Reham Futsal</span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="../index.php" class="text-gray-700 hover:text-green-600 transition">Beranda</a>
                    <?php if ($isLoggedIn): ?>
                        <a href="dashboard.php" class="text-gray-700 hover:text-green-600 transition">Dashboard</a>
                        <a href="booking.php" class="text-gray-700 hover:text-green-600 transition">Booking</a>
                    <?php endif; ?>
                    <a href="lapangan.php" class="text-green-600 font-semibold">Lapangan</a>
                    <a href="jadwal.php" class="text-gray-700 hover:text-green-600 transition">Jadwal</a>
                    <?php if (!$isLoggedIn): ?>
                        <a href="login.php" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-6 py-12">
        <!-- Header Section -->
        <div class="text-center mb-12 fade-in-up">
            <h1 class="text-5xl font-bold text-gray-800 mb-4">
                <i class="fas fa-list-ul text-green-600 mr-2"></i>Daftar Lapangan & Harga
            </h1>
            <p class="text-xl text-gray-600">Pilih lapangan sesuai kebutuhan dan budget Anda</p>
        </div>

        <!-- Price Comparison -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <!-- Lapangan Biasa -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden price-card fade-in-up">
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 p-8 text-white text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-10 rounded-full -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full -ml-16 -mb-16"></div>
                    <i class="fas fa-certificate text-7xl mb-4 opacity-80 relative z-10"></i>
                    <h2 class="text-3xl font-bold mb-2 relative z-10">Lapangan Biasa</h2>
                    <p class="text-blue-100 relative z-10">Lantai Vinyl Berkualitas Premium</p>
                </div>
                <div class="p-8">
                    <div class="text-center mb-8 bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl">
                        <div class="text-5xl font-bold text-blue-600 mb-2">Rp 50.000</div>
                        <div class="text-gray-600 font-semibold">per jam</div>
                        <div class="mt-3 text-sm text-blue-600">
                            <i class="fas fa-info-circle mr-1"></i>Harga spesial untuk member
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-gray-800 mb-4 text-lg">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>Fasilitas Lengkap:
                    </h3>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Lantai vinyl premium anti-slip berkualitas tinggi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Pencahayaan LED super terang setara stadion</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Bola futsal berkualitas tersedia gratis</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Fasilitas toilet & mushola bersih</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Area parkir luas & aman</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Kantin dengan menu lengkap</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Ruang ganti nyaman</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-star text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">AC tersedia di ruang tunggu</span>
                        </li>
                    </ul>

                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-blue-800 font-semibold mb-2">
                            <i class="fas fa-users mr-2"></i>Cocok untuk:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">Main Santai</span>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">Latihan Rutin</span>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">Turnamen Lokal</span>
                        </div>
                    </div>

                    <a href="booking.php" class="block text-center bg-gradient-to-r from-blue-600 to-blue-700 text-white py-4 rounded-lg font-bold text-lg hover:from-blue-700 hover:to-blue-800 transition shadow-lg">
                        <i class="fas fa-calendar-check mr-2"></i>Booking Sekarang
                    </a>
                </div>
            </div>

            <!-- Lapangan Rumput -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden price-card fade-in-up border-4 border-green-400 relative">
                <div class="absolute top-0 right-0 bg-yellow-400 text-yellow-900 px-6 py-2 rounded-bl-xl font-bold badge-pulse z-20 shadow-lg">
                    <i class="fas fa-star mr-1"></i>PREMIUM
                </div>
                <div class="bg-gradient-to-r from-green-500 to-green-700 p-8 text-white text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-white opacity-10 rounded-full -mr-20 -mt-20"></div>
                    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white opacity-10 rounded-full -ml-16 -mb-16"></div>
                    <i class="fas fa-crown text-7xl mb-4 opacity-80 relative z-10"></i>
                    <h2 class="text-3xl font-bold mb-2 relative z-10">Lapangan Rumput</h2>
                    <p class="text-green-100 relative z-10">Rumput Sintetis Premium Import</p>
                </div>
                <div class="p-8">
                    <div class="text-center mb-8 bg-gradient-to-r from-green-50 to-green-100 p-6 rounded-xl border-2 border-green-200">
                        <div class="text-5xl font-bold text-green-600 mb-2">Rp 100.000</div>
                        <div class="text-gray-600 font-semibold">per jam</div>
                        <div class="mt-3 text-sm text-green-600">
                            <i class="fas fa-gift mr-1"></i>Promo weekend tersedia!
                        </div>
                    </div>
                    
                    <h3 class="font-bold text-gray-800 mb-4 text-lg">
                        <i class="fas fa-crown text-yellow-500 mr-2"></i>Fasilitas Premium:
                    </h3>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <i class="fas fa-medal text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700 font-semibold">Rumput sintetis import FIFA quality</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-medal text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Sensasi bermain seperti di stadion profesional</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-medal text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Pencahayaan profesional 360 derajat</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-medal text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Bola futsal premium brand gratis</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-medal text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Semua fasilitas lapangan biasa</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-medal text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Ruang ganti VIP ber-AC</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-medal text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Mineral water gratis untuk pemain</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-medal text-yellow-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">Priority booking untuk member</span>
                        </li>
                    </ul>

                    <div class="bg-green-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-green-800 font-semibold mb-2">
                            <i class="fas fa-trophy mr-2"></i>Ideal untuk:
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">Tim Profesional</span>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">Event Spesial</span>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">Turnamen</span>
                        </div>
                    </div>

                    <a href="booking.php" class="block text-center bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-lg font-bold text-lg hover:from-green-700 hover:to-green-800 transition shadow-lg">
                        <i class="fas fa-calendar-check mr-2"></i>Booking Sekarang
                    </a>
                </div>
            </div>
        </div>

        <!-- Detail Semua Lapangan -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-12 fade-in-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">
                <i class="fas fa-th-large text-blue-600 mr-2"></i>Detail Semua Lapangan
            </h2>
            <p class="text-center text-gray-600 mb-8">Kami memiliki 5 lapangan berkualitas untuk Anda</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($_SESSION['lapangan'] as $lap): ?>
                <div class="border-2 border-gray-200 rounded-xl overflow-hidden hover:border-green-400 transition group">
                    <div class="relative overflow-hidden">
                        <img src="<?php echo $lap['gambar']; ?>" alt="<?php echo $lap['nama']; ?>" class="w-full h-56 object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute top-4 right-4 bg-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                            <i class="fas fa-<?php echo $lap['jenis'] == 'rumput' ? 'crown' : 'certificate'; ?> mr-1"></i><?php echo ucfirst($lap['jenis']); ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo $lap['nama']; ?></h3>
                        <p class="text-gray-600 text-sm mb-4"><?php echo $lap['deskripsi']; ?></p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-3xl font-bold text-green-600">Rp <?php echo number_format($lap['harga'], 0, ',', '.'); ?></span>
                            <span class="text-gray-500 text-sm">/ jam</span>
                        </div>
                        <div class="mb-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Fasilitas:</p>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($lap['fasilitas'] as $fas): ?>
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">
                                    <i class="fas fa-check text-green-600 mr-1"></i><?php echo $fas; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="booking.php?lapangan=<?php echo $lap['id']; ?>" class="flex-1 text-center bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                                <i class="fas fa-calendar-plus mr-1"></i>Booking
                            </a>
                            <a href="jadwal.php" class="flex-1 text-center bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                                <i class="fas fa-calendar-alt mr-1"></i>Jadwal
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Paket Berlangganan -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-8 mb-12 text-white fade-in-up">
            <div class="text-center mb-8">
                <i class="fas fa-box-open text-6xl mb-4"></i>
                <h2 class="text-4xl font-bold mb-4">Paket Berlangganan</h2>
                <p class="text-purple-100 text-lg">Hemat lebih banyak dengan paket member!</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold mb-2">Paket 5x</div>
                    <div class="text-4xl font-bold text-yellow-300 mb-4">Rp 225.000</div>
                    <p class="text-sm mb-4">Hemat Rp 25.000</p>
                    <ul class="text-sm space-y-2 mb-6">
                        <li><i class="fas fa-check mr-2"></i>5x Main (Lapangan Biasa)</li>
                        <li><i class="fas fa-check mr-2"></i>Gratis 1x Minuman</li>
                        <li><i class="fas fa-check mr-2"></i>Berlaku 2 Bulan</li>
                    </ul>
                    <button class="bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-purple-50 transition w-full">
                        Beli Paket
                    </button>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 text-center border-4 border-yellow-400">
                    <div class="bg-yellow-400 text-purple-900 px-3 py-1 rounded-full text-xs font-bold inline-block mb-2">TERPOPULER</div>
                    <div class="text-3xl font-bold mb-2">Paket 10x</div>
                    <div class="text-4xl font-bold text-yellow-300 mb-4">Rp 400.000</div>
                    <p class="text-sm mb-4">Hemat Rp 100.000</p>
                    <ul class="text-sm space-y-2 mb-6">
                        <li><i class="fas fa-check mr-2"></i>10x Main (Lapangan Biasa)</li>
                        <li><i class="fas fa-check mr-2"></i>Gratis 3x Minuman</li>
                        <li><i class="fas fa-check mr-2"></i>Berlaku 3 Bulan</li>
                    </ul>
                    <button class="bg-yellow-400 text-purple-900 px-6 py-3 rounded-lg font-bold hover:bg-yellow-300 transition w-full">
                        Beli Paket
                    </button>
                </div>
                <div class="bg-white/10 backdrop-blur-lg rounded-xl p-6 text-center">
                    <div class="text-3xl font-bold mb-2">Paket 20x</div>
                    <div class="text-4xl font-bold text-yellow-300 mb-4">Rp 700.000</div>
                    <p class="text-sm mb-4">Hemat Rp 300.000</p>
                    <ul class="text-sm space-y-2 mb-6">
                        <li><i class="fas fa-check mr-2"></i>20x Main (Lapangan Biasa)</li>
                        <li><i class="fas fa-check mr-2"></i>Gratis 10x Minuman</li>
                        <li><i class="fas fa-check mr-2"></i>Berlaku 6 Bulan</li>
                    </ul>
                    <button class="bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-purple-50 transition w-full">
                        Beli Paket
                    </button>
                </div>
            </div>
        </div>

        <!-- Promo Section -->
        <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl p-8 text-white text-center fade-in-up">
            <i class="fas fa-gift text-7xl mb-6"></i>
            <h2 class="text-4xl font-bold mb-4">Promo Spesial Bulan Ini!</h2>
            <p class="text-xl mb-4">Booking 5 jam, gratis 1 jam main!</p>
            <p class="text-lg opacity-90 mb-6">Dapatkan diskon hingga 20% untuk member setia</p>
            <div class="flex justify-center gap-4 flex-wrap">
                <a href="booking.php" class="inline-block bg-white text-orange-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-orange-50 transition">
                    <i class="fas fa-rocket mr-2"></i>Ambil Promo Sekarang
                </a>
                <?php if (!$isLoggedIn): ?>
                <a href="daftar.php" class="inline-block bg-orange-700 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-orange-800 transition">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Member
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>