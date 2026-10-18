<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/DataStructures.php';
require_user_session();

$userId = current_user_id();
$pdo = get_db();

try {
    $s = $pdo->prepare("SELECT * FROM deleted_contacts WHERE user_id = ? ORDER BY deleted_at ASC, id ASC");
    $s->execute([$userId]);
    $rows = $s->fetchAll();

    if (empty($rows)) {
        header("Location: recently_deleted.php?msg=" . urlencode("Nothing to restore."));
        exit;
    }

    $stack = new Stack();
    foreach ($rows as $r) $stack->push($r);
    $restored = $stack->pop();

    $pdo->beginTransaction();
    $ins = $pdo->prepare("INSERT INTO contacts (user_id, full_name, email, phone, address) VALUES (?,?,?,?,?)");
    $ins->execute([$userId, $restored['full_name'], $restored['email'], $restored['phone'], $restored['address']]);
    $pdo->prepare("DELETE FROM deleted_contacts WHERE id=? AND user_id=?")->execute([$restored['id'], $userId]);
    $pdo->commit();

    header("Location: recently_deleted.php?msg=" . urlencode("Contact restored."));
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header("Location: recently_deleted.php?msg=" . urlencode("Restore failed."));
    exit;
}