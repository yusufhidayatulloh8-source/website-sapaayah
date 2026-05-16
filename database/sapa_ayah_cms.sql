-- =====================================================
-- DATABASE: sapa_ayah_cms
-- =====================================================
CREATE DATABASE IF NOT EXISTS sapa_ayah_cms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sapa_ayah_cms;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS donations;
DROP TABLE IF EXISTS testimonials;
DROP TABLE IF EXISTS galleries;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS program_registrations;
DROP TABLE IF EXISTS programs;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS remember_tokens;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    permission_key VARCHAR(100) NOT NULL UNIQUE,
    label VARCHAR(150) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE role_permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_role_permission (role_id, permission_id),
    CONSTRAINT fk_role_permissions_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    gender ENUM('male','female') DEFAULT NULL,
    birth_place VARCHAR(100) DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    address TEXT DEFAULT NULL,
    profile_photo VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    status ENUM('active','inactive','blocked') NOT NULL DEFAULT 'active',
    last_login_at DATETIME DEFAULT NULL,
    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE remember_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_remember_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_remember_token (token)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_password_reset_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_reset_token (token)
) ENGINE=InnoDB;

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED DEFAULT NULL,
    author_id BIGINT UNSIGNED DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    thumbnail VARCHAR(255) DEFAULT NULL,
    excerpt TEXT DEFAULT NULL,
    content LONGTEXT NOT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at DATETIME DEFAULT NULL,
    views INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_articles_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_articles_author FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE programs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    thumbnail VARCHAR(255) DEFAULT NULL,
    short_description TEXT DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    schedule_info TEXT DEFAULT NULL,
    gallery_json LONGTEXT DEFAULT NULL,
    registration_link VARCHAR(255) DEFAULT NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE program_registrations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    program_id BIGINT UNSIGNED NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(30) NOT NULL,
    city VARCHAR(120) DEFAULT NULL,
    motivation TEXT DEFAULT NULL,
    status ENUM('pending','contacted','accepted','cancelled') NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_program_registrations_program (program_id),
    INDEX idx_program_registrations_status (status),
    CONSTRAINT fk_program_registrations_program FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(190) NOT NULL UNIQUE,
    thumbnail VARCHAR(255) DEFAULT NULL,
    description LONGTEXT DEFAULT NULL,
    event_date DATE NOT NULL,
    event_time VARCHAR(50) DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    gmap_embed TEXT DEFAULT NULL,
    video_url VARCHAR(255) DEFAULT NULL,
    status ENUM('upcoming','completed','cancelled') NOT NULL DEFAULT 'upcoming',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE galleries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(180) NOT NULL,
    media_type ENUM('photo','video') NOT NULL DEFAULT 'photo',
    file_path VARCHAR(255) DEFAULT NULL,
    video_url VARCHAR(255) DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE testimonials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    role_or_job VARCHAR(150) DEFAULT NULL,
    photo VARCHAR(255) DEFAULT NULL,
    testimonial TEXT NOT NULL,
    rating TINYINT UNSIGNED NOT NULL DEFAULT 5,
    is_featured TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE donations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    donor_name VARCHAR(150) NOT NULL,
    donor_email VARCHAR(150) DEFAULT NULL,
    donor_phone VARCHAR(30) DEFAULT NULL,
    amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    transfer_date DATE DEFAULT NULL,
    bank_name VARCHAR(120) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    proof_file VARCHAR(255) DEFAULT NULL,
    status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE contacts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    subject VARCHAR(200) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    article_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    name VARCHAR(150) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    comment TEXT NOT NULL,
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_comments_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(120) NOT NULL UNIQUE,
    setting_value LONGTEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    action VARCHAR(120) NOT NULL,
    entity VARCHAR(120) DEFAULT NULL,
    entity_id BIGINT DEFAULT NULL,
    meta LONGTEXT DEFAULT NULL,
    ip_address VARCHAR(64) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO roles (id, name, description) VALUES
(1, 'admin', 'Akses penuh seluruh sistem'),
(2, 'user', 'Akses terbatas sesuai permission')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO permissions (permission_key, label) VALUES
('dashboard.view', 'Lihat Dashboard'),
('users.view', 'Lihat User'),
('users.create', 'Tambah User'),
('users.edit', 'Edit User'),
('users.delete', 'Hapus User'),
('articles.view', 'Lihat Artikel'),
('articles.create', 'Tambah Artikel'),
('articles.edit', 'Edit Artikel'),
('articles.delete', 'Hapus Artikel'),
('events.view', 'Lihat Event'),
('events.create', 'Tambah Event'),
('events.edit', 'Edit Event'),
('events.delete', 'Hapus Event'),
('programs.view', 'Lihat Program'),
('programs.create', 'Tambah Program'),
('programs.edit', 'Edit Program'),
('programs.delete', 'Hapus Program'),
('galleries.view', 'Lihat Galeri'),
('galleries.create', 'Tambah Galeri'),
('galleries.edit', 'Edit Galeri'),
('galleries.delete', 'Hapus Galeri'),
('testimonials.view', 'Lihat Testimoni'),
('testimonials.create', 'Tambah Testimoni'),
('testimonials.edit', 'Edit Testimoni'),
('testimonials.delete', 'Hapus Testimoni'),
('donations.view', 'Lihat Donasi'),
('donations.edit', 'Verifikasi Donasi'),
('settings.manage', 'Kelola Pengaturan')
ON DUPLICATE KEY UPDATE label = VALUES(label);

INSERT INTO role_permissions (role_id, permission_id)
SELECT 2, p.id
FROM permissions p
WHERE p.permission_key IN (
    'dashboard.view',
    'articles.view','articles.create','articles.edit',
    'events.view','events.create','events.edit',
    'programs.view','programs.create','programs.edit',
    'galleries.view','galleries.create','galleries.edit',
    'testimonials.view','testimonials.create','testimonials.edit',
    'donations.view'
)
ON DUPLICATE KEY UPDATE created_at = VALUES(created_at);

INSERT INTO categories (name, slug, description) VALUES
('Pengasuhan', 'pengasuhan', 'Tips dan wawasan pengasuhan ayah'),
('Keluarga', 'keluarga', 'Relasi hangat dalam keluarga'),
('Kesehatan Mental', 'kesehatan-mental', 'Pemulihan dan ketahanan emosi')
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- Default admin password: Admin@12345
INSERT INTO users (
    role_id, full_name, username, email, password, phone, gender, birth_place, birth_date,
    address, bio, status, registered_at, created_at, updated_at
) VALUES (
    1, 'Administrator SAPA Ayah', 'admin', 'admin@sapaayah.or.id',
    '$2y$10$QWZp0Q8PdT9tu.5eYSf4WeO09p5yVXSLHir20gr3b9FoOjT6Mi2lW',
    '081234567890', 'male', 'Jakarta', '1990-01-01',
    'Sekretariat Yayasan SAPA Ayah', 'Administrator sistem SAPA Ayah', 'active', NOW(), NOW(), NOW()
)
ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at);

INSERT INTO programs (title, slug, short_description, description, schedule_info, registration_link, is_featured, status) VALUES
('SAPA Journey', 'sapa-journey', 'Perjalanan belajar ayah bertahap dan terstruktur.', 'Program pembinaan berkelanjutan untuk meningkatkan kapasitas ayah dalam pengasuhan, komunikasi, dan kepemimpinan keluarga.', 'Setiap Sabtu, 09.00 - 11.00 WIB', '#', 1, 'active'),
('SAPA Start', 'sapa-start', 'Program awal untuk ayah muda.', 'Pengenalan dasar pengasuhan ayah berbasis empati, kedekatan, dan komunikasi efektif.', 'Minggu ke-2 setiap bulan', '#', 1, 'active'),
('SAPA Grow', 'sapa-grow', 'Pendampingan peningkatan kualitas relasi keluarga.', 'Fokus pada pertumbuhan karakter ayah, manajemen emosi, dan disiplin positif.', 'Setiap Rabu, 19.30 WIB', '#', 1, 'active'),
('SAPA Lead', 'sapa-lead', 'Kepemimpinan ayah dalam keluarga dan komunitas.', 'Pelatihan leadership untuk ayah agar menjadi teladan positif di rumah dan lingkungan.', 'Bulanan', '#', 0, 'active'),
('SAPA Mentor', 'sapa-mentor', 'Program mentoring antar ayah.', 'Sesi mentoring peer-to-peer untuk saling menguatkan dan berbagi praktik baik.', 'Dua minggu sekali', '#', 0, 'active'),
('SAPA Class', 'sapa-class', 'Kelas tematik pengasuhan.', 'Kelas topikal: komunikasi, kedisiplinan, pendidikan karakter, dan manajemen konflik.', 'Sesuai kalender', '#', 1, 'active'),
('SAPA Camp', 'sapa-camp', 'Family camp untuk ayah dan anak.', 'Aktivitas experiential learning untuk mempererat bonding ayah-anak.', 'Triwulanan', '#', 1, 'active'),
('SAPA Stories', 'sapa-stories', 'Cerita inspiratif ayah.', 'Kanal berbagi kisah transformasi dan pembelajaran nyata para ayah.', 'Konten mingguan', '#', 0, 'active'),
('SAPA Talk', 'sapa-talk', 'Talkshow bersama narasumber ahli.', 'Diskusi interaktif bersama praktisi keluarga, psikolog, dan komunitas ayah.', 'Bulanan', '#', 0, 'active'),
('SAPA Bond', 'sapa-bond', 'Penguatan ikatan ayah-anak.', 'Program aktivitas sederhana namun berdampak untuk membangun kelekatan.', 'Mingguan', '#', 0, 'active'),
('SAPA Hobby', 'sapa-hobby', 'Komunitas hobi ayah.', 'Ruang bertemu, berjejaring, dan membangun komunitas sehat lewat hobi positif.', 'Sesuai komunitas', '#', 0, 'active'),
('SAPA Recovery', 'sapa-recovery', 'Pemulihan ayah pasca krisis.', 'Pendampingan emosional dan spiritual untuk ayah yang sedang memulihkan diri.', 'Sesuai kebutuhan', '#', 1, 'active')
ON DUPLICATE KEY UPDATE short_description = VALUES(short_description);

INSERT INTO testimonials (name, role_or_job, testimonial, rating, is_featured) VALUES
('Fadli Rahman', 'Peserta SAPA Class', 'Program SAPA Ayah membuat saya lebih dekat dengan anak dan lebih sabar dalam mengasuh.', 5, 1),
('Rizki Maulana', 'Peserta SAPA Journey', 'Materi praktis dan mentor sangat suportif. Perubahan terasa di rumah.', 5, 1),
('Hendra Saputra', 'Relawan Komunitas', 'Komunitas yang hangat, profesional, dan benar-benar membangun kualitas ayah.', 5, 1)
ON DUPLICATE KEY UPDATE testimonial = VALUES(testimonial);

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Yayasan SAPA Ayah'),
('site_tagline', 'Menyapa untuk Tumbuh, Tumbuh untuk Menyapa'),
('site_email', 'info@sapaayah.or.id'),
('site_phone', '+62 812-3456-7890'),
('site_address', 'Jakarta, Indonesia'),
('hero_video_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ'),
('bank_account', 'BCA 1234567890 a.n Yayasan SAPA Ayah'),
('qris_image', ''),
('whatsapp_number', '6281234567890'),
('instagram_url', 'https://instagram.com'),
('facebook_url', 'https://facebook.com'),
('youtube_url', 'https://youtube.com')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO articles (category_id, author_id, title, slug, excerpt, content, status, published_at)
SELECT c.id, u.id,
'Peran Ayah dalam Tumbuh Kembang Anak',
'peran-ayah-dalam-tumbuh-kembang-anak',
'Kontribusi ayah berdampak besar pada perkembangan emosi, sosial, dan kognitif anak.',
'<p>Ayah memiliki peran sentral sebagai figur aman, pemberi arah, dan sumber kehangatan dalam keluarga. Keterlibatan ayah yang konsisten membantu anak membangun kepercayaan diri dan regulasi emosi yang lebih baik.</p><p>Melalui pendekatan SAPA Ayah, para ayah didampingi untuk hadir secara utuh, bukan hanya fisik tetapi juga emosional.</p>',
'published', NOW()
FROM categories c, users u
WHERE c.slug = 'pengasuhan' AND u.username = 'admin'
LIMIT 1;
