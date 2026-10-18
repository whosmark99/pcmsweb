<?php
require_once __DIR__ . '/includes/auth.php';
if (is_user_logged_in()) { header("Location: index.php"); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        [$ok, $msg] = register_user($username, $password, $email);
        if ($ok) { header("Location: login.php?registered=1"); exit; }
        $error = $msg;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - PCMS</title>
<link rel="stylesheet" href="assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <h1>Create Account</h1>
        <p class="auth-subtitle">Personal Contact Management System</p>

        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="POST">
            <div class="form-group"><label>Username</label>
                <input type="text" name="username" required minlength="3" autocomplete="username"></div>
            <div class="form-group"><label>Email (optional)</label>
                <input type="email" name="email" autocomplete="email"></div>
            <div class="form-group"><label>Password</label>
                <input type="password" name="password" required minlength="6" autocomplete="new-password"></div>
            <div class="form-group"><label>Confirm Password</label>
                <input type="password" name="confirm_password" required minlength="6" autocomplete="new-password"></div>
            <button type="submit" class="btn btn-primary btn-full">Register</button>
        </form>

        <p class="auth-link">Have an account? <a href="login.php">Login</a></p>
    </div>
</div>
<script src="assets/script.js"></script>
</body>
</html>