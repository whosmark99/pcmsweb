<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_session();

$pdo = get_db();
$id = (int)($_GET['id'] ?? 0);

$s = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$s->execute([$id]);
$user = $s->fetch();

if (!$user) { header("Location: users.php?err=" . urlencode("User not found.")); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';

    if ($username === '') $errors[] = "Username is required.";
    if (strlen($username) < 3) $errors[] = "Username must be 3+ chars.";
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";

    $c = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $c->execute([$username, $id]);
    if ($c->fetch()) $errors[] = "Username already taken.";

    if (!in_array($role, ['user','admin'], true)) $role = 'user';

    if (empty($errors)) {
        if ($password !== '') {
            if (strlen($password) < 6) $errors[] = "Password must be 6+ chars.";
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $up = $pdo->prepare("UPDATE users SET username=?, email=?, role=?, password_hash=? WHERE id=?");
                $up->execute([$username, $email, $role, $hash, $id]);
            }
        } else {
            $up = $pdo->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
            $up->execute([$username, $email, $role, $id]);
        }
        if (empty($errors)) { header("Location: users.php?msg=" . urlencode("User updated.")); exit; }
    }
    $user['username']=$username; $user['email']=$email; $user['role']=$role;
}

$pageTitle = 'Edit User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User - PCMS</title>
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
                <h1>Edit User #<?= (int)$user['id'] ?></h1>
                <a href="users.php" class="btn btn-secondary">← Back</a>
            </div>
            <div class="section-card form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group"><label>Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required></div>
                    <div class="form-group"><label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"></div>
                    <div class="form-group"><label>Role</label>
                        <select name="role">
                            <option value="user"  <?= $user['role']==='user'?'selected':'' ?>>User</option>
                            <option value="admin" <?= $user['role']==='admin'?'selected':'' ?>>Admin</option>
                        </select></div>
                    <div class="form-group"><label>New Password <small style="color:#9ca3af;">(blank = keep)</small></label>
                        <input type="password" name="password" minlength="6"></div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="users.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script src="../assets/script.js"></script>
</body>
</html>