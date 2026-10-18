<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_session();

$stats = get_admin_stats();
$pageTitle = 'Admin Settings';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings - PCMS</title>
<link rel="stylesheet" href="../assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="app-wrapper">
    <?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>
    <div class="main-wrapper" id="mainWrapper">
        <?php include __DIR__ . '/../includes/admin_topbar.php'; ?>
        <section class="content">
            <div class="page-header"><h1>⚙️ Admin Settings</h1></div>

            <div class="section-card">
                <h2>System Information</h2>
                <p>Status of your PCMS installation</p>
                <div class="stats-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:0;">
                    <div class="stat-card"><h3>Database</h3><strong style="font-size:15px;"><?= htmlspecialchars(DB_NAME) ?></strong></div>
                    <div class="stat-card"><h3>Host</h3><strong style="font-size:15px;"><?= htmlspecialchars(DB_HOST) ?></strong></div>
                    <div class="stat-card"><h3>PHP Version</h3><strong style="font-size:15px;"><?= htmlspecialchars(PHP_VERSION) ?></strong></div>
                    <div class="stat-card"><h3>Server Time</h3><strong style="font-size:15px;"><?= date('M d, Y H:i') ?></strong></div>
                </div>
            </div>

            <div class="section-card">
                <h2>System Data</h2>
                <p>Overview of stored data</p>
                <div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:0;">
                    <div class="stat-card"><h3>Users</h3><strong><?= $stats['total_users'] ?></strong></div>
                    <div class="stat-card"><h3>Admins</h3><strong><?= $stats['total_admins'] ?></strong></div>
                    <div class="stat-card"><h3>Contacts</h3><strong><?= $stats['total_contacts'] ?></strong></div>
                </div>
            </div>

            <div class="section-card">
                <h2>Session</h2>
                <p>Sign out from this admin account</p>
                <div class="form-actions">
                    <a href="logout.php" class="btn btn-danger">Admin Logout</a>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="../assets/script.js"></script>
</body>
</html>