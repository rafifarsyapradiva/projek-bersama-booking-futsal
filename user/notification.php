<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Silakan login terlebih dahulu!';
    header('Location: login.php');
    exit;
}

// Initialize notifications
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [
        [
            'id' => 1,
            'user_id' => $_SESSION['user_id'],
            'type' => 'welcome',
            'title' => 'Selamat Datang di Reham Futsal!',
            'message' => 'Terima kasih telah bergabung. Dapatkan promo spesial untuk booking pertama Anda!',
            'icon' => 'user-plus',
            'color' => 'blue',
            'link' => 'promo.php',
            'read' => false,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],
        [
            'id' => 2,
            'user_id' => $_SESSION['user_id'],
            'type' => 'promo',
            'title' => 'Promo Spesial Weekend!',
            'message' => 'Diskon 20% untuk booking di hari Sabtu & Minggu. Gunakan kode WEEKEND20.',
            'icon' => 'gift',
            'color' => 'pink',
            'link' => 'promo.php',
            'read' => false,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
        ],
        [
            'id' => 3,
            'user_id' => $_SESSION['user_id'],
            'type' => 'reminder',
            'title' => 'Reminder Booking Anda',
            'message' => 'Jangan lupa! Anda memiliki booking besok jam 16:00 di Lapangan Futsal 1.',
            'icon' => 'calendar-check',
            'color' => 'green',
            'link' => 'booking_history.php',
            'read' => false,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
        ],
        [
            'id' => 4,
            'user_id' => $_SESSION['user_id'],
            'type' => 'info',
            'title' => 'Update Fasilitas',
            'message' => 'Kami telah menambahkan ruang ganti VIP dengan AC di semua lapangan rumput!',
            'icon' => 'info-circle',
            'color' => 'purple',
            'link' => 'lapangan.php',
            'read' => true,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ],
        [
            'id' => 5,
            'user_id' => $_SESSION['user_id'],
            'type' => 'reward',
            'title' => 'Point Reward Bertambah!',
            'message' => 'Selamat! Anda mendapatkan 50 poin dari booking terakhir. Total poin Anda sekarang: ' . ($_SESSION['users'][0]['points'] ?? 0),
            'icon' => 'star',
            'color' => 'yellow',
            'link' => 'member_card.php',
            'read' => true,
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
        ]
    ];
}

// Handle mark as read
if (isset($_GET['read']) && isset($_GET['id'])) {
    $notifId = intval($_GET['id']);
    foreach ($_SESSION['notifications'] as &$notif) {
        if ($notif['id'] == $notifId && $notif['user_id'] == $_SESSION['user_id']) {
            $notif['read'] = true;
            break;
        }
    }
    header('Location: notification.php');
    exit;
}

// Handle mark all as read
if (isset($_GET['read_all'])) {
    foreach ($_SESSION['notifications'] as &$notif) {
        if ($notif['user_id'] == $_SESSION['user_id']) {
            $notif['read'] = true;
        }
    }
    header('Location: notification.php');
    exit;
}

// Handle delete notification
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $notifId = intval($_GET['id']);
    foreach ($_SESSION['notifications'] as $key => $notif) {
        if ($notif['id'] == $notifId && $notif['user_id'] == $_SESSION['user_id']) {
            unset($_SESSION['notifications'][$key]);
            $_SESSION['notifications'] = array_values($_SESSION['notifications']);
            break;
        }
    }
    header('Location: notification.php');
    exit;
}

// Get user notifications
$userNotifications = [];
$unreadCount = 0;
foreach ($_SESSION['notifications'] as $notif) {
    if ($notif['user_id'] == $_SESSION['user_id']) {
        $userNotifications[] = $notif;
        if (!$notif['read']) $unreadCount++;
    }
}

// Sort by date (newest first)
usort($userNotifications, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Filter
$filter = $_GET['filter'] ?? 'all';
if ($filter !== 'all') {
    if ($filter == 'unread') {
        $userNotifications = array_filter($userNotifications, function($n) {
            return !$n['read'];
        });
    } else {
        $userNotifications = array_filter($userNotifications, function($n) use ($filter) {
            return $n['type'] == $filter;
        });
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes bell {
            0%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-10deg); }
            20%, 40% { transform: rotate(10deg); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .slide-in { animation: slideIn 0.8s ease-out; }
        .bell-animation { animation: bell 1s ease-in-out infinite; }
        .notif-card { transition: all 0.3s ease; }
        .notif-card:hover { transform: translateX(10px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="../index.php" class="flex items-center space-x-2">
                    <i class="fas fa-futbol text-green-600 text-3xl"></i>
                    <span class="text-2xl font-bold text-gray-800">Reham Futsal</span>
                </a>
                <div class="flex items-center space-x-6">
                    <a href="dashboard.php" class="text-gray-700 hover:text-green-600 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard
                    </a>
                    <div class="relative">
                        <i class="fas fa-bell text-green-600 text-2xl bell-animation"></i>
                        <?php if ($unreadCount > 0): ?>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center">
                            <?php echo $unreadCount; ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-6 py-12">
        <!-- Title -->
        <div class="text-center mb-8 fade-in">
            <h1 class="text-5xl font-bold text-gray-800 mb-3">
                <i class="fas fa-bell text-blue-600 mr-2"></i>Pusat Notifikasi
            </h1>
            <p class="text-gray-600 text-lg">Semua informasi penting untuk Anda</p>
        </div>

        <div class="max-w-4xl mx-auto">
            <!-- Stats & Actions -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 fade-in">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600"><?php echo count($userNotifications); ?></div>
                            <div class="text-sm text-gray-600">Total</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-600"><?php echo $unreadCount; ?></div>
                            <div class="text-sm text-gray-600">Belum Dibaca</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600"><?php echo count($userNotifications) - $unreadCount; ?></div>
                            <div class="text-sm text-gray-600">Terbaca</div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <?php if ($unreadCount > 0): ?>
                        <a href="?read_all=1" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            <i class="fas fa-check-double mr-2"></i>Tandai Semua Dibaca
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-xl shadow-lg p-4 mb-8 fade-in">
                <div class="flex flex-wrap gap-3">
                    <a href="?filter=all" class="px-4 py-2 rounded-lg font-semibold transition <?php echo $filter == 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        <i class="fas fa-list mr-2"></i>Semua
                    </a>
                    <a href="?filter=unread" class="px-4 py-2 rounded-lg font-semibold transition <?php echo $filter == 'unread' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        <i class="fas fa-envelope mr-2"></i>Belum Dibaca
                    </a>
                    <a href="?filter=promo" class="px-4 py-2 rounded-lg font-semibold transition <?php echo $filter == 'promo' ? 'bg-pink-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        <i class="fas fa-gift mr-2"></i>Promo
                    </a>
                    <a href="?filter=reminder" class="px-4 py-2 rounded-lg font-semibold transition <?php echo $filter == 'reminder' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        <i class="fas fa-calendar-check mr-2"></i>Reminder
                    </a>
                    <a href="?filter=info" class="px-4 py-2 rounded-lg font-semibold transition <?php echo $filter == 'info' ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        <i class="fas fa-info-circle mr-2"></i>Info
                    </a>
                    <a href="?filter=reward" class="px-4 py-2 rounded-lg font-semibold transition <?php echo $filter == 'reward' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        <i class="fas fa-star mr-2"></i>Reward
                    </a>
                </div>
            </div>

            <!-- Notifications List -->
            <?php if (empty($userNotifications)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center fade-in">
                <i class="fas fa-bell-slash text-gray-300 text-8xl mb-6"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Tidak Ada Notifikasi</h2>
                <p class="text-gray-600 mb-6">
                    <?php if ($filter == 'all'): ?>
                        Anda belum memiliki notifikasi.
                    <?php else: ?>
                        Tidak ada notifikasi untuk kategori <?php echo ucfirst($filter); ?>.
                    <?php endif; ?>
                </p>
                <a href="dashboard.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
                </a>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($userNotifications as $notif): 
                    $colorClasses = [
                        'blue' => 'bg-blue-100 text-blue-600',
                        'green' => 'bg-green-100 text-green-600',
                        'pink' => 'bg-pink-100 text-pink-600',
                        'purple' => 'bg-purple-100 text-purple-600',
                        'yellow' => 'bg-yellow-100 text-yellow-600',
                        'red' => 'bg-red-100 text-red-600'
                    ];
                    $bgColor = $colorClasses[$notif['color']] ?? 'bg-gray-100 text-gray-600';
                ?>
                <div class="notif-card bg-white rounded-xl shadow-lg overflow-hidden slide-in <?php echo !$notif['read'] ? 'border-l-4 border-blue-500' : ''; ?>">
                    <div class="flex items-start p-6">
                        <!-- Icon -->
                        <div class="<?php echo $bgColor; ?> w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 mr-4">
                            <i class="fas fa-<?php echo $notif['icon']; ?> text-2xl"></i>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($notif['title']); ?></h3>
                                    <p class="text-gray-600"><?php echo htmlspecialchars($notif['message']); ?></p>
                                </div>
                                <?php if (!$notif['read']): ?>
                                <span class="ml-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full flex-shrink-0">
                                    BARU
                                </span>
                                <?php endif; ?>
                            </div>

                            <!-- Time & Actions -->
                            <div class="flex items-center justify-between mt-4">
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?php 
                                    $time_diff = time() - strtotime($notif['created_at']);
                                    if ($time_diff < 3600) {
                                        echo floor($time_diff / 60) . ' menit yang lalu';
                                    } elseif ($time_diff < 86400) {
                                        echo floor($time_diff / 3600) . ' jam yang lalu';
                                    } else {
                                        echo floor($time_diff / 86400) . ' hari yang lalu';
                                    }
                                    ?>
                                </div>

                                <div class="flex gap-2">
                                    <?php if (!$notif['read']): ?>
                                    <a href="?read=1&id=<?php echo $notif['id']; ?>" 
                                        class="text-blue-600 hover:text-blue-700 text-sm font-semibold">
                                        <i class="fas fa-check mr-1"></i>Tandai Dibaca
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="<?php echo $notif['link']; ?>" 
                                        class="text-green-600 hover:text-green-700 text-sm font-semibold">
                                        <i class="fas fa-arrow-right mr-1"></i>Lihat Detail
                                    </a>
                                    
                                    <a href="?delete=1&id=<?php echo $notif['id']; ?>" 
                                        onclick="return confirm('Hapus notifikasi ini?')"
                                        class="text-red-600 hover:text-red-700 text-sm font-semibold">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Notification Settings -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 mt-8 text-white text-center fade-in">
                <i class="fas fa-cog text-6xl mb-4 opacity-90"></i>
                <h3 class="text-2xl font-bold mb-3">Pengaturan Notifikasi</h3>
                <p class="text-lg mb-6 opacity-90">Kelola preferensi notifikasi Anda</p>
                <button onclick="alert('Fitur pengaturan notifikasi akan tersedia segera!')" 
                    class="bg-white text-blue-600 px-8 py-3 rounded-full font-bold hover:bg-blue-50 transition">
                    <i class="fas fa-sliders-h mr-2"></i>Atur Notifikasi
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto refresh every 30 seconds
        setTimeout(() => {
            // Uncomment untuk auto refresh
            // window.location.reload();
        }, 30000);

        // Mark as read when clicked
        document.querySelectorAll('.notif-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('a')) {
                    // Auto mark as read when notification is clicked
                }
            });
        });
    </script>
</body>
</html>