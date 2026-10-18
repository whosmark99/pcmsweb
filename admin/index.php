<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_session();

$stats = get_admin_stats();
$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - PCMS</title>
<link rel="icon" type="image/svg+xml" href="../assets/logo-mark.svg">
<meta name="theme-color" content="#6366f1">
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
                <h1>Admin Dashboard</h1>
                <a href="users.php" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Manage Users
                </a>
            </div>

            <div class="stats-grid stagger">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <strong><?= $stats['total_users'] ?></strong>
                </div>
                <div class="stat-card">
                    <h3>Active Users</h3>
                    <strong><?= $stats['active_users'] ?></strong>
                </div>
                <div class="stat-card">
                    <h3>Total Contacts</h3>
                    <strong><?= $stats['total_contacts'] ?></strong>
                </div>
                <div class="stat-card">
                    <h3>Deleted Contacts</h3>
                    <strong><?= $stats['total_deleted'] ?></strong>
                </div>
            </div>

            <div class="section-card">
                <h2>Welcome, <?= htmlspecialchars(current_admin_username()) ?></h2>
                <p>You have full access to manage every user account and their contacts on this system.</p>

                <div class="recent-grid stagger">

                    <a href="users.php" class="recent-card">
                        <h3>
                            <span class="rc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                            Manage Users
                        </h3>
                        <p>View, search, edit, disable, or delete any user</p>
                    </a>

                    <a href="contacts.php" class="recent-card">
                        <h3>
                            <span class="rc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                                    <circle cx="12" cy="9" r="2.5"/>
                                    <path d="M8.5 15a4 4 0 0 1 7 0"/>
                                </svg>
                            </span>
                            All Contacts
                        </h3>
                        <p>Browse every contact in the system</p>
                    </a>

                    <a href="settings.php" class="recent-card">
                        <h3>
                            <span class="rc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                                </svg>
                            </span>
                            Settings
                        </h3>
                        <p>System information and configuration</p>
                    </a>

                </div>
            </div>
        </section>
    </div>
</div>
<script src="../assets/script.js"></script>
</body>
</html>