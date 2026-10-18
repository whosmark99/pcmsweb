<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/Algorithms.php';
require_user_session();

$userId = current_user_id();
$pdo = get_db();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    $errors = validate_contact($fullName, $email, $phone);
    if (empty($errors)) {
        try {
            $s = $pdo->prepare("INSERT INTO contacts (user_id, full_name, email, phone, address) VALUES (?,?,?,?,?)");
            $s->execute([$userId, $fullName, $email, $phone, $address]);
            header("Location: contacts.php?msg=" . urlencode("Contact added successfully."));
            exit;
        } catch (PDOException $e) {
            // FK violation = stale session user_id
            if ($e->getCode() === '23000') {
                logout_user();
                header("Location: login.php?msg=" . urlencode("Your session expired. Please log in again."));
                exit;
            }
            throw $e;
        }
    }
}
$pageTitle = 'Add Contact';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Contact - PCMS</title>
<link rel="stylesheet" href="assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="app-wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="main-wrapper" id="mainWrapper">
        <?php include __DIR__ . '/includes/topbar.php'; ?>
        <section class="content">
            <div class="page-header"><h1>Add Contact</h1></div>
            <div class="section-card form-card">
                <p>Fill in the details to create a new contact.</p>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group"><label>Full Name</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required></div>
                    <div class="form-group"><label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"></div>
                    <div class="form-group"><label>Phone Number</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"></div>
                    <div class="form-group"><label>Address</label>
                        <textarea name="address" rows="4"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea></div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Contact</button>
                        <a href="contacts.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script src="assets/script.js"></script>
</body>
</html>