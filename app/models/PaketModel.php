<?php
declare(strict_types=1);

class PaketModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private function stmt(string $sql, array $params = []): PDOStatement
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st;
    }

    /* ---------- Public ---------- */

    public function getAllAktif(): array
    {
        return $this->stmt(
            "SELECT * FROM paket_umroh WHERE status = 'aktif' ORDER BY created_at DESC"
        )->fetchAll();
    }

    /** Daftar paket + harga terendah + jadwal terdekat (untuk kartu public). */
    public function getDaftarAktif(): array
    {
        return $this->stmt(
            "SELECT p.*,
                    (SELECT MIN(h.harga) FROM harga_paket h WHERE h.paket_id = p.id) AS harga_min,
                    (SELECT MIN(j.tanggal_berangkat) FROM jadwal_keberangkatan j
                      WHERE j.paket_id = p.id AND j.status = 'dibuka' AND j.tanggal_berangkat >= CURDATE()) AS jadwal_terdekat
             FROM paket_umroh p
             WHERE p.status = 'aktif'
             ORDER BY p.created_at DESC"
        )->fetchAll();
    }

    public function getFeatured(int $limit = 3): array
    {
        return $this->stmt(
            "SELECT p.*,
                    (SELECT MIN(h.harga) FROM harga_paket h WHERE h.paket_id = p.id) AS harga_min,
                    (SELECT MIN(j.tanggal_berangkat) FROM jadwal_keberangkatan j
                      WHERE j.paket_id = p.id AND j.status = 'dibuka' AND j.tanggal_berangkat >= CURDATE()) AS jadwal_terdekat
             FROM paket_umroh p
             WHERE p.status = 'aktif'
             ORDER BY p.created_at DESC
             LIMIT " . (int) $limit
        )->fetchAll();
    }

    public function getBySlug(string $slug): ?array
    {
        $row = $this->stmt("SELECT * FROM paket_umroh WHERE slug = :slug LIMIT 1", ['slug' => $slug])->fetch();
        return $row ?: null;
    }

    /* ---------- Admin ---------- */

    public function getAll(): array
    {
        return $this->stmt(
            "SELECT p.*,
                    (SELECT COUNT(*) FROM jadwal_keberangkatan j WHERE j.paket_id = p.id) AS jumlah_jadwal,
                    (SELECT MIN(h.harga) FROM harga_paket h WHERE h.paket_id = p.id) AS harga_min
             FROM paket_umroh p ORDER BY p.created_at DESC"
        )->fetchAll();
    }

    public function countAktif(): int
    {
        return (int) $this->stmt("SELECT COUNT(*) FROM paket_umroh WHERE status = 'aktif'")->fetchColumn();
    }

    public function countAll(): int
    {
        return (int) $this->stmt("SELECT COUNT(*) FROM paket_umroh")->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM paket_umroh WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function create(array $d): int
    {
        $this->stmt(
            "INSERT INTO paket_umroh (nama, slug, deskripsi, durasi_hari, thumbnail, status, meta_title, meta_description)
             VALUES (:nama, :slug, :deskripsi, :durasi_hari, :thumbnail, :status, :meta_title, :meta_description)",
            [
                'nama'             => $d['nama'],
                'slug'             => $d['slug'],
                'deskripsi'        => $d['deskripsi'],
                'durasi_hari'      => (int) $d['durasi_hari'],
                'thumbnail'        => $d['thumbnail'] ?? null,
                'status'           => $d['status'] ?? 'aktif',
                'meta_title'       => $d['meta_title'] ?? null,
                'meta_description' => $d['meta_description'] ?? null,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            "UPDATE paket_umroh SET nama = :nama, slug = :slug, deskripsi = :deskripsi, durasi_hari = :durasi_hari,
                    thumbnail = :thumbnail, status = :status, meta_title = :meta_title, meta_description = :meta_description
             WHERE id = :id",
            [
                'id'               => $id,
                'nama'             => $d['nama'],
                'slug'             => $d['slug'],
                'deskripsi'        => $d['deskripsi'],
                'durasi_hari'      => (int) $d['durasi_hari'],
                'thumbnail'        => $d['thumbnail'] ?? null,
                'status'           => $d['status'] ?? 'aktif',
                'meta_title'       => $d['meta_title'] ?? null,
                'meta_description' => $d['meta_description'] ?? null,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM paket_umroh WHERE id = :id', ['id' => $id]);
    }

    public function slugExists(string $slug, int $ignoreId = 0): bool
    {
        $st = $this->stmt('SELECT COUNT(*) FROM paket_umroh WHERE slug = :slug AND id <> :id', ['slug' => $slug, 'id' => $ignoreId]);
        return (int) $st->fetchColumn() > 0;
    }

    public function uniqueSlug(string $slug, int $ignoreId = 0): string
    {
        $base = $slug;
        $i = 2;
        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    /* ---------- Relasi: harga ---------- */

    public function getHarga(int $paketId): array
    {
        return $this->stmt(
            'SELECT * FROM harga_paket WHERE paket_id = :id ORDER BY FIELD(tipe_kamar,\'quad\',\'triple\',\'double\')',
            ['id' => $paketId]
        )->fetchAll();
    }

    public function syncHarga(int $paketId, array $rows): void
    {
        $this->stmt('DELETE FROM harga_paket WHERE paket_id = :id', ['id' => $paketId]);
        foreach ($rows as $row) {
            if (!in_array($row['tipe_kamar'], ['quad', 'triple', 'double'], true)) {
                continue;
            }
            $harga = str_replace(['.', ',', ' '], '', (string) $row['harga']);
            if ($harga === '' || !is_numeric($harga)) {
                continue;
            }
            $this->stmt(
                'INSERT INTO harga_paket (paket_id, tipe_kamar, harga) VALUES (:p, :t, :h)',
                ['p' => $paketId, 't' => $row['tipe_kamar'], 'h' => (float) $harga]
            );
        }
    }

    /* ---------- Relasi: fasilitas (M2M) ---------- */

    public function getFasilitasIds(int $paketId): array
    {
        $rows = $this->stmt('SELECT fasilitas_id FROM paket_fasilitas WHERE paket_id = :id', ['id' => $paketId])->fetchAll();
        return array_map(static fn($r) => (int) $r['fasilitas_id'], $rows);
    }

    public function getFasilitas(int $paketId): array
    {
        return $this->stmt(
            'SELECT f.* FROM fasilitas f JOIN paket_fasilitas pf ON pf.fasilitas_id = f.id WHERE pf.paket_id = :id ORDER BY f.nama',
            ['id' => $paketId]
        )->fetchAll();
    }

    public function syncFasilitas(int $paketId, array $ids): void
    {
        $this->stmt('DELETE FROM paket_fasilitas WHERE paket_id = :id', ['id' => $paketId]);
        foreach ($ids as $fid) {
            $this->stmt(
                'INSERT INTO paket_fasilitas (paket_id, fasilitas_id) VALUES (:p, :f) ON DUPLICATE KEY UPDATE paket_id = paket_id',
                ['p' => $paketId, 'f' => (int) $fid]
            );
        }
    }

    /* ---------- Relasi: hotel & maskapai (M2M) ---------- */

    public function getHotelIds(int $paketId): array
    {
        $rows = $this->stmt('SELECT hotel_id FROM paket_hotel WHERE paket_id = :id', ['id' => $paketId])->fetchAll();
        return array_map(static fn($r) => (int) $r['hotel_id'], $rows);
    }

    public function getHotels(int $paketId): array
    {
        return $this->stmt(
            'SELECT h.* FROM hotel h JOIN paket_hotel ph ON ph.hotel_id = h.id WHERE ph.paket_id = :id ORDER BY h.nama',
            ['id' => $paketId]
        )->fetchAll();
    }

    public function syncHotels(int $paketId, array $ids): void
    {
        $this->stmt('DELETE FROM paket_hotel WHERE paket_id = :id', ['id' => $paketId]);
        foreach ($ids as $hid) {
            $this->stmt(
                'INSERT INTO paket_hotel (paket_id, hotel_id) VALUES (:p, :h) ON DUPLICATE KEY UPDATE paket_id = paket_id',
                ['p' => $paketId, 'h' => (int) $hid]
            );
        }
    }

    public function getMaskapaiIds(int $paketId): array
    {
        $rows = $this->stmt('SELECT maskapai_id FROM paket_maskapai WHERE paket_id = :id', ['id' => $paketId])->fetchAll();
        return array_map(static fn($r) => (int) $r['maskapai_id'], $rows);
    }

    public function getMaskapai(int $paketId): array
    {
        return $this->stmt(
            'SELECT m.* FROM maskapai m JOIN paket_maskapai pm ON pm.maskapai_id = m.id WHERE pm.paket_id = :id ORDER BY m.nama',
            ['id' => $paketId]
        )->fetchAll();
    }

    public function syncMaskapai(int $paketId, array $ids): void
    {
        $this->stmt('DELETE FROM paket_maskapai WHERE paket_id = :id', ['id' => $paketId]);
        foreach ($ids as $mid) {
            $this->stmt(
                'INSERT INTO paket_maskapai (paket_id, maskapai_id) VALUES (:p, :m) ON DUPLICATE KEY UPDATE paket_id = paket_id',
                ['p' => $paketId, 'm' => (int) $mid]
            );
        }
    }

    /* ---------- Relasi: itinerary ---------- */

    public function getItinerary(int $paketId): array
    {
        return $this->stmt(
            'SELECT * FROM paket_itinerary WHERE paket_id = :id ORDER BY urutan ASC, id ASC',
            ['id' => $paketId]
        )->fetchAll();
    }

    public function syncItinerary(int $paketId, array $rows): void
    {
        $this->stmt('DELETE FROM paket_itinerary WHERE paket_id = :id', ['id' => $paketId]);
        foreach ($rows as $row) {
            if (trim((string) ($row['judul'] ?? '')) === '' && trim((string) ($row['deskripsi'] ?? '')) === '') {
                continue;
            }
            $this->stmt(
                'INSERT INTO paket_itinerary (paket_id, hari, judul, deskripsi, tag, is_highlight, urutan)
                 VALUES (:p, :hari, :judul, :deskripsi, :tag, :hl, :urutan)',
                [
                    'p'         => $paketId,
                    'hari'      => trim((string) ($row['hari'] ?? '')) ?: 'Hari ke-1',
                    'judul'     => trim((string) ($row['judul'] ?? '')),
                    'deskripsi' => trim((string) ($row['deskripsi'] ?? '')),
                    'tag'       => trim((string) ($row['tag'] ?? '')) !== '' ? trim((string) $row['tag']) : null,
                    'hl'        => !empty($row['is_highlight']) ? 1 : 0,
                    'urutan'    => (int) ($row['urutan'] ?? 0),
                ]
            );
        }
    }
}
