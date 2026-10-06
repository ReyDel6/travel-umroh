-- ============================================================
-- Seed data: Website & CMS Travel Umroh (Sakinah Journeys)
-- Akun admin: admin / admin123   |   staff / staff123
-- ============================================================
USE travel_umroh;

-- ---------- Admin ----------
INSERT INTO admin (id, username, password_hash) VALUES
(1, 'admin', '$2y$10$04IT.QDtjnzs1l0KbBhrCun1kvR9z5fBqGl35QmRLwxYioYCGC2NW'),
(2, 'staff', '$2y$10$88mXBW984dABOS7SYKgLzOK7PO0ro6JuqajMg5KBpyvl1eQwu5pzy');

-- ---------- Profil Perusahaan (dikelola via CMS, bukan hardcode) ----------
INSERT INTO profil_perusahaan (id, nama_perusahaan, tagline, tentang_kami, alamat, telepon, email, jam_operasional, maps_embed, meta_title, meta_description, izin_ppiu, tahun_berdiri, stat_grup_maks, stat_rasio_pembimbing, stat_sesi_manasik) VALUES
(1,
 'PT Sakinah Harmoni Mulia',
 'Perjalanan Spiritual yang Teduh',
 'Sakinah Journeys hadir menemani keluarga Indonesia menunaikan umroh dengan ritme yang teduh. Setiap etape perjalanan dirancang lapang: manasik berjarak dekat, pendampingan dokter dan muthawwif, serta akomodasi terbaik di sekitar Masjidil Haram dan Masjid Nabawi.\n\nBerdiri sejak 2021 dan mengantongi izin PPIU No. U.412 dari Kemenag RI dengan Akreditasi A, kami memilih tumbuh pelan: grup kecil maksimal 24 jamaah, rasio pembimbing 1:15, dan ruang tanya yang tidak dibatasi waktu. Bagi kami, ibadah yang khusyuk adalah ukuran keberhasilan perjalanan.',
 'Griya Sakinah, Jalan Brawijaya Raya No. 18, Kebayoran Baru, Jakarta Selatan, 12160',
 '+62 21 7892 0110 / +62 811 8892 011',
 'salam@sakinahjourneys.id',
 'Senin – Sabtu, 08.00 – 17.00 WIB',
 'https://www.google.com/maps?q=Jalan+Brawijaya+Raya+No.18+Kebayoran+Baru+Jakarta+Selatan',
 'Sakinah Journeys — Travel Umroh Resmi & Terpercaya',
 'Paket umroh reguler dan eksekutif dengan pendampingan manasik, dokter, dan muthawwif. Izin PPIU Kemenag RI Akreditasi A.',
 'PPIU No. U.412 Tahun 2021 — Akreditasi A',
 2021, 24, 15, 3);

-- ---------- Fasilitas ----------
INSERT INTO fasilitas (id, nama, deskripsi, ikon) VALUES
(1, 'Tiket Pesawat Ekonomi Premium', 'Penerbangan pulang-pergi dengan bagasi 30 kg dan kelas kursi legroom lapang.', 'flight'),
(2, 'Visa Umroh Resmi', 'Pengurusan visa lengkap dengan pendampingan biro rekanan di Tanah Suci.', 'badge'),
(3, 'Hotel Bintang Lima', 'Akomodasi dekat Haram, jarak tempuh kurang dari 700 meter dari Masjidil Haram.', 'hotel'),
(4, 'Makan Tiga Kali Sehari', 'Menu nusantara dan timur tengah disajikan prasmanan di restoran hotel.', 'restaurant'),
(5, 'Transportasi Privat AC', 'Bus premium antar-jemput bandara, hotel, dan lokasi ziarah.', 'directions_bus'),
(6, 'Bimbingan Manasik', 'Sesi manasik intensif sebelum keberangkatan berserta modul cetak.', 'school'),
(7, 'Dokter Pendamping', 'Dokter umum menemani rombongan selama perjalanan di Tanah Suci.', 'medical_services'),
(8, 'Muthawwif Bersertifikat', 'Pemandu ibadah berpengalaman yang mendampingi hingga kembali ke tanah air.', 'menu_book'),
(9, 'Ziarah Kota Bersejarah', 'Rombongan ziarah Masjid Quba, Jabal Uhud, Arafah, Mina, dan Muzdalifah.', 'map'),
(10, 'Perlengkapan Umroh', 'Koper, tas kabin, sajadah, mukena, dan buku doa harian.', 'luggage'),
(11, 'Wifi Selama Perjalanan', 'Kartu SIM dan hotspot tersedia untuk rombongan kecil.', 'wifi'),
(12, 'Air Zam-Zam 5 Liter', 'Pulang membawa air zam-zam kemasan resmi per orang.', 'water_drop');

-- ---------- Hotel ----------
INSERT INTO hotel (id, nama, kota, kelas, gambar) VALUES
(1, 'Raffles Makkah Palace', 'Makkah', 'Bintang 5', 'hotel-makkah.jpg'),
(2, 'Swissotel Al Maqam Makkah', 'Makkah', 'Bintang 5', 'hotel-makkah.jpg'),
(3, 'Pullman ZamZam Madinah', 'Madinah', 'Bintang 5', 'hotel-madinah.jpg'),
(4, 'Dar Al Taqwa Madinah', 'Madinah', 'Bintang 4', 'hotel-madinah.jpg');

-- ---------- Maskapai ----------
INSERT INTO maskapai (id, nama, logo) VALUES
(1, 'Saudia', NULL),
(2, 'Garuda Indonesia', NULL),
(3, 'Qatar Airways', NULL);

-- ---------- Paket Umroh ----------
INSERT INTO paket_umroh (id, nama, slug, deskripsi, durasi_hari, thumbnail, status, meta_title, meta_description, created_at) VALUES
(1, 'Rawdha Khidmat 12 Hari', 'rawdha-khidmat-12-hari',
 'Paket penuh khidmat 12 hari dengan tempo ibadah yang lapang di Madinah dan Makkah. Dirancang untuk keluarga besar dan jamaah lansia: dua hari penuh di Masjid Nabawi, empat hari di Masjidil Haram, kereta cepat Haramain, serta pendampingan dokter sepanjang perjalanan.',
 12, 'rawdha-khidmat.jpg', 'aktif',
 'Rawdha Khidmat 12 Hari — Paket Umroh Keluarga | Sakinah Journeys',
 'Umroh 12 hari dengan tempo lapang, hotel bintang 5 dekat Haram, kereta Haramain, dokter dan muthawwif pendamping.',
 '2026-08-10 09:00:00'),

(2, 'Safwa Itikaf 9 Hari', 'safwa-itikaf-9-hari',
 'Paket 9 hari yang dipadatkan dengan nilai ibadah tinggi: dua malam itikaf di Masjid Nabawi dan tawaf sunnah setiap hari. Cocok untuk jamaah muda dan profesional yang ingin kualitas ibadah maksimal dalam waktu singkat.',
 9, 'safwa-itikaf.jpg', 'aktif',
 'Safwa Itikaf 9 Hari — Paket Umroh Singkat | Sakinah Journeys',
 'Umroh 9 hari dengan itikaf di Masjid Nabawi, hotel dekat Haram, dan manasik intensif.',
 '2026-08-12 10:30:00'),

(3, 'Barakah Syawal 16 Hari', 'barakah-syawal-16-hari',
 'Perjalanan 16 hari yang menambahkan ziarah menyeluruh ke Arafah, Muzdalifah, Mina, Jabal Rahmah, dan Thaif. Waktu yang panjang memberi ruang belajar mendalam berserta muthawwif sepanjang hari.',
 16, 'barakah-syawal.jpg', 'aktif',
 'Barakah Syawal 16 Hari — Paket Umroh Ziarah Lengkap | Sakinah Journeys',
 'Umroh 16 hari dengan ziarah lengkap Arafah, Muzdalifah, Mina, dan Thaif berserta pendampingan penuh.',
 '2026-08-15 08:15:00'),

(4, 'Al-Haramain Nisfu 10 Hari', 'al-haramain-nisfu-10-hari',
 'Paket sepuluh hari yang menyeimbangkan waktu antara Masjid Nabawi dan Masjidil Haram, dilengkapi perjalanan kereta cepat Haramain yang nyaman. Pilihan favorit jamaah yang baru pertama kali berangkat.',
 10, 'alharamain-nisfu.jpg', 'aktif',
 'Al-Haramain Nisfu 10 Hari — Paket Umroh Kereta Cepat | Sakinah Journeys',
 'Umroh 10 hari dengan kereta cepat Haramain, hotel dekat Haram, dan manasik terpandu.',
 '2026-08-20 11:45:00'),

(5, 'Ramadhan Lailatul Qadr 14 Hari', 'ramadhan-lailatul-qadr-14-hari',
 'Paket 14 hari di penghujung Ramadhan dengan niat mengejar lailatul qadr bersama rombongan kecil. Tawaf dan sahab di malam-malam terakhir, diakhiri dengan takbiran kemenangan di tanah suci.',
 14, 'ramadhan-qadr.jpg', 'nonaktif',
 'Ramadhan Lailatul Qadr 14 Hari — Paket Umroh Ramadhan | Sakinah Journeys',
 'Umroh Ramadhan 14 hari mengejar lailatul qadr dengan rombongan kecil dan pendampingan penuh.',
 '2026-09-01 13:20:00');

-- ---------- Harga Paket ----------
INSERT INTO harga_paket (paket_id, tipe_kamar, harga) VALUES
(1, 'quad', 32500000), (1, 'triple', 36800000), (1, 'double', 41500000),
(2, 'quad', 27900000), (2, 'triple', 31200000), (2, 'double', 34900000),
(3, 'quad', 39800000), (3, 'triple', 44500000), (3, 'double', 49900000),
(4, 'quad', 29500000), (4, 'triple', 33400000), (4, 'double', 37600000),
(5, 'quad', 46500000), (5, 'triple', 51200000), (5, 'double', 56800000);

-- ---------- Jadwal Keberangkatan ----------
INSERT INTO jadwal_keberangkatan (paket_id, tanggal_berangkat, kuota, status) VALUES
(1, '2026-11-14', 24, 'dibuka'),
(1, '2027-01-09', 24, 'dibuka'),
(1, '2026-10-25', 24, 'penuh'),
(2, '2026-11-28', 20, 'dibuka'),
(2, '2027-02-13', 20, 'dibuka'),
(3, '2027-04-02', 26, 'dibuka'),
(3, '2027-05-14', 26, 'dibuka'),
(4, '2026-12-05', 22, 'dibuka'),
(4, '2027-03-06', 22, 'dibuka'),
(5, '2027-02-27', 18, 'dibuka');

-- ---------- Relasi Paket : Fasilitas ----------
INSERT INTO paket_fasilitas (paket_id, fasilitas_id)
SELECT p.id, f.id FROM paket_umroh p JOIN fasilitas f
WHERE (p.id = 1 AND f.id IN (1,2,3,4,5,6,7,8,9,10,11,12))
   OR (p.id = 2 AND f.id IN (1,2,3,4,5,6,8,9,10,12))
   OR (p.id = 3 AND f.id IN (1,2,3,4,5,6,7,8,9,10,11,12))
   OR (p.id = 4 AND f.id IN (1,2,3,4,5,6,8,9,10,12))
   OR (p.id = 5 AND f.id IN (1,2,3,4,5,6,7,8,9,10,12));

-- ---------- Relasi Paket : Hotel ----------
INSERT INTO paket_hotel (paket_id, hotel_id) VALUES
(1,1),(1,3),(2,2),(2,4),(3,1),(3,3),(4,2),(4,4),(5,1),(5,3);

-- ---------- Relasi Paket : Maskapai ----------
INSERT INTO paket_maskapai (paket_id, maskapai_id) VALUES
(1,1),(1,2),(2,1),(3,1),(3,3),(4,2),(5,1);

-- ---------- Itinerary Paket (rangkaian harian, dikelola via CMS) ----------
INSERT INTO paket_itinerary (paket_id, hari, judul, deskripsi, tag, is_highlight, urutan) VALUES
(1, 'Hari 1', 'Keberangkatan Santun & Ketibaan di Madinah Munawwarah',
 'Berkumpul di lounge khusus bandara untuk briefing santai dan doa bersama. Penerbangan langsung menuju Bandara Madinah. Setibanya, handling imigrasi dibantu tim ground handling, dilanjutkan perjalanan singkat menuju hotel. Check-in leluasa dan istirahat penuh untuk memulihkan stamina.',
 'CGK — MED', 0, 0),
(1, 'Hari 2 - 4', 'Ziarah Khidmat, Ibadah di Raudhah & Tadabbur Sejarah',
 'Ziarah makam Rasulullah SAW dan dua sahabat agung dengan bimbingan tasalsul muthawwif. Jadwal masuk Raudhah Syarifah yang teratur melalui izin resmi, didampingi pembimbing agar jamaah dapat sholat dan berdoa tanpa berdesak-desakan. Diisi pula ziarah Masjid Quba dan Kebun Kurma dengan transportasi berpendingin udara.',
 'Madinah Al-Munawwarah', 0, 1),
(1, 'Hari 5', 'Miqat Bir Ali, Kereta Cepat Haramain & Pelaksanaan Umroh',
 'Mandi sunnah ihram di hotel. Menuju Bir Ali untuk berniat ihram, dilanjutkan perjalanan nyaman dengan Kereta Cepat Haramain menuju Stasiun Makkah. Check-in di hotel pelataran, istirahat dan makan malam ringan. Ba''da Isya, pelaksanaan umroh utama dipandu muthawwif dengan pendampingan kursi roda bagi yang membutuhkan.',
 'Puncak Ibadah', 1, 2),
(1, 'Hari 6 - 9', 'Menyelami Kedamaian Makkah & Halaqah Bimbingan Batin',
 'Fokus memperbanyak sholat fardhu berjamaah dan thawaf sunnah di pelataran Ka''bah. Sore hari diadakan sesi tausiyah sirah nabawiyah di ruang pertemuan hotel. Tersedia opsi ziarah kota Makkah bagi yang berkenan.',
 'Makkah Al-Mukarramah', 0, 3),
(1, 'Hari 10 - 12', 'Thawaf Wada, Menuju Jeddah & Ketibaan di Tanah Air',
 'Pelaksanaan Thawaf Wada yang penuh haru dengan tempo santun. Menuju Bandara King Abdul Aziz Jeddah dengan bus privat. Penerbangan langsung menuju Jakarta membawa keberkahan dan kenangan ibadah yang menenteramkan sanubari keluarga.',
 'JED — CGK', 0, 4),

(2, 'Hari 1', 'Keberangkatan Santun & Ketibaan di Madinah Munawwarah',
 'Berkumpul di lounge khusus bandara untuk briefing santai dan doa bersama. Penerbangan langsung menuju Bandara Madinah. Setibanya, handling imigrasi dibantu tim ground handling, dilanjutkan perjalanan singkat menuju hotel. Check-in leluasa dan istirahat penuh untuk memulihkan stamina.',
 'CGK — MED', 0, 0),
(2, 'Hari 2 - 3', 'Ziarah Khidmat, Ibadah di Raudhah & Tadabbur Sejarah',
 'Ziarah makam Rasulullah SAW dan dua sahabat agung dengan bimbingan tasalsul muthawwif. Jadwal masuk Raudhah Syarifah yang teratur melalui izin resmi. Dilanjutkan itikaf ringan di Masjid Nabawi untuk jamaah yang ingin mendekatkan diri.',
 'Madinah Al-Munawwarah', 0, 1),
(2, 'Hari 4', 'Miqat Bir Ali, Kereta Cepat Haramain & Pelaksanaan Umroh',
 'Mandi sunnah ihram di hotel. Menuju Bir Ali untuk berniat ihram, dilanjutkan perjalanan nyaman dengan Kereta Cepat Haramain menuju Stasiun Makkah. Ba''da Isya, pelaksanaan umroh utama dipandu muthawwif dengan pendampingan kursi roda bagi yang membutuhkan.',
 'Puncak Ibadah', 1, 2),
(2, 'Hari 5 - 7', 'Menyelami Kedamaian Makkah & Halaqah Bimbingan Batin',
 'Fokus memperbanyak sholat fardhu berjamaah dan thawaf sunnah setiap hari di pelataran Ka''bah. Sore hari diadakan sesi tausiyah sirah nabawiyah di ruang pertemuan hotel.',
 'Makkah Al-Mukarramah', 0, 3),
(2, 'Hari 8 - 9', 'Thawaf Wada, Menuju Jeddah & Ketibaan di Tanah Air',
 'Pelaksanaan Thawaf Wada yang penuh haru dengan tempo santun. Menuju Bandara King Abdul Aziz Jeddah dengan bus privat. Penerbangan langsung menuju Jakarta membawa keberkahan dan kenangan yang menenteramkan sanubari keluarga.',
 'JED — CGK', 0, 4),

(3, 'Hari 1', 'Keberangkatan Santun & Ketibaan di Madinah Munawwarah',
 'Berkumpul di lounge khusus bandara untuk briefing santai dan doa bersama. Penerbangan langsung menuju Bandara Madinah. Setibanya, handling imigrasi dibantu tim ground handling, dilanjutkan perjalanan singkat menuju hotel. Check-in leluasa dan istirahat penuh untuk memulihkan stamina.',
 'CGK — MED', 0, 0),
(3, 'Hari 2 - 4', 'Ziarah Khidmat, Ibadah di Raudhah & Tadabbur Sejarah',
 'Ziarah makam Rasulullah SAW dan dua sahabat agung dengan bimbingan tasalsul muthawwif. Jadwal masuk Raudhah Syarifah yang teratur melalui izin resmi. Diisi pula ziarah Masjid Quba, Kebun Kurma, dan Jabal Uhud dengan transportasi berpendingin udara.',
 'Madinah Al-Munawwarah', 0, 1),
(3, 'Hari 5', 'Miqat Bir Ali, Kereta Cepat Haramain & Pelaksanaan Umroh',
 'Mandi sunnah ihram di hotel. Menuju Bir Ali untuk berniat ihram, dilanjutkan perjalanan nyaman dengan Kereta Cepat Haramain menuju Stasiun Makkah. Check-in di hotel pelataran, istirahat dan makan malam ringan. Ba''da Isya, pelaksanaan umroh utama dipandu muthawwif dengan pendampingan kursi roda bagi yang membutuhkan.',
 'Puncak Ibadah', 1, 2),
(3, 'Hari 6 - 13', 'Ziarah Menyeluruh Arafah, Muzdalifah, Mina & Halaqah Bimbingan Batin',
 'Fokus memperbanyak sholat fardhu berjamaah dan thawaf sunnah di pelataran Ka''bah. Ziarah menyeluruh ke Arafah, Muzdalifah, Mina, dan Jabal Rahmah dengan tempo lapang. Sore hari diadakan sesi tausiyah sirah nabawiyah di ruang pertemuan hotel.',
 'Makkah Al-Mukarramah', 0, 3),
(3, 'Hari 14 - 16', 'Thawaf Wada, Menuju Jeddah & Ketibaan di Tanah Air',
 'Pelaksanaan Thawaf Wada yang penuh haru dengan tempo santun. Menuju Bandara King Abdul Aziz Jeddah dengan bus privat. Penerbangan langsung menuju Jakarta membawa keberkahan dan kenangan ibadah yang menenteramkan sanubari keluarga.',
 'JED — CGK', 0, 4),

(4, 'Hari 1', 'Keberangkatan Santun & Ketibaan di Madinah Munawwarah',
 'Berkumpul di lounge khusus bandara untuk briefing santai dan doa bersama. Penerbangan langsung menuju Bandara Madinah. Setibanya, handling imigrasi dibantu tim ground handling, dilanjutkan perjalanan singkat menuju hotel. Check-in leluasa dan istirahat penuh untuk memulihkan stamina.',
 'CGK — MED', 0, 0),
(4, 'Hari 2 - 4', 'Ziarah Khidmat, Ibadah di Raudhah & Tadabbur Sejarah',
 'Ziarah makam Rasulullah SAW dan dua sahabat agung dengan bimbingan tasalsul muthawwif. Jadwal masuk Raudhah Syarifah yang teratur melalui izin resmi. Diisi pula ziarah Masjid Quba dan Kebun Kurma dengan transportasi berpendingin udara.',
 'Madinah Al-Munawwarah', 0, 1),
(4, 'Hari 5', 'Miqat Bir Ali, Kereta Cepat Haramain & Pelaksanaan Umroh',
 'Mandi sunnah ihram di hotel. Menuju Bir Ali untuk berniat ihram, dilanjutkan perjalanan nyaman dengan Kereta Cepat Haramain menuju Stasiun Makkah. Check-in di hotel pelataran, istirahat dan makan malam ringan. Ba''da Isya, pelaksanaan umroh utama dipandu muthawwif dengan pendampingan kursi roda bagi yang membutuhkan.',
 'Puncak Ibadah', 1, 2),
(4, 'Hari 6 - 8', 'Menyelami Kedamaian Makkah & Halaqah Bimbingan Batin',
 'Fokus memperbanyak sholat fardhu berjamaah dan thawaf sunnah di pelataran Ka''bah. Sore hari diadakan sesi tausiyah sirah nabawiyah di ruang pertemuan hotel. Tersedia opsi ziarah kota Makkah bagi yang berkenan.',
 'Makkah Al-Mukarramah', 0, 3),
(4, 'Hari 9 - 10', 'Thawaf Wada, Menuju Jeddah & Ketibaan di Tanah Air',
 'Pelaksanaan Thawaf Wada yang penuh haru dengan tempo santun. Menuju Bandara King Abdul Aziz Jeddah dengan bus privat. Penerbangan langsung menuju Jakarta membawa keberkahan dan kenangan ibadah yang menenteramkan sanubari keluarga.',
 'JED — CGK', 0, 4);

-- ---------- Testimoni Jamaah ----------
INSERT INTO testimoni (nama, asal_kota, teks, urutan) VALUES
('Hendra & Ibu Maryam', 'Jakarta',
 'Membawa ibu yang berusia 73 tahun sebelumnya terasa mencemaskan. Bersama Sakinah, ritme ziarah disesuaikan dengan stamina beliau, dokter selalu siaga, dan kami bisa shalat berjamaah tanpa terburu-buru.',
 1),
('dr. Farhan & Anisa', 'Surabaya',
 'Tidak ada riuh pengumuman yang membingungkan. Segalanya tertata hening, hotel berjarak dekat, sehingga energi kami sepenuhnya tertuju pada ibadah.',
 2);

-- ---------- Galeri ----------
INSERT INTO galeri (judul, gambar, kategori, created_at) VALUES
('Fajar hening di pelataran Masjid Nabawi', 'galeri-01.jpg', 'Madinah 2025', '2026-06-02 07:10:00'),
('Menunggu kereta cepat Haramain di Madinah', 'galeri-02.jpg', 'Perjalanan', '2026-06-02 07:25:00'),
('Pendamping menemani jamaah lansia naik kursi roda', 'galeri-03.jpg', 'Pendampingan', '2026-06-03 08:40:00'),
('Halaqah kecil di ruang santai berpemandangan pelataran', 'galeri-04.jpg', 'Manasik', '2026-06-04 16:05:00'),
('Pelukan haru ayah dan anak usai salat berjamaah', 'galeri-05.jpg', 'Keluarga', '2026-06-05 18:30:00'),
('Bincang santai dengan tenaga medis di lounge griya hotel', 'galeri-06.jpg', 'Pendampingan', '2026-06-06 10:15:00'),
('Rombongan jamaah sepuh di pohon palem Miqat Bir Ali', 'galeri-07.jpg', 'Perjalanan', '2026-06-07 06:50:00'),
('Manasik intensif kelompok kecil di hotel bintang lima', 'galeri-08.jpg', 'Manasik', '2026-06-08 09:20:00'),
('Berdiri hening di kaki bukit Jabal Uhud', 'galeri-09.jpg', 'Madinah 2025', '2026-06-09 06:35:00'),
('Koridor butik hotel Madinah menatap menara lewat jendela mashrabiya', 'galeri-10.jpg', 'Akomodasi', '2026-06-10 11:00:00'),
('Suasana pelataran Masjid Nabawi di pagi keemasan', 'galeri-11.jpg', 'Madinah 2025', '2026-06-11 06:05:00'),
('Cahaya lentera pagi di lantai marmer Madinah', 'galeri-12.jpg', 'Madinah 2025', '2026-06-12 05:45:00'),
('Kaki pegunungan Taif di bawah embun pagi', 'galeri-13.jpg', 'Ziarah', '2026-06-13 07:30:00'),
('Gerbang masuk hotel bintang lima di Madinah', 'galeri-14.jpg', 'Akomodasi', '2026-06-14 13:10:00'),
('Foyer megah hotel di Makkah dengan pilar marmer hangat', 'galeri-15.jpg', 'Akomodasi', '2026-06-15 13:25:00');

-- ---------- Artikel ----------
INSERT INTO artikel (judul, slug, konten, thumbnail, status, meta_title, meta_description, created_at) VALUES
('Menyiapkan Batin Sebelum Berangkat Umroh', 'menyiapkan-batin-sebelum-berangkat-umroh',
 'Umroh bukan sekadar perjalanan fisik. Ia adalah perpindahan keadaan hati dari ramai menuju tenang.\n\nMenyiapkan batin berarti memberi ruang untuk memaafkan, menata niat kembali, dan melatih kesabaran jauh sebelum tiket di tangan. Jamaah yang batinnya siap cenderung lebih mudah menyesuaikan diri dengan ritme Tanah Suci: antrian panjang, cuaca panas, dan jarak antar-sholat yang menuntut kesiapan.\n\nSesi manasik kami mulai dengan diskusi kecil tanpa panggung. Setiap jamaah diberi ruang menyebut kekhawatirannya, lalu bersama-sama menyusun jawaban sederhana. Dari sanalah ketenangan mulai tumbuh.\n\nDatang dengan hati yang lapang membuat pulang membawa perubahan yang bertahan lama.',
 'artikel-01.jpg', 'publish', 'Menyiapkan Batin Sebelum Berangkat Umroh', 'Panduan menyiapkan niat dan keadaan batin sebelum berangkat umroh bersama Sakinah Journeys.', '2026-09-01 09:00:00'),

('Adab dan Tata Cara Itikaf di Masjid Nabawi', 'adab-dan-tata-cara-itikaf-di-masjid-nabawi',
 'Itikaf adalah tinggal di masjid dengan niat beribadah. Di Masjid Nabawi, ruang-ruang selasar menjadi tempat paling tenang untuknya.\n\nMulailah dengan wudu terbaik, bawa Al-Qur’an kecil, dan tentukan durasi yang realistis. Dua puluh menit yang khusyuk lebih berharga daripada dua jam yang terpotong telepon.\n\nJaga area sekitar dari barang bawaan berlebih, dan hormati jamaah lain yang sedang sujud. Jika berbuka, pilihlah waktu yang tidak mengganggu sholat berjamaah.\n\nItikaf yang ringan namun konsisten akan membentuk kebiasaan ibadah setelah pulang.',
 'artikel-02.jpg', 'publish', 'Adab dan Tata Cara Itikaf di Masjid Nabawi', 'Panduan itikaf di Masjid Nabawi: adab, durasi, dan persiapan yang khusyuk.', '2026-09-08 09:00:00'),

('Air Zam-Zam: Sejarah, Adab, dan Cara Membawanya Pulang', 'air-zam-zam-sejarah-adab-dan-cara-membawanya-pulang',
 'Air Zam-Zam menyimpan sejarah panjang yang dimulai dari doa Nabi Isma’il. Hari ini, ia tetap menjadi kenangan paling dekat dengan Tanah Suci.\n\nAdab meminumnya dimulai dengan basmalah, duduk, dan menghadap kiblat jika memungkinkan. Cukup tiga teguk, lalu berdoa di antara tegukan.\n\nUntuk membawa pulang, kami menyediakan kemasan lima liter per jamaah dan memastikan label resmi tidak terlepas. Simpan di tempat bersih dan jangan dicampur dengan botol lain.\n\nSekecil apa pun adabnya, ia yang membedakan perjalanan biasa dari perjalanan yang penuh makna.',
 'artikel-03.jpg', 'publish', 'Air Zam-Zam: Sejarah, Adab, dan Cara Membawanya', 'Sejarah, adab minum, dan cara membawa air Zam-Zam pulang dengan benar.', '2026-09-15 09:00:00'),

('Mengatur Tempo Ibadah agar Tidak Kelelahan', 'mengatur-tempo-ibadah-agar-tidak-kelelahan',
 'Banyak jamaah pulang dengan kaki lecet dan ibadah terhenti di hari kelima. Penyebabnya biasa sama: terlalu bersemangat di awal.\n\nBagi hari menjadi tiga ritme. Pagi untuk ibadah utama, siang untuk istirahat dan makan yang cukup, sore untuk tawaf dan sahab ringan. Malam kembali ke masjid dengan tenang.\n\nGunakan alas kaki yang sudah diuji jalan jauh, dan sisakan satu hari tanpa rencana ziarah. Tubuh yang sehat menopang hati yang khusyuk.\n\nTempo yang terjaga membuat ibadah bertahan hingga hari terakhir, bukan hanya di hari pertama.',
 'artikel-04.jpg', 'publish', 'Mengatur Tempo Ibadah agar Tidak Kelelahan', 'Tips mengatur ritme ibadah dan istirahat selama umroh agar tetap sehat dan khusyuk.', '2026-09-22 09:00:00'),

('Empat Kesalahan Umum Jamaah Pertama Kali', 'empat-kesalahan-umum-jamaah-pertama-kali',
 'Perjalanan pertama selalu penuh antusiasme. Empat hal ini paling sering kami temui, dan semuanya bisa dicegah sejak awal.\n\nPertama, membawa barang berlebihan sehingga koper tidak muat di kabin. Kedua, melewatkan manasik karena merasa cukup menonton video. Ketiga, tidak menyiapkan uang kecil untuk kebutuhan harian. Keempat, menolak pendampingan dokter saat badan mulai tidak nyaman.\n\nSemuanya sepele, namun berpengaruh besar pada kenyamanan dua belas hari ke depan.\n\nDatang ke sesi persiapan, siapkan daftar singkat, dan izinkan diri untuk bertanya. Itu sudah lebih dari cukup.',
 'artikel-05.jpg', 'publish', 'Empat Kesalahan Umum Jamaah Pertama Kali', 'Empat kesalahan yang sering dialami jamaah umroh pertama kali dan cara menghindarinya.', '2026-09-29 09:00:00'),

('Catatan Internal: Rencana Rombongan Musim Semi', 'catatan-internal-rencana-rombongan-musim-semi',
 'Draf internal mengenai rencana keberangkatan musim semi tahun depan. Status: draft, belum dipublikasikan ke publik.',
 'artikel-06.jpg', 'draft', 'Rencana Rombongan Musim Semi', 'Catatan internal rencana keberangkatan musim semi.', '2026-10-02 09:00:00');

-- ---------- FAQ ----------
INSERT INTO faq (pertanyaan, jawaban, urutan) VALUES
('Berapa lama proses pengurusan visa umroh?', 'Rata-rata 7–14 hari kerja sejak seluruh dokumen lengkap kami terima. Kami menyarankan paling lambat 45 hari sebelum keberangkatan agar ada ruang perbaikan dokumen bila diperlukan.', 1),
('Apakah bisa mendaftar untuk jamaah lansia?', 'Bisa. Setiap rombongan kami sediakan pendamping khusus, dan tersedia kursi roda serta layanan prioritas antrean di masjid. Sampaikan kondisi kesehatan saat konsultasi agar kami menyiapkan pendampingan yang tepat.', 2),
('Bagaimana cara pembayaran dan jadwal terminnya?', 'Pendaftaran dimulai dengan tanda jadi 10%, dilanjutkan angsuran bulanan, dan pelunasan paling lambat 30 hari sebelum keberangkatan. Seluruh nominal dan jadwal tercantum dalam surat perjanjian.', 3),
('Apakah tersedia pembimbing manasik sebelum berangkat?', 'Ya. Tersedia tiga sesi manasik wajib sebelum keberangkatan, dilengkapi modul cetak dan sesi tanya jawab terbuka bersama muthawwif.', 4),
('Apa perbedaan tipe kamar quad, triple, dan double?', 'Quad berisi empat orang per kamar, triple tiga orang, dan double dua orang. Semua tipe menggunakan kasur dan kamar mandi dalam dengan fasilitas yang sama; yang membedakan adalah luas dan privasinya.', 5),
('Apakah ada pendampingan kesehatan selama perjalanan?', 'Tersedia dokter pendamping pada paket Rawdha Khidmat, Barakah Syawal, dan Ramadhan. Untuk paket lain, tersedia kotak obat dan rujukan klinik rekanan di setiap kota.', 6),
('Bisakah memilih tanggal keberangkatan sendiri?', 'Tanggal keberangkatan mengikuti jadwal yang sudah kami buka di halaman paket. Untuk rombongan keluarga minimal 12 orang, kami dapat membuka jadwal khusus sesuai permintaan.', 7),
('Bagaimana jika batal berangkat karena hal mendesak?', 'Kebijakan pembatalan mengikuti ketentuan yang tertuang dalam surat perjanjian, dengan skala pengembalian yang menyesuaikan waktu pemberitahuan. Hubungi kami segera agar prosesnya dapat dibantu.', 8);

-- ---------- Inquiry ----------
INSERT INTO inquiry (nama, kontak, email, paket_id, pesan, status, created_at) VALUES
('Ibu Nurhayati', '081298765432', 'nurhayati.keluarga@gmail.com', 1, 'Ingin bertanya paket Rawdha Khidmat untuk ibu saya yang berusia 72 tahun. Apakah kursi roda disiapkan di bandara?', 'baru', '2026-10-03 09:12:00'),
('Bapak Rudi Hartono', '081377889900', 'rudi.hartono@gmail.com', 4, 'Rencana berangkat berdua dengan istri bulan Desember. Mohon info kelas kamar double dan jadwal yang masih dibuka.', 'ditindaklanjuti', '2026-09-28 14:45:00'),
('Siti Aminah', '085611223344', NULL, 2, 'Apakah paket Safwa Itikaf masih tersedia untuk keberangkatan akhir November?', 'baru', '2026-10-05 20:30:00');

-- ---------- Meta SEO Halaman Publik (kosong = gunakan nilai bawaan halaman) ----------
INSERT INTO meta_halaman (kode, meta_title, meta_description) VALUES
('home',    NULL, NULL),
('paket',   NULL, NULL),
('galeri',  NULL, NULL),
('artikel', NULL, NULL),
('faq',     NULL, NULL),
('kontak',  NULL, NULL),
('tentang', NULL, NULL);
