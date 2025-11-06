<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reham Futsal - Booking Lapangan Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3B82F6;
            --navy-blue: #1E3A8A;
            --cyan-bright: #06B6D4;
            --teal-accent: #14B8A6;
            --slate-darker: #111827;
        }

        body {
            background: linear-gradient(135deg, #f8f9fb 0%, #e5e7eb 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        header {
            background: linear-gradient(135deg, var(--navy-blue) 0%, #1e40af 100%);
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.15);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--cyan-bright) 0%, var(--teal-accent) 100%);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--cyan-bright);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after { width: 100%; }
        .nav-link:hover { color: var(--cyan-bright); }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--cyan-bright) 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .hero {
            background: linear-gradient(rgba(30, 58, 138, 0.9), rgba(30, 58, 138, 0.9)), url('https://images.unsplash.com/photo-1575361204480-aadea25e6e68?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') center/cover;
            color: white;
            padding: 6rem 1rem;
            text-align: center;
        }

        .lapangan-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .lapangan-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        .lapangan-img {
            height: 180px;
            object-fit: cover;
        }

        .lapangan-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .lapangan-card:hover .lapangan-img img {
            transform: scale(1.05);
        }

        .badge-rumput { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .badge-biasa { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }

        .mobile-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .mobile-menu.active { max-height: 500px; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }

        #toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 100;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transform: translateX(120%);
            transition: transform 0.4s ease;
            max-width: 320px;
        }

        #toast.show { transform: translateX(0); }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="text-white">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="logo-icon">
                    <i class="fas fa-futbol"></i>
                </div>
                <span class="text-xl font-bold hidden md:block">Reham Futsal</span>
            </div>

            <nav class="hidden md:flex space-x-8">
                <a href="#home" class="nav-link">Home</a>
                <a href="#lapangan" class="nav-link">Lapangan</a>
                <a href="#lokasi" class="nav-link">Lokasi</a>
            </nav>

            <!-- LINK KE user/login.php -->
            <div class="hidden md:flex space-x-3">
                <a href="user/login.php" class="btn-primary">Login</a>
                <a href="user/daftar.php" class="bg-white text-blue-900 px-6 py-2 rounded-lg font-bold hover:bg-gray-100">Daftar</a>
            </div>

            <button class="md:hidden" id="mobileMenuBtn">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <div class="md:hidden mobile-menu" id="mobileMenu">
            <a href="#home" class="block py-3 px-4 border-b">Home</a>
            <a href="#lapangan" class="block py-3 px-4 border-b">Lapangan</a>
            <a href="#lokasi" class="block py-3 px-4 border-b">Lokasi</a>
            <a href="user/login.php" class="block py-3 px-4 text-blue-600">Login</a>
            <a href="user/daftar.php" class="block py-3 px-4 bg-blue-600 text-white">Daftar</a>
        </div>
    </header>

    <!-- Hero -->
    <section id="home" class="hero">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 animate-fadeInUp">
                Selamat Datang di <span class="text-cyan-300">Reham Futsal</span>!
            </h1>
            <p class="text-lg md:text-xl mb-8 max-w-3xl mx-auto animate-fadeInUp" style="animation-delay: 0.2s;">
                Booking lapangan futsal terbaik: 5 rumput sintetis, 5 lapangan biasa.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fadeInUp" style="animation-delay: 0.4s;">
                <a href="user/daftar.php" class="bg-white text-blue-900 px-8 py-4 rounded-lg font-bold text-lg hover:bg-gray-100">Daftar Sekarang</a>
                <a href="user/login.php" class="border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-blue-900">Login</a>
            </div>
        </div>
    </section>

    <!-- Lapangan -->
    <section id="lapangan" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-3">Pilihan Lapangan Kami</h2>
                <p class="text-gray-600">Total <strong>10 lapangan</strong></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                $lapangan_list = [
                    ['nama' => 'Lapangan Rumput 1', 'jenis' => 'Rumput', 'harga' => 120000, 'badge' => 'badge-rumput', 'img' => 'https://images.pexels.com/photos/274506/pexels-photo-274506.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Rumput 2', 'jenis' => 'Rumput', 'harga' => 120000, 'badge' => 'badge-rumput', 'img' => 'https://images.pexels.com/photos/1595108/pexels-photo-1595108.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Rumput 3', 'jenis' => 'Rumput', 'harga' => 120000, 'badge' => 'badge-rumput', 'img' => 'https://images.pexels.com/photos/1618269/pexels-photo-1618269.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Rumput 4', 'jenis' => 'Rumput', 'harga' => 120000, 'badge' => 'badge-rumput', 'img' => 'https://images.pexels.com/photos/209977/pexels-photo-209977.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Rumput 5', 'jenis' => 'Rumput', 'harga' => 120000, 'badge' => 'badge-rumput', 'img' => 'https://images.pexels.com/photos/274422/pexels-photo-274422.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Biasa 1', 'jenis' => 'Biasa', 'harga' => 60000, 'badge' => 'badge-biasa', 'img' => 'https://images.pexels.com/photos/143203/pexels-photo-143203.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Biasa 2', 'jenis' => 'Biasa', 'harga' => 60000, 'badge' => 'badge-biasa', 'img' => 'https://images.pexels.com/photos/143182/pexels-photo-143182.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Biasa 3', 'jenis' => 'Biasa', 'harga' => 60000, 'badge' => 'badge-biasa', 'img' => 'https://images.pexels.com/photos/143305/pexels-photo-143305.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Biasa 4', 'jenis' => 'Biasa', 'harga' => 60000, 'badge' => 'badge-biasa', 'img' => 'https://images.pexels.com/photos/143203/pexels-photo-143203.jpeg?auto=compress&cs=tinysrgb&w=600'],
                    ['nama' => 'Lapangan Biasa 5', 'jenis' => 'Biasa', 'harga' => 60000, 'badge' => 'badge-biasa', 'img' => 'https://images.pexels.com/photos/274506/pexels-photo-274506.jpeg?auto=compress&cs=tinysrgb&w=600'],
                ];

                foreach ($lapangan_list as $i => $lapangan):
                ?>
                <div class="lapangan-card animate-fadeInUp" style="animation-delay: <?= $i * 0.1 ?>s;">
                    <div class="lapangan-img">
                        <img src="<?= $lapangan['img'] ?>" alt="<?= $lapangan['nama'] ?>" onerror="this.src='https://via.placeholder.com/600x400?text=No+Image';">
                    </div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-semibold text-gray-800"><?= $lapangan['nama'] ?></h3>
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?= $lapangan['badge'] ?>">
                                <?= $lapangan['jenis'] ?>
                            </span>
                        </div>
                        <p class="text-2xl font-bold text-green-600">
                            Rp <?= number_format($lapangan['harga'], 0, ',', '.') ?>/jam
                        </p>
                        <div class="mt-4 flex gap-2">
                            <a href="user/login.php" class="flex-1 bg-blue-600 text-white text-center py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Booking</a>
                            <a href="#lokasi" class="flex-1 border border-blue-600 text-blue-600 text-center py-2 rounded-lg text-sm font-medium hover:bg-blue-50">Lihat Lokasi</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Lokasi & Footer tetap sama -->
    <!-- (disingkat demi ruang) -->

    <script>
        // Mobile menu, smooth scroll, toast — sama seperti sebelumnya
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            const icon = mobileMenuBtn.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });

        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                document.querySelector(a.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Toast
        function showToast(msg, type = 'info') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle text-green-500' : 'fa-info-circle text-blue-500'} text-2xl"></i>
                               <p class="font-medium">${msg}</p>
                               <button onclick="this.parentElement.remove()" class="ml-4 text-gray-400">x</button>`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => toast.remove(), 4000);
        }

        window.addEventListener('load', () => {
            setTimeout(() => showToast('Pilih lapangan & login untuk booking!', 'info'), 1000);
        });
    </script>
</body>
</html>