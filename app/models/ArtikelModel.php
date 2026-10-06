<?php
declare(strict_types=1);

class ArtikelModel
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

    public function getPublish(int $limit = 0, int $offset = 0, string $cari = ''): array
    {
        $sql = "SELECT * FROM artikel WHERE status = 'publish'";
        $params = [];
        if ($cari !== '') {
            $sql .= ' AND (judul LIKE :q1 OR konten LIKE :q2)';
            $params['q1'] = '%' . $cari . '%';
            $params['q2'] = '%' . $cari . '%';
        }
        $sql .= ' ORDER BY created_at DESC';
        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
        }
        return $this->stmt($sql, $params)->fetchAll();
    }

    public function countPublish(string $cari = ''): int
    {
        $sql = "SELECT COUNT(*) FROM artikel WHERE status = 'publish'";
        $params = [];
        if ($cari !== '') {
            $sql .= ' AND (judul LIKE :q1 OR konten LIKE :q2)';
            $params['q1'] = '%' . $cari . '%';
            $params['q2'] = '%' . $cari . '%';
        }
        return (int) $this->stmt($sql, $params)->fetchColumn();
    }

    public function getFeatured(): ?array
    {
        $row = $this->stmt("SELECT * FROM artikel WHERE status = 'publish' ORDER BY created_at DESC LIMIT 1")->fetch();
        return $row ?: null;
    }

    public function getBySlugPublish(string $slug): ?array
    {
        $row = $this->stmt("SELECT * FROM artikel WHERE slug = :slug AND status = 'publish' LIMIT 1", ['slug' => $slug])->fetch();
        return $row ?: null;
    }

    public function getTerkait(int $id, int $limit = 3): array
    {
        return $this->stmt(
            "SELECT * FROM artikel WHERE status = 'publish' AND id <> :id ORDER BY created_at DESC LIMIT " . (int) $limit,
            ['id' => $id]
        )->fetchAll();
    }

    /* ---------- Admin ---------- */

    public function getAll(?string $status = null, string $cari = ''): array
    {
        $sql = 'SELECT * FROM artikel WHERE 1 = 1';
        $params = [];
        if ($status !== null && in_array($status, ['publish', 'draft'], true)) {
            $sql .= ' AND status = :status';
            $params['status'] = $status;
        }
        if ($cari !== '') {
            $sql .= ' AND judul LIKE :q';
            $params['q'] = '%' . $cari . '%';
        }
        $sql .= ' ORDER BY created_at DESC';
        return $this->stmt($sql, $params)->fetchAll();
    }

    public function countByStatus(string $status): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM artikel WHERE status = :s', ['s' => $status])->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM artikel WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function getBySlug(string $slug): ?array
    {
        $row = $this->stmt('SELECT * FROM artikel WHERE slug = :slug LIMIT 1', ['slug' => $slug])->fetch();
        return $row ?: null;
    }

    public function create(array $d): int
    {
        $this->stmt(
            "INSERT INTO artikel (judul, slug, konten, thumbnail, status, meta_title, meta_description)
             VALUES (:judul, :slug, :konten, :thumbnail, :status, :meta_title, :meta_description)",
            [
                'judul'           => $d['judul'],
                'slug'            => $d['slug'],
                'konten'          => $d['konten'],
                'thumbnail'       => $d['thumbnail'] ?? null,
                'status'          => $d['status'] ?? 'draft',
                'meta_title'      => $d['meta_title'] ?? null,
                'meta_description' => $d['meta_description'] ?? null,
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            "UPDATE artikel SET judul = :judul, slug = :slug, konten = :konten, thumbnail = :thumbnail,
                    status = :status, meta_title = :meta_title, meta_description = :meta_description
             WHERE id = :id",
            [
                'id'              => $id,
                'judul'           => $d['judul'],
                'slug'            => $d['slug'],
                'konten'          => $d['konten'],
                'thumbnail'       => $d['thumbnail'] ?? null,
                'status'          => $d['status'] ?? 'draft',
                'meta_title'      => $d['meta_title'] ?? null,
                'meta_description' => $d['meta_description'] ?? null,
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM artikel WHERE id = :id', ['id' => $id]);
    }

    public function uniqueSlug(string $slug, int $ignoreId = 0): string
    {
        $base = $slug;
        $i = 2;
        while (true) {
            $st = $this->stmt('SELECT COUNT(*) FROM artikel WHERE slug = :slug AND id <> :id', ['slug' => $slug, 'id' => $ignoreId]);
            if ((int) $st->fetchColumn() === 0) {
                return $slug;
            }
            $slug = $base . '-' . $i++;
        }
    }
}
