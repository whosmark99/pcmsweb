<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/Algorithms.php';
require_admin_session();

$pdo = get_db();
$id  = (int)($_GET['id'] ?? 0);

$s = $pdo->prepare("
    SELECT c.*, u.username
    FROM contacts c
    JOIN users u ON u.id = c.user_id
    WHERE c.id = ?
");
$s->execute([$id]);
$contact = $s->fetch();

if (!$contact) {
    header("Location: contacts.php?err=" . urlencode("Contact not found."));
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    $errors = validate_contact($fullName, $email, $phone);
    if (empty($errors)) {
        $up = $pdo->prepare("UPDATE contacts SET full_name=?, email=?, phone=?, address=? WHERE id=?");
        $up->execute([$fullName, $email, $phone, $address, $id]);
        header("Location: contacts.php?msg=" . urlencode("Contact updated."));
        exit;
    }
    $contact['full_name'] = $fullName;
    $contact['email']     = $email;
    $contact['phone']     = $phone;
    $contact['address']   = $address;
}

$pageTitle = 'Edit Contact';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Contact - Admin</title>
<link rel="stylesheet" href="../assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="app-wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="main-wrapper" id="mainWrapper">
        <?php include __DIR__ . '/../includes/admin_topbar.php'; ?>
        <section class="content">
            <div class="page-header">
                <h1>Edit Contact <small style="color:var(--text-muted);font-size:14px;">(owner: @<?= htmlspecialchars($contact['username']) ?>)</small></h1>
                <a href="contacts.php" class="btn btn-secondary">← Back</a>
            </div>
            <div class="section-card form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group"><label>Full Name</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($contact['full_name']) ?>" required></div>
                    <div class="form-group"><label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>"></div>
                    <div class="form-group"><label>Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>"></div>
                    <div class="form-group"><label>Address</label>
                        <textarea name="address" rows="4"><?= htmlspecialchars($contact['address'] ?? '') ?></textarea></div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="contacts.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script src="../assets/script.js"></script>
</body>
</html>