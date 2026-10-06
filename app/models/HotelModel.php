<?php
declare(strict_types=1);

class HotelModel
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
        return $this->stmt('SELECT * FROM hotel ORDER BY nama')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM hotel WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function countAll(): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM hotel')->fetchColumn();
    }

    public function create(array $d): int
    {
        $this->stmt(
            'INSERT INTO hotel (nama, kota, kelas, gambar) VALUES (:nama, :kota, :kelas, :gambar)',
            [
                'nama'   => $d['nama'],
                'kota'   => $d['kota'] ?? null,
                'kelas'  => $d['kelas'] ?? null,
                'gambar' => $d['gambar'] ?? null,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            'UPDATE hotel SET nama = :nama, kota = :kota, kelas = :kelas, gambar = :gambar WHERE id = :id',
            [
                'id'     => $id,
                'nama'   => $d['nama'],
                'kota'   => $d['kota'] ?? null,
                'kelas'  => $d['kelas'] ?? null,
                'gambar' => $d['gambar'] ?? null,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM hotel WHERE id = :id', ['id' => $id]);
    }
}
