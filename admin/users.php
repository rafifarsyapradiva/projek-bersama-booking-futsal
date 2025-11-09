<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $nama = trim($_POST['nama']);
        $email = trim($_POST['email']);
        $telepon = trim($_POST['telepon']);
        $alamat = trim($_POST['alamat']);
        
        // Validasi email unik
        $email_exists = false;
        foreach ($_SESSION['users'] as $user) {
            if (strtolower($user['email']) == strtolower($email)) {
                $email_exists = true;
                break;
            }
        }
        
        if ($email_exists) {
            $error = 'Email sudah terdaftar!';
        } else {
            $new_id = max(array_column($_SESSION['users'], 'id')) + 1;
            $_SESSION['users'][] = [
                'id' => $new_id,
                'nama' => $nama,
                'email' => $email,
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'telepon' => $telepon,
                'alamat' => $alamat,
                'foto' => 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=random&color=fff&size=200',
                'member_since' => date('Y-m-d'),
                'total_booking' => 0,
                'points' => 0,
                'status' => 'active'
            ];
            $success = 'Member baru berhasil ditambahkan!';
        }
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        foreach ($_SESSION['users'] as $key => $user) {
            if ($user['id'] == $user_id) {
                unset($_SESSION['users'][$key]);
                $_SESSION['users'] = array_values($_SESSION['users']);
                $success = 'Member berhasil dihapus!';
                break;
            }
        }
    } elseif (isset($_POST['toggle_status'])) {
        $user_id = $_POST['user_id'];
        foreach ($_SESSION['users'] as &$user) {
            if ($user['id'] == $user_id) {
                $user['status'] = $user['status'] == 'active' ? 'inactive' : 'active';
                $success = 'Status member berhasil diubah!';
                break;
            }
        }
    }
}

// Filter & Search
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';

$filtered_users = $_SESSION['users'];

// Filter by status
if ($status_filter != 'all') {
    $filtered_users = array_filter($filtered_users, function($u) use ($status_filter) {
        return isset($u['status']) && $u['status'] == $status_filter;
    });
}

// Search
if (!empty($search)) {
    $filtered_users = array_filter($filtered_users, function($u) use ($search) {
        return stripos($u['nama'], $search) !== false ||
               stripos($u['email'], $search) !== false ||
               stripos($u['telepon'], $search) !== false;
    });
}

// Sort by member_since (newest first)
usort($filtered_users, function($a, $b) {
    return strtotime($b['member_since']) - strtotime($a['member_since']);
});

// Statistics
$total_users = count($_SESSION['users']);
$active_users = count(array_filter($_SESSION['users'], fn($u) => isset($u['status']) && $u['status'] == 'active'));
$inactive_users = count(array_filter($_SESSION['users'], fn($u) => isset($u['status']) && $u['status'] == 'inactive'));
$total_bookings = array_sum(array_column($_SESSION['users'], 'total_booking'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Member - Admin Reham Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeIn 0.5s ease-out; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-red-600 to-pink-600 text-white shadow-2xl z-50">
        <div class="p-6 border-b border-red-500">
            <div class="flex items-center space-x-3">
                <div class="bg-white w-12 h-12 rounded-full flex items-center justify-center">
                    <i class="fas fa-futbol text-red-600 text-2xl"></i>
                </div>
                <div>
                    <h2 class="font-bold text-lg">Reham Futsal</h2>
                    <p class="text-xs text-red-100">Admin Panel</p>
                </div>
            </div>
        </div>

        <nav class="p-4">
            <a href="dashboard.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="bookings.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-calendar-check w-5"></i>
                <span>Booking</span>
            </a>
            <a href="users.php" class="flex items-center space-x-3 bg-white/20 text-white px-4 py-3 rounded-lg mb-2">
                <i class="fas fa-users w-5"></i>
                <span>Member</span>
            </a>
            <a href="lapangan.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-layer-group w-5"></i>
                <span>Lapangan</span>
            </a>
            <a href="keuangan.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-chart-line w-5"></i>
                <span>Keuangan</span>
            </a>
            <a href="promo.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-tags w-5"></i>
                <span>Promo</span>
            </a>
            <a href="settings.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-cog w-5"></i>
                <span>Settings</span>
            </a>
        </nav>

        <div class="absolute bottom-0 w-64 p-4 border-t border-red-500">
            <a href="logout.php" class="flex items-center justify-center space-x-2 bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 p-8">
        <!-- Header -->
        <div class="mb-8 fade-in">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Manajemen Member</h1>
                    <p class="text-gray-600">Kelola data member dan pelanggan</p>
                </div>
                <button onclick="openAddModal()" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>Tambah Member
                </button>
            </div>
        </div>

        <?php if ($success): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-6 fade-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2 text-xl"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-6 fade-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Member</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_users; ?></p>
                    </div>
                    <i class="fas fa-users text-blue-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Aktif</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $active_users; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Nonaktif</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $inactive_users; ?></p>
                    </div>
                    <i class="fas fa-times-circle text-red-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Booking</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_bookings; ?></p>
                    </div>
                    <i class="fas fa-calendar-alt text-purple-500 text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 fade-in">
            <div class="flex flex-wrap gap-4 items-center justify-between">
                <div class="flex flex-wrap gap-2">
                    <a href="?status=all" class="px-4 py-2 rounded-lg <?php echo $status_filter == 'all' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                        Semua
                    </a>
                    <a href="?status=active" class="px-4 py-2 rounded-lg <?php echo $status_filter == 'active' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                        Aktif
                    </a>
                    <a href="?status=inactive" class="px-4 py-2 rounded-lg <?php echo $status_filter == 'inactive' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> transition">
                        Nonaktif
                    </a>
                </div>
                <form method="GET" class="flex gap-2">
                    <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                        placeholder="Cari member..." 
                        class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Member Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (count($filtered_users) > 0): ?>
                <?php foreach ($filtered_users as $user): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition fade-in">
                    <div class="bg-gradient-to-r from-red-500 to-pink-500 h-24"></div>
                    <div class="px-6 pb-6 -mt-12">
                        <img src="<?php echo $user['foto']; ?>" alt="" class="w-24 h-24 rounded-full border-4 border-white mx-auto mb-4">
                        <div class="text-center mb-4">
                            <h3 class="text-xl font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($user['nama']); ?></h3>
                            <p class="text-sm text-gray-600 mb-2"><?php echo htmlspecialchars($user['email']); ?></p>
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full <?php echo (isset($user['status']) && $user['status'] == 'active') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo isset($user['status']) ? ucfirst($user['status']) : 'Active'; ?>
                            </span>
                        </div>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-phone w-5 mr-2"></i>
                                <span><?php echo $user['telepon']; ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar w-5 mr-2"></i>
                                <span>Sejak <?php echo date('d M Y', strtotime($user['member_since'])); ?></span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-clipboard-list w-5 mr-2"></i>
                                <span><?php echo $user['total_booking']; ?> booking</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-star w-5 mr-2"></i>
                                <span><?php echo $user['points']; ?> points</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button onclick="viewDetail(<?php echo htmlspecialchars(json_encode($user)); ?>)" 
                                class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-sm">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </button>
                            <form method="POST" class="flex-1">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="toggle_status" 
                                    class="w-full bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-sm">
                                    <i class="fas fa-toggle-on mr-1"></i>Status
                                </button>
                            </form>
                            <form method="POST" class="inline">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" 
                                    class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                                    onclick="return confirm('Hapus member ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
            <div class="col-span-3 text-center py-12 text-gray-500">
                <i class="fas fa-users-slash text-6xl mb-4 text-gray-300"></i>
                <p>Tidak ada member ditemukan</p>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Tambah Member -->
    <div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Tambah Member Baru</h3>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" required 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" name="email" required 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Telepon</label>
                    <input type="tel" name="telepon" required pattern="[0-9]{10,13}"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                    <textarea name="alamat" required rows="3"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none"></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeAddModal()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-lg font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" name="add_user"
                        class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Detail Member</h3>
                <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="detailContent"></div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function viewDetail(user) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('detailContent');
            
            content.innerHTML = `
                <div class="flex items-center space-x-4 mb-6">
                    <img src="${user.foto}" alt="" class="w-24 h-24 rounded-full border-4 border-red-500">
                    <div>
                        <h4 class="text-xl font-bold text-gray-800">${user.nama}</h4>
                        <p class="text-gray-600">${user.email}</p>
                        <span class="inline-block px-3 py-1 mt-2 text-xs font-semibold rounded-full ${user.status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${user.status.toUpperCase()}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Telepon</p>
                        <p class="font-bold">${user.telepon}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Member Sejak</p>
                        <p class="font-bold">${new Date(user.member_since).toLocaleDateString('id-ID')}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Booking</p>
                        <p class="font-bold">${user.total_booking}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Points</p>
                        <p class="font-bold">${user.points}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Alamat</p>
                        <p class="font-bold">${user.alamat}</p>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.add('hidden');
        }

        // Close modal on outside click
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) closeDetailModal();
        });
    </script>
</body>
</html>