<?php
declare(strict_types=1);

class GaleriModel
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

    public function getPublic(?string $kategori = null, int $limit = 0, int $offset = 0): array
    {
        $sql = 'SELECT * FROM galeri';
        $params = [];
        if ($kategori !== null && $kategori !== '') {
            $sql .= ' WHERE kategori = :kat';
            $params['kat'] = $kategori;
        }
        $sql .= ' ORDER BY created_at DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
        }
        return $this->stmt($sql, $params)->fetchAll();
    }

    public function countPublic(?string $kategori = null): int
    {
        $sql = 'SELECT COUNT(*) FROM galeri';
        $params = [];
        if ($kategori !== null && $kategori !== '') {
            $sql .= ' WHERE kategori = :kat';
            $params['kat'] = $kategori;
        }
        return (int) $this->stmt($sql, $params)->fetchColumn();
    }

    public function getKategori(): array
    {
        $rows = $this->stmt('SELECT DISTINCT kategori FROM galeri WHERE kategori IS NOT NULL AND kategori <> \'\' ORDER BY kategori')->fetchAll();
        return array_map(static fn($r) => $r['kategori'], $rows);
    }

    public function getAll(): array
    {
        return $this->stmt('SELECT * FROM galeri ORDER BY created_at DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM galeri WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function countAll(): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM galeri')->fetchColumn();
    }

    public function create(array $d): int
    {
        $this->stmt(
            'INSERT INTO galeri (judul, gambar, kategori) VALUES (:judul, :gambar, :kategori)',
            [
                'judul'    => $d['judul'],
                'gambar'   => $d['gambar'],
                'kategori' => $d['kategori'] ?? null,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            'UPDATE galeri SET judul = :judul, gambar = :gambar, kategori = :kategori WHERE id = :id',
            [
                'id'       => $id,
                'judul'    => $d['judul'],
                'gambar'   => $d['gambar'],
                'kategori' => $d['kategori'] ?? null,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM galeri WHERE id = :id', ['id' => $id]);
    }
}
