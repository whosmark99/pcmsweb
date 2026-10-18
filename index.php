<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_user_session();

$userId = current_user_id();
$pdo = get_db();

$totalContacts = get_user_contact_count($userId);
$totalDeleted  = get_user_deleted_count($userId);

$s = $pdo->prepare("SELECT COUNT(*) FROM contacts WHERE user_id = ? AND DATE(created_at)=CURDATE()");
$s->execute([$userId]);
$addedToday = (int)$s->fetchColumn();

$s = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at DESC LIMIT 6");
$s->execute([$userId]);
$recent = $s->fetchAll();

$message = $_GET['msg'] ?? '';
$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - PCMS</title>
<link rel="icon" type="image/svg+xml" href="assets/logo-mark.svg">
<meta name="theme-color" content="#6366f1">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="app-wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="main-wrapper" id="mainWrapper">
        <?php include __DIR__ . '/includes/topbar.php'; ?>
        <section class="content">
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <div class="page-header">
                <h1>Welcome, <?= htmlspecialchars(current_username()) ?>!</h1>
                <a href="add_contact.php" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Contact
                </a>
            </div>

            <div class="stats-grid stagger">
                <div class="stat-card">
                    <h3>Total Contacts</h3>
                    <strong><?= $totalContacts ?></strong>
                </div>
                <div class="stat-card">
                    <h3>Added Today</h3>
                    <strong><?= $addedToday ?></strong>
                </div>
                <div class="stat-card">
                    <h3>Deleted</h3>
                    <strong><?= $totalDeleted ?></strong>
                </div>
                <div class="stat-card">
                    <h3>Account</h3>
                    <strong style="font-size:18px;">Active</strong>
                </div>
            </div>

            <div class="section-card">
                <h2>Quick Actions</h2>
                
                <div class="recent-grid stagger">

                    <a href="contacts.php" class="recent-card">
                        <h3>
                            <span class="rc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                            My Contacts
                        </h3>
                        <p>View all your contacts</p>
                    </a>

                    <a href="recently_added.php" class="recent-card">
                        <h3>
                            <span class="rc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </span>
                            Recently Added
                        </h3>
                        <p>Latest additions</p>
                    </a>

                    <a href="recently_deleted.php" class="recent-card">
                        <h3>
                            <span class="rc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                </svg>
                            </span>
                            Recently Deleted
                        </h3>
                        <p>Restore deleted contacts</p>
                    </a>

                    <a href="profile.php" class="recent-card">
                        <h3>
                            <span class="rc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            Profile
                        </h3>
                        <p>Manage your account</p>
                    </a>

                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <div>
                        <h2>Latest Contacts</h2>
                        <p>Your 6 most recent contacts</p>
                    </div>
                    <a href="contacts.php" class="btn btn-secondary">
                        View All
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </a>
                </div>

                <div class="recent-grid stagger">
                    <?php if (empty($recent)): ?>
                        <div class="empty-state" style="grid-column:1/-1;">
                            <div class="empty-icon"></div>
                            No contacts yet. Add your first one!
                        </div>
                    <?php else: foreach ($recent as $c): ?>
                        <div class="recent-card">
                            <h3>
                                <span class="rc-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </span>
                                <?= htmlspecialchars($c['full_name']) ?>
                            </h3>
                            <p>
                                <span class="rc-mini-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                        <polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                </span>
                                <?= htmlspecialchars($c['email'] ?: 'No email') ?>
                            </p>
                            <p>
                                <span class="rc-mini-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.9.37 1.78.7 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.27a2 2 0 0 1 2.11-.45c.84.33 1.72.57 2.62.7a2 2 0 0 1 1.72 2.03z"/>
                                    </svg>
                                </span>
                                <?= htmlspecialchars($c['phone'] ?: 'No phone') ?>
                            </p>
                            <small>
                                <span class="rc-mini-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </span>
                                <?= htmlspecialchars($c['created_at']) ?>
                            </small>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="assets/script.js"></script>
</body>
</html>