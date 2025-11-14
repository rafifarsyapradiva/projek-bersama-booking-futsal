<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Inisialisasi data lapangan
if (!isset($_SESSION['lapangan'])) {
    $_SESSION['lapangan'] = [
        [
            'id' => 1,
            'nama' => 'Lapangan Futsal 1',
            'jenis' => 'Vinyl',
            'harga_per_jam' => 50000,
            'status' => 'available',
            'foto' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=400',
            'fasilitas' => ['Lampu Sorot', 'Parkir Luas', 'Kantin'],
            'keterangan' => 'Lapangan vinyl standar internasional'
        ],
        [
            'id' => 2,
            'nama' => 'Lapangan Futsal 2',
            'jenis' => 'Vinyl',
            'harga_per_jam' => 50000,
            'status' => 'available',
            'foto' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=400',
            'fasilitas' => ['Lampu Sorot', 'Parkir Luas'],
            'keterangan' => 'Lapangan vinyl dengan AC'
        ],
        [
            'id' => 3,
            'nama' => 'Lapangan Futsal 3',
            'jenis' => 'Vinyl',
            'harga_per_jam' => 50000,
            'status' => 'maintenance',
            'foto' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=400',
            'fasilitas' => ['Lampu Sorot'],
            'keterangan' => 'Sedang dalam perbaikan'
        ],
        [
            'id' => 4,
            'nama' => 'Lapangan Rumput 1',
            'jenis' => 'Rumput Sintetis',
            'harga_per_jam' => 100000,
            'status' => 'available',
            'foto' => 'https://images.unsplash.com/photo-1551958219-acbc608c6377?w=400',
            'fasilitas' => ['Lampu Sorot', 'Parkir Luas', 'Kantin', 'Mushola'],
            'keterangan' => 'Lapangan rumput premium'
        ],
        [
            'id' => 5,
            'nama' => 'Lapangan Rumput 2',
            'jenis' => 'Rumput Sintetis',
            'harga_per_jam' => 100000,
            'status' => 'available',
            'foto' => 'https://images.unsplash.com/photo-1551958219-acbc608c6377?w=400',
            'fasilitas' => ['Lampu Sorot', 'Parkir Luas', 'Kantin'],
            'keterangan' => 'Lapangan rumput outdoor'
        ]
    ];
}

$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_lapangan'])) {
        $nama = trim($_POST['nama']);
        $jenis = trim($_POST['jenis']);
        $harga = intval($_POST['harga_per_jam']);
        $status = $_POST['status'];
        $keterangan = trim($_POST['keterangan']);
        $fasilitas = isset($_POST['fasilitas']) ? $_POST['fasilitas'] : [];
        
        $new_id = max(array_column($_SESSION['lapangan'], 'id')) + 1;
        $_SESSION['lapangan'][] = [
            'id' => $new_id,
            'nama' => $nama,
            'jenis' => $jenis,
            'harga_per_jam' => $harga,
            'status' => $status,
            'foto' => 'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=400',
            'fasilitas' => $fasilitas,
            'keterangan' => $keterangan
        ];
        $success = 'Lapangan baru berhasil ditambahkan!';
        
    } elseif (isset($_POST['edit_lapangan'])) {
        $id = intval($_POST['lapangan_id']);
        $nama = trim($_POST['nama']);
        $jenis = trim($_POST['jenis']);
        $harga = intval($_POST['harga_per_jam']);
        $status = $_POST['status'];
        $keterangan = trim($_POST['keterangan']);
        $fasilitas = isset($_POST['fasilitas']) ? $_POST['fasilitas'] : [];
        
        foreach ($_SESSION['lapangan'] as &$lap) {
            if ($lap['id'] == $id) {
                $lap['nama'] = $nama;
                $lap['jenis'] = $jenis;
                $lap['harga_per_jam'] = $harga;
                $lap['status'] = $status;
                $lap['keterangan'] = $keterangan;
                $lap['fasilitas'] = $fasilitas;
                $success = 'Lapangan berhasil diupdate!';
                break;
            }
        }
        
    } elseif (isset($_POST['delete_lapangan'])) {
        $id = intval($_POST['lapangan_id']);
        foreach ($_SESSION['lapangan'] as $key => $lap) {
            if ($lap['id'] == $id) {
                unset($_SESSION['lapangan'][$key]);
                $_SESSION['lapangan'] = array_values($_SESSION['lapangan']);
                $success = 'Lapangan berhasil dihapus!';
                break;
            }
        }
        
    } elseif (isset($_POST['toggle_status'])) {
        $id = intval($_POST['lapangan_id']);
        foreach ($_SESSION['lapangan'] as &$lap) {
            if ($lap['id'] == $id) {
                if ($lap['status'] == 'available') {
                    $lap['status'] = 'maintenance';
                } else {
                    $lap['status'] = 'available';
                }
                $success = 'Status lapangan berhasil diubah!';
                break;
            }
        }
    }
}

// Statistics - PERBAIKAN: Pastikan setiap elemen array memiliki key 'status'
$total_lapangan = count($_SESSION['lapangan']);
$available = 0;
$maintenance = 0;

foreach ($_SESSION['lapangan'] as $lap) {
    if (isset($lap['status'])) {
        if ($lap['status'] == 'available') {
            $available++;
        } elseif ($lap['status'] == 'maintenance') {
            $maintenance++;
        }
    }
}

$avg_price = $total_lapangan > 0 ? array_sum(array_column($_SESSION['lapangan'], 'harga_per_jam')) / $total_lapangan : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lapangan - Admin Reham Futsal</title>
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
            <a href="users.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-users w-5"></i>
                <span>Member</span>
            </a>
            <a href="lapangan.php" class="flex items-center space-x-3 bg-white/20 text-white px-4 py-3 rounded-lg mb-2">
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
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Manajemen Lapangan</h1>
                    <p class="text-gray-600">Kelola data lapangan futsal</p>
                </div>
                <button onclick="openAddModal()" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition shadow-lg">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Lapangan
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

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Lapangan</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_lapangan; ?></p>
                    </div>
                    <i class="fas fa-layer-group text-blue-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Tersedia</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $available; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Maintenance</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $maintenance; ?></p>
                    </div>
                    <i class="fas fa-tools text-yellow-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Rata-rata Harga</p>
                        <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($avg_price / 1000, 0); ?>K</p>
                    </div>
                    <i class="fas fa-money-bill-wave text-purple-500 text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Lapangan Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($_SESSION['lapangan'] as $lap): ?>
            <?php 
            // PERBAIKAN: Pastikan key 'status' ada sebelum digunakan
            $status = isset($lap['status']) ? $lap['status'] : 'available';
            ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition fade-in">
                <img src="<?php echo $lap['foto']; ?>" alt="" class="w-full h-48 object-cover">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($lap['nama']); ?></h3>
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full <?php echo $status == 'available' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </div>

                    <div class="space-y-2 mb-4 text-sm">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-tag w-5 mr-2"></i>
                            <span><?php echo $lap['jenis']; ?></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-money-bill-wave w-5 mr-2"></i>
                            <span class="font-bold text-green-600">Rp <?php echo number_format($lap['harga_per_jam'], 0, ',', '.'); ?>/jam</span>
                        </div>
                        <div class="flex items-start text-gray-600">
                            <i class="fas fa-star w-5 mr-2 mt-1"></i>
                            <div class="flex-1 flex flex-wrap gap-1">
                                <?php foreach ($lap['fasilitas'] as $fas): ?>
                                <span class="inline-block px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded"><?php echo $fas; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="flex items-start text-gray-600">
                            <i class="fas fa-info-circle w-5 mr-2 mt-1"></i>
                            <span class="text-xs"><?php echo htmlspecialchars($lap['keterangan']); ?></span>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="editLapangan(<?php echo htmlspecialchars(json_encode($lap)); ?>)" 
                            class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                        <form method="POST" class="flex-1">
                            <input type="hidden" name="lapangan_id" value="<?php echo $lap['id']; ?>">
                            <button type="submit" name="toggle_status" 
                                class="w-full bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-sm">
                                <i class="fas fa-toggle-on mr-1"></i>Status
                            </button>
                        </form>
                        <form method="POST" class="inline">
                            <input type="hidden" name="lapangan_id" value="<?php echo $lap['id']; ?>">
                            <button type="submit" name="delete_lapangan" 
                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                                onclick="return confirm('Hapus lapangan ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Modal Add/Edit -->
    <div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-2xl font-bold text-gray-800 mb-6" id="modalTitle">Tambah Lapangan</h3>
            <form method="POST" class="space-y-4" id="lapanganForm">
                <input type="hidden" name="lapangan_id" id="lapangan_id">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nama Lapangan</label>
                        <input type="text" name="nama" id="nama" required 
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Jenis</label>
                        <select name="jenis" id="jenis" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                            <option value="Vinyl">Vinyl</option>
                            <option value="Rumput Sintetis">Rumput Sintetis</option>
                            <option value="Parquet">Parquet</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Harga per Jam (Rp)</label>
                        <input type="number" name="harga_per_jam" id="harga_per_jam" required min="0" step="1000"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <select name="status" id="status" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                            <option value="available">Available</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Fasilitas</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="fasilitas[]" value="Lampu Sorot" class="mr-2">
                            <span class="text-sm">Lampu Sorot</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fasilitas[]" value="Parkir Luas" class="mr-2">
                            <span class="text-sm">Parkir Luas</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fasilitas[]" value="Kantin" class="mr-2">
                            <span class="text-sm">Kantin</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fasilitas[]" value="Mushola" class="mr-2">
                            <span class="text-sm">Mushola</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fasilitas[]" value="AC" class="mr-2">
                            <span class="text-sm">AC</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="fasilitas[]" value="Toilet" class="mr-2">
                            <span class="text-sm">Toilet</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3"
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

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Lapangan';
            document.getElementById('lapanganForm').reset();
            document.getElementById('lapangan_id').value = '';
            document.getElementById('submitBtn').name = 'add_lapangan';
            document.querySelectorAll('input[name="fasilitas[]"]').forEach(cb => cb.checked = false);
            document.getElementById('formModal').classList.remove('hidden');
        }

        function editLapangan(lap) {
            document.getElementById('modalTitle').textContent = 'Edit Lapangan';
            document.getElementById('lapangan_id').value = lap.id;
            document.getElementById('nama').value = lap.nama;
            document.getElementById('jenis').value = lap.jenis;
            document.getElementById('harga_per_jam').value = lap.harga_per_jam;
            document.getElementById('status').value = lap.status || 'available';
            document.getElementById('keterangan').value = lap.keterangan;
            document.getElementById('submitBtn').name = 'edit_lapangan';
            
            // Set fasilitas checkboxes
            document.querySelectorAll('input[name="fasilitas[]"]').forEach(cb => {
                cb.checked = lap.fasilitas.includes(cb.value);
            });
            
            document.getElementById('formModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('formModal').classList.add('hidden');
        }

        document.getElementById('formModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
