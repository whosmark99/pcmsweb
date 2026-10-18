<?php
/**
 * Security helpers — CSRF, headers, session hardening, rate limiting, password policy.
 */

require_once __DIR__ . '/db.php';

/* =========================================================
   1. SECURITY HEADERS
   Call this at the top of every page (after session_start).
========================================================= */
function send_security_headers(): void
{
    if (headers_sent()) return;

    // Prevent clickjacking
    header("X-Frame-Options: DENY");

    // Prevent MIME type sniffing
    header("X-Content-Type-Options: nosniff");

    // XSS protection (legacy browsers)
    header("X-XSS-Protection: 1; mode=block");

    // Referrer policy
    header("Referrer-Policy: strict-origin-when-cross-origin");

    // Content Security Policy — allows own scripts + inline (needed for theme init)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");

    // HSTS — only enable if you're using HTTPS
    // header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
}

/* =========================================================
   2. HARDENED SESSION START
   Replaces bare session_start() in auth.php
========================================================= */
function secure_session_start(): void
{
    if (session_status() !== PHP_SESSION_NONE) return;

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
               || (($_SERVER['SERVER_PORT'] ?? 80) == 443);

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,     // only over HTTPS in production
        'httponly' => true,          // no JS access
        'samesite' => 'Strict',      // CSRF mitigation
    ]);

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.gc_maxlifetime', 1800); // 30 min idle timeout

    session_start();

    // Idle timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 1800) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();

    // Regenerate ID every 15 minutes
    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
    } elseif (time() - $_SESSION['created_at'] > 900) {
        session_regenerate_id(true);
        $_SESSION['created_at'] = time();
    }
}

/* =========================================================
   3. CSRF TOKEN
========================================================= */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
         . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function require_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!csrf_verify($token)) {
            http_response_code(419);
            die("Session expired or invalid request. Please go back and try again.");
        }
    }
}

/* =========================================================
   4. PASSWORD POLICY
========================================================= */
function validate_password_strength(string $password): array
{
    $errors = [];
    if (strlen($password) < 8)          $errors[] = "Password must be at least 8 characters.";
    if (!preg_match('/[A-Z]/', $password)) $errors[] = "Password must contain at least one uppercase letter.";
    if (!preg_match('/[a-z]/', $password)) $errors[] = "Password must contain at least one lowercase letter.";
    if (!preg_match('/[0-9]/', $password)) $errors[] = "Password must contain at least one number.";
    return $errors;
}

/* =========================================================
   5. LOGIN RATE LIMITING (per username + IP)
========================================================= */
function check_login_rate_limit(string $username, string $scope = 'user'): bool
{
    $pdo = get_db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = hash('sha256', $scope . '|' . strtolower($username) . '|' . $ip);

    // Clean old entries (> 15 min)
    $pdo->prepare("DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 15 MINUTE)")->execute();

    // Count failures in last 15 min
    $s = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE attempt_key = ?");
    $s->execute([$key]);
    $count = (int)$s->fetchColumn();

    return $count < 5; // max 5 failed attempts per 15 min
}

function record_login_failure(string $username, string $scope = 'user'): void
{
    $pdo = get_db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = hash('sha256', $scope . '|' . strtolower($username) . '|' . $ip);

    $s = $pdo->prepare("INSERT INTO login_attempts (attempt_key, ip_address, username) VALUES (?,?,?)");
    $s->execute([$key, $ip, $username]);
}

function clear_login_failures(string $username, string $scope = 'user'): void
{
    $pdo = get_db();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = hash('sha256', $scope . '|' . strtolower($username) . '|' . $ip);
    $pdo->prepare("DELETE FROM login_attempts WHERE attempt_key = ?")->execute([$key]);
}

/* =========================================================
   6. SAFE REDIRECT (blocks open redirect)
========================================================= */
function safe_redirect(string $url, string $fallback = 'index.php'): void
{
    // Only allow relative URLs (no scheme, no host)
    if (preg_match('#^https?://#i', $url) || strpos($url, '//') === 0) {
        $url = $fallback;
    }
    header("Location: " . $url);
    exit;
}