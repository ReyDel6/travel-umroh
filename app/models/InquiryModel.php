<?php
declare(strict_types=1);

class InquiryModel
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

    public function getAll(?string $status = null): array
    {
        $sql = 'SELECT i.*, p.nama AS nama_paket
                FROM inquiry i
                LEFT JOIN paket_umroh p ON p.id = i.paket_id';
        $params = [];
        if ($status !== null && in_array($status, ['baru', 'ditindaklanjuti'], true)) {
            $sql .= ' WHERE i.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY i.created_at DESC';
        return $this->stmt($sql, $params)->fetchAll();
    }

    public function find(int $id): ?array
    {
        $row = $this->stmt(
            'SELECT i.*, p.nama AS nama_paket FROM inquiry i LEFT JOIN paket_umroh p ON p.id = i.paket_id WHERE i.id = :id',
            ['id' => $id]
        )->fetch();
        return $row ?: null;
    }

    public function countBaru(): int
    {
        return (int) $this->stmt("SELECT COUNT(*) FROM inquiry WHERE status = 'baru'")->fetchColumn();
    }

    public function countAll(): int
    {
        return (int) $this->stmt('SELECT COUNT(*) FROM inquiry')->fetchColumn();
    }

    /** Dipanggil dari public website (form konsultasi). */
    public function create(array $d): int
    {
        $this->stmt(
            'INSERT INTO inquiry (nama, kontak, email, paket_id, pesan, status) VALUES (:nama, :kontak, :email, :paket, :pesan, \'baru\')',
            [
                'nama'   => $d['nama'],
                'kontak' => $d['kontak'],
                'email'  => ($d['email'] ?? '') !== '' ? $d['email'] : null,
                'paket'  => !empty($d['paket_id']) ? (int) $d['paket_id'] : null,
                'pesan'  => $d['pesan'],
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    public function setStatus(int $id, string $status): void
    {
        $this->stmt('UPDATE inquiry SET status = :s WHERE id = :id', ['s' => $status, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->stmt('DELETE FROM inquiry WHERE id = :id', ['id' => $id]);
    }
}
