<?php
require_once __DIR__ . '/includes/auth.php';
if (is_user_logged_in()) { header("Location: index.php"); exit; }

$error = ''; $success = '';
if (isset($_GET['registered'])) $success = "Account created. Please log in.";
if (isset($_GET['msg'])) $success = $_GET['msg'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (login_user($username, $password)) { header("Location: index.php"); exit; }
    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - PCMS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="auth-container">
        <div class="auth-card">
                <div style="text-align:center;margin-bottom:24px;">
            <div style="display:flex;justify-content:center;margin-bottom:14px;">
                <span class="logo-icon" style="width:56px;height:56px;" aria-hidden="true">
                    <img src="assets/logo.svg" alt="" width="56" height="56">
                </span>
            </div>
            <h1>Personal Contact</h1>
            <p class="auth-subtitle">Sign in to manage your contact</p>
        </div>

        <?php if ($success): ?><div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autocomplete="username" placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required autocomplete="current-password" placeholder="Enter your password">
            </div>
            <button type="submit" class="btn btn-primary btn-full" style="margin-top:8px;">Login</button>
        </form>

        <p class="auth-link">No account? <a href="register.php">Register →</a></p>
    </div>
</div>
<script src="assets/script.js"></script>
</body>
</html>