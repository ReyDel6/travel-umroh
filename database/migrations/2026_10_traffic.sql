-- ============================================================
-- Migrasi: Fitur Analitik Traffic Pengunjung
-- Spec: docs/SPEC-TRAFFIC-ANALYTICS.md
-- Membuat 2 tabel: visitor_logs (log per kunjungan, debounce 30
-- menit per IP) & visitor_daily_stats (agregat harian utk grafis).
-- Idempotent: aman dijalankan berulang.
-- ============================================================

CREATE TABLE IF NOT EXISTS visitor_logs (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS visitor_daily_stats (
    tanggal DATE PRIMARY KEY,
    total_hits INT UNSIGNED NOT NULL DEFAULT 0,
    unique_visitors INT UNSIGNED NOT NULL DEFAULT 0,
    top_country_code VARCHAR(3) NULL,
    top_browser VARCHAR(50) NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;