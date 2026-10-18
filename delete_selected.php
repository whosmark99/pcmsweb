<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_user_session();

$userId = current_user_id();
$pdo = get_db();

$ids = $_POST['ids'] ?? [];
if (!is_array($ids) || empty($ids)) {
    header("Location: contacts.php?msg=" . urlencode("No contacts selected."));
    exit;
}

$ids = array_values(array_filter(array_map('intval', $ids), fn($i) => $i > 0));
if (empty($ids)) {
    header("Location: contacts.php?msg=" . urlencode("Invalid selection."));
    exit;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));

try {
    $pdo->beginTransaction();
    $params = array_merge([$userId], $ids);
    $s = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? AND id IN ($placeholders)");
    $s->execute($params);
    $rows = $s->fetchAll();

    if (empty($rows)) {
        $pdo->rollBack();
        header("Location: contacts.php?msg=" . urlencode("No matching contacts found."));
        exit;
    }

    $ins = $pdo->prepare("INSERT INTO deleted_contacts (original_id, user_id, full_name, email, phone, address) VALUES (?,?,?,?,?,?)");
    $del = $pdo->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");

    foreach ($rows as $r) {
        $ins->execute([$r['id'], $userId, $r['full_name'], $r['email'], $r['phone'], $r['address']]);
        $del->execute([$r['id'], $userId]);
    }

    $pdo->commit();
    $n = count($rows);
    header("Location: contacts.php?msg=" . urlencode("$n contact" . ($n>1?'s':''). " moved to Recently Deleted."));
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header("Location: contacts.php?msg=" . urlencode("Bulk delete failed."));
    exit;
}