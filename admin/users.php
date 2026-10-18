<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_session();

$search = trim($_GET['search'] ?? '');
$users  = search_users($search);
$message = $_GET['msg'] ?? '';
$error   = $_GET['err'] ?? '';
$pageTitle = 'Manage Users';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users - PCMS</title>
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
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <div class="page-header">
                <h1>Manage Users <span class="count-badge"><?= count($users) ?></span></h1>
            </div>

            <div class="section-card">
                <form method="GET" class="filter-bar">
                    <input type="text" name="search" id="searchInput"
                           placeholder="Search by username, email, or ID..."
                           value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search !== ''): ?>
                        <a href="users.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="table-container">
                    <table class="users-table">
                        <thead><tr>
                            <th>ID</th><th>Username</th><th>Email</th><th>Role</th>
                            <th>Status</th><th>Contacts</th><th>Joined</th><th>Actions</th>
                        </tr></thead>
                        <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="8" class="empty-state">No users found.</td></tr>
                        <?php else: foreach ($users as $u): ?>
                            <tr>
                                <td>#<?= (int)$u['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($u['username']) ?></strong>
                                    <?php if ((int)$u['id'] === current_admin_id()): ?>
                                        <small style="color:#dc2626;"> (you)</small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($u['email'] ?: '—') ?></td>
                                <td><span class="badge <?= $u['role']==='admin'?'badge-admin':'badge-user' ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                                <td>
                                    <?php if ($u['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= (int)$u['contact_count'] ?></td>
                                <td><?= htmlspecialchars(date('M d, Y', strtotime($u['created_at']))) ?></td>
                                <td class="action-cell">
                                    <?php if ($u['role'] !== 'admin'): ?>
                                        <div class="action-group">

                                            <!-- View Contacts -->
                                            <a href="user_view.php?id=<?= (int)$u['id'] ?>"
                                               class="icon-btn view"
                                               data-tip="View Contacts"
                                               aria-label="View Contacts">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>

                                            <!-- Edit User -->
                                            <a href="user_edit.php?id=<?= (int)$u['id'] ?>"
                                               class="icon-btn edit"
                                               data-tip="Edit User"
                                               aria-label="Edit User">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9"/>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                                </svg>
                                            </a>

                                            <!-- Disable / Enable -->
                                            <a href="user_delete.php?id=<?= (int)$u['id'] ?>&action=toggle"
                                               class="icon-btn <?= $u['is_active'] ? 'warn' : 'ok' ?>"
                                               data-tip="<?= $u['is_active'] ? 'Disable User' : 'Enable User' ?>"
                                               aria-label="<?= $u['is_active'] ? 'Disable User' : 'Enable User' ?>"
                                               data-confirm="<?= $u['is_active'] ? 'Disable' : 'Enable' ?> this user?">
                                                <?php if ($u['is_active']): ?>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18.36 6.64a9 9 0 1 1-12.73 0"/>
                                                        <line x1="12" y1="2" x2="12" y2="12"/>
                                                    </svg>
                                                <?php else: ?>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                         stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                        <polyline points="22 4 12 14.01 9 11.01"/>
                                                    </svg>
                                                <?php endif; ?>
                                            </a>

                                            <!-- Delete User -->
                                            <a href="user_delete.php?id=<?= (int)$u['id'] ?>&action=delete"
                                               class="icon-btn danger"
                                               data-tip="Delete User"
                                               aria-label="Delete User"
                                               data-confirm="Permanently DELETE this user and ALL their contacts?">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                </svg>
                                            </a>

                                        </div>
                                    <?php else: ?>
                                        <div class="action-group">
                                            <a href="user_view.php?id=<?= (int)$u['id'] ?>"
                                               class="icon-btn view"
                                               data-tip="View Contacts"
                                               aria-label="View Contacts">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                    <circle cx="12" cy="12" r="3"/>
                                                </svg>
                                            </a>
                                            <span class="icon-btn disabled" data-tip="Protected admin account" aria-label="Protected">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                                </svg>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="../assets/script.js"></script>
</body>
</html>