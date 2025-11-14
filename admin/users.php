<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Inisialisasi data users
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        [
            'id' => 1,
            'nama' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Pandanaran No. 123, Semarang',
            'tanggal_daftar' => '2024-01-15',
            'total_booking' => 12,
            'total_transaksi' => 600000,
            'status' => 'active',
            'foto' => 'https://ui-avatars.com/api/?name=Budi+Santoso&background=random'
        ],
        [
            'id' => 2,
            'nama' => 'Siti Aminah',
            'email' => 'siti@email.com',
            'telepon' => '082345678901',
            'alamat' => 'Jl. Pemuda No. 45, Semarang',
            'tanggal_daftar' => '2024-02-10',
            'total_booking' => 8,
            'total_transaksi' => 400000,
            'status' => 'active',
            'foto' => 'https://ui-avatars.com/api/?name=Siti+Aminah&background=random'
        ],
        [
            'id' => 3,
            'nama' => 'Ahmad Wijaya',
            'email' => 'ahmad@email.com',
            'telepon' => '083456789012',
            'alamat' => 'Jl. Gajah Mada No. 78, Semarang',
            'tanggal_daftar' => '2024-03-05',
            'total_booking' => 15,
            'total_transaksi' => 750000,
            'status' => 'active',
            'foto' => 'https://ui-avatars.com/api/?name=Ahmad+Wijaya&background=random'
        ],
        [
            'id' => 4,
            'nama' => 'Dewi Lestari',
            'email' => 'dewi@email.com',
            'telepon' => '084567890123',
            'alamat' => 'Jl. Ahmad Yani No. 90, Semarang',
            'tanggal_daftar' => '2024-04-20',
            'total_booking' => 5,
            'total_transaksi' => 250000,
            'status' => 'active',
            'foto' => 'https://ui-avatars.com/api/?name=Dewi+Lestari&background=random'
        ],
        [
            'id' => 5,
            'nama' => 'Eko Prasetyo',
            'email' => 'eko@email.com',
            'telepon' => '085678901234',
            'alamat' => 'Jl. Diponegoro No. 56, Semarang',
            'tanggal_daftar' => '2024-05-12',
            'total_booking' => 3,
            'total_transaksi' => 150000,
            'status' => 'suspended',
            'foto' => 'https://ui-avatars.com/api/?name=Eko+Prasetyo&background=random'
        ]
    ];
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
        
        // Check email unik
        $email_exists = false;
        foreach ($_SESSION['users'] as $u) {
            if ($u['email'] == $email) {
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
                'telepon' => $telepon,
                'alamat' => $alamat,
                'tanggal_daftar' => date('Y-m-d'),
                'total_booking' => 0,
                'total_transaksi' => 0,
                'status' => 'active',
                'foto' => 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=random'
            ];
            $success = 'Member baru berhasil ditambahkan!';
        }
        
    } elseif (isset($_POST['edit_user'])) {
        $id = intval($_POST['user_id']);
        $nama = trim($_POST['nama']);
        $telepon = trim($_POST['telepon']);
        $alamat = trim($_POST['alamat']);
        
        foreach ($_SESSION['users'] as &$user) {
            if ($user['id'] == $id) {
                $user['nama'] = $nama;
                $user['telepon'] = $telepon;
                $user['alamat'] = $alamat;
                $user['foto'] = 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=random';
                $success = 'Data member berhasil diupdate!';
                break;
            }
        }
        
    } elseif (isset($_POST['delete_user'])) {
        $id = intval($_POST['user_id']);
        foreach ($_SESSION['users'] as $key => $user) {
            if ($user['id'] == $id) {
                unset($_SESSION['users'][$key]);
                $_SESSION['users'] = array_values($_SESSION['users']);
                $success = 'Member berhasil dihapus!';
                break;
            }
        }
        
    } elseif (isset($_POST['toggle_status'])) {
        $id = intval($_POST['user_id']);
        foreach ($_SESSION['users'] as &$user) {
            if ($user['id'] == $id) {
                $user['status'] = $user['status'] == 'active' ? 'suspended' : 'active';
                $success = 'Status member berhasil diubah!';
                break;
            }
        }
    }
}

// Statistics
$total_members = count($_SESSION['users']);
$active_members = 0;
$suspended_members = 0;

foreach ($_SESSION['users'] as $u) {
    if (isset($u['status'])) {
        if ($u['status'] == 'active') {
            $active_members++;
        } elseif ($u['status'] == 'suspended') {
            $suspended_members++;
        }
    }
}

$total_revenue = array_sum(array_column($_SESSION['users'], 'total_transaksi'));
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
                    <p class="text-gray-600">Kelola data member Reham Futsal</p>
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
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_members; ?></p>
                    </div>
                    <i class="fas fa-users text-blue-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Member Aktif</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $active_members; ?></p>
                    </div>
                    <i class="fas fa-user-check text-green-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Booking</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_bookings; ?></p>
                    </div>
                    <i class="fas fa-calendar-check text-purple-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($total_revenue / 1000, 0); ?>K</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-yellow-500 text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Members Table -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden fade-in">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">Daftar Member</h2>
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Cari member..." 
                            class="px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none w-64">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full" id="membersTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Member</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Kontak</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Tgl Daftar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Booking</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Total Transaksi</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($_SESSION['users'] as $user): ?>
                        <?php $user_status = isset($user['status']) ? $user['status'] : 'active'; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="<?php echo $user['foto']; ?>" alt="" class="w-10 h-10 rounded-full mr-3">
                                    <div>
                                        <div class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['nama']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-800"><?php echo htmlspecialchars($user['telepon']); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($user['alamat']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php echo date('d/m/Y', strtotime($user['tanggal_daftar'])); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                                    <?php echo $user['total_booking']; ?>x
                                </span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-green-600">
                                Rp <?php echo number_format($user['total_transaksi'], 0, ',', '.'); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full <?php echo $user_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo ucfirst($user_status); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button onclick="viewUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" 
                                        class="text-blue-600 hover:text-blue-800 transition" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)" 
                                        class="text-yellow-600 hover:text-yellow-800 transition" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="toggle_status" 
                                            class="text-purple-600 hover:text-purple-800 transition" title="Toggle Status">
                                            <i class="fas fa-toggle-on"></i>
                                        </button>
                                    </form>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" 
                                            class="text-red-600 hover:text-red-800 transition" title="Hapus"
                                            onclick="return confirm('Hapus member ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Add/Edit -->
    <div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-6" id="modalTitle">Tambah Member</h3>
            <form method="POST" class="space-y-4" id="userForm">
                <input type="hidden" name="user_id" id="user_id">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" required 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Email</label>
                    <input type="email" name="email" id="email" required 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nomor Telepon</label>
                    <input type="tel" name="telepon" id="telepon" required 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none"></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 rounded-lg font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn"
                        class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal View Detail -->
    <div id="viewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Detail Member</h3>
                <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div id="viewContent" class="space-y-4">
                <!-- Content will be filled by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Member';
            document.getElementById('userForm').reset();
            document.getElementById('user_id').value = '';
            document.getElementById('email').readOnly = false;
            document.getElementById('submitBtn').name = 'add_user';
            document.getElementById('formModal').classList.remove('hidden');
        }

        function editUser(user) {
            document.getElementById('modalTitle').textContent = 'Edit Member';
            document.getElementById('user_id').value = user.id;
            document.getElementById('nama').value = user.nama;
            document.getElementById('email').value = user.email;
            document.getElementById('email').readOnly = true;
            document.getElementById('telepon').value = user.telepon;
            document.getElementById('alamat').value = user.alamat;
            document.getElementById('submitBtn').name = 'edit_user';
            document.getElementById('formModal').classList.remove('hidden');
        }

        function viewUser(user) {
            const content = `
                <div class="flex items-center space-x-4 mb-6">
                    <img src="${user.foto}" alt="" class="w-20 h-20 rounded-full">
                    <div>
                        <h4 class="text-2xl font-bold text-gray-800">${user.nama}</h4>
                        <p class="text-gray-600">${user.email}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <p class="text-gray-500 text-sm">Nomor Telepon</p>
                        <p class="font-semibold text-gray-800">${user.telepon}</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4">
                        <p class="text-gray-500 text-sm">Tanggal Daftar</p>
                        <p class="font-semibold text-gray-800">${new Date(user.tanggal_daftar).toLocaleDateString('id-ID')}</p>
                    </div>
                    <div class="border-l-4 border-purple-500 pl-4">
                        <p class="text-gray-500 text-sm">Total Booking</p>
                        <p class="font-semibold text-gray-800">${user.total_booking} kali</p>
                    </div>
                    <div class="border-l-4 border-yellow-500 pl-4">
                        <p class="text-gray-500 text-sm">Total Transaksi</p>
                        <p class="font-semibold text-gray-800">Rp ${user.total_transaksi.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="col-span-2 border-l-4 border-red-500 pl-4">
                        <p class="text-gray-500 text-sm">Alamat</p>
                        <p class="font-semibold text-gray-800">${user.alamat}</p>
                    </div>
                    <div class="col-span-2 border-l-4 border-indigo-500 pl-4">
                        <p class="text-gray-500 text-sm">Status</p>
                        <p class="font-semibold ${user.status === 'active' ? 'text-green-600' : 'text-red-600'}">${user.status === 'active' ? 'Aktif' : 'Suspended'}</p>
                    </div>
                </div>
            `;
            document.getElementById('viewContent').innerHTML = content;
            document.getElementById('viewModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('formModal').classList.add('hidden');
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }

        document.getElementById('formModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.getElementById('viewModal').addEventListener('click', function(e) {
            if (e.target === this) closeViewModal();
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#membersTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
