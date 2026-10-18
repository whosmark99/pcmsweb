<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_session();

$adminId = current_admin_id();
$pdo = get_db();
$success = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_avatar') {
    [$ok, $msg] = handle_avatar_upload($_FILES['avatar'] ?? [], $adminId);
    if ($ok) $success = "Profile picture updated.";
    elseif ($msg) $error = $msg;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'remove_avatar') {
    delete_user_avatar($adminId);
    $success = "Profile picture removed.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    $cur = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $con = $_POST['confirm_password'] ?? '';

    if ($cur === '' || $new === '' || $con === '') $error = "All fields required.";
    elseif (strlen($new) < 6) $error = "New password must be 6+ chars.";
    elseif ($new !== $con) $error = "Passwords do not match.";
    else {
        $s = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $s->execute([$adminId]);
        $row = $s->fetch();
        if (!$row || !password_verify($cur, $row['password_hash'])) {
            $error = "Current password is incorrect.";
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $adminId]);
            $success = "Password changed successfully.";
        }
    }
}

$s = $pdo->prepare("SELECT id, username, email, role, created_at, avatar FROM users WHERE id = ?");
$s->execute([$adminId]);
$admin = $s->fetch();

$pageTitle = 'Admin Profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Profile - PCMS</title>
<link rel="stylesheet" href="../assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="app-wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="main-wrapper" id="mainWrapper">
        <?php include __DIR__ . '/../includes/admin_topbar.php'; ?>
        <section class="content">
            <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <div class="page-header"><h1>Admin Profile</h1></div>

            <div class="section-card">
                <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
                    <div class="avatar admin" style="width:96px;height:96px;font-size:36px;">
                        <?php if ($av = $admin['avatar']): ?>
                            <img src="../<?= htmlspecialchars($av) ?>" alt="Avatar">
                        <?php else: ?>
                            <?= htmlspecialchars(strtoupper(substr($admin['username'],0,1))) ?>
                        <?php endif; ?>
                    </div>
                    <div style="flex:1;min-width:220px;">
                        <h2 style="font-size:22px;margin-bottom:4px;">
                            <?= htmlspecialchars($admin['username']) ?>
                            <span class="badge badge-admin" style="vertical-align:middle;">admin</span>
                        </h2>
                        <p style="color:#6b7280;font-size:13px;margin:0;">
                            Administrator · Member since <?= htmlspecialchars(date('M d, Y', strtotime($admin['created_at']))) ?>
                        </p>

                        <form method="POST" enctype="multipart/form-data"
                              style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                            <input type="hidden" name="action" value="upload_avatar">
                            <input type="file" name="avatar" accept="image/*" required style="font-size:13px;">
                            <button type="submit" class="btn btn-primary btn-small">Upload Picture</button>
                        </form>

                        <?php if ($admin['avatar']): ?>
                            <form method="POST" style="margin-top:8px;"
                                  onsubmit="return confirm('Remove your profile picture?');">
                                <input type="hidden" name="action" value="remove_avatar">
                                <button type="submit" class="btn btn-danger btn-small"> Remove Picture</button>
                            </form>
                        <?php endif; ?>

                        <small style="color:#9ca3af;display:block;margin-top:8px;">
                            JPG, PNG, GIF or WEBP · max 2MB
                        </small>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <h2>Change Password</h2>
                <p>Update your password below</p>
                <form method="POST" style="margin-top:20px;">
                    <input type="hidden" name="action" value="change_password">
                    <div class="form-group"><label>Current Password</label>
                        <input type="password" name="current_password" required></div>
                    <div class="form-group"><label>New Password</label>
                        <input type="password" name="new_password" required minlength="6"></div>
                    <div class="form-group"><label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required minlength="6"></div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>

            
        </section>
    </div>
</div>
<script src="../assets/script.js"></script>
</body>
</html>