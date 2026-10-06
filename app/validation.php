<?php
declare(strict_types=1);

/* =========================================================
 * Validasi form reusable (admin & public)
 * Mengembalikan array errors: kosong = valid.
 * Aturan panjang mengikuti batas kolom di database/schema.sql
 * agar kelebihan input ditolak sebagai error field (bukan exception DB).
 * =======================================================*/

/** Tambah error jika nilai melebihi batas panjang kolom. */
function batas(array &$errors, array $i, string $field, int $maks, string $label): void
{
    $nilai = trim((string) ($i[$field] ?? ''));
    if ($nilai !== '' && mb_strlen($nilai) > $maks) {
        $errors[$field] = $label . ' maksimal ' . $maks . ' karakter.';
    }
}

function validate_paket(array $i): array
{
    $errors = [];
    if (trim($i['nama'] ?? '') === '') {
        $errors['nama'] = 'Nama paket wajib diisi.';
    }
    batas($errors, $i, 'nama', 150, 'Nama paket');
    batas($errors, $i, 'slug', 170, 'Slug');
    if (isset($i['slug']) && trim((string) $i['slug']) !== '' && !preg_match('/^[a-z0-9\-]+$/', trim((string) $i['slug']))) {
        $errors['slug'] = 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.';
    }
    if (!isset($i['durasi_hari']) || $i['durasi_hari'] === '' || !ctype_digit((string) $i['durasi_hari']) || (int) $i['durasi_hari'] < 1) {
        $errors['durasi_hari'] = 'Durasi hari wajib berupa angka lebih dari 0.';
    }
    if (trim($i['deskripsi'] ?? '') === '') {
        $errors['deskripsi'] = 'Deskripsi paket wajib diisi.';
    }
    if (isset($i['status']) && !in_array($i['status'], ['aktif', 'nonaktif'], true)) {
        $errors['status'] = 'Status tidak valid.';
    }
    batas($errors, $i, 'meta_title', 160, 'Meta title');
    batas($errors, $i, 'meta_description', 255, 'Meta description');
    return $errors;
}

function validate_jadwal(array $i): array
{
    $errors = [];
    $paketId = (string) ($i['paket_id'] ?? '');
    if ($paketId === '' || !ctype_digit($paketId) || (int) $paketId < 1) {
        $errors['paket_id'] = 'Paket wajib dipilih.';
    } elseif (!fetch_one('SELECT id FROM paket_umroh WHERE id = ?', [(int) $paketId])) {
        // FK dicek di aplikasi agar muncul error field, bukan exception PDO
        $errors['paket_id'] = 'Paket yang dipilih tidak ditemukan. Muat ulang daftar paket.';
    }

    $tanggal = trim((string) ($i['tanggal_berangkat'] ?? ''));
    if ($tanggal === '') {
        $errors['tanggal_berangkat'] = 'Tanggal keberangkatan wajib diisi.';
    } elseif (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $tanggal, $m)
        || !checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
        $errors['tanggal_berangkat'] = 'Format tanggal harus YYYY-MM-DD dan tanggal yang valid.';
    }

    $kuota = (string) ($i['kuota'] ?? '');
    if ($kuota === '' || !ctype_digit($kuota) || (int) $kuota < 1 || (int) $kuota > 100000) {
        $errors['kuota'] = 'Kuota wajib berupa angka 1–100000.';
    }
    if (isset($i['status']) && !in_array($i['status'], ['dibuka', 'penuh', 'ditutup'], true)) {
        $errors['status'] = 'Status jadwal tidak valid.';
    }
    return $errors;
}

function validate_fasilitas(array $i): array
{
    $errors = [];
    if (trim($i['nama'] ?? '') === '') {
        $errors['nama'] = 'Nama fasilitas wajib diisi.';
    }
    batas($errors, $i, 'nama', 100, 'Nama fasilitas');
    batas($errors, $i, 'ikon', 255, 'Ikon');
    return $errors;
}

function validate_hotel(array $i): array
{
    $errors = [];
    if (trim($i['nama'] ?? '') === '') {
        $errors['nama'] = 'Nama hotel wajib diisi.';
    }
    batas($errors, $i, 'nama', 150, 'Nama hotel');
    batas($errors, $i, 'kota', 100, 'Kota');
    batas($errors, $i, 'kelas', 50, 'Kelas/bintang');
    return $errors;
}

function validate_maskapai(array $i): array
{
    $errors = [];
    if (trim($i['nama'] ?? '') === '') {
        $errors['nama'] = 'Nama maskapai wajib diisi.';
    }
    batas($errors, $i, 'nama', 100, 'Nama maskapai');
    return $errors;
}

function validate_galeri(array $i): array
{
    $errors = [];
    if (trim($i['judul'] ?? '') === '') {
        $errors['judul'] = 'Judul galeri wajib diisi.';
    }
    batas($errors, $i, 'judul', 150, 'Judul galeri');
    batas($errors, $i, 'kategori', 100, 'Kategori');
    return $errors;
}

function validate_artikel(array $i): array
{
    $errors = [];
    if (trim($i['judul'] ?? '') === '') {
        $errors['judul'] = 'Judul artikel wajib diisi.';
    }
    batas($errors, $i, 'judul', 200, 'Judul artikel');
    if (trim($i['konten'] ?? '') === '') {
        $errors['konten'] = 'Konten artikel wajib diisi.';
    }
    $slug = trim((string) ($i['slug'] ?? ''));
    if ($slug !== '') {
        if (mb_strlen($slug) > 220) {
            $errors['slug'] = 'Slug maksimal 220 karakter.';
        } elseif (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            $errors['slug'] = 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.';
        }
    }
    batas($errors, $i, 'meta_title', 160, 'Meta title');
    batas($errors, $i, 'meta_description', 255, 'Meta description');
    if (isset($i['status']) && !in_array($i['status'], ['publish', 'draft'], true)) {
        $errors['status'] = 'Status artikel tidak valid.';
    }
    return $errors;
}

function validate_faq(array $i): array
{
    $errors = [];
    if (trim($i['pertanyaan'] ?? '') === '') {
        $errors['pertanyaan'] = 'Pertanyaan wajib diisi.';
    }
    batas($errors, $i, 'pertanyaan', 255, 'Pertanyaan');
    if (trim($i['jawaban'] ?? '') === '') {
        $errors['jawaban'] = 'Jawaban wajib diisi.';
    }
    if (isset($i['urutan']) && $i['urutan'] !== '' && !ctype_digit((string) $i['urutan'])) {
        $errors['urutan'] = 'Urutan wajib berupa angka.';
    }
    return $errors;
}

function validate_inquiry(array $i): array
{
    $errors = [];
    if (trim($i['nama'] ?? '') === '') {
        $errors['nama'] = 'Nama lengkap wajib diisi.';
    }
    batas($errors, $i, 'nama', 100, 'Nama lengkap');
    $kontak = trim($i['kontak'] ?? '');
    if ($kontak === '') {
        $errors['kontak'] = 'Nomor telepon / email wajib diisi.';
    } elseif (!filter_var($kontak, FILTER_VALIDATE_EMAIL) && !preg_match('/^[0-9+\-\s()]{8,20}$/', $kontak)) {
        $errors['kontak'] = 'Masukkan nomor telepon atau email yang valid.';
    }
    batas($errors, $i, 'kontak', 100, 'Nomor telepon / email');
    $email = trim((string) ($i['email'] ?? ''));
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format surel tidak valid.';
    } elseif ($email !== '' && mb_strlen($email) > 100) {
        $errors['email'] = 'Surel maksimal 100 karakter.';
    }
    if (trim($i['pesan'] ?? '') === '') {
        $errors['pesan'] = 'Pesan / kebutuhan konsultasi wajib diisi.';
    }
    return $errors;
}

function validate_profil(array $i): array
{
    $errors = [];
    if (trim($i['nama_perusahaan'] ?? '') === '') {
        $errors['nama_perusahaan'] = 'Nama perusahaan wajib diisi.';
    }
    if (!empty($i['email']) && !filter_var($i['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Format email tidak valid.';
    }
    batas($errors, $i, 'nama_perusahaan', 150, 'Nama perusahaan');
    batas($errors, $i, 'tagline', 150, 'Tagline');
    batas($errors, $i, 'telepon', 60, 'Telepon');
    batas($errors, $i, 'email', 100, 'Surel');
    batas($errors, $i, 'jam_operasional', 150, 'Jam operasional');
    batas($errors, $i, 'meta_title', 160, 'Meta title');
    batas($errors, $i, 'meta_description', 255, 'Meta description');
    return $errors;
}

/** Tampilkan error per-field untuk form edit/create. */
function field_error(array $errors, string $field): string
{
    if (!isset($errors[$field])) {
        return '';
    }
    return '<p class="text-[#ba1a1a] font-body-sm text-body-sm mt-1">' . e($errors[$field]) . '</p>';
}

function old_value(array $data, string $field, $default = ''): string
{
    return e((string) ($data[$field] ?? $default));
}
