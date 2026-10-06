<?php
declare(strict_types=1);

class ProfilModel
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

    /** Profil perusahaan (umumnya hanya satu baris). */
    public function get(): array
    {
        $row = $this->stmt('SELECT * FROM profil_perusahaan ORDER BY id ASC LIMIT 1')->fetch();
        return $row ?: [];
    }

    public function save(array $d): void
    {
        $existing = $this->stmt('SELECT id FROM profil_perusahaan ORDER BY id ASC LIMIT 1')->fetch();
        $data = [
            'nama_perusahaan'      => $d['nama_perusahaan'] ?? '',
            'tagline'              => $d['tagline'] ?? null,
            'tentang_kami'         => $d['tentang_kami'] ?? null,
            'alamat'               => $d['alamat'] ?? null,
            'telepon'              => $d['telepon'] ?? null,
            'email'                => $d['email'] ?? null,
            'jam_operasional'      => $d['jam_operasional'] ?? null,
            'maps_embed'           => $d['maps_embed'] ?? null,
            'meta_title'           => $d['meta_title'] ?? null,
            'meta_description'     => $d['meta_description'] ?? null,
            'izin_ppiu'            => $d['izin_ppiu'] ?? null,
            'tahun_berdiri'        => isset($d['tahun_berdiri']) && $d['tahun_berdiri'] !== '' ? (int) $d['tahun_berdiri'] : null,
            'stat_grup_maks'       => isset($d['stat_grup_maks']) && $d['stat_grup_maks'] !== '' ? (int) $d['stat_grup_maks'] : null,
            'stat_rasio_pembimbing'=> isset($d['stat_rasio_pembimbing']) && $d['stat_rasio_pembimbing'] !== '' ? (int) $d['stat_rasio_pembimbing'] : null,
            'stat_sesi_manasik'    => isset($d['stat_sesi_manasik']) && $d['stat_sesi_manasik'] !== '' ? (int) $d['stat_sesi_manasik'] : null,
        ];
        if ($existing) {
            $data['id'] = (int) $existing['id'];
            $this->stmt(
                'UPDATE profil_perusahaan SET nama_perusahaan = :nama_perusahaan, tagline = :tagline, tentang_kami = :tentang_kami,
                        alamat = :alamat, telepon = :telepon, email = :email, jam_operasional = :jam_operasional,
                        maps_embed = :maps_embed, meta_title = :meta_title, meta_description = :meta_description,
                        izin_ppiu = :izin_ppiu, tahun_berdiri = :tahun_berdiri, stat_grup_maks = :stat_grup_maks,
                        stat_rasio_pembimbing = :stat_rasio_pembimbing, stat_sesi_manasik = :stat_sesi_manasik
                 WHERE id = :id',
                $data
            );
        } else {
            $this->stmt(
                'INSERT INTO profil_perusahaan (nama_perusahaan, tagline, tentang_kami, alamat, telepon, email, jam_operasional, maps_embed, meta_title, meta_description, izin_ppiu, tahun_berdiri, stat_grup_maks, stat_rasio_pembimbing, stat_sesi_manasik)
                 VALUES (:nama_perusahaan, :tagline, :tentang_kami, :alamat, :telepon, :email, :jam_operasional, :maps_embed, :meta_title, :meta_description, :izin_ppiu, :tahun_berdiri, :stat_grup_maks, :stat_rasio_pembimbing, :stat_sesi_manasik)',
                $data
            );
        }
    }
}
