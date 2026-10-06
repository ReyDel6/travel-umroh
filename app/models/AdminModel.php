<?php
declare(strict_types=1);

class AdminModel
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
        return $this->stmt('SELECT id, username, created_at FROM admin ORDER BY id ASC')->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM admin')->fetchColumn();
    }

    public function create(string $username, string $password): int
    {
        $this->stmt(
            'INSERT INTO admin (username, password_hash) VALUES (:u, :p)',
            ['u' => $username, 'p' => password_hash($password, PASSWORD_DEFAULT)]
        );
        return (int) $this->db->lastInsertId();
    }

    public function updatePassword(int $id, string $password): void
    {
        $this->stmt(
            'UPDATE admin SET password_hash = :p WHERE id = :id',
            ['p' => password_hash($password, PASSWORD_DEFAULT), 'id' => $id]
        );
    }

    public function getPasswordHash(int $id): ?string
    {
        $hash = $this->stmt('SELECT password_hash FROM admin WHERE id = :id', ['id' => $id])->fetchColumn();
        return $hash !== false && $hash !== null ? (string) $hash : null;
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM admin WHERE id = :id', ['id' => $id]);
    }
}
