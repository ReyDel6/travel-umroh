<?php
declare(strict_types=1);

class FasilitasModel
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
        return $this->stmt('SELECT * FROM fasilitas ORDER BY nama')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM fasilitas WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function countAll(): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM fasilitas')->fetchColumn();
    }

    public function create(array $d): int
    {
        $this->stmt(
            'INSERT INTO fasilitas (nama, deskripsi, ikon) VALUES (:nama, :deskripsi, :ikon)',
            [
                'nama'      => $d['nama'],
                'deskripsi' => $d['deskripsi'] ?? null,
                'ikon'      => $d['ikon'] ?? null,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            'UPDATE fasilitas SET nama = :nama, deskripsi = :deskripsi, ikon = :ikon WHERE id = :id',
            [
                'id'        => $id,
                'nama'      => $d['nama'],
                'deskripsi' => $d['deskripsi'] ?? null,
                'ikon'      => $d['ikon'] ?? null,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM fasilitas WHERE id = :id', ['id' => $id]);
    }
}
