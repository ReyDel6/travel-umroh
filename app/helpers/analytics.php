<?php
declare(strict_types=1);

/* =========================================================
 * Analitik traffic publik (Spec: docs/SPEC-TRAFFIC-ANALYTICS.md)
 * Pencatatan first-party, zero-dependency, tidak memblokir render.
 * =======================================================*/

/** Ekstraksi IP asli pengunjung (dukung Cloudflare / X-Forwarded-For). */
function get_client_ip(): string
{
    $keys = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR',
    ];
    foreach ($keys as $k) {
        if (!empty($_SERVER[$k])) {
            $ipList = explode(',', (string) $_SERVER[$k]);
            $ip = trim($ipList[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '127.0.0.1';
}

/** Deteksi sparse bot/crawler dari User-Agent (dikecualikan dari grafik riil). */
function is_bot_ua(string $ua): bool
{
    return (bool) preg_match('/bot|crawl|spider|slurp|facebookexternalhit|whatsapp|preview|headless|phantom/i', $ua);
}

/** Deteksi browser & OS ringan via regex User-Agent. Return [browser, os]. */
function detect_browser(string $ua): array
{
    $browser = 'Other';
    if (preg_match('/(edg|edge)\/[\d\.]+/i', $ua)) {
        $browser = 'Edge';
    } elseif (preg_match('/(opera|opr)\/[\d\.]+/i', $ua)) {
        $browser = 'Opera';
    } elseif (preg_match('/samsungbrowser\/[\d\.]+/i', $ua)) {
        $browser = 'Samsung Internet';
    } elseif (preg_match('/ucbrowser\/[\d\.]+/i', $ua)) {
        $browser = 'UC Browser';
    } elseif (preg_match('/chrome\/[\d\.]+/i', $ua)) {
        $browser = 'Chrome';
    } elseif (preg_match('/version\/[\d\.]+.*safari/i', $ua)) {
        $browser = 'Safari';
    } elseif (preg_match('/firefox\/[\d\.]+/i', $ua)) {
        $browser = 'Firefox';
    }

    $os = 'Other';
    if (preg_match('/(windows nt|windows phone)/i', $ua)) {
        $os = 'Windows';
    } elseif (preg_match('/android/i', $ua)) {
        $os = 'Android';
    } elseif (preg_match('/(iphone|ipad|ipod)/i', $ua)) {
        $os = 'iOS';
    } elseif (preg_match('/mac os x/i', $ua)) {
        $os = 'macOS';
    } elseif (preg_match('/linux/i', $ua)) {
        $os = 'Linux';
    }
    return [$browser, $os];
}

/** Nama negara dari kode ISO 2 huruf (fallback nama bila header CF hanya memberi kode). */
function country_name(string $code): string
{
    $code = strtoupper(trim($code));
    $map = [
        'ID' => 'Indonesia',
        'SA' => 'Arab Saudi',
        'MY' => 'Malaysia',
        'SG' => 'Singapura',
        'BR' => 'Brasil',
        'JP' => 'Jepang',
        'US' => 'Amerika Serikat',
        'GB' => 'Inggris',
        'AE' => 'Uni Emirat Arab',
        'EG' => 'Mesir',
        'OM' => 'Oman',
        'QA' => 'Qatar',
        'TH' => 'Thailand',
        'AU' => 'Australia',
        'NL' => 'Belanda',
    ];
    return $map[$code] ?? $code;
}

/** Deteksi negara asal: header Cloudflare dulu, fallback lookup geolokasi (cache session). */
function detect_country(): array
{
    // Jalur utama: server di balik Cloudflare menyediakan kode negara.
    $code = trim((string) ($_SERVER['HTTP_CF_IPCOUNTRY'] ?? ''));
    if (strlen($code) === 2) {
        return [strtoupper($code), country_name($code)];
    }

    // IP lokal/private → Indonesia (local/dev tanpa biaya lookup).
    $ip = get_client_ip();
    if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return ['ID', 'Indonesia (Local)'];
    }

    // Cache per session agar tidak ada latensi berulang.
    if (isset($_SESSION['geo_ip']) && $_SESSION['geo_ip'] === $ip && !empty($_SESSION['geo_code'])) {
        return [$_SESSION['geo_code'], $_SESSION['geo_name']];
    }

    $lookup = geoip_lookup($ip);
    if (isset($lookup['code'], $lookup['name'])) {
        $_SESSION['geo_ip'] = $ip;
        $_SESSION['geo_code'] = $lookup['code'];
        $_SESSION['geo_name'] = $lookup['name'];
        return [$lookup['code'], $lookup['name']];
    }
    return ['ID', 'Indonesia'];
}

/** Lookup geolokasi publik (ip-api.com) dengan timeout pendek — gagal = diam diam ke default. */
function geoip_lookup(string $ip): array
{
    $ctx = stream_context_create(['http' => [
        'timeout' => 2,
        'ignore_errors' => true,
        'header' => "User-Agent: SakinahJourneys/1.0\r\n",
    ]]);
    try {
        $raw = @file_get_contents('http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,countryCode,country&lang=id', false, $ctx);
        if ($raw === false) {
            return [];
        }
        $data = json_decode($raw, true);
        if (is_array($data) && ($data['status'] ?? '') === 'success' && !empty($data['countryCode'])) {
            $name = trim((string) ($data['country'] ?? country_name((string) $data['countryCode'])));
            return ['code' => strtoupper((string) $data['countryCode']), 'name' => $name];
        }
    } catch (Throwable) {
        // abaikan: fallback default
    }
    return [];
}

/** Samarkan IP untuk tampilan admin (privasi). 180.252.11.10 → 180.252.11.xxx */
function mask_ip(string $ip): string
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        $parts = explode('.', $ip);
        $parts[3] = 'xxx';
        return implode('.', $parts);
    }
    if (str_contains($ip, ':')) {
        $seg = explode(':', $ip);
        return implode(':', array_slice($seg, 0, 2)) . ':xxxx:xxxx';
    }
    return $ip;
}

/**
 * Catat kunjungan publik ke visitor_logs + upsert visitor_daily_stats.
 * - Dikecualikan: admin yang login, bot, User-Agent kosong.
 * - Debounce: maksimal 1 entri per sesi per 30 menit (Spec §8.1).
 * - Gagal apapun tidak pernah menggagalkan render halaman.
 */
function log_visitor(): void
{
    if (is_logged_in()) {
        return;
    }
    $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($ua === '' || is_bot_ua($ua)) {
        return;
    }
    if (isset($_SESSION['visitor_logged_at']) && (time() - (int) $_SESSION['visitor_logged_at']) < 1800) {
        return;
    }
    $_SESSION['visitor_logged_at'] = time();

    $ip = get_client_ip();
    [$browser, $os] = detect_browser($ua);
    [$cc, $cn] = detect_country();
    $page = substr((string) ($_SERVER['REQUEST_URI'] ?? '/'), 0, 255);
    $ref = !empty($_SERVER['HTTP_REFERER']) ? substr((string) $_SERVER['HTTP_REFERER'], 0, 255) : null;

    try {
        $db = db();
        $isUnique = 0;
        $st = $db->prepare('SELECT 1 FROM visitor_logs WHERE ip_address = :ip AND DATE(visited_at) = CURDATE() LIMIT 1');
        $st->execute(['ip' => $ip]);
        if ($st->fetch() === false) {
            $isUnique = 1;
        }

        $ins = $db->prepare(
            'INSERT INTO visitor_logs (ip_address, user_agent, browser, os, country_code, country_name, page_url, referer)
             VALUES (:ip, :ua, :browser, :os, :cc, :cn, :url, :ref)'
        );
        $ins->execute([
            'ip'      => $ip,
            'ua'      => substr($ua, 0, 255),
            'browser' => $browser,
            'os'      => $os,
            'cc'      => $cc,
            'cn'      => $cn,
            'url'     => $page,
            'ref'     => $ref,
        ]);

        $agg = $db->prepare(
            'INSERT INTO visitor_daily_stats (tanggal, total_hits, unique_visitors, top_country_code, top_browser, updated_at)
             VALUES (CURDATE(), 1, :uniq, :cc, :browser, NOW())
             ON DUPLICATE KEY UPDATE
               total_hits = total_hits + 1,
               unique_visitors = unique_visitors + :uniq2,
               top_country_code = VALUES(top_country_code),
               top_browser = VALUES(top_browser),
               updated_at = NOW()'
        );
        $agg->execute(['uniq' => $isUnique, 'uniq2' => $isUnique, 'cc' => $cc, 'browser' => $browser]);
    } catch (Throwable) {
        // Pencatatan analytics tidak boleh mengganggu permintaan publik.
    }
}