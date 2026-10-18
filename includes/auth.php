<?php
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

/* ============ USER AUTH ============ */
function register_user(string $username, string $password, string $email = ''): array
{
    $username = trim($username);
    $email = trim($email);

    if ($username === '') return [false, "Username is required."];
    if (strlen($username) < 3) return [false, "Username must be at least 3 characters."];
    if (strlen($password) < 6) return [false, "Password must be at least 6 characters."];
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
        return [false, "Please enter a valid email address."];

    $pdo = get_db();
    $c = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $c->execute([$username]);
    if ($c->fetch()) return [false, "That username is already taken."];

    if ($email !== '') {
        $c = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $c->execute([$email]);
        if ($c->fetch()) return [false, "That email is already registered."];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $s = $pdo->prepare("INSERT INTO users (username, password_hash, email, role) VALUES (?,?,?, 'user')");
    $s->execute([$username, $hash, $email]);
    return [true, "Account created successfully."];
}

function login_user(string $username, string $password): bool
{
    $pdo = get_db();
    $s = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'user'");
    $s->execute([trim($username)]);
    $user = $s->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        if (isset($user['is_active']) && !$user['is_active']) return false;
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    return false;
}

function require_user_session(): void
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $pdo = get_db();
    $s = $pdo->prepare("SELECT id FROM users WHERE id = ? AND is_active = 1 AND role = 'user'");
    $s->execute([$_SESSION['user_id']]);
    if (!$s->fetch()) {
        logout_user();
        header("Location: login.php?msg=" . urlencode("Your session expired. Please log in again."));
        exit;
    }
}

function current_user_id(): ?int { return $_SESSION['user_id'] ?? null; }
function current_username(): string { return $_SESSION['username'] ?? 'User'; }
function is_user_logged_in(): bool { return isset($_SESSION['user_id']); }

function logout_user(): void
{
    unset($_SESSION['user_id'], $_SESSION['username']);
}

/* ============ ADMIN AUTH ============ */
function register_admin(string $username, string $password, string $email = ''): array
{
    $username = trim($username);
    $email = trim($email);

    if ($username === '') return [false, "Username is required."];
    if (strlen($username) < 3) return [false, "Username must be at least 3 characters."];
    if (strlen($password) < 6) return [false, "Password must be at least 6 characters."];
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
        return [false, "Please enter a valid email address."];

    $pdo = get_db();
    $c = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $c->execute([$username]);
    if ($c->fetch()) return [false, "That username is already taken."];

    if ($email !== '') {
        $c = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $c->execute([$email]);
        if ($c->fetch()) return [false, "That email is already registered."];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $s = $pdo->prepare("INSERT INTO users (username, password_hash, email, role) VALUES (?,?,?, 'admin')");
    $s->execute([$username, $hash, $email]);
    return [true, "Admin account created successfully."];
}

function login_admin(string $username, string $password): bool
{
    $pdo = get_db();
    $s = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = 'admin'");
    $s->execute([trim($username)]);
    $user = $s->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        if (isset($user['is_active']) && !$user['is_active']) return false;
        $_SESSION['admin_id']       = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        return true;
    }
    return false;
}

function require_admin_session(): void
{
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }

    $pdo = get_db();
    $s = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin'");
    $s->execute([$_SESSION['admin_id']]);
    if (!$s->fetch()) {
        logout_admin();
        header("Location: login.php?msg=" . urlencode("Your admin session expired. Please log in again."));
        exit;
    }
}

function current_admin_id(): ?int { return $_SESSION['admin_id'] ?? null; }
function current_admin_username(): string { return $_SESSION['admin_username'] ?? 'Admin'; }
function is_admin_logged_in(): bool { return isset($_SESSION['admin_id']); }

function logout_admin(): void
{
    unset($_SESSION['admin_id'], $_SESSION['admin_username']);
}

/* ============ HELPER QUERIES ============ */
function get_all_users(): array
{
    return get_db()->query("
        SELECT u.*,
        (SELECT COUNT(*) FROM contacts WHERE user_id = u.id) AS contact_count,
        (SELECT COUNT(*) FROM deleted_contacts WHERE user_id = u.id) AS deleted_count
        FROM users u ORDER BY u.created_at DESC
    ")->fetchAll();
}

function search_users(string $keyword): array
{
    $pdo = get_db();
    $keyword = trim($keyword);
    if ($keyword === '') return get_all_users();

    $like = '%' . $keyword . '%';
    $s = $pdo->prepare("
        SELECT u.*,
        (SELECT COUNT(*) FROM contacts WHERE user_id = u.id) AS contact_count,
        (SELECT COUNT(*) FROM deleted_contacts WHERE user_id = u.id) AS deleted_count
        FROM users u
        WHERE u.username LIKE ? OR u.email LIKE ? OR CAST(u.id AS CHAR) = ?
        ORDER BY u.created_at DESC
    ");
    $s->execute([$like, $like, $keyword]);
    return $s->fetchAll();
}

function toggle_user_active(int $id): bool
{
    $s = get_db()->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ? AND role != 'admin'");
    return $s->execute([$id]);
}

function get_admin_stats(): array
{
    $pdo = get_db();
    return [
        'total_users'    => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
        'total_admins'   => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn(),
        'total_contacts' => (int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
        'total_deleted'  => (int)$pdo->query("SELECT COUNT(*) FROM deleted_contacts")->fetchColumn(),
        'active_users'   => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_active=1 AND role='user'")->fetchColumn(),
    ];
}

function get_all_contacts_admin(int $limit = 200): array
{
    $s = get_db()->prepare("
        SELECT c.*, u.username, u.avatar FROM contacts c
        JOIN users u ON u.id = c.user_id
        ORDER BY c.created_at DESC LIMIT ?
    ");
    $s->bindValue(1, $limit, PDO::PARAM_INT);
    $s->execute();
    return $s->fetchAll();
}

function get_user_contacts_admin(int $userId): array
{
    $s = get_db()->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at DESC");
    $s->execute([$userId]);
    return $s->fetchAll();
}

function get_user_deleted_admin(int $userId): array
{
    $s = get_db()->prepare("SELECT * FROM deleted_contacts WHERE user_id = ? ORDER BY deleted_at DESC");
    $s->execute([$userId]);
    return $s->fetchAll();
}

function get_user_contact_count(int $userId): int
{
    $s = get_db()->prepare("SELECT COUNT(*) FROM contacts WHERE user_id = ?");
    $s->execute([$userId]);
    return (int)$s->fetchColumn();
}

function get_user_deleted_count(int $userId): int
{
    $s = get_db()->prepare("SELECT COUNT(*) FROM deleted_contacts WHERE user_id = ?");
    $s->execute([$userId]);
    return (int)$s->fetchColumn();
}

/* ============ AVATAR HELPERS ============ */
function get_user_avatar(?int $userId): ?string
{
    if (!$userId) return null;
    $s = get_db()->prepare("SELECT avatar FROM users WHERE id = ?");
    $s->execute([$userId]);
    $row = $s->fetch();
    return $row['avatar'] ?? null;
}

function current_user_avatar(): ?string
{
    return get_user_avatar(current_user_id());
}

function current_admin_avatar(): ?string
{
    return get_user_avatar(current_admin_id());
}

function handle_avatar_upload(array $file, int $userId): array
{
    if (!isset($file['error']) || is_array($file['error'])) {
        return [false, "Invalid file upload."];
    }
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return [false, "No file was selected."];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, "Upload failed (error code " . $file['error'] . ")."];
    }
    if ($file['size'] > 2 * 1024 * 1024) {
        return [false, "Image must be smaller than 2MB."];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    if (!isset($allowed[$mime])) {
        return [false, "Only JPG, PNG, GIF, or WEBP images are allowed."];
    }
    if (!@getimagesize($file['tmp_name'])) {
        return [false, "File is not a valid image."];
    }

    $ext      = $allowed[$mime];
    $filename = 'avatar_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $destDir  = __DIR__ . '/../uploads/avatars/';
    $destPath = $destDir . $filename;

    if (!is_dir($destDir)) {
        @mkdir($destDir, 0755, true);
    }
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return [false, "Failed to save the uploaded file."];
    }

    $old = get_user_avatar($userId);
    if ($old && str_starts_with($old, 'uploads/avatars/')) {
        $oldPath = __DIR__ . '/../' . $old;
        if (is_file($oldPath)) @unlink($oldPath);
    }

    $relPath = 'uploads/avatars/' . $filename;
    $up = get_db()->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $up->execute([$relPath, $userId]);

    return [true, $relPath];
}

function delete_user_avatar(int $userId): void
{
    $old = get_user_avatar($userId);
    if ($old && str_starts_with($old, 'uploads/avatars/')) {
        $oldPath = __DIR__ . '/../' . $old;
        if (is_file($oldPath)) @unlink($oldPath);
    }
    get_db()->prepare("UPDATE users SET avatar = NULL WHERE id = ?")->execute([$userId]);
}