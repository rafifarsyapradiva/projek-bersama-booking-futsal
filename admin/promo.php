<?php
session_start();

// Cek login admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Inisialisasi data promo
if (!isset($_SESSION['promo'])) {
    $_SESSION['promo'] = [
        [
            'id' => 1,
            'kode' => 'WELCOME10',
            'nama' => 'Diskon Welcome Member Baru',
            'tipe' => 'percentage',
            'nilai' => 10,
            'min_transaksi' => 50000,
            'max_diskon' => 50000,
            'kuota' => 100,
            'terpakai' => 25,
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+30 days')),
            'status' => 'active',
            'deskripsi' => 'Diskon 10% untuk member baru'
        ],
        [
            'id' => 2,
            'kode' => 'WEEKEND20',
            'nama' => 'Diskon Weekend',
            'tipe' => 'percentage',
            'nilai' => 20,
            'min_transaksi' => 100000,
            'max_diskon' => 100000,
            'kuota' => 50,
            'terpakai' => 12,
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+60 days')),
            'status' => 'active',
            'deskripsi' => 'Diskon spesial weekend 20%'
        ],
        [
            'id' => 3,
            'kode' => 'MIDNIGHT50K',
            'nama' => 'Potongan Midnight',
            'tipe' => 'fixed',
            'nilai' => 50000,
            'min_transaksi' => 150000,
            'max_diskon' => 50000,
            'kuota' => 30,
            'terpakai' => 8,
            'tanggal_mulai' => date('Y-m-d'),
            'tanggal_selesai' => date('Y-m-d', strtotime('+15 days')),
            'status' => 'active',
            'deskripsi' => 'Potongan Rp 50.000 untuk booking malam'
        ]
    ];
}

$success = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_promo'])) {
        $kode = strtoupper(trim($_POST['kode']));
        $nama = trim($_POST['nama']);
        $tipe = $_POST['tipe'];
        $nilai = intval($_POST['nilai']);
        $min_transaksi = intval($_POST['min_transaksi']);
        $max_diskon = intval($_POST['max_diskon']);
        $kuota = intval($_POST['kuota']);
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $deskripsi = trim($_POST['deskripsi']);
        
        // Check kode unik
        $kode_exists = false;
        foreach ($_SESSION['promo'] as $p) {
            if ($p['kode'] == $kode) {
                $kode_exists = true;
                break;
            }
        }
        
        if ($kode_exists) {
            $error = 'Kode promo sudah digunakan!';
        } else {
            $new_id = max(array_column($_SESSION['promo'], 'id')) + 1;
            $_SESSION['promo'][] = [
                'id' => $new_id,
                'kode' => $kode,
                'nama' => $nama,
                'tipe' => $tipe,
                'nilai' => $nilai,
                'min_transaksi' => $min_transaksi,
                'max_diskon' => $max_diskon,
                'kuota' => $kuota,
                'terpakai' => 0,
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'status' => 'active',
                'deskripsi' => $deskripsi
            ];
            $success = 'Promo baru berhasil ditambahkan!';
        }
        
    } elseif (isset($_POST['edit_promo'])) {
        $id = intval($_POST['promo_id']);
        $nama = trim($_POST['nama']);
        $tipe = $_POST['tipe'];
        $nilai = intval($_POST['nilai']);
        $min_transaksi = intval($_POST['min_transaksi']);
        $max_diskon = intval($_POST['max_diskon']);
        $kuota = intval($_POST['kuota']);
        $tanggal_mulai = $_POST['tanggal_mulai'];
        $tanggal_selesai = $_POST['tanggal_selesai'];
        $deskripsi = trim($_POST['deskripsi']);
        
        foreach ($_SESSION['promo'] as &$promo) {
            if ($promo['id'] == $id) {
                $promo['nama'] = $nama;
                $promo['tipe'] = $tipe;
                $promo['nilai'] = $nilai;
                $promo['min_transaksi'] = $min_transaksi;
                $promo['max_diskon'] = $max_diskon;
                $promo['kuota'] = $kuota;
                $promo['tanggal_mulai'] = $tanggal_mulai;
                $promo['tanggal_selesai'] = $tanggal_selesai;
                $promo['deskripsi'] = $deskripsi;
                $success = 'Promo berhasil diupdate!';
                break;
            }
        }
        
    } elseif (isset($_POST['delete_promo'])) {
        $id = intval($_POST['promo_id']);
        foreach ($_SESSION['promo'] as $key => $promo) {
            if ($promo['id'] == $id) {
                unset($_SESSION['promo'][$key]);
                $_SESSION['promo'] = array_values($_SESSION['promo']);
                $success = 'Promo berhasil dihapus!';
                break;
            }
        }
        
    } elseif (isset($_POST['toggle_status'])) {
        $id = intval($_POST['promo_id']);
        foreach ($_SESSION['promo'] as &$promo) {
            if ($promo['id'] == $id) {
                $promo['status'] = $promo['status'] == 'active' ? 'inactive' : 'active';
                $success = 'Status promo berhasil diubah!';
                break;
            }
        }
    }
}

// Statistics
$total_promo = count($_SESSION['promo']);
$active_promo = count(array_filter($_SESSION['promo'], fn($p) => $p['status'] == 'active'));
$total_terpakai = array_sum(array_column($_SESSION['promo'], 'terpakai'));
$total_kuota = array_sum(array_column($_SESSION['promo'], 'kuota'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Promo - Admin Reham Futsal</title>
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
    <!-- Sidebar (sama seperti file sebelumnya) -->
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
            <a href="lapangan.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-layer-group w-5"></i>
                <span>Lapangan</span>
            </a>
            <a href="keuangan.php" class="flex items-center space-x-3 hover:bg-white/10 text-white px-4 py-3 rounded-lg mb-2 transition">
                <i class="fas fa-chart-line w-5"></i>
                <span>Keuangan</span>
            </a>
            <a href="promo.php" class="flex items-center space-x-3 bg-white/20 text-white px-4 py-3 rounded-lg mb-2">
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
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Manajemen Promo</h1>
                    <p class="text-gray-600">Kelola promo dan voucher diskon</p>
                </div>
                <button onclick="openAddModal()" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition shadow-lg">
                    <i class="fas fa-plus-circle mr-2"></i>Tambah Promo
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
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Promo</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_promo; ?></p>
                    </div>
                    <i class="fas fa-tags text-purple-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Aktif</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $active_promo; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Terpakai</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_terpakai; ?></p>
                    </div>
                    <i class="fas fa-user-check text-blue-500 text-4xl"></i>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500 fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Kuota</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_kuota; ?></p>
                    </div>
                    <i class="fas fa-ticket-alt text-orange-500 text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Promo Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($_SESSION['promo'] as $promo): ?>
            <?php
            $progress = $promo['kuota'] > 0 ? ($promo['terpakai'] / $promo['kuota']) * 100 : 0;
            $is_expired = strtotime($promo['tanggal_selesai']) < time();
            ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition fade-in <?php echo $is_expired ? 'opacity-50' : ''; ?>">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 p-4 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-2xl font-bold font-mono"><?php echo $promo['kode']; ?></h3>
                        <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold">
                            <?php echo $promo['tipe'] == 'percentage' ? $promo['nilai'] . '%' : 'Rp ' . number_format($promo['nilai'], 0, ',', '.'); ?>
                        </span>
                    </div>
                    <p class="text-sm text-purple-100"><?php echo htmlspecialchars($promo['nama']); ?></p>
                </div>

                <div class="p-6">
                    <div class="space-y-3 mb-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Min. Transaksi:</span>
                            <span class="font-semibold">Rp <?php echo number_format($promo['min_transaksi'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Max. Diskon:</span>
                            <span class="font-semibold">Rp <?php echo number_format($promo['max_diskon'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Periode:</span>
                            <span class="font-semibold text-xs">
                                <?php echo date('d/m/Y', strtotime($promo['tanggal_mulai'])); ?> - 
                                <?php echo date('d/m/Y', strtotime($promo['tanggal_selesai'])); ?>
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-gray-600">Kuota:</span>
                                <span class="font-semibold"><?php echo $promo['terpakai']; ?> / <?php echo $promo['kuota']; ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                        </div>
                        <div>
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full <?php echo $promo['status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <?php echo $is_expired ? 'Expired' : ucfirst($promo['status']); ?>
                            </span>
                        </div>
                    </div>

                    <p class="text-xs text-gray-600 mb-4 italic"><?php echo htmlspecialchars($promo['deskripsi']); ?></p>

                    <div class="flex gap-2">
                        <button onclick="editPromo(<?php echo htmlspecialchars(json_encode($promo)); ?>)" 
                            class="flex-1 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition text-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </button>
                        <form method="POST" class="flex-1">
                            <input type="hidden" name="promo_id" value="<?php echo $promo['id']; ?>">
                            <button type="submit" name="toggle_status" 
                                class="w-full bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-sm">
                                <i class="fas fa-toggle-on mr-1"></i>Status
                            </button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="promo_id" value="<?php echo $promo['id']; ?>">
                            <button type="submit" name="delete_promo" 
                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm"
                                onclick="return confirm('Hapus promo ini?')">
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
            <h3 class="text-2xl font-bold text-gray-800 mb-6" id="modalTitle">Tambah Promo</h3>
            <form method="POST" class="space-y-4" id="promoForm">
                <input type="hidden" name="promo_id" id="promo_id">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Kode Promo</label>
                        <input type="text" name="kode" id="kode" required style="text-transform: uppercase;"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tipe Diskon</label>
                        <select name="tipe" id="tipe" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                            <option value="percentage">Persentase (%)</option>
                            <option value="fixed">Nominal (Rp)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nama Promo</label>
                    <input type="text" name="nama" id="nama" required 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nilai</label>
                        <input type="number" name="nilai" id="nilai" required min="0"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Min. Transaksi</label>
                        <input type="number" name="min_transaksi" id="min_transaksi" required min="0" step="1000"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Max. Diskon</label>
                        <input type="number" name="max_diskon" id="max_diskon" required min="0" step="1000"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Kuota</label>
                        <input type="number" name="kuota" id="kuota" required min="1"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-red-600 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
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
            document.getElementById('modalTitle').textContent = 'Tambah Promo';
            document.getElementById('promoForm').reset();
            document.getElementById('promo_id').value = '';
            document.getElementById('submitBtn').name = 'add_promo';
            document.getElementById('kode').readOnly = false;
            document.getElementById('formModal').classList.remove('hidden');
        }

        function editPromo(promo) {
            document.getElementById('modalTitle').textContent = 'Edit Promo';
            document.getElementById('promo_id').value = promo.id;
            document.getElementById('kode').value = promo.kode;
            document.getElementById('kode').readOnly = true;
            document.getElementById('nama').value = promo.nama;
            document.getElementById('tipe').value = promo.tipe;
            document.getElementById('nilai').value = promo.nilai;
            document.getElementById('min_transaksi').value = promo.min_transaksi;
            document.getElementById('max_diskon').value = promo.max_diskon;
            document.getElementById('kuota').value = promo.kuota;
            document.getElementById('tanggal_mulai').value = promo.tanggal_mulai;
            document.getElementById('tanggal_selesai').value = promo.tanggal_selesai;
            document.getElementById('deskripsi').value = promo.deskripsi;
            document.getElementById('submitBtn').name = 'edit_promo';
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