<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_session();

$id     = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id === current_admin_id()) {
    header("Location: users.php?err=" . urlencode("You cannot modify your own admin account here."));
    exit;
}

$pdo = get_db();
$s = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$s->execute([$id]);
$user = $s->fetch();

if (!$user) { header("Location: users.php?err=" . urlencode("User not found.")); exit; }
if ($user['role'] === 'admin') {
    header("Location: users.php?err=" . urlencode("Cannot modify admin accounts."));
    exit;
}

if ($action === 'toggle') {
    toggle_user_active($id);
    header("Location: users.php?msg=" . urlencode("User status updated."));
    exit;
}

if ($action === 'delete') {
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM contacts WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM deleted_contacts WHERE user_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'")->execute([$id]);
        $pdo->commit();
        header("Location: users.php?msg=" . urlencode("User deleted."));
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        header("Location: users.php?err=" . urlencode("Delete failed."));
        exit;
    }
}

header("Location: users.php");
exit;