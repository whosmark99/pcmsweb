<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/Algorithms.php';
require_user_session();

$userId = current_user_id();
$pdo = get_db();

$s = $pdo->prepare("SELECT * FROM contacts WHERE user_id = ? ORDER BY created_at DESC");
$s->execute([$userId]);
$allContacts = $s->fetchAll();

$search = trim($_GET['search'] ?? '');
$contacts = $allContacts;
if ($search !== '') $contacts = linearSearch($allContacts, $search, 'full_name');

$sort = $_GET['sort'] ?? 'created_at';
$allowed = ['full_name','email','phone','created_at'];
if (!in_array($sort, $allowed, true)) $sort = 'created_at';

if ($sort === 'created_at') {
    usort($contacts, fn($a,$b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));
} else {
    $contacts = mergeSort($contacts, $sort);
}

$message = $_GET['msg'] ?? '';
$pageTitle = 'My Contacts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Contacts - PCMS</title>
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
            <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

            <div class="page-header">
                <h1>My Contacts <span class="count-badge"><?= count($allContacts) ?></span></h1>
                <a href="add_contact.php" class="btn btn-primary">➕ Add Contact</a>
            </div>

            <div class="section-card">
                <!-- Auto-sorting filter bar -->
                <form method="GET" class="filter-bar" id="filterForm">
                    <input type="text" name="search" id="searchInput" placeholder="Search by name..."
                           value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                    <select name="sort" id="sortSelect">
                        <option value="created_at" <?= $sort==='created_at'?'selected':'' ?>>Recently Added</option>
                        <option value="full_name"  <?= $sort==='full_name' ?'selected':'' ?>>Name (A–Z)</option>
                        <option value="email"      <?= $sort==='email'     ?'selected':'' ?>>Email (A–Z)</option>
                        <option value="phone"      <?= $sort==='phone'     ?'selected':'' ?>>Phone (A–Z)</option>
                    </select>
                    <?php if ($search !== ''): ?>
                        <a href="contacts.php" class="btn btn-secondary btn-small">✕ Clear</a>
                    <?php endif; ?>
                </form>

                <!-- Bulk action toolbar -->
                <div class="bulk-toolbar" id="bulkToolbar" style="display:none;">
                    <span id="selectedCount">0 selected</span>
                    <button type="button" class="btn btn-danger btn-small" id="deleteSelectedBtn">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none"
                             stroke="currentColor" stroke-width="1.8"
                             stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/><path d="M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                        Delete Selected
                    </button>
                </div>

                <form method="POST" action="delete_selected.php" id="bulkForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))) ?>">
                    <div class="table-container">
                        <table class="contacts-table">
                            <thead>
                                <tr>
                                    <th style="width:36px;">
                                        <input type="checkbox" id="selectAll" title="Select all">
                                    </th>
                                    <th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Created</th><th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($contacts)): ?>
                                <tr><td colspan="7" class="empty-state">No contacts found.</td></tr>
                            <?php else: foreach ($contacts as $c): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="<?= (int)$c['id'] ?>" class="row-check">
                                    </td>
                                    <td><strong><?= htmlspecialchars($c['full_name']) ?></strong></td>
                                    <td><?= htmlspecialchars($c['email'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($c['phone'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($c['address'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($c['created_at']) ?></td>
                                    <td class="action-cell">
                                        <div class="action-group">

                                            <!-- Edit Contact -->
                                            <a href="edit_contact.php?id=<?= (int)$c['id'] ?>"
                                               class="icon-btn edit"
                                               data-tip="Edit Contact"
                                               aria-label="Edit Contact">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 20h9"/>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                                </svg>
                                            </a>

                                            <!-- Delete Contact -->
                                            <a href="delete_contact.php?id=<?= (int)$c['id'] ?>"
                                               class="icon-btn danger"
                                               data-tip="Delete Contact"
                                               aria-label="Delete Contact"
                                               data-confirm="Move this contact to Recently Deleted?">
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
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<script src="assets/script.js"></script>
<script>
(function(){
    // Auto-submit on sort change
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) sortSelect.addEventListener('change', () => document.getElementById('filterForm').submit());

    // Select all + bulk toolbar
    const selectAll = document.getElementById('selectAll');
    const checks    = document.querySelectorAll('.row-check');
    const toolbar   = document.getElementById('bulkToolbar');
    const countEl   = document.getElementById('selectedCount');
    const bulkForm  = document.getElementById('bulkForm');
    const delBtn    = document.getElementById('deleteSelectedBtn');

    function refreshToolbar() {
        const n = document.querySelectorAll('.row-check:checked').length;
        countEl.textContent = n + ' selected';
        toolbar.style.display = n > 0 ? 'flex' : 'none';
        if (selectAll) selectAll.checked = (n > 0 && n === checks.length);
    }
    if (selectAll) {
        selectAll.addEventListener('change', () => {
            checks.forEach(c => c.checked = selectAll.checked);
            refreshToolbar();
        });
    }
    checks.forEach(c => c.addEventListener('change', refreshToolbar));

    if (delBtn) {
        delBtn.addEventListener('click', () => {
            const n = document.querySelectorAll('.row-check:checked').length;
            if (n === 0) return;
            if (confirm(`Move ${n} selected contact${n>1?'s':''} to Recently Deleted?`)) {
                bulkForm.submit();
            }
        });
    }
})();
</script>
</body>
</html>