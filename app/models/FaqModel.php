<?php
declare(strict_types=1);

class FaqModel
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
        return $this->stmt('SELECT * FROM faq ORDER BY urutan ASC, id ASC')->fetchAll();
    }

    public function getPublic(): array
    {
        return $this->stmt('SELECT * FROM faq ORDER BY urutan ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt('SELECT * FROM faq WHERE id = :id', ['id' => $id])->fetch();
        return $row ?: null;
    }

    public function countAll(): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM faq')->fetchColumn();
    }

    public function create(array $d): int
    {
        $this->stmt(
            'INSERT INTO faq (pertanyaan, jawaban, urutan) VALUES (:p, :j, :u)',
            [
                'p' => $d['pertanyaan'],
                'j' => $d['jawaban'],
                'u' => (int) ($d['urutan'] ?? 0),
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $d): void
    {
        $this->stmt(
            'UPDATE faq SET pertanyaan = :p, jawaban = :j, urutan = :u WHERE id = :id',
            [
                'id' => $id,
                'p'  => $d['pertanyaan'],
                'j'  => $d['jawaban'],
                'u'  => (int) ($d['urutan'] ?? 0),
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM faq WHERE id = :id', ['id' => $id]);
    }
}
