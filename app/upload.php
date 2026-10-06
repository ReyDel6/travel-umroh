<?php
declare(strict_types=1);

/* =========================================================
 * Upload gambar: validasi MIME asli + ukuran di server
 * =======================================================*/

/**
 * @param array  $file      elemen $_FILES[...]
 * @param string $modul     sub-folder tujuan (paket|galeri|artikel|hotel|maskapai|fasilitas|profil)
 * @return array ['ok'=>bool, 'file'=>?string, 'error'=>?string]
 */
function handle_image_upload(array $file, string $modul): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['ok' => false, 'file' => null, 'error' => 'Struktur upload tidak valid.'];
    }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'file' => null, 'error' => null]; // tidak ada file baru = boleh
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'file' => null, 'error' => 'Upload gagal (kode ' . $file['error'] . ').'];
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        return ['ok' => false, 'file' => null, 'error' => 'Ukuran gambar maksimal 2MB.'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset(ALLOWED_MIME[$mime])) {
        return ['ok' => false, 'file' => null, 'error' => 'Format gambar harus JPG, PNG, atau WebP.'];
    }

    $ext = ALLOWED_MIME[$mime];
    $dir = UPLOAD_PATH . '/' . trim($modul, '/');
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        return ['ok' => false, 'file' => null, 'error' => 'Gagal membuat folder tujuan upload.'];
    }

    $filename = uniqid('img_', false) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
        return ['ok' => false, 'file' => null, 'error' => 'Gagal menyimpan file ke server.'];
    }
    return ['ok' => true, 'file' => $filename, 'error' => null];
}

function delete_upload(?string $filename, string $modul): void
{
    if (!$filename || preg_match('#^https?://#i', $filename)) {
        return;
    }
    $modul = basename(trim((string) $modul, '/'));
    $path = UPLOAD_PATH . '/' . $modul . '/' . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}
