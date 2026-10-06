<?php
declare(strict_types=1);

class MaskapaiModel
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
        return $this->stmt('SELECT * FROM maskapai ORDER BY nama')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM maskapai WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function countAll(): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM maskapai')->fetchColumn();
    }

    public function create(array $d): int
    {
        $this->stmt(
            'INSERT INTO maskapai (nama, logo) VALUES (:nama, :logo)',
            ['nama' => $d['nama'], 'logo' => $d['logo'] ?? null]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            'UPDATE maskapai SET nama = :nama, logo = :logo WHERE id = :id',
            ['id' => $id, 'nama' => $d['nama'], 'logo' => $d['logo'] ?? null]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM maskapai WHERE id = :id', ['id' => $id]);
    }
}
