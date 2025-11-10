<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);

// Get selected date (default today)
$selectedDate = $_GET['date'] ?? date('Y-m-d');
$displayDate = date('l, d F Y', strtotime($selectedDate));

// Jam operasional
$jamOperasional = ['06:00', '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'];

// Buat array jadwal untuk semua lapangan
$jadwalLapangan = [];
foreach ($_SESSION['lapangan'] as $lap) {
    $jadwalLapangan[$lap['id']] = [];
    foreach ($jamOperasional as $jam) {
        // Cek apakah jam ini sudah dibooking
        $bookingInfo = null;
        if (isset($_SESSION['bookings'])) {
            foreach ($_SESSION['bookings'] as $booking) {
                if ($booking['lapangan_id'] == $lap['id'] && 
                    $booking['tanggal'] == $selectedDate && 
                    $booking['status'] == 'aktif') {
                    $jamMulai = substr($booking['jam_mulai'], 0, 5);
                    $jamSelesai = substr($booking['jam_selesai'], 0, 5);
                    if ($jam >= $jamMulai && $jam < $jamSelesai) {
                        $bookingInfo = $booking;
                        break;
                    }
                }
            }
        }
        
        // Cek apakah sudah lewat
        $isPast = false;
        if ($selectedDate == date('Y-m-d')) {
            $isPast = strtotime($jam) < strtotime(date('H:00'));
        } elseif ($selectedDate < date('Y-m-d')) {
            $isPast = true;
        }
        
        $jadwalLapangan[$lap['id']][$jam] = [
            'booked' => $bookingInfo !== null,
            'past' => $isPast,
            'booking' => $bookingInfo
        ];
    }
}

// Hitung statistik
$totalSlots = count($_SESSION['lapangan']) * count($jamOperasional);
$bookedSlots = 0;
$availableSlots = 0;
foreach ($jadwalLapangan as $lapId => $slots) {
    foreach ($slots as $jam => $info) {
        if ($info['booked']) $bookedSlots++;
        elseif (!$info['past']) $availableSlots++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Lapangan - Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .fade-in { animation: fadeIn 0.6s ease-out; }
        .slide-in { animation: slideIn 0.8s ease-out; }
        .time-slot { transition: all 0.3s ease; cursor: pointer; }
        .time-slot:hover:not(.booked):not(.past) {
            transform: scale(1.05);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-purple-50 min-h-screen">
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
                    <a href="lapangan.php" class="text-gray-700 hover:text-green-600 transition">Lapangan</a>
                    <a href="jadwal.php" class="text-green-600 font-semibold">Jadwal</a>
                    <?php if (!$isLoggedIn): ?>
                        <a href="login.php" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 transition">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mx-auto px-6 py-12">
        <!-- Header Section -->
        <div class="text-center mb-8 fade-in">
            <h1 class="text-5xl font-bold text-gray-800 mb-4">
                <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>Jadwal Lapangan
            </h1>
            <p class="text-xl text-gray-600 mb-6">Lihat ketersediaan lapangan dan booking langsung!</p>
            
            <!-- Date Selector -->
            <div class="flex justify-center items-center gap-4 flex-wrap">
                <form method="GET" class="flex items-center gap-3">
                    <input type="date" name="date" value="<?php echo $selectedDate; ?>" min="<?php echo date('Y-m-d'); ?>"
                        class="px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-600 focus:outline-none transition"
                        onchange="this.form.submit()">
                </form>
                <div class="bg-white px-6 py-3 rounded-full shadow-lg">
                    <i class="fas fa-calendar-day text-indigo-600 mr-2"></i>
                    <span class="font-bold text-gray-800"><?php echo $displayDate; ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Date Navigation -->
        <div class="flex justify-center gap-3 mb-8 flex-wrap fade-in">
            <?php for ($i = 0; $i < 7; $i++): 
                $date = date('Y-m-d', strtotime("+$i day"));
                $dayName = date('D', strtotime($date));
                $dayNum = date('d', strtotime($date));
                $isSelected = $date == $selectedDate;
            ?>
            <a href="?date=<?php echo $date; ?>" class="px-4 py-3 rounded-lg font-semibold transition text-center min-w-[80px] <?php echo $isSelected ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-indigo-50'; ?>">
                <div class="text-xs"><?php echo $dayName; ?></div>
                <div class="text-xl font-bold"><?php echo $dayNum; ?></div>
            </a>
            <?php endfor; ?>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 text-center fade-in">
                <div class="text-4xl font-bold text-blue-600 mb-2"><?php echo $totalSlots; ?></div>
                <div class="text-gray-600">Total Slot</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center fade-in">
                <div class="text-4xl font-bold text-green-600 mb-2"><?php echo $availableSlots; ?></div>
                <div class="text-gray-600">Tersedia</div>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6 text-center fade-in">
                <div class="text-4xl font-bold text-red-600 mb-2"><?php echo $bookedSlots; ?></div>
                <div class="text-gray-600">Terbooked</div>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8 fade-in">
            <h3 class="font-bold text-gray-800 mb-4 text-lg">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Keterangan Status:
            </h3>
            <div class="flex flex-wrap gap-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-lg mr-3"></div>
                    <div>
                        <div class="font-semibold text-gray-800">Tersedia</div>
                        <div class="text-xs text-gray-500">Klik untuk booking</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-red-500 rounded-lg mr-3"></div>
                    <div>
                        <div class="font-semibold text-gray-800">Terbooked</div>
                        <div class="text-xs text-gray-500">Sudah dibooking user lain</div>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-300 rounded-lg mr-3"></div>
                    <div>
                        <div class="font-semibold text-gray-800">Lewat</div>
                        <div class="text-xs text-gray-500">Waktu sudah terlewat</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Grid -->
        <div class="space-y-6">
            <?php foreach ($_SESSION['lapangan'] as $lap): ?>
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden fade-in">
                <!-- Header Lapangan -->
                <div class="bg-gradient-to-r from-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-600 to-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-700 p-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="bg-white/20 backdrop-blur-lg p-4 rounded-full mr-4">
                                <i class="fas fa-futbol text-white text-3xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white"><?php echo $lap['nama']; ?></h2>
                                <p class="text-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-100">
                                    <?php echo ucfirst($lap['jenis']); ?> - Rp <?php echo number_format($lap['harga'], 0, ',', '.'); ?>/jam
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <span class="bg-white text-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-700 px-4 py-2 rounded-full font-bold">
                                Lapangan <?php echo $lap['id']; ?>
                            </span>
                            <a href="booking.php?lapangan=<?php echo $lap['id']; ?>" class="bg-white text-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-700 px-6 py-2 rounded-full font-bold hover:bg-<?php echo $lap['jenis'] == 'rumput' ? 'green' : 'blue'; ?>-50 transition">
                                <i class="fas fa-calendar-plus mr-2"></i>Booking
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Schedule Slots -->
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-8 gap-3">
                        <?php foreach ($jamOperasional as $jam): ?>
                        <?php 
                            $info = $jadwalLapangan[$lap['id']][$jam];
                            $statusColor = 'gray';
                            $statusIcon = 'times';
                            $statusText = 'Lewat';
                            
                            if ($info['booked']) {
                                $statusColor = 'red';
                                $statusIcon = 'lock';
                                $statusText = 'Terbooked';
                            } elseif (!$info['past']) {
                                $statusColor = 'green';
                                $statusIcon = 'check';
                                $statusText = 'Tersedia';
                            }
                        ?>
                        <div class="time-slot <?php echo $info['booked'] ? 'booked' : ''; ?> <?php echo $info['past'] ? 'past' : ''; ?> 
                                    bg-<?php echo $statusColor; ?>-500 text-white rounded-lg p-4 text-center 
                                    <?php echo (!$info['booked'] && !$info['past']) ? 'hover:bg-' . $statusColor . '-600' : ''; ?>"
                             onclick="<?php echo (!$info['booked'] && !$info['past']) ? "bookSlot(" . $lap['id'] . ", '" . $selectedDate . "', '" . $jam . "')" : ''; ?>">
                            <div class="text-2xl font-bold mb-1"><?php echo $jam; ?></div>
                            <div class="text-xs flex items-center justify-center">
                                <i class="fas fa-<?php echo $statusIcon; ?> mr-1"></i><?php echo $statusText; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-6 flex justify-between items-center flex-wrap gap-4 p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-clock text-indigo-600 mr-2"></i>
                            Jam Operasional: <span class="font-semibold">06:00 - 22:00 WIB</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Klik slot <span class="font-semibold text-green-600">Tersedia</span> untuk booking cepat
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA Section -->
        <div class="mt-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl p-8 text-white text-center fade-in">
            <i class="fas fa-bell text-6xl mb-4"></i>
            <h3 class="text-3xl font-bold mb-3">Lihat Slot yang Tersedia?</h3>
            <p class="text-lg mb-6">Jangan sampai kehabisan slot favorit Anda. Booking mudah dan cepat!</p>
            <div class="flex justify-center space-x-4 flex-wrap gap-4">
                <a href="booking.php" class="inline-block bg-white text-orange-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-orange-50 transition">
                    <i class="fas fa-calendar-plus mr-2"></i>Booking Sekarang
                </a>
                <a href="lapangan.php" class="inline-block bg-orange-700 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-orange-800 transition">
                    <i class="fas fa-list mr-2"></i>Lihat Harga
                </a>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="mt-8 bg-white rounded-xl shadow-lg p-6 text-center fade-in">
            <p class="text-gray-700 mb-3 text-lg">
                <i class="fas fa-phone text-green-600 mr-2"></i>
                Butuh bantuan? Hubungi kami di <a href="tel:+6281234567890" class="font-bold text-green-600 hover:text-green-700">+62 812-3456-7890</a>
            </p>
            <p class="text-gray-600 text-sm">
                <i class="fas fa-clock text-blue-600 mr-2"></i>
                Customer service tersedia 24/7
            </p>
        </div>
    </div>

    <script>
        function bookSlot(lapanganId, tanggal, jam) {
            const jamSelesai = String(parseInt(jam) + 1).padStart(2, '0') + ':00';
            window.location.href = booking.php?lapangan=${lapanganId}&tanggal=${tanggal}&jam_mulai=${jam}&jam_selesai=${jamSelesai};
        }
    </script>
</body>
</html>