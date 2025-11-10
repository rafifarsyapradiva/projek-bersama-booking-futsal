<?php
session_start();

// Inisialisasi data dummy jika belum ada
if (!isset($_SESSION['initialized'])) {
    // Data Users
    $_SESSION['users'] = [
        [
            'id' => 1,
            'nama' => 'Ahmad Rizki',
            'email' => 'ahmad.rizki@email.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'telepon' => '081234567890',
            'alamat' => 'Jl. Pemuda No. 45, Semarang',
            'foto' => 'https://ui-avatars.com/api/?name=Ahmad+Rizki&background=10b981&color=fff&size=200',
            'member_since' => '2024-01-15',
            'total_booking' => 0,
            'points' => 0
        ]
    ];
    
    // Data Lapangan
    $_SESSION['lapangan'] = [
        [
            'id' => 1,
            'nama' => 'Lapangan Futsal 1',
            'jenis' => 'biasa',
            'harga' => 50000,
            'gambar' => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=400',
            'deskripsi' => 'Lapangan vinyl berkualitas dengan pencahayaan LED terang',
            'fasilitas' => ['Toilet', 'Mushola', 'Parkir', 'Kantin']
        ],
        [
            'id' => 2,
            'nama' => 'Lapangan Futsal 2',
            'jenis' => 'biasa',
            'harga' => 50000,
            'gambar' => 'https://images.unsplash.com/photo-1551958219-acbc608c6377?w=400',
            'deskripsi' => 'Lapangan standard dengan fasilitas lengkap',
            'fasilitas' => ['Toilet', 'Mushola', 'Parkir', 'Kantin']
        ],
        [
            'id' => 3,
            'nama' => 'Lapangan Futsal 3',
            'jenis' => 'biasa',
            'harga' => 50000,
            'gambar' => 'https://images.unsplash.com/photo-1589487391730-58f20eb2c308?w=400',
            'deskripsi' => 'Lapangan nyaman untuk bermain futsal',
            'fasilitas' => ['Toilet', 'Mushola', 'Parkir', 'Kantin']
        ],
        [
            'id' => 4,
            'nama' => 'Lapangan Rumput 1',
            'jenis' => 'rumput',
            'harga' => 100000,
            'gambar' => 'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=400',
            'deskripsi' => 'Lapangan rumput sintetis premium import',
            'fasilitas' => ['Toilet', 'Mushola', 'Parkir', 'Kantin', 'Ruang Ganti VIP']
        ],
        [
            'id' => 5,
            'nama' => 'Lapangan Rumput 2',
            'jenis' => 'rumput',
            'harga' => 100000,
            'gambar' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=400',
            'deskripsi' => 'Lapangan rumput dengan kualitas terbaik',
            'fasilitas' => ['Toilet', 'Mushola', 'Parkir', 'Kantin', 'Ruang Ganti VIP']
        ]
    ];
    
    // Data Bookings
    $_SESSION['bookings'] = [];
    
    // Data Promo
    $_SESSION['promo'] = [
        [
            'id' => 1,
            'kode' => 'WELCOME10',
            'nama' => 'Diskon Welcome 10%',
            'diskon' => 10,
            'min_booking' => 50000,
            'max_diskon' => 50000,
            'berlaku_sampai' => '2025-12-31',
            'aktif' => true
        ],
        [
            'id' => 2,
            'kode' => 'WEEKEND20',
            'nama' => 'Diskon Weekend 20%',
            'diskon' => 20,
            'min_booking' => 100000,
            'max_diskon' => 100000,
            'berlaku_sampai' => '2025-12-31',
            'aktif' => true
        ]
    ];
    
    // Data Reviews
    $_SESSION['reviews'] = [
        [
            'id' => 1,
            'user_nama' => 'Budi Santoso',
            'rating' => 5,
            'komentar' => 'Lapangan bagus, bersih, dan pelayanan ramah!',
            'tanggal' => '2025-10-15'
        ],
        [
            'id' => 2,
            'user_nama' => 'Siti Nurhaliza',
            'rating' => 5,
            'komentar' => 'Tempat favorit untuk main futsal di Semarang',
            'tanggal' => '2025-10-20'
        ]
    ];
    
    $_SESSION['initialized'] = true;
}

$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_nama'] : 'Guest';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reham Futsal - Booking Lapangan Futsal Terbaik di Semarang</title>
    <meta name="description" content="Booking lapangan futsal terbaik di Semarang. Fasilitas lengkap, harga terjangkau, lokasi strategis.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .fade-in-up { animation: fadeInUp 0.8s ease-out; }
        .slide-in-left { animation: slideInLeft 0.8s ease-out; }
        .hover-scale { transition: transform 0.3s ease; }
        .hover-scale:hover { transform: scale(1.05); }
        .bounce-animation { animation: bounce 2s infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="index.php" class="flex items-center space-x-2 slide-in-left">
                    <i class="fas fa-futbol text-green-600 text-3xl"></i>
                    <span class="text-2xl font-bold text-gray-800">Reham Futsal</span>
                </a>
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="text-green-600 font-semibold hover:text-green-700 transition">Beranda</a>
                    <a href="user/lapangan.php" class="text-gray-700 hover:text-green-600 transition">Lapangan</a>
                    <a href="user/jadwal.php" class="text-gray-700 hover:text-green-600 transition">Jadwal</a>
                    <?php if ($isLoggedIn): ?>
                        <a href="user/dashboard.php" class="text-gray-700 hover:text-green-600 transition">
                            <i class="fas fa-user-circle mr-1"></i><?php echo $userName; ?>
                        </a>
                        <a href="user/booking.php" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition">
                            <i class="fas fa-calendar-plus mr-1"></i>Booking
                        </a>
                    <?php else: ?>
                        <a href="user/login.php" class="text-gray-700 hover:text-green-600 transition">Login</a>
                        <a href="user/daftar.php" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition">Daftar</a>
                    <?php endif; ?>
                </div>
                <button class="md:hidden text-gray-700" id="mobileMenuBtn">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="container mx-auto px-6 py-20">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="fade-in-up">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-6 leading-tight">
                    Lapangan Futsal <span class="text-green-600">Terbaik</span> di Semarang
                </h1>
                <p class="text-xl text-gray-600 mb-8">Fasilitas lengkap, harga terjangkau, booking mudah. Wujudkan permainan terbaikmu bersama tim!</p>
                <div class="flex flex-wrap gap-4">
                    <a href="user/booking.php" class="bg-green-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-green-700 transition hover-scale inline-flex items-center">
                        <i class="fas fa-calendar-check mr-2"></i>Booking Sekarang
                    </a>
                    <a href="user/lapangan.php" class="bg-white text-green-600 px-8 py-4 rounded-full text-lg font-semibold border-2 border-green-600 hover:bg-green-50 transition hover-scale inline-flex items-center">
                        <i class="fas fa-list mr-2"></i>Lihat Lapangan
                    </a>
                </div>
                <div class="mt-8 flex items-center space-x-6 text-gray-700">
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-400 text-2xl mr-2"></i>
                        <div>
                            <div class="font-bold">4.9/5.0</div>
                            <div class="text-sm">Rating</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-500 text-2xl mr-2"></i>
                        <div>
                            <div class="font-bold">1000+</div>
                            <div class="text-sm">Member</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-futbol text-green-500 text-2xl mr-2"></i>
                        <div>
                            <div class="font-bold">5</div>
                            <div class="text-sm">Lapangan</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="fade-in-up bounce-animation">
                <img src="https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=600" alt="Futsal Court" class="rounded-2xl shadow-2xl">
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="bg-white py-20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 fade-in-up">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Mengapa Pilih Reham Futsal?</h2>
                <p class="text-gray-600 text-lg">Fasilitas dan layanan terbaik untuk pengalaman bermain yang maksimal</p>
            </div>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center p-6 rounded-xl hover:bg-green-50 transition fade-in-up">
                    <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-2">Lapangan Berkualitas</h3>
                    <p class="text-gray-600">Vinyl & rumput sintetis import dengan standar internasional</p>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-blue-50 transition fade-in-up">
                    <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hand-holding-usd text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-2">Harga Terjangkau</h3>
                    <p class="text-gray-600">Mulai dari Rp 50.000/jam dengan promo menarik</p>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-purple-50 transition fade-in-up">
                    <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-mobile-alt text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-2">Booking Mudah</h3>
                    <p class="text-gray-600">Sistem booking online 24/7, kapan saja dimana saja</p>
                </div>
                <div class="text-center p-6 rounded-xl hover:bg-orange-50 transition fade-in-up">
                    <div class="bg-orange-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tools text-orange-600 text-3xl"></i>
                    </div>
                    <h3 class="font-bold text-xl mb-2">Fasilitas Lengkap</h3>
                    <p class="text-gray-600">Toilet, mushola, parkir luas, kantin, ruang ganti</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Lapangan Section -->
    <section class="container mx-auto px-6 py-20">
        <div class="text-center mb-12 fade-in-up">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Pilihan Lapangan Kami</h2>
            <p class="text-gray-600 text-lg">5 Lapangan Berkualitas dengan Harga Kompetitif</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($_SESSION['lapangan'] as $lap): ?>
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover-scale fade-in-up">
                <div class="relative h-56 overflow-hidden">
                    <img src="<?php echo $lap['gambar']; ?>" alt="<?php echo $lap['nama']; ?>" class="w-full h-full object-cover hover:scale-110 transition duration-500">
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
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php foreach ($lap['fasilitas'] as $fas): ?>
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs">
                            <i class="fas fa-check text-green-600 mr-1"></i><?php echo $fas; ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <a href="user/booking.php?lapangan=<?php echo $lap['id']; ?>" class="block text-center bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-calendar-plus mr-2"></i>Booking Lapangan
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Testimonial -->
    <section class="bg-gradient-to-r from-green-600 to-blue-600 py-20">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-4xl font-bold text-white mb-4">Apa Kata Mereka?</h2>
                <p class="text-green-100 text-lg">Testimoni dari pelanggan setia kami</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8">
                <?php foreach ($_SESSION['reviews'] as $review): ?>
                <div class="bg-white rounded-xl p-8 shadow-xl fade-in-up">
                    <div class="flex items-center mb-4">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($review['user_nama']); ?>&background=10b981&color=fff" class="w-16 h-16 rounded-full mr-4">
                        <div>
                            <h4 class="font-bold text-gray-800"><?php echo $review['user_nama']; ?></h4>
                            <div class="text-yellow-400">
                                <?php for($i=0; $i<$review['rating']; $i++): ?>
                                <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-700 italic">"<?php echo $review['komentar']; ?>"</p>
                    <p class="text-gray-500 text-sm mt-4"><?php echo date('d F Y', strtotime($review['tanggal'])); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Lokasi Section -->
    <section class="container mx-auto px-6 py-20">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden fade-in-up">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="p-12">
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>Lokasi Kami
                    </h2>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-location-dot text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-700 font-semibold mb-1">Alamat Lengkap:</p>
                                <p class="text-gray-600">Jl. Ulin Utara 2 No. 320, Semarang, Jawa Tengah 50149</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-clock text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-700 font-semibold mb-1">Jam Operasional:</p>
                                <p class="text-gray-600">Senin - Minggu: 06.00 - 22.00 WIB</p>
                                <p class="text-sm text-green-600 mt-1"><i class="fas fa-check-circle mr-1"></i>Buka Setiap Hari</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-phone text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-700 font-semibold mb-1">Hubungi Kami:</p>
                                <p class="text-gray-600">+62 812-3456-7890</p>
                                <p class="text-gray-600">info@rehamfutsal.com</p>
                            </div>
                        </div>
                        <a href="user/booking.php" class="inline-block bg-gradient-to-r from-green-600 to-blue-600 text-white px-8 py-4 rounded-lg font-bold hover:from-green-700 hover:to-blue-700 transition">
                            <i class="fas fa-calendar-check mr-2"></i>Booking Sekarang
                        </a>
                    </div>
                </div>
                <div class="h-96 md:h-auto">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.2!2d110.4!3d-6.99!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwNTknMjQuMCJTIDExMMKwMjQnMDAuMCJF!5e0!3m2!1sen!2sid!4v1234567890" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" class="grayscale hover:grayscale-0 transition duration-300"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-orange-500 to-red-600 py-20">
        <div class="container mx-auto px-6 text-center">
            <div class="fade-in-up">
                <i class="fas fa-trophy text-white text-6xl mb-6"></i>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Siap Bermain Hari Ini?</h2>
                <p class="text-white text-xl mb-8 max-w-2xl mx-auto">Jangan tunda lagi! Booking lapangan favoritmu sekarang dan nikmati pengalaman bermain futsal terbaik di Semarang.</p>
                <a href="user/booking.php" class="inline-block bg-white text-orange-600 px-12 py-5 rounded-full text-xl font-bold hover:bg-orange-50 transition hover-scale">
                    <i class="fas fa-calendar-check mr-2"></i>Booking Sekarang Juga!
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <i class="fas fa-futbol text-green-500 text-3xl"></i>
                        <span class="text-2xl font-bold">Reham Futsal</span>
                    </div>
                    <p class="text-gray-400 mb-4">Lapangan futsal terbaik di Semarang dengan fasilitas lengkap dan harga terjangkau.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-gray-800 w-10 h-10 rounded-full flex items-center justify-center hover:bg-green-600 transition">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="bg-gray-800 w-10 h-10 rounded-full flex items-center justify-center hover:bg-green-600 transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="bg-gray-800 w-10 h-10 rounded-full flex items-center justify-center hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Menu Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php" class="text-gray-400 hover:text-green-500 transition">Beranda</a></li>
                        <li><a href="user/lapangan.php" class="text-gray-400 hover:text-green-500 transition">Lapangan</a></li>
                        <li><a href="user/jadwal.php" class="text-gray-400 hover:text-green-500 transition">Jadwal</a></li>
                        <li><a href="user/booking.php" class="text-gray-400 hover:text-green-500 transition">Booking</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Layanan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition">Member Card</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition">Promo & Voucher</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition">Tournament</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-500 transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-map-marker-alt mr-2 text-green-500"></i>Jl. Ulin Utara 2 No. 320</li>
                        <li><i class="fas fa-phone mr-2 text-green-500"></i>+62 812-3456-7890</li>
                        <li><i class="fas fa-envelope mr-2 text-green-500"></i>info@rehamfutsal.com</li>
                        <li><i class="fas fa-clock mr-2 text-green-500"></i>06:00 - 22:00 WIB</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2025 Reham Futsal. All rights reserved. | Made with <i class="fas fa-heart text-red-500"></i> in Semarang</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile Menu Toggle
        document.getElementById('mobileMenuBtn')?.addEventListener('click', function() {
            alert('Mobile menu - Coming soon!');
        });
    </script>
</body>
</html>