<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <span class="logo-icon" aria-hidden="true">
                <img src="../assets/logo.svg" alt="" width="36" height="36">
            </span>
            <span class="logo-text">Admin Panel<span class="logo-sub"></span></span>
        </div>
        <button type="button" class="sidebar-toggle-topbar" id="sidebarToggleBtn" aria-label="Toggle sidebar">☰</button>
    </div>

    <nav class="sidebar-nav">
        <a href="index.php" class="<?= $currentPage==='index.php'?'active':'' ?>">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                    <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                </svg>
            </span>
            <span class="nav-label">Admin Dashboard</span>
        </a>
        <a href="users.php" class="<?= $currentPage==='users.php'?'active':'' ?>">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            <span class="nav-label">Manage Users</span>
        </a>
        <a href="contacts.php" class="<?= $currentPage==='contacts.php'?'active':'' ?>">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    <circle cx="12" cy="9" r="2.5"/>
                    <path d="M8.5 15a4 4 0 0 1 7 0"/>
                </svg>
            </span>
            <span class="nav-label">All Contacts</span>
        </a>
        <a href="settings.php" class="<?= $currentPage==='settings.php'?'active':'' ?>">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
            </span>
            <span class="nav-label">Settings</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="profile.php" class="sidebar-user" style="text-decoration:none;">
            <div class="avatar admin">
                <?php if ($av = current_admin_avatar()): ?>
                    <img src="../<?= htmlspecialchars($av) ?>" alt="Avatar">
                <?php else: ?>
                    <?= htmlspecialchars(strtoupper(substr(current_admin_username(),0,1))) ?>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars(current_admin_username()) ?></span>
                <span class="user-role">Administrator</span>
            </div>
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>