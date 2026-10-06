<?php
declare(strict_types=1);

class JadwalModel
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

    public function getAll(): array
    {
        return $this->stmt(
            'SELECT j.*, p.nama AS nama_paket, p.slug AS slug_paket
             FROM jadwal_keberangkatan j
             JOIN paket_umroh p ON p.id = j.paket_id
             ORDER BY j.tanggal_berangkat ASC'
        )->fetchAll();
    }

    public function getByPaket(int $paketId, bool $hanyaAktif = false): array
    {
        $sql = 'SELECT * FROM jadwal_keberangkatan WHERE paket_id = :id';
        if ($hanyaAktif) {
            $sql .= " AND status = 'dibuka' AND tanggal_berangkat >= CURDATE()";
        }
        $sql .= ' ORDER BY tanggal_berangkat ASC';
        return $this->stmt($sql, ['id' => $paketId])->fetchAll();
    }

    /** Jadwal mendatang untuk seluruh public (limit). */
    public function getMendatang(int $limit = 6): array
    {
        return $this->stmt(
            'SELECT j.*, p.nama AS nama_paket, p.slug AS slug_paket
             FROM jadwal_keberangkatan j
             JOIN paket_umroh p ON p.id = j.paket_id
             WHERE j.status = \'dibuka\' AND j.tanggal_berangkat >= CURDATE() AND p.status = \'aktif\'
             ORDER BY j.tanggal_berangkat ASC
             LIMIT ' . (int) $limit
        )->fetchAll();
    }

    public function countMendatang(): int
    {
        $st = $this->stmt(
            "SELECT COUNT(*) FROM jadwal_keberangkatan j JOIN paket_umroh p ON p.id = j.paket_id
             WHERE j.status = 'dibuka' AND j.tanggal_berangkat >= CURDATE() AND p.status = 'aktif'"
        );
        return (int) $st->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM jadwal_keberangkatan WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function create(array $d): int
    {
        $this->stmt(
            'INSERT INTO jadwal_keberangkatan (paket_id, tanggal_berangkat, kuota, status) VALUES (:p, :t, :k, :s)',
            [
                'p' => (int) $d['paket_id'],
                't' => $d['tanggal_berangkat'],
                'k' => (int) $d['kuota'],
                's' => $d['status'] ?? 'dibuka',
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            'UPDATE jadwal_keberangkatan SET paket_id = :p, tanggal_berangkat = :t, kuota = :k, status = :s WHERE id = :id',
            [
                'id' => $id,
                'p'  => (int) $d['paket_id'],
                't'  => $d['tanggal_berangkat'],
                'k'  => (int) $d['kuota'],
                's'  => $d['status'] ?? 'dibuka',
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM jadwal_keberangkatan WHERE id = :id', ['id' => $id]);
    }
}
