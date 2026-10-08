<?php
declare(strict_types=1);

/* =========================================================
 * Query agregasi analitik traffic untuk dashboard CMS.
 * Spec: docs/SPEC-TRAFFIC-ANALYTICS.md bagian 7.
 * =======================================================*/

class TrafficModel
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

    /** Validasi rentang hari (1..90) & lekatkan ke SQL sebagai integer aman. */
    private function daysSafe(int $days): int
    {
        return max(1, min(90, $days));
    }

    /** Tren kunjungan harian: total hits + IP unik per hari. */
    public function getDailyChart(int $days = 7): array
    {
        $n = $this->daysSafe($days);
        return $this->stmt(
            "SELECT DATE(visited_at) AS tgl,
                    COUNT(*) AS total_hits,
                    COUNT(DISTINCT ip_address) AS unique_ip
             FROM visitor_logs
             WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL {$n} DAY)
             GROUP BY DATE(visited_at)
             ORDER BY tgl ASC"
        )->fetchAll();
    }

    /** Pangsa browser (5 teratas, kolom kedua berisi persentase). */
    public function getBrowserStats(int $days = 30): array
    {
        $n = $this->daysSafe($days);
        return $this->stmt(
            "SELECT browser, COUNT(*) AS jumlah,
                    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM visitor_logs WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL {$n} DAY)), 1) AS persentase
             FROM visitor_logs
             WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL {$n} DAY)
             GROUP BY browser
             ORDER BY jumlah DESC
             LIMIT 5"
        )->fetchAll();
    }

    /** Peringkat negara pengunjung (6 teratas). */
    public function getCountryStats(int $days = 30): array
    {
        $n = $this->daysSafe($days);
        return $this->stmt(
            "SELECT country_code, country_name, COUNT(*) AS jumlah,
                    COUNT(DISTINCT ip_address) AS unique_ip
             FROM visitor_logs
             WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL {$n} DAY)
             GROUP BY country_code, country_name
             ORDER BY jumlah DESC
             LIMIT 6"
        )->fetchAll();
    }

    /** 10 kunjungan publik terakhir. */
    public function getRecentLogs(int $limit = 10): array
    {
        $limit = max(1, min(50, $limit));
        return $this->stmt(
            "SELECT ip_address, browser, os, country_code, country_name, page_url, visited_at
             FROM visitor_logs
             ORDER BY visited_at DESC
             LIMIT {$limit}"
        )->fetchAll();
    }

    /** Ringkasan total hits & IP unik untuk kartu panel. */
    public function getTotals(int $days = 30): array
    {
        $n = $this->daysSafe($days);
        $row = $this->stmt(
            "SELECT COUNT(*) AS total_hits, COUNT(DISTINCT ip_address) AS unique_visitors
             FROM visitor_logs
             WHERE visited_at >= DATE_SUB(CURDATE(), INTERVAL {$n} DAY)"
        )->fetch();
        return [
            'total_hits'       => (int) ($row['total_hits'] ?? 0),
            'unique_visitors'  => (int) ($row['unique_visitors'] ?? 0),
        ];
    }
}