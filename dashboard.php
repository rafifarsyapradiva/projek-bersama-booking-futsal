<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Reham Futsal</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3B82F6;
            --navy-blue: #1E3A8A;
            --cyan-bright: #06B6D4;
            --teal-accent: #14B8A6;
            --orange-warm: #F59E0B;
            --slate-dark: #1F2937;
            --slate-darker: #111827;
            --charcoal: #374151;
            --gray-light: #9CA3AF;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
        }

        * {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: #f8f9fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, var(--navy-blue) 0%, #1e3a8a 100%);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 4px 0 15px rgba(30, 58, 138, 0.2);
            z-index: 40;
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-icon {
            background: linear-gradient(135deg, var(--cyan-bright) 0%, var(--teal-accent) 100%);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            box-shadow: 0 6px 15px rgba(6, 182, 212, 0.3);
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.875rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: rgba(6, 182, 212, 0.2);
            color: white;
            padding-left: 2rem;
        }

        .sidebar-link i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--cyan-bright);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-link.active::before,
        .sidebar-link:hover::before {
            transform: scaleY(1);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--cyan-bright), var(--teal-accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown a {
            display: block;
            padding: 0.75rem 1rem;
            color: #374151;
            font-size: 0.9rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .dropdown a:last-child {
            border-bottom: none;
            color: #dc2626;
        }

        .dropdown a:hover {
            background: #f3f4f6;
        }

        /* Content Area */
        .content {
            padding: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-total { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .stat-upcoming { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-completed { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-payment { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

        .stat-value {
            font-size: 1.75rem;
            font-weight: bold;
            color: #1f2937;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .booking-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table-header {
            background: #f3f4f6;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-row {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            transition: background 0.2s ease;
        }

        .table-row:hover {
            background: #f8fafc;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .status {
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-selesai { background: #dcfce7; color: #166534; }
        .status-akan-datang { background: #fef3c7; color: #92400e; }
        .status-dibatalkan { background: #fee2e2; color: #991b1b; }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-detail { background: #dbeafe; color: #1d4ed8; }
        .btn-detail:hover { background: #bfdbfe; }

        /* Toast */
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
            border-left: 4px solid;
        }

        #toast.show {
            transform: translateX(0);
        }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-toggle {
                display: block;
            }
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: #374151;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo-icon">
                <i class="fas fa-futbol"></i>
            </div>
            <h2 class="text-lg font-bold">Reham Futsal</h2>
            <p class="text-xs opacity-80">Dashboard Pengguna</p>
        </div>
        <nav class="sidebar-menu">
            <a href="#" class="sidebar-link active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="#" class="sidebar-link">
                <i class="fas fa-calendar-check"></i> Booking Saya
            </a>
            <a href="#" class="sidebar-link">
                <i class="fas fa-history"></i> Riwayat
            </a>
            <a href="#" class="sidebar-link">
                <i class="fas fa-user"></i> Profil
            </a>
            <a href="../index.php" class="sidebar-link">
                <i class="fas fa-home"></i> Kembali ke Home
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Topbar -->
        <header class="topbar">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
            <div class="user-menu" id="userMenu">
                <div class="user-avatar">U</div>
                <span class="hidden md:block font-medium">User</span>
                <i class="fas fa-chevron-down text-sm ml-1"></i>
                <div class="dropdown" id="dropdown">
                    <a href="#"><i class="fas fa-user mr-2"></i> Profil</a>
                    <a href="#"><i class="fas fa-cog mr-2"></i> Pengaturan</a>
                    <a href="../index.php"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content">
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-total">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <div>
                        <div class="stat-value">12</div>
                        <div class="stat-label">Total Booking</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-upcoming">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="stat-value">3</div>
                        <div class="stat-label">Akan Datang</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-completed">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <div class="stat-value">8</div>
                        <div class="stat-label">Selesai</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-payment">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="stat-value">Rp 1.2jt</div>
                        <div class="stat-label">Total Bayar</div>
                    </div>
                </div>
            </div>

            <!-- Booking Table -->
            <div class="section-title">
                <i class="fas fa-list-alt"></i> Booking Terakhir
            </div>
            <div class="booking-table">
                <div class="table-header hidden md:flex">
                    <div class="flex-1">Lapangan</div>
                    <div class="flex-1">Tanggal</div>
                    <div class="flex-1">Jam</div>
                    <div class="flex-1 text-center">Status</div>
                    <div class="flex-1 text-center">Aksi</div>
                </div>

                <!-- Booking 1 -->
                <div class="table-row">
                    <div class="flex-1 font-medium">Lapangan Rumput 3</div>
                    <div class="flex-1">10 Nov 2025</div>
                    <div class="flex-1">19:00 - 20:00</div>
                    <div class="flex-1 text-center">
                        <span class="status status-akan-datang">Akan Datang</span>
                    </div>
                    <div class="flex-1 text-center">
                        <button class="btn-action btn-detail">Detail</button>
                    </div>
                </div>

                <!-- Booking 2 -->
                <div class="table-row">
                    <div class="flex-1 font-medium">Lapangan Biasa 1</div>
                    <div class="flex-1">05 Nov 2025</div>
                    <div class="flex-1">17:00 - 18:00</div>
                    <div class="flex-1 text-center">
                        <span class="status status-selesai">Selesai</span>
                    </div>
                    <div class="flex-1 text-center">
                        <button class="btn-action btn-detail">Detail</button>
                    </div>
                </div>

                <!-- Booking 3 -->
                <div class="table-row">
                    <div class="flex-1 font-medium">Lapangan Rumput 5</div>
                    <div class="flex-1">01 Nov 2025</div>
                    <div class="flex-1">20:00 - 21:00</div>
                    <div class="flex-1 text-center">
                        <span class="status status-selesai">Selesai</span>
                    </div>
                    <div class="flex-1 text-center">
                        <button class="btn-action btn-detail">Detail</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast -->
    <div id="toast" class="hidden">
        <i id="toast-icon" class="text-2xl"></i>
        <p id="toast-message" class="font-medium"></p>
        <button onclick="hideToast()" class="ml-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <script>
        // Mobile Sidebar Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const sidebar = document.querySelector('.sidebar');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        }

        // User Dropdown
        const userMenu = document.getElementById('userMenu');
        const dropdown = document.getElementById('dropdown');
        userMenu.addEventListener('click', () => {
            dropdown.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });

        // Toast
        let toastTimeout;
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toast-icon');
            const msg = document.getElementById('toast-message');

            if (toastTimeout) clearTimeout(toastTimeout);

            msg.textContent = message;
            toast.style.borderLeftColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6';

            const icons = {
                success: 'fas fa-check-circle text-green-500',
                error: 'fas fa-exclamation-circle text-red-500',
                info: 'fas fa-info-circle text-blue-500'
            };
            icon.className = icons[type] || icons.info;

            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('show'), 10);

            toastTimeout = setTimeout(hideToast, 4000);
        }

        function hideToast() {
            const toast = document.getElementById('toast');
            toast.classList.remove('show');
            setTimeout(() => toast.classList.add('hidden'), 400);
        }

        // Welcome Toast
        window.addEventListener('load', () => {
            setTimeout(() => {
                showToast('Selamat datang kembali, User!', 'success');
            }, 1000);
        });
    </script>
</body>
</html>