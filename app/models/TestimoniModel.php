<?php
declare(strict_types=1);

class TestimoniModel
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

    /** Testimoni untuk publik, urut prioritas lalu terbaru. */
    public function getAktif(int $limit = 0): array
    {
        $sql = 'SELECT * FROM testimoni ORDER BY urutan ASC, id ASC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit;
        }
        return $this->stmt($sql)->fetchAll();
    }

    public function getAll(): array
    {
        return $this->stmt('SELECT * FROM testimoni ORDER BY urutan ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM testimoni WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function countAll(): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM testimoni')->fetchColumn();
    }

    public function create(array $d): int
    {
        $this->stmt(
            'INSERT INTO testimoni (nama, asal_kota, teks, urutan) VALUES (:nama, :asal_kota, :teks, :urutan)',
            [
                'nama'      => $d['nama'],
                'asal_kota' => $d['asal_kota'] ?? null,
                'teks'      => $d['teks'],
                'urutan'    => (int) ($d['urutan'] ?? 0),
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            'UPDATE testimoni SET nama = :nama, asal_kota = :asal_kota, teks = :teks, urutan = :urutan WHERE id = :id',
            [
                'id'        => $id,
                'nama'      => $d['nama'],
                'asal_kota' => $d['asal_kota'] ?? null,
                'teks'      => $d['teks'],
                'urutan'    => (int) ($d['urutan'] ?? 0),
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM testimoni WHERE id = :id', ['id' => $id]);
    }
}