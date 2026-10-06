<?php
declare(strict_types=1);

class MetaHalamanModel
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

    /** Ambil meta SEO satu halaman publik berdasarkan kode halaman. */
    public function getByKode(string $kode): ?array
    {
        $row = $this->stmt('SELECT kode, meta_title, meta_description FROM meta_halaman WHERE kode = :kode', ['kode' => $kode])->fetch();
        return $row ?: null;
    }

    /** Seluruh baris meta halaman. */
    public function getAll(): array
    {
        return $this->stmt('SELECT kode, meta_title, meta_description FROM meta_halaman ORDER BY kode')->fetchAll();
    }

    /** Upsert meta SEO satu halaman (kode = PRIMARY KEY). */
    public function update(string $kode, ?string $metaTitle, ?string $metaDescription): void
    {
        $this->stmt(
            'INSERT INTO meta_halaman (kode, meta_title, meta_description) VALUES (:kode, :title, :desc)
             ON DUPLICATE KEY UPDATE meta_title = :title2, meta_description = :desc2',
            ['kode' => $kode, 'title' => $metaTitle, 'desc' => $metaDescription, 'title2' => $metaTitle, 'desc2' => $metaDescription]
        );
    }
}