<?php
declare(strict_types=1);

/* =========================================================
 * Helper umum: output escaping, URL, flash, CSRF, format
 * =======================================================*/

/** Escape output (XSS). */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Bangun URL absolut sesuai BASE_URL. url('paket') => '/paket' */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** URL aset statis. */
function asset(string $path): string
{
    return url('public/assets/' . ltrim($path, '/'));
}

/** URL absolut lengkap untuk SEO / canonical. */
function full_url(string $path = ''): string
{
    $scheme = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443')) ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = trim(BASE_URL, '/');
    $relative = ltrim($path, '/');
    if ($relative === '') {
        $relative = '';
    }
    if ($base !== '') {
        $relative = $base . '/' . ltrim($relative, '/');
    }
    $relative = '/' . ltrim($relative, '/');
    return $scheme . '://' . $host . $relative;
}

/** URL file upload. */
function upload_url(?string $file, string $modul): string
{
    if (!$file) {
        return asset('img/placeholder.svg');
    }
    if (preg_match('#^https?://#i', $file)) {
        return $file;
    }
    return url('public/uploads/' . trim($modul, '/') . '/' . ltrim($file, '/'));
}

/** Terapkan meta halaman dari CMS (tabel meta_halaman) bila diisi; fallback halaman dipertahankan bila kosong. */
function meta_halaman_apply(string $kode, string &$metaTitle, string &$metaDescription): void
{
    $cms = model('meta')->getByKode($kode);
    if (!$cms) {
        return;
    }
    if (trim((string) $cms['meta_title']) !== '') {
        $metaTitle = $cms['meta_title'];
    }
    if (trim((string) $cms['meta_description']) !== '') {
        $metaDescription = $cms['meta_description'];
    }
}

function redirect(string $path): never
{
    header('Location: ' . (preg_match('#^https?://#i', $path) ? $path : url($path)));
    exit;
}

/** Simpan pesan flash (sekali tampil). */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/** CSRF token. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = (string) ($_POST['csrf_token'] ?? '');
    if ($token !== '' && hash_equals(csrf_token(), $token)) {
        return;
    }
    // Token kedaluwarsa/tidak valid: kembali ke halaman asal dengan pesan,
    // bukan menampilkan halaman teks polos tanpa layout.
    flash_set('error', 'Sesi keamanan Anda telah berakhir. Silakan coba lagi.');
    $back = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    header('Location: ' . ($back !== '' ? $back : url('')));
    exit;
}

/** Slugify untuk URL readable (SEO). */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text !== '' ? $text : 'item-' . substr(md5((string) microtime(true)), 0, 6);
}

/** Format rupiah: 28500000 => "Rp 28.500.000" */
function rupiah($number): string
{
    return 'Rp ' . number_format((float) $number, 0, ',', '.');
}

/** Format tanggal Indonesia. */
function tanggal(?string $date, string $format = 'd M Y'): string
{
    if (!$date) {
        return '-';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '-';
    }
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    if ($format === 'd M Y') {
        return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }
    if ($format === 'long') {
        $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $hari[(int) date('w', $ts)] . ', ' . date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }
    return date($format, $ts);
}

function excerpt(?string $text, int $length = 120): string
{
    $text = trim(strip_tags((string) $text));
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length)) . '…';
}

/** Render badge status ala design system. */
function badge(string $label, string $tone = 'teal'): string
{
    $tones = [
        'teal'  => 'bg-primary-container text-on-primary-container',
        'gold'  => 'bg-secondary-fixed text-on-secondary-fixed-variant',
        'gray'  => 'bg-surface-container-high text-on-surface-variant',
        'error' => 'bg-error-container text-on-error-container',
    ];
    $cls = $tones[$tone] ?? $tones['teal'];
    return '<span class="inline-flex items-center px-2 py-0.5 rounded text-label-sm font-label-sm font-semibold ' . $cls . '">' . e($label) . '</span>';
}

/** Pagination sederhana (query string tetap dipertahankan; jendela ±2 halaman + elipsis agar tidak meledak). */
function pagination(int $total, int $perPage, int $currentPage, string $basePath, array $extraQuery = []): string
{
    $totalPages = (int) ceil($total / $perPage);
    if ($totalPages <= 1) {
        return '';
    }
    $currentPage = max(1, min($currentPage, $totalPages));

    // Jendela tautan: halaman aktif ±2, selalu sertakan 1 dan terakhir
    $candidat = [1, $totalPages];
    for ($i = $currentPage - 2; $i <= $currentPage + 2; $i++) {
        if ($i >= 1 && $i <= $totalPages) {
            $candidat[] = $i;
        }
    }
    $candidat = array_values(array_unique($candidat));
    sort($candidat);

    $link = static function (int $i) use ($extraQuery, $basePath): string {
        $query = array_merge($extraQuery, ['halaman' => $i]);
        return e(url($basePath) . '?' . http_build_query($query));
    };

    $out = '<nav class="flex items-center justify-center gap-2 mt-space-xl" aria-label="Navigasi halaman">';
    $prev = 0;
    foreach ($candidat as $i) {
        if ($prev !== 0 && $i > $prev + 1) {
            $out .= '<span class="min-w-9 h-9 px-1 inline-flex items-center justify-center font-label-md text-label-md text-on-surface-variant" aria-hidden="true">&hellip;</span>';
        }
        $active = $i === $currentPage ? ' bg-primary text-white border-primary' : ' bg-white text-on-surface-variant border-outline-variant hover:border-primary hover:text-primary';
        $out .= '<a href="' . $link($i) . '" class="min-w-9 h-9 px-3 inline-flex items-center justify-center rounded border font-label-md text-label-md transition-colors' . $active . '"' . ($i === $currentPage ? ' aria-current="page"' : '') . '>' . $i . '</a>';
        $prev = $i;
    }
    $out .= '</nav>';
    return $out;
}

/** Ubah URL lokasi Google Maps menjadi URL embed (output=embed) agar tampil di <iframe> tanpa API key. */
function maps_embed_src(?string $url): string
{
    $url = (string) $url;
    if ($url === '' || str_contains($url, '/maps/embed')) {
        return $url;
    }
    $sep = str_contains($url, '?') ? '&' : '?';
    return $url . $sep . 'output=embed';
}
