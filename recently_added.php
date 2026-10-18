<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/DataStructures.php';
require_user_session();

$userId = current_user_id();
$pdo = get_db();

$s = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at ASC");
$s->execute([$userId]);
$rows = $s->fetchAll();

$q = new Queue();
foreach ($rows as $r) $q->enqueue($r);
$recentlyAdded = array_slice(array_reverse($q->toArray()), 0, 10);

$pageTitle = 'Recently Added';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recently Added - PCMS</title>
<link rel="icon" type="image/svg+xml" href="assets/logo-mark.svg">
<meta name="theme-color" content="#6366f1">
<link rel="stylesheet" href="assets/style.css">
<script>(function(){ if(localStorage.getItem("theme")==="dark") document.documentElement.classList.add("dark-mode"); })();</script>
</head>
<body>
<div class="app-wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="main-wrapper" id="mainWrapper">
        <?php include __DIR__ . '/includes/topbar.php'; ?>
        <section class="content">
            <div class="page-header">
                <h1>Recently Added <span class="count-badge"><?= count($recentlyAdded) ?></span></h1>
                <a href="add_contact.php" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Contact
                </a>
            </div>

            <div class="section-card">
                <h2>Your Latest Contacts</h2>
                

                <div class="recent-grid">
                <?php if (empty($recentlyAdded)): ?>
                    <div class="empty-state" style="grid-column:1/-1;">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" width="42" height="42" fill="none"
                                 stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 12h-6l-2 3h-4l-2-3H2"/>
                                <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"/>
                            </svg>
                        </div>
                        No contacts yet.
                    </div>
                <?php else: foreach ($recentlyAdded as $c): ?>
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
                            <?= htmlspecialchars($c['email'] ?? 'No email') ?>
                        </p>
                        <p>
                            <span class="rc-mini-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.9.37 1.78.7 2.62a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.46-1.27a2 2 0 0 1 2.11-.45c.84.33 1.72.57 2.62.7a2 2 0 0 1 1.72 2.03z"/>
                                </svg>
                            </span>
                            <?= htmlspecialchars($c['phone'] ?? 'No phone') ?>
                        </p>
                        <small>
                            <span class="rc-mini-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                            </span>
                            Added: <?= htmlspecialchars($c['created_at']) ?>
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