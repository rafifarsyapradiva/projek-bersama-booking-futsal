<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Silakan login terlebih dahulu!';
    header('Location: login.php');
    exit;
}

// Ambil data user
$userData = null;
foreach ($_SESSION['users'] as $user) {
    if ($user['id'] == $_SESSION['user_id']) {
        $userData = $user;
        break;
    }
}

// Hitung level member
$totalBooking = $userData['total_booking'];
$memberLevel = 'Bronze';
$memberColor = 'orange';
$nextLevel = 'Silver';
$nextLevelTarget = 5;
$progress = 0;

if ($totalBooking >= 20) {
    $memberLevel = 'Platinum';
    $memberColor = 'purple';
    $nextLevel = 'Platinum';
    $nextLevelTarget = 20;
    $progress = 100;
} elseif ($totalBooking >= 10) {
    $memberLevel = 'Gold';
    $memberColor = 'yellow';
    $nextLevel = 'Platinum';
    $nextLevelTarget = 20;
    $progress = ($totalBooking / $nextLevelTarget) * 100;
} elseif ($totalBooking >= 5) {
    $memberLevel = 'Silver';
    $memberColor = 'gray';
    $nextLevel = 'Gold';
    $nextLevelTarget = 10;
    $progress = ($totalBooking / $nextLevelTarget) * 100;
} else {
    $progress = ($totalBooking / $nextLevelTarget) * 100;
}

// Generate member ID
$memberId = 'RFM' . str_pad($userData['id'], 6, '0', STR_PAD_LEFT);

// Benefits per level
$benefits = [
    'Bronze' => [
        'Diskon 5% setiap booking',
        'Akses booking online 24/7',
        'Point reward 1x setiap transaksi',
        'Customer support prioritas'
    ],
    'Silver' => [
        'Diskon 10% setiap booking',
        'Akses booking online 24/7',
        'Point reward 1.5x setiap transaksi',
        'Customer support prioritas',
        'Free minuman 1x per bulan',
        'Akses promo eksklusif member'
    ],
    'Gold' => [
        'Diskon 15% setiap booking',
        'Akses booking online 24/7',
        'Point reward 2x setiap transaksi',
        'Customer support prioritas VIP',
        'Free minuman 2x per bulan',
        'Akses promo eksklusif member',
        'Priority booking pada jam prime time',
        'Free sewa rompi tim'
    ],
    'Platinum' => [
        'Diskon 20% setiap booking',
        'Akses booking online 24/7',
        'Point reward 3x setiap transaksi',
        'Customer support prioritas VIP',
        'Free minuman unlimited',
        'Akses promo eksklusif member',
        'Priority booking pada jam prime time',
        'Free sewa rompi tim',
        'Akses lapangan rumput dengan harga biasa',
        'Voucher ulang tahun special',
        'Undangan event eksklusif'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Card - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shine {
            0% { background-position: -200px; }
            100% { background-position: 200px; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .member-card {
            background: linear-gradient(135deg, var(--color-start) 0%, var(--color-end) 100%);
            position: relative;
            overflow: hidden;
        }
        .member-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shine 3s infinite;
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        .card-3d {
            transform-style: preserve-3d;
            transition: transform 0.6s;
        }
        .card-3d:hover {
            transform: rotateY(10deg) rotateX(5deg);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-black min-h-screen text-white">
    <!-- Header -->
    <header class="bg-black/50 backdrop-blur-lg shadow-lg sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <a href="../index.php" class="flex items-center space-x-2">
                    <i class="fas fa-futbol text-green-500 text-3xl"></i>
                    <span class="text-2xl font-bold">Reham Futsal</span>
                </a>
                <div class="flex items-center space-x-6">
                    <a href="dashboard.php" class="text-gray-300 hover:text-green-500 transition">
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-6 py-12">
        <!-- Title -->
        <div class="text-center mb-12 fade-in">
            <h1 class="text-5xl md:text-6xl font-bold mb-4">
                <i class="fas fa-id-card text-<?php echo $memberColor; ?>-500 mr-3"></i>Member Card
            </h1>
            <p class="text-gray-400 text-xl">Kartu member digital Anda dengan berbagai keuntungan</p>
        </div>

        <div class="max-w-6xl mx-auto">
            <!-- Member Card Display -->
            <div class="mb-12 fade-in float-animation">
                <div class="member-card card-3d rounded-3xl p-8 md:p-12 shadow-2xl text-white max-w-2xl mx-auto"
                    style="--color-start: <?php 
                        echo $memberColor == 'orange' ? '#f97316' : 
                            ($memberColor == 'gray' ? '#6b7280' : 
                            ($memberColor == 'yellow' ? '#eab308' : '#a855f7')); 
                    ?>; --color-end: <?php 
                        echo $memberColor == 'orange' ? '#ea580c' : 
                            ($memberColor == 'gray' ? '#4b5563' : 
                            ($memberColor == 'yellow' ? '#ca8a04' : '#7e22ce')); 
                    ?>;">
                    
                    <!-- Card Header -->
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <div class="text-sm opacity-90 mb-1">REHAM FUTSAL</div>
                            <div class="text-3xl font-bold"><?php echo strtoupper($memberLevel); ?> MEMBER</div>
                        </div>
                        <div class="bg-white/20 backdrop-blur-lg rounded-full p-4">
                            <i class="fas fa-crown text-4xl"></i>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="mb-8">
                        <div class="text-sm opacity-75 mb-2">Member ID</div>
                        <div class="text-3xl font-mono font-bold tracking-wider mb-6"><?php echo $memberId; ?></div>
                        
                        <div class="text-sm opacity-75 mb-2">Card Holder</div>
                        <div class="text-2xl font-bold mb-1"><?php echo strtoupper($userData['nama']); ?></div>
                        <div class="text-sm opacity-90"><?php echo $userData['email']; ?></div>
                    </div>

                    <!-- Card Footer -->
                    <div class="flex justify-between items-end">
                        <div>
                            <div class="text-xs opacity-75 mb-1">Member Since</div>
                            <div class="font-semibold"><?php echo date('m/Y', strtotime($userData['member_since'])); ?></div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs opacity-75 mb-1">Points</div>
                            <div class="text-2xl font-bold"><?php echo number_format($userData['points']); ?></div>
                        </div>
                    </div>

                    <!-- Decorative Elements -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
                </div>

                <!-- QR Code Section -->
                <div class="text-center mt-8">
                    <button onclick="document.getElementById('qrModal').classList.remove('hidden')" 
                        class="bg-white/10 backdrop-blur-lg hover:bg-white/20 text-white px-8 py-4 rounded-full font-bold transition shadow-lg">
                        <i class="fas fa-qrcode mr-2"></i>Tampilkan QR Code
                    </button>
                </div>
            </div>

            <!-- Level Progress -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 fade-in">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">
                        <i class="fas fa-chart-line text-green-500 mr-2"></i>Progress Ke Level <?php echo $nextLevel; ?>
                    </h2>
                    <span class="text-3xl font-bold text-<?php echo $memberColor; ?>-500"><?php echo round($progress); ?>%</span>
                </div>
                
                <div class="bg-gray-700 rounded-full h-6 mb-4 overflow-hidden">
                    <div class="bg-gradient-to-r from-<?php echo $memberColor; ?>-500 to-<?php echo $memberColor; ?>-600 h-6 rounded-full transition-all duration-1000" 
                        style="width: <?php echo $progress; ?>%"></div>
                </div>
                
                <div class="flex justify-between text-sm">
                    <span class="text-gray-400">
                        <i class="fas fa-calendar-check mr-1"></i><?php echo $totalBooking; ?> booking saat ini
                    </span>
                    <?php if ($memberLevel != 'Platinum'): ?>
                    <span class="text-gray-400">
                        <?php echo ($nextLevelTarget - $totalBooking); ?> booking lagi ke <?php echo $nextLevel; ?>
                        <i class="fas fa-arrow-right ml-1"></i>
                    </span>
                    <?php else: ?>
                    <span class="text-<?php echo $memberColor; ?>-500 font-bold">
                        <i class="fas fa-check-circle mr-1"></i>Level Tertinggi!
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-500/20 backdrop-blur-lg rounded-xl p-6 text-center fade-in hover:bg-blue-500/30 transition">
                    <i class="fas fa-calendar-check text-5xl text-blue-400 mb-3"></i>
                    <div class="text-3xl font-bold mb-1"><?php echo $userData['total_booking']; ?></div>
                    <div class="text-gray-400">Total Booking</div>
                </div>
                <div class="bg-green-500/20 backdrop-blur-lg rounded-xl p-6 text-center fade-in hover:bg-green-500/30 transition">
                    <i class="fas fa-star text-5xl text-green-400 mb-3"></i>
                    <div class="text-3xl font-bold mb-1"><?php echo number_format($userData['points']); ?></div>
                    <div class="text-gray-400">Points</div>
                </div>
                <div class="bg-purple-500/20 backdrop-blur-lg rounded-xl p-6 text-center fade-in hover:bg-purple-500/30 transition">
                    <i class="fas fa-crown text-5xl text-purple-400 mb-3"></i>
                    <div class="text-3xl font-bold mb-1"><?php echo $memberLevel; ?></div>
                    <div class="text-gray-400">Member Level</div>
                </div>
                <div class="bg-orange-500/20 backdrop-blur-lg rounded-xl p-6 text-center fade-in hover:bg-orange-500/30 transition">
                    <i class="fas fa-gift text-5xl text-orange-400 mb-3"></i>
                    <div class="text-3xl font-bold mb-1"><?php echo count($benefits[$memberLevel]); ?></div>
                    <div class="text-gray-400">Benefits</div>
                </div>
            </div>

            <!-- Benefits -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 mb-8 fade-in">
                <h2 class="text-3xl font-bold mb-6">
                    <i class="fas fa-gift text-yellow-500 mr-2"></i>Benefits <?php echo $memberLevel; ?> Member
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($benefits[$memberLevel] as $benefit): ?>
                    <div class="bg-white/5 rounded-lg p-4 flex items-start hover:bg-white/10 transition">
                        <i class="fas fa-check-circle text-green-500 text-2xl mr-4 mt-1"></i>
                        <span class="text-gray-200"><?php echo $benefit; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- All Levels Comparison -->
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-8 fade-in">
                <h2 class="text-3xl font-bold mb-8 text-center">
                    <i class="fas fa-layer-group text-blue-500 mr-2"></i>Perbandingan Semua Level
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <?php 
                    $levels = ['Bronze', 'Silver', 'Gold', 'Platinum'];
                    $colors = ['orange', 'gray', 'yellow', 'purple'];
                    $requirements = [0, 5, 10, 20];
                    
                    foreach ($levels as $index => $level): 
                        $color = $colors[$index];
                        $isCurrent = $level == $memberLevel;
                    ?>
                    <div class="bg-<?php echo $color; ?>-500/<?php echo $isCurrent ? '30' : '10'; ?> backdrop-blur-lg rounded-xl p-6 <?php echo $isCurrent ? 'ring-4 ring-' . $color . '-500' : ''; ?> hover:bg-<?php echo $color; ?>-500/20 transition">
                        <?php if ($isCurrent): ?>
                        <div class="bg-<?php echo $color; ?>-500 text-white text-xs font-bold px-3 py-1 rounded-full inline-block mb-3">
                            <i class="fas fa-check mr-1"></i>LEVEL ANDA
                        </div>
                        <?php endif; ?>
                        
                        <div class="text-center mb-4">
                            <i class="fas fa-crown text-5xl text-<?php echo $color; ?>-500 mb-3"></i>
                            <h3 class="text-2xl font-bold mb-2"><?php echo $level; ?></h3>
                            <p class="text-sm text-gray-400">≥ <?php echo $requirements[$index]; ?> booking</p>
                        </div>
                        
                        <ul class="space-y-2 text-sm">
                            <?php 
                            $displayBenefits = array_slice($benefits[$level], 0, 4);
                            foreach ($displayBenefits as $benefit): 
                            ?>
                            <li class="flex items-start">
                                <i class="fas fa-check text-<?php echo $color; ?>-500 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs"><?php echo $benefit; ?></span>
                            </li>
                            <?php endforeach; ?>
                            <?php if (count($benefits[$level]) > 4): ?>
                            <li class="text-<?php echo $color; ?>-500 text-xs font-semibold">
                                +<?php echo count($benefits[$level]) - 4; ?> benefit lainnya
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="text-center mt-12 fade-in">
                <div class="bg-gradient-to-r from-green-500 to-blue-500 rounded-2xl p-8">
                    <i class="fas fa-rocket text-6xl mb-4"></i>
                    <h3 class="text-3xl font-bold mb-4">Tingkatkan Level Anda!</h3>
                    <p class="text-xl mb-6 text-gray-100">Booking lebih banyak, dapatkan benefit lebih besar</p>
                    <a href="booking.php" class="inline-block bg-white text-green-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition shadow-lg">
                        <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Modal -->
    <div id="qrModal" class="hidden fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full text-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">QR Code Member</h3>
                <button onclick="document.getElementById('qrModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            
            <div class="text-center mb-6">
                <div class="bg-gradient-to-br from-<?php echo $memberColor; ?>-100 to-<?php echo $memberColor; ?>-200 p-6 rounded-xl inline-block mb-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php echo $memberId; ?>" alt="QR Code" class="w-64 h-64">
                </div>
                <div class="font-mono text-2xl font-bold text-<?php echo $memberColor; ?>-600 mb-2"><?php echo $memberId; ?></div>
                <div class="text-gray-600"><?php echo $userData['nama']; ?></div>
            </div>

            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Tunjukkan QR code ini ke kasir untuk check-in dan dapatkan poin reward
                </p>
            </div>

            <button onclick="window.print()" class="w-full mt-6 bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition">
                <i class="fas fa-print mr-2"></i>Cetak QR Code
            </button>
        </div>
    </div>

    <script>
        // Add card tilt effect on mouse move
        const card = document.querySelector('.card-3d');
        if (card) {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = (y - centerY) / 10;
                const rotateY = (centerX - x) / 10;
                
                card.style.transform = perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg);
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
            });
        }
    </script>
</body>
</html>