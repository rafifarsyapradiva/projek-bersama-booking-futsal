<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mulia Prasama Danarta • Koperasi Simpan Pinjam</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(160deg, #0D1B2A, #1B263B);
      color: #E6EEF8;
    }
    .navbar {
      background-color: rgba(13, 27, 42, 0.9) !important;
      backdrop-filter: blur(6px);
    }
    .hero {
      padding: 4rem 1rem;
      background: radial-gradient(circle at top, rgba(255,255,255,0.1), transparent 60%);
      color: #EAF2FF;
    }
    .hero h1 {
      font-weight: 800;
    }
    .hero .btn-primary {
      background: linear-gradient(45deg, #0D47A1, #1E5ACB);
      border: none;
      box-shadow: 0 4px 18px rgba(29,91,203,0.4);
    }
    .hero .btn-outline-light {
      border-color: rgba(255,255,255,0.25);
      color: #E6EEF8;
    }
    .hero .btn-outline-light:hover {
      background-color: rgba(255,255,255,0.15);
    }
    .footer {
      border-top: 1px solid rgba(255,255,255,0.08);
      padding: 1rem;
      text-align: center;
      color: #AFC3E6;
      margin-top: 3rem;
    }
    .card {
      background-color: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 1rem;
      color: #EAF2FF;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
        <img src="img/logo.png" alt="Logo" width="32" height="32" class="me-2 rounded bg-white p-1">
        Mulia Prasama Danarta
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNav">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="#pinjaman">Pinjaman</a></li>
          <li class="nav-item"><a class="nav-link" href="#simpanan">Simpanan</a></li>
          <li class="nav-item"><a class="nav-link" href="#tentang">Tentang</a></li>
          <li class="nav-item"><a class="nav-link" href="#kontak">Kontak</a></li>
        </ul>
        <div class="d-flex gap-2">
          <a class="btn btn-outline-light btn-sm" href="login.php">Masuk</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero text-center text-lg-start">
    <div class="container py-5">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <h1 class="display-5 mb-3">Wujudkan Rencana Finansial</h1>
          <p class="lead text-primary fw-bold">Koperasi Simpan Pinjam Mulia Prasama Danarta</p>
          <p class="mb-4">
            Mitra tepercaya untuk simpan pinjam yang aman, mudah, dan transparan — membantu mewujudkan berbagai kebutuhan dengan layanan yang ramah dan fleksibel.
          </p>
          <div class="d-flex flex-wrap gap-2">
            <a href="login.php" class="btn btn-primary btn-lg">Masuk untuk login</a>
            <a href="#pinjaman" class="btn btn-outline-light btn-lg">Lihat Produk</a>
          </div>
        </div>
        <div class="col-lg-5 text-center">
          <img src="img/logo.png" class="img-fluid rounded-circle bg-white p-3 shadow" style="max-width: 220px;" alt="Logo Koperasi">
        </div>
      </div>
    </div>
  </section>

  <!-- Produk Pinjaman -->
  <section id="pinjaman" class="py-5">
    <div class="container">
      <h2 class="fw-bold mb-4 text-center text-light">Produk Pinjaman</h2>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 p-3">
            <h5 class="fw-bold">Modal Usaha</h5>
            <ul class="mb-0">
              <li>Pembiayaan usaha mikro dan kecil</li>
              <li>Proses cepat, syarat sederhana</li>
              <li>Angsuran fleksibel mengikuti arus kas</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 p-3">
            <h5 class="fw-bold">Pendidikan</h5>
            <ul class="mb-0">
              <li>Biaya sekolah, kursus, hingga akademik</li>
              <li>Pencairan tepat waktu</li>
              <li>Angsuran ringan dan terjadwal</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 p-3">
            <h5 class="fw-bold">Renovasi Rumah</h5>
            <ul class="mb-0">
              <li>Dukungan perbaikan & peningkatan hunian</li>
              <li>Pencairan bertahap sesuai kebutuhan</li>
              <li>Tenor menyesuaikan nilai proyek</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 p-3">
            <h5 class="fw-bold">Kendaraan & Kesehatan</h5>
            <ul class="mb-0">
              <li>Pembiayaan kendaraan pribadi</li>
              <li>Biaya berobat & kebutuhan darurat</li>
              <li>Dokumen ringkas dan transparan</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Produk Simpanan -->
  <section id="simpanan" class="py-5 bg-dark bg-opacity-25">
    <div class="container">
      <h2 class="fw-bold mb-4 text-center text-light">Produk Simpanan</h2>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 p-3">
            <h5 class="fw-bold">BINA (Anggaran)</h5>
            <ul class="mb-0">
              <li>Jangka minimal 6 bulan</li>
              <li>Setoran minimal Rp 10.000/bulan</li>
              <li>Jasa simpanan 2%</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 p-3">
            <h5 class="fw-bold">TARUNA</h5>
            <ul class="mb-0">
              <li>Simpan & tarik kapan saja</li>
              <li>Setoran minimal Rp 10.000</li>
              <li>Jasa simpanan 1%</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 p-3">
            <h5 class="fw-bold">DEPORKITA</h5>
            <ul class="mb-0">
              <li>Tenor 1/3/6/12 bulan</li>
              <li>Minimal simpanan Rp 1.000.000</li>
              <li>Jasa 4%; penarikan awal pinalti 50% jasa</li>
            </ul>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="card h-100 p-3">
            <h5 class="fw-bold">INTAN</h5>
            <ul class="mb-0">
              <li>Tabungan hari tua</li>
              <li>Setoran awal Rp 50.000; lanjut Rp 10.000–100.000</li>
              <li>Jasa 5,5%/tahun; tarik setelah usia 55 tahun</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="kontak" class="footer">
    © 2025 Mulia Prasama Danarta • Inovasi Digital Koperasi
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
