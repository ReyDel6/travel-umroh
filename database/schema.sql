-- ============================================================
-- Database: travel_umroh
-- Website & CMS Travel Umroh (Sakinah Journeys)
-- Sesuai Technical Spec bagian 3
-- ============================================================

CREATE DATABASE IF NOT EXISTS travel_umroh
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE travel_umroh;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS inquiry;
DROP TABLE IF EXISTS faq;
DROP TABLE IF EXISTS artikel;
DROP TABLE IF EXISTS galeri;
DROP TABLE IF EXISTS paket_maskapai;
DROP TABLE IF EXISTS paket_hotel;
DROP TABLE IF EXISTS maskapai;
DROP TABLE IF EXISTS hotel;
DROP TABLE IF EXISTS paket_fasilitas;
DROP TABLE IF EXISTS fasilitas;
DROP TABLE IF EXISTS jadwal_keberangkatan;
DROP TABLE IF EXISTS harga_paket;
DROP TABLE IF EXISTS paket_umroh;
DROP TABLE IF EXISTS profil_perusahaan;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS testimoni;
DROP TABLE IF EXISTS paket_itinerary;
DROP TABLE IF EXISTS meta_halaman;
DROP TABLE IF EXISTS visitor_daily_stats;
DROP TABLE IF EXISTS visitor_logs;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE profil_perusahaan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_perusahaan VARCHAR(150),
    tagline VARCHAR(150),
    tentang_kami TEXT,
    alamat TEXT,
    telepon VARCHAR(60),
    email VARCHAR(100),
    jam_operasional VARCHAR(150),
    maps_embed TEXT,
    meta_title VARCHAR(160),
    meta_description VARCHAR(255),
    izin_ppiu VARCHAR(150),
    tahun_berdiri INT,
    stat_grup_maks INT,
    stat_rasio_pembimbing INT,
    stat_sesi_manasik INT
) ENGINE=InnoDB;

CREATE TABLE meta_halaman (
    kode VARCHAR(40) PRIMARY KEY,
    meta_title VARCHAR(160) NULL,
    meta_description VARCHAR(255) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE testimoni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    asal_kota VARCHAR(100) NULL,
    teks TEXT NOT NULL,
    urutan INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE paket_itinerary (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paket_id INT NOT NULL,
    hari VARCHAR(80) NOT NULL,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT NOT NULL,
    tag VARCHAR(80) NULL,
    is_highlight TINYINT(1) NOT NULL DEFAULT 0,
    urutan INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_itinerary_paket FOREIGN KEY (paket_id)
        REFERENCES paket_umroh(id) ON DELETE CASCADE,
    INDEX idx_itinerary_paket (paket_id)
) ENGINE=InnoDB;

CREATE TABLE paket_umroh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    slug VARCHAR(170) UNIQUE NOT NULL,
    deskripsi TEXT,
    durasi_hari INT,
    thumbnail VARCHAR(255),
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    meta_title VARCHAR(160),
    meta_description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE harga_paket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paket_id INT NOT NULL,
    tipe_kamar ENUM('quad','triple','double') NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (paket_id) REFERENCES paket_umroh(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE jadwal_keberangkatan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paket_id INT NOT NULL,
    tanggal_berangkat DATE NOT NULL,
    kuota INT NOT NULL,
    status ENUM('dibuka','penuh','ditutup') DEFAULT 'dibuka',
    FOREIGN KEY (paket_id) REFERENCES paket_umroh(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE fasilitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    ikon VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE paket_fasilitas (
    paket_id INT NOT NULL,
    fasilitas_id INT NOT NULL,
    PRIMARY KEY (paket_id, fasilitas_id),
    FOREIGN KEY (paket_id) REFERENCES paket_umroh(id) ON DELETE CASCADE,
    FOREIGN KEY (fasilitas_id) REFERENCES fasilitas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE hotel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    kota VARCHAR(100),
    kelas VARCHAR(50),
    gambar VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE maskapai (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    logo VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE paket_hotel (
    paket_id INT NOT NULL,
    hotel_id INT NOT NULL,
    PRIMARY KEY (paket_id, hotel_id),
    FOREIGN KEY (paket_id) REFERENCES paket_umroh(id) ON DELETE CASCADE,
    FOREIGN KEY (hotel_id) REFERENCES hotel(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE paket_maskapai (
    paket_id INT NOT NULL,
    maskapai_id INT NOT NULL,
    PRIMARY KEY (paket_id, maskapai_id),
    FOREIGN KEY (paket_id) REFERENCES paket_umroh(id) ON DELETE CASCADE,
    FOREIGN KEY (maskapai_id) REFERENCES maskapai(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE galeri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150),
    gambar VARCHAR(255) NOT NULL,
    kategori VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE artikel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    slug VARCHAR(220) UNIQUE NOT NULL,
    konten TEXT,
    thumbnail VARCHAR(255),
    status ENUM('publish','draft') DEFAULT 'draft',
    meta_title VARCHAR(160),
    meta_description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pertanyaan VARCHAR(255) NOT NULL,
    jawaban TEXT,
    urutan INT DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE inquiry (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kontak VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    paket_id INT NULL,
    pesan TEXT,
    status ENUM('baru','ditindaklanjuti') DEFAULT 'baru',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paket_id) REFERENCES paket_umroh(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------- Analitik Traffic (Spec: docs/SPEC-TRAFFIC-ANALYTICS.md) ----------
CREATE TABLE visitor_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255) NULL,
    browser VARCHAR(50) NOT NULL DEFAULT 'Other',
    os VARCHAR(50) NOT NULL DEFAULT 'Other',
    country_code VARCHAR(3) NOT NULL DEFAULT 'ID',
    country_name VARCHAR(100) NOT NULL DEFAULT 'Indonesia',
    page_url VARCHAR(255) NOT NULL,
    referer VARCHAR(255) NULL,
    visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_visited_at (visited_at),
    INDEX idx_ip_date (ip_address, visited_at),
    INDEX idx_browser (browser),
    INDEX idx_country (country_code)
) ENGINE=InnoDB;

CREATE TABLE visitor_daily_stats (
    tanggal DATE PRIMARY KEY,
    total_hits INT UNSIGNED NOT NULL DEFAULT 0,
    unique_visitors INT UNSIGNED NOT NULL DEFAULT 0,
    top_country_code VARCHAR(3) NULL,
    top_browser VARCHAR(50) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
