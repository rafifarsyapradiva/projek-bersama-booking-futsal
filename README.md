# ðŸŸ REHAM FUTSAL - Sistem Booking Lapangan Futsal

## ðŸ“‹ Deskripsi Proyek
Website booking lapangan futsal modern dengan fitur lengkap, tampilan menarik, dan sistem manajemen berbasis PHP Session (tanpa database).

## ðŸŽ¯ Fitur Utama

### âœ… Fitur Dasar (8 File Pertama)
1. *index.php* - Halaman utama dengan hero section, daftar lapangan, testimoni, lokasi
2. *user/login.php* - Login dengan validasi & toggle password
3. *user/daftar.php* - Registrasi user dengan validasi lengkap
4. *user/dashboard.php* - Dashboard dengan statistik, profil, recent bookings
5. *user/booking.php* - Form booking dengan validasi bentrok, promo, real-time calculator
6. *user/booking_history.php* - Riwayat booking dengan filter & cancel booking
7. *user/lapangan.php* - Daftar lapangan & harga dengan paket berlangganan
8. *user/jadwal.php* - Jadwal real-time dengan color coding status

### ðŸš€ Fitur Prioritas Demo (5 File Baru)
9. *user/profile_edit.php* - Edit profil dengan:
   - Update data pribadi (nama, telepon, alamat)
   - Ubah password dengan validasi
   - Ganti avatar (8 pilihan warna)
   - Password strength indicator
   - Zona bahaya (delete account)

10. *user/payment.php* - Simulasi pembayaran dengan:
    - 4 metode pembayaran (Transfer Bank, E-Wallet, QRIS, Cash)
    - Detail rekening bank
    - Generate QR Code
    - Upload bukti transfer
    - Order summary

11. *user/member_card.php* - Kartu member digital dengan:
    - 4 Level member (Bronze, Silver, Gold, Platinum)
    - Progress bar ke level berikutnya
    - QR Code member untuk check-in
    - Benefits per level
    - Statistik lengkap
    - Card 3D effect dengan hover animation

12. *user/promo.php* - Promo & voucher dengan:
    - Daftar promo tersedia
    - Claim voucher system
    - My vouchers (diklaim user)
    - Filter & search
    - Copy kode promo
    - Tutorial penggunaan
    - Syarat & ketentuan

13. *user/notification.php* - Pusat notifikasi dengan:
    - 5 tipe notifikasi (Welcome, Promo, Reminder, Info, Reward)
    - Filter by category
    - Mark as read / unread
    - Delete notification
    - Bell animation
    - Real-time counter

## ðŸ“ Struktur Folder


reham-futsal/
â”‚
â”œâ”€â”€ index.php                    # Halaman utama
â”‚
â””â”€â”€ user/
    â”œâ”€â”€ login.php               # Login
    â”œâ”€â”€ daftar.php              # Registrasi
    â”œâ”€â”€ dashboard.php           # Dashboard user
    â”œâ”€â”€ profile_edit.php        # Edit profil â­
    â”œâ”€â”€ booking.php             # Form booking
    â”œâ”€â”€ booking_history.php     # Riwayat booking
    â”œâ”€â”€ payment.php             # Pembayaran â­
    â”œâ”€â”€ lapangan.php            # Daftar lapangan
    â”œâ”€â”€ jadwal.php              # Jadwal real-time
    â”œâ”€â”€ member_card.php         # Kartu member â­
    â”œâ”€â”€ promo.php               # Promo & voucher â­
    â””â”€â”€ notification.php        # Notifikasi â­


â­ = Fitur baru prioritas demo

## ðŸ’¾ Data Dummy

### User Demo

Email: ahmad.rizki@email.com
Password: password123


### Lapangan (5)
- *Lapangan Futsal 1, 2, 3* (Vinyl - Rp 50.000/jam)
- *Lapangan Rumput 1, 2* (Rumput Sintetis - Rp 100.000/jam)

### Kode Promo (2)
- *WELCOME10* - Diskon 10% (Min. Rp 50.000)
- *WEEKEND20* - Diskon 20% (Min. Rp 100.000)

### Member Level
- *Bronze* (0-4 booking) - Diskon 5%
- *Silver* (5-9 booking) - Diskon 10%
- *Gold* (10-19 booking) - Diskon 15%
- *Platinum* (20+ booking) - Diskon 20%

## ðŸŽ¨ Teknologi

- *Backend:* PHP 7.4+ dengan Session Storage
- *Frontend:* HTML5, Tailwind CSS 3.x
- *Icons:* Font Awesome 6.4
- *Maps:* Google Maps Embed
- *QR Code:* QR Server API
- *Avatar:* UI Avatars API

## âš™ Fitur Teknis

### Session Management
php
$_SESSION['users']           // Data users
$_SESSION['lapangan']        // Data lapangan
$_SESSION['bookings']        // Data booking
$_SESSION['promo']           // Data promo
$_SESSION['user_vouchers']   // Voucher user
$_SESSION['notifications']   // Notifikasi


### Validasi
- âœ… Email format & unique check
- âœ… Password min 6 karakter + strength indicator
- âœ… Telepon format (10-13 digit)
- âœ… Tanggal tidak boleh masa lalu
- âœ… Jam bentrok detection
- âœ… Durasi min 1 jam, max 8 jam
- âœ… Kode promo validation

### Security
- âœ… Password hashing (bcrypt)
- âœ… CSRF protection ready
- âœ… XSS prevention (htmlspecialchars)
- âœ… SQL injection safe (no database)
- âœ… Session timeout ready

## ðŸŽ¯ Alur Penggunaan

### 1ï¸âƒ£ Registrasi & Login

1. Buka index.php
2. Klik "Daftar" atau gunakan akun demo
3. Login dengan kredensial
4. Redirect ke dashboard


### 2ï¸âƒ£ Booking Lapangan

1. Dashboard â†’ Booking
2. Pilih lapangan
3. Pilih tanggal & jam
4. Masukkan kode promo (opsional)
5. Konfirmasi booking
6. Redirect ke payment (simulasi)


### 3ï¸âƒ£ Claim Voucher

1. Menu â†’ Promo
2. Pilih promo â†’ Klaim voucher
3. Voucher masuk ke "Voucher Saya"
4. Gunakan saat booking


### 4ï¸âƒ£ Lihat Member Card

1. Menu â†’ Member Card
2. Lihat level & progress
3. Scan QR Code untuk check-in
4. Lihat benefits per level


### 5ï¸âƒ£ Edit Profil

1. Dashboard â†’ Edit Profil
2. Update data pribadi
3. Ubah password
4. Ganti avatar


## ðŸŽ¨ Desain & Animasi

### Warna Tema
- *Primary:* Green (#10b981)
- *Secondary:* Blue (#3b82f6)
- *Accent:* Purple (#a855f7)
- *Warning:* Orange (#f97316)

### Animasi
- âœ¨ Fade in / Slide in
- ðŸŽ¯ Hover scale effects
- ðŸ”” Bell shake animation
- ðŸŽ´ Card 3D tilt effect
- ðŸ’« Shine overlay
- ðŸŽª Bounce animation

### Responsive
- ðŸ“± Mobile First
- ðŸ’» Tablet optimized
- ðŸ–¥ Desktop enhanced

## ðŸ“Š Statistik Dashboard


â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚  Total Booking      â”‚   10  â”‚
â”‚  Booking Aktif      â”‚    2  â”‚
â”‚  Selesai            â”‚    7  â”‚
â”‚  Total Pengeluaran  â”‚ 500K  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜


## ðŸ”” Tipe Notifikasi

1. *Welcome* - Selamat datang user baru
2. *Promo* - Info promo & diskon
3. *Reminder* - Pengingat booking
4. *Info* - Update fasilitas/sistem
5. *Reward* - Poin & achievement

## ðŸŽ Benefit Member

### Bronze
- Diskon 5%
- Point 1x
- Support prioritas

### Silver (5+ booking)
- Diskon 10%
- Point 1.5x
- Free minuman 1x/bulan

### Gold (10+ booking)
- Diskon 15%
- Point 2x
- Free minuman 2x/bulan
- Priority booking

### Platinum (20+ booking)
- Diskon 20%
- Point 3x
- Free minuman unlimited
- Akses lapangan rumput dengan harga biasa
- Voucher ulang tahun

## ðŸš€ Cara Install & Run

### Requirement
- PHP 7.4+
- Web Server (Apache/Nginx)
- Browser modern

### Langkah-langkah
bash
# 1. Clone/Download project
# 2. Pindahkan ke folder htdocs/www
# 3. Akses via browser
http://localhost/reham-futsal/

# 4. Login dengan akun demo
Email: ahmad.rizki@email.com
Password: password123


## ðŸ“ˆ Roadmap Development

### Phase 1 - Core Features âœ…
- [x] User authentication
- [x] Booking system
- [x] Payment simulation
- [x] Member card
- [x] Promo system
- [x] Notification center

### Phase 2 - Enhancement (Future)
- [ ] Real database integration
- [ ] Email notification
- [ ] WhatsApp integration
- [ ] Live chat support
- [ ] Tournament system
- [ ] Leaderboard
- [ ] Review & rating
- [ ] Mobile app

## ðŸ› Known Issues & Limitations

1. *Data tidak persisten* - Reset saat session habis/clear
2. *No email sending* - Hanya simulasi
3. *No file upload* - Avatar dari API
4. *Single user session* - 1 device per user
5. *No real payment* - Hanya simulasi

## ðŸ’¡ Tips & Tricks

### Best Practices
- Selalu gunakan kode promo untuk hemat
- Booking H-1 untuk jam prime time
- Claim voucher sebelum expired
- Check notification untuk promo terbaru
- Upgrade level untuk benefit lebih

### Troubleshooting
- *Session hilang?* - Data dummy auto-reinitialize
- *Booking bentrok?* - Pilih jam lain
- *Promo tidak berlaku?* - Check min. transaksi
- *Voucher sudah diklaim?* - Check "Voucher Saya"

## ðŸ“ž Kontak & Support

*Lokasi:* Jl. Ulin Utara 2 No. 320, Semarang, Jawa Tengah
*Telepon:* +62 812-3456-7890
*Email:* info@rehamfutsal.com
*Jam Operasional:* 06:00 - 22:00 WIB (Setiap Hari)

---

## ðŸ† Credits

*Developer:* AI Assistant
*Framework:* PHP Native + Tailwind CSS
*Version:* 1.0.0 (Demo)
*Year:* 2025

---

### ðŸ“ Changelog

*v1.0.0* (2025-01-XX)
- âœ¨ Initial release
- âœ… 13 halaman lengkap
- ðŸŽ¨ Modern UI/UX
- ðŸ’¾ Session-based storage
- ðŸ”” Notification system
- ðŸŽ Promo & voucher
- ðŸ’³ Payment simulation
- ðŸŽ´ Member card digital

---

*Â© 2025 Reham Futsal. All Rights Reserved.*

Made with â¤ for demo purposes