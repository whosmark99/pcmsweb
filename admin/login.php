<?php
require_once __DIR__ . '/../includes/auth.php';
if (is_admin_logged_in()) { header("Location: index.php"); exit; }

$error = ''; $success = '';
if (isset($_GET['msg'])) $success = $_GET['msg'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login_admin($username, $password)) { header("Location: index.php"); exit; }
    $error = "Invalid admin credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login - PCMS</title>
<link rel="stylesheet" href="../assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="auth-container" style="background:linear-gradient(135deg,#1a1a2e 0%,#dc2626 100%);">
    <div class="auth-card">
        <div style="text-align:center;margin-bottom:24px;">
            <div style="display:flex;justify-content:center;margin-bottom:14px;">
                <span class="logo-icon" style="width:56px;height:56px;" aria-hidden="true">
                    <img src="../assets/logo.svg" alt="" width="56" height="56">
                </span>
            </div>
            <h1>Admin Login</h1>
            <p class="auth-subtitle">Administrator access only</p>
        </div>

        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group"><label>Admin Username</label>
                <input type="text" name="username" required autocomplete="username"></div>
            <div class="form-group"><label>Password</label>
                <input type="password" name="password" required autocomplete="current-password"></div>
            <button type="submit" class="btn btn-primary btn-full">Login as Admin</button>
        </form>

        <p class="auth-link">Not admin? <a href="../login.php">Back to user →</a></p>
        
    </div>
</div>
<script src="../assets/script.js"></script>
</body>
</html>