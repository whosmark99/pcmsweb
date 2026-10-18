<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_user_session();

$userId = current_user_id();
$pdo = get_db();
$id = (int)($_GET['id'] ?? 0);

$s = $pdo->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
$s->execute([$id, $userId]);
$contact = $s->fetch();

if (!$contact) { header("Location: contacts.php?msg=" . urlencode("Contact not found.")); exit; }

try {
    $pdo->beginTransaction();
    $ins = $pdo->prepare("INSERT INTO deleted_contacts (original_id, user_id, full_name, email, phone, address) VALUES (?,?,?,?,?,?)");
    $ins->execute([$contact['id'], $userId, $contact['full_name'], $contact['email'], $contact['phone'], $contact['address']]);
    $pdo->prepare("DELETE FROM contacts WHERE id=? AND user_id=?")->execute([$id, $userId]);
    $pdo->commit();
    header("Location: contacts.php?msg=" . urlencode("Contact deleted."));
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    header("Location: contacts.php?msg=" . urlencode("Delete failed."));
    exit;
}