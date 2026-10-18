<header class="topbar">
    <div class="topbar-left">
        <button type="button" class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open menu">☰</button>
        <span class="topbar-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></span>
    </div>

    <div class="topbar-right">

        <!-- Theme toggle — same height as profile button -->
        <button type="button"
                id="themeToggle"
                class="theme-toggle"
                aria-label="Toggle dark mode"
                aria-pressed="true"
                title="Switch to light mode">
            <span class="tt-halo" aria-hidden="true"></span>
            <span class="tt-icon-stack" aria-hidden="true">
                <svg class="tt-ic tt-ic-moon" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    <circle cx="9"  cy="11" r="0.8" fill="currentColor" stroke="none" opacity="0.5"/>
                    <circle cx="8"  cy="14" r="0.6" fill="currentColor" stroke="none" opacity="0.4"/>
                    <circle cx="12" cy="15" r="0.5" fill="currentColor" stroke="none" opacity="0.35"/>
                </svg>
                <svg class="tt-ic tt-ic-sun" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2"/><path d="M12 20v2"/>
                    <path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/>
                    <path d="M2 12h2"/><path d="M20 12h2"/>
                    <path d="m4.93 19.07 1.41-1.41"/><path d="m17.66 6.34 1.41-1.41"/>
                </svg>
            </span>
        </button>

        <div class="profile-wrapper">
            <button type="button" id="profileBtn" class="profile-button">
                <span class="avatar small">
                    <?php if ($av = current_user_avatar()): ?>
                        <img src="<?= htmlspecialchars($av) ?>" alt="Avatar">
                    <?php else: ?>
                        <?= htmlspecialchars(strtoupper(substr(current_username(),0,1))) ?>
                    <?php endif; ?>
                </span>
                <span class="profile-name"><?= htmlspecialchars(current_username()) ?></span>
                <span class="profile-caret" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="10" height="10" fill="none"
                         stroke="currentColor" stroke-width="2.4"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </span>
            </button>

            <div id="profileDropdown" class="profile-dropdown">
                <div class="dropdown-header">
                    <strong><?= htmlspecialchars(current_username()) ?></strong>
                    <small>Signed in</small>
                </div>

                <a href="profile.php">
                    <span class="dd-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </span>
                    <span>User Profile</span>
                </a>

                <a href="logout.php" class="danger">
                    <span class="dd-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </span>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</header>