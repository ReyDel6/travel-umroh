<?php
declare(strict_types=1);

/* =========================================================
 * Autentikasi admin berbasis PHP session
 * =======================================================*/

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443')) {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

function is_logged_in(): bool
{
    return isset($_SESSION['admin_id']);
}

function requireLogin(): void
{
    if (!is_logged_in()) {
        header('Location: ' . url('admin/login.php'));
        exit;
    }
}

function try_login(PDO $db, string $username, string $password): bool
{
    $stmt = $db->prepare('SELECT id, username, password_hash FROM admin WHERE username = :u LIMIT 1');
    $stmt->execute(['u' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password_hash'])) {
        session_regenerate_id(true);
        unset($_SESSION['csrf_token']); // rotasi token CSRF setelah login sukses
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        return true;
    }
    return false;
}

function do_logout(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $flash = $_SESSION['flash'] ?? null;
    $_SESSION = [];
    session_destroy(); // hapus sesi di server (PRD §6.1), bukan hanya datanya
    session_start();   // sesi baru khusus menyimpan flash
    if ($flash !== null) {
        $_SESSION['flash'] = $flash;
    }
}
