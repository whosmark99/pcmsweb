<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <span class="logo-icon" aria-hidden="true">
                <img src="assets/logo.svg" alt="" width="36" height="36">
            </span>
            <span class="logo-text">PCMS</span>
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
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="contacts.php" class="<?= $currentPage==='contacts.php'?'active':'' ?>">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </span>
            <span class="nav-label">My Contacts</span>
        </a>
        <a href="recently_added.php" class="<?= $currentPage==='recently_added.php'?'active':'' ?>">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <span class="nav-label">Recently Added</span>
        </a>
        <a href="recently_deleted.php" class="<?= $currentPage==='recently_deleted.php'?'active':'' ?>">
            <span class="nav-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/>
                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    <line x1="10" y1="11" x2="10" y2="17"/>
                    <line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
            </span>
            <span class="nav-label">Recently Deleted</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <a href="profile.php" class="sidebar-user" style="text-decoration:none;">
            <div class="avatar">
                <?php if ($av = current_user_avatar()): ?>
                    <img src="<?= htmlspecialchars($av) ?>" alt="Avatar">
                <?php else: ?>
                    <?= htmlspecialchars(strtoupper(substr(current_username(),0,1))) ?>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars(current_username()) ?></span>
                <span class="user-role">Personal Account</span>
            </div>
        </a>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>