# 🛡️ REHAM FUTSAL - Admin Panel

## 📋 Deskripsi
Panel admin untuk mengelola sistem booking lapangan futsal Reham. Dibangun dengan PHP Session (tanpa database) untuk demo purposes dengan tampilan modern dan responsif.

## 🎯 Fitur Admin Panel

### ✅ Fitur Tersedia

#### 1. **Dashboard Admin** (`dashboard.php`)
- 📊 Statistik real-time:
  - Total Member
  - Total Booking
  - Total Pendapatan
  - Pending Booking
- 📅 Booking hari ini
- ⚡ Quick Actions
- 📈 Chart statistik (booking & pembayaran)
- 🕐 Live clock
- 👥 Member terbaru

#### 2. **Manajemen Booking** (`bookings.php`)
- ✅ Konfirmasi booking
- ❌ Tolak booking
- 🗑️ Hapus booking
- 👁️ Detail booking
- 🔍 Filter by status (All, Pending, Confirmed, Rejected)
- 🔎 Search booking
- 📊 Statistik booking

#### 3. **Manajemen Member** (`users.php`)
- ➕ Tambah member baru
- 👁️ Detail member
- 🔄 Toggle status (Active/Inactive)
- 🗑️ Hapus member
- 🔍 Filter by status
- 🔎 Search member
- 📊 Statistik member
- 📱 Card view dengan avatar

#### 4. **Login & Logout**
- 🔐 Login admin dengan validasi
- 🚪 Logout secure
- 🎨 Tampilan menarik dengan animasi

## 🗂️ Struktur Folder

```
admin/
├── login.php          # Login admin
├── logout.php         # Logout admin
├── dashboard.php      # Dashboard utama
├── bookings.php       # Manajemen booking
├── users.php          # Manajemen member
├── lapangan.php       # (Placeholder - untuk pengembangan)
├── keuangan.php       # (Placeholder - untuk pengembangan)
├── promo.php          # (Placeholder - untuk pengembangan)
├── settings.php       # (Placeholder - untuk pengembangan)
└── README.md          # Dokumentasi
```

## 🔑 Kredensial Admin Demo

```
Username: admin
Password: admin123
```

## 🎨 Teknologi

- **Backend:** PHP 7.4+ dengan Session Storage
- **Frontend:** HTML5, Tailwind CSS 3.x
- **Icons:** Font Awesome 6.4
- **Charts:** Chart.js
- **Storage:** PHP Session (No Database)

## 📊 Data Dummy

### Admin
```php
Username: admin
Password: admin123 (hashed with bcrypt)
Nama: Administrator
Email: admin@rehamfutsal.com
Role: Super Admin
```

### Member (2 users)
1. Ahmad Rizki - ahmad.rizki@email.com
2. Budi Santoso - budi.santoso@email.com

### Booking (3 bookings)
- Status: Dikonfirmasi, Menunggu Konfirmasi, Ditolak
- Payment methods: Bank Transfer, E-Wallet, Cash

## ⚙️ Session Management

### Admin Session Variables
```php
$_SESSION['admin_id']          // ID admin
$_SESSION['admin_username']    // Username admin
$_SESSION['admin_nama']        // Nama lengkap
$_SESSION['admin_email']       // Email admin
$_SESSION['admin_role']        // Role admin
$_SESSION['admin_foto']        // URL foto profil
$_SESSION['admin_login_time']  // Waktu login
```

### Data Arrays
```php
$_SESSION['admins']    // Data admin
$_SESSION['users']     // Data member
$_SESSION['bookings']  // Data booking
$_SESSION['lapangan']  // Data lapangan
$_SESSION['promo']     // Data promo
```

## 🚀 Cara Menggunakan

### 1️⃣ Akses Admin Panel
```
http://localhost/reham-futsal/admin/login.php
```

### 2️⃣ Login
- Masukkan username: `admin`
- Masukkan password: `admin123`
- Klik "Masuk ke Dashboard"

### 3️⃣ Navigasi
- **Dashboard** - Lihat statistik & ringkasan
- **Booking** - Kelola booking pelanggan
- **Member** - Kelola data member
- **Logout** - Keluar dari admin panel

## 📱 Fitur Detail

### Dashboard Features
- ✨ Real-time statistics cards dengan animasi hover
- 📊 2 Chart interaktif (Line & Doughnut)
- 📅 List booking hari ini dengan status color-coded
- ⚡ Quick actions untuk navigasi cepat
- 🕐 Live clock dengan format HH:MM:SS
- 👥 Widget member terbaru (top 3)

### Booking Management Features
- 📋 Table view dengan pagination-ready
- 🎨 Status badges dengan color coding:
  - 🟡 Yellow - Menunggu Konfirmasi
  - 🟢 Green - Dikonfirmasi
  - 🔴 Red - Ditolak
  - 🔵 Blue - Selesai
- 🔍 Multi-filter (All, Pending, Confirmed, Rejected)
- 🔎 Search by ID, member, atau lapangan
- ✅ Quick confirm/reject actions
- 👁️ Detail modal dengan informasi lengkap
- 🗑️ Delete dengan konfirmasi

### Member Management Features
- 🎴 Card view yang menarik dengan avatar
- ➕ Form tambah member dengan validasi
- 📊 Statistics cards (Total, Active, Inactive, Total Booking)
- 🔍 Filter by status
- 🔎 Search by nama, email, atau telepon
- 🔄 Toggle status active/inactive
- 👁️ Detail modal dengan info lengkap
- 🗑️ Delete dengan konfirmasi
- 📱 Responsive grid layout

## 🎨 Desain & Animasi

### Color Scheme
- **Primary:** Red (#ef4444) - untuk sidebar & branding
- **Secondary:** Pink (#ec4899) - untuk gradients
- **Success:** Green (#22c55e)
- **Warning:** Yellow (#fbbf24)
- **Info:** Blue (#3b82f6)
- **Danger:** Red (#ef4444)

### Animations
- ✨ Fade in on page load
- 📊 Hover effects pada cards
- 🎯 Smooth transitions
- 💫 Chart animations
- 🌊 Gradient backgrounds

### Components
- 📊 Stat cards dengan icons
- 📋 Responsive tables
- 🎴 Member cards
- 🔘 Action buttons
- 🔔 Alert notifications
- 📱 Modals
- 🎨 Badges & tags

## 🔒 Security Features

### Implemented
- ✅ Session-based authentication
- ✅ Password hashing (bcrypt)
- ✅ Login validation
- ✅ Access control (cek login)
- ✅ XSS prevention (htmlspecialchars)
- ✅ CSRF-ready structure

### Not Implemented (Demo Purposes)
- ❌ Database
- ❌ Email notifications
- ❌ Two-factor authentication
- ❌ Rate limiting
- ❌ Audit logging

## 📈 Roadmap Development

### Phase 1 - Core Features ✅
- [x] Login system
- [x] Dashboard with stats
- [x] Booking management
- [x] Member management
- [x] Logout functionality

### Phase 2 - Enhancement (Future)
- [ ] Lapangan management
- [ ] Financial reports
- [ ] Promo management
- [ ] Settings & configuration
- [ ] Export data (PDF, Excel)
- [ ] Email notifications
- [ ] WhatsApp integration
- [ ] Activity log
- [ ] Advanced analytics
- [ ] Real database integration

## 🛠️ Troubleshooting

### Problem: Session hilang
**Solution:** Data dummy akan auto-reinitialize saat session kosong

### Problem: Login gagal
**Solution:** 
- Pastikan username: `admin`
- Pastikan password: `admin123`
- Check browser cookies enabled

### Problem: Data tidak muncul
**Solution:** Refresh halaman, session akan auto-populate data dummy

## 📞 Support

**Lokasi:** Jl. Ulin Utara 2 No. 320, Semarang, Jawa Tengah  
**Admin Panel:** http://localhost/reham-futsal/admin/  
**User Panel:** http://localhost/reham-futsal/

## 🎯 Best Practices

### Admin Workflow
1. Login ke admin panel
2. Check dashboard untuk overview
3. Handle pending bookings
4. Manage members jika perlu
5. Review statistics
6. Logout saat selesai

### Data Management
- Konfirmasi booking secepat mungkin
- Keep member data up-to-date
- Review statistics regularly
- Monitor pending bookings

## ⚠️ Limitations

1. **No Database** - Data hilang saat session clear
2. **Single Admin** - Tidak ada multi-admin management
3. **No Persistence** - Data tidak tersimpan permanen
4. **Demo Only** - Tidak production-ready
5. **No Email** - Notifikasi hanya simulasi

## 💡 Tips & Tricks

### Quick Navigation
- Gunakan sidebar untuk navigasi cepat
- Dashboard menampilkan info penting
- Pending bookings highlighted

### Efficient Management
- Gunakan filter untuk sorting data
- Search untuk cari data spesifik
- Quick actions untuk proses cepat

### Statistics
- Dashboard charts update real-time
- Monitor pending bookings
- Track member growth

## 🏆 Credits

**Developer:** AI Assistant  
**Framework:** PHP Native + Tailwind CSS  
**Version:** 1.0.0 (Admin Demo)  
**Year:** 2025

---

## 📝 Changelog

**v1.0.0** (2025-01-XX)
- ✨ Initial admin panel release
- 📊 Dashboard with statistics & charts
- 📅 Booking management system
- 👥 Member management system
- 🔐 Login & authentication
- 🎨 Modern UI with Tailwind CSS
- 📱 Responsive design
- ⚡ Quick actions
- 🔍 Search & filter
- 📈 Real-time charts

---

**© 2025 Reham Futsal Admin Panel. All Rights Reserved.**


Made with ❤️ for demo purposes
