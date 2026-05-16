# Yayasan SAPA Ayah - Company Profile & CMS

Website company profile + sistem manajemen konten (CMS) berbasis:
- HTML5
- CSS3
- PHP Native (tanpa framework)
- MySQL
- JavaScript (ringan)

## Struktur Folder

- `assets/` CSS, JS, image brand
- `uploads/` file upload user/konten
- `config/` konfigurasi aplikasi & database
- `includes/` core (auth, security, csrf, db, middleware)
- `admin/` entry admin panel + layout partials
- `auth/` login/register/forgot/reset/logout
- `modules/` modul frontend dan backend
- `templates/` template reusable + error pages
- `database/` SQL schema + seed
- `api/` endpoint JSON sederhana
- `logs/` log aplikasi

## Instalasi (XAMPP)

1. Pastikan project ada di:
   - `C:\xampp\htdocs\sapa`
2. Buat database dengan import file:
   - `database/sapa_ayah_cms.sql`
3. Sesuaikan kredensial DB di:
   - `config/database.php`
4. Sesuaikan URL aplikasi di:
   - `config/app.php`
   - `app_url` default: `http://localhost/sapa`
5. Jalankan Apache dan MySQL.
6. Buka frontend:
   - `http://localhost/sapa`
7. Buka admin:
   - `http://localhost/sapa/auth/login.php`

## Akun Default Admin

- Username: `admin`
- Email: `admin@sapaayah.or.id`
- Password: `Admin@12345`

## Fitur Frontend

- Home (hero, statistik, program, testimoni, video, partner)
- Tentang Kami (profil, visi, misi, nilai, timeline, struktur)
- Program + detail tiap program
- Event & Kegiatan + detail
- Artikel/Blog + kategori + search + related + komentar
- Gallery foto/video + lightbox
- Testimoni
- Kontak + form + maps + WA floating
- Donasi + rekening + konfirmasi donasi

## Fitur Admin

- Dashboard statistik + chart interaksi
- CRUD user + role assignment
- CRUD kategori artikel
- CRUD artikel
- CRUD event
- CRUD program
- CRUD gallery
- CRUD testimoni
- Verifikasi donasi
- Inbox kontak
- Moderasi komentar
- Pengaturan website
- Log aktivitas
- Edit profile + ganti password

## Keamanan yang Diimplementasikan

- Enkripsi parameter ID di URL (AES/OpenSSL)
- Password hashing (`password_hash`, `password_verify`)
- SQL Injection protection (PDO prepared statements)
- XSS protection (`htmlspecialchars`, sanitasi input)
- CSRF token untuk seluruh form POST
- Session timeout + regenerate session ID
- Remember me token hashing
- Upload validation (mime, size, rename otomatis)
- Auth middleware (cek login + role/permission)
- Custom error pages (403/404/419/500)
- Logging error + activity log
- Upload folder hardening (`uploads/.htaccess`)

## Catatan Branding & Design

- Warna utama: biru lembut + putih sesuai identitas SAPA Ayah
- Style: modern, clean, warm, professional
- Responsive: mobile, tablet, desktop
- Animasi ringan + smooth scrolling
- Typography modern dan mudah dibaca

## Catatan Figma

Saya mengimplementasikan UI premium yang konsisten dengan brief SAPA Ayah. Jika Anda ingin pixel-perfect terhadap file Figma tertentu, kirimkan export frame (PNG/PDF/spec spacing) karena URL Figma yang diberikan tidak bisa diakses langsung dari environment ini.

## Pengembangan Lanjutan yang Disarankan

- Integrasi SMTP untuk email reset password otomatis
- Integrasi SweetAlert2/Toast library real-time
- Editor WYSIWYG untuk artikel
- Audit permission matrix lebih granular
- Unit test + integration test
- Deploy hardening HTTPS-only cookies (`secure=true`)
