<?php
declare(strict_types=1);

/* =========================================================
 * Layer akses data: PDO wrapper + registry model
 * Semua query WAJIB lewat prepared statement.
 * =======================================================*/

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Koneksi database gagal. Pastikan MySQL berjalan dan kredensial di app/config.php benar.');
        }
    }
    return $pdo;
}

/** Helper query singkat: return statement hasil prepare + execute. */
function q(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function fetch_one(string $sql, array $params = []): ?array
{
    $row = q($sql, $params)->fetch();
    return $row ?: null;
}

function fetch_all(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

function scalar(string $sql, array $params = []): int
{
    $val = q($sql, $params)->fetchColumn();
    return (int) $val;
}

/** Registry model singleton — menghindari duplikasi instance di tiap halaman. */
function model(string $name): object
{
    static $registry = [];
    if (!isset($registry[$name])) {
        $map = [
            'paket'     => PaketModel::class,
            'jadwal'    => JadwalModel::class,
            'fasilitas' => FasilitasModel::class,
            'hotel'     => HotelModel::class,
            'maskapai'  => MaskapaiModel::class,
            'galeri'    => GaleriModel::class,
            'artikel'   => ArtikelModel::class,
            'faq'       => FaqModel::class,
            'inquiry'   => InquiryModel::class,
            'profil'    => ProfilModel::class,
            'admin'     => AdminModel::class,
            'meta'      => MetaHalamanModel::class,
            'testimoni' => TestimoniModel::class,
        ];
        if (!isset($map[$name])) {
            throw new RuntimeException('Model tidak dikenal: ' . $name);
        }
        $class = $map[$name];
        $registry[$name] = new $class(db());
    }
    return $registry[$name];
}
