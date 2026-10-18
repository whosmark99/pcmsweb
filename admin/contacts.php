<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_session();

$contacts = get_all_contacts_admin(200);
$pageTitle = 'All Contacts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Contacts - PCMS</title>
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
                <h1>All Contacts <span class="count-badge"><?= count($contacts) ?></span></h1>
            </div>
            <div class="section-card">
                <h2>All Users</h2>
                
                <div class="table-container">
                    <table>
                        <thead><tr><th>Owner</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Created</th></tr></thead>
                        <tbody>
                        <?php if (empty($contacts)): ?>
                            <tr><td colspan="6" class="empty-state">No contacts.</td></tr>
                        <?php else: foreach ($contacts as $c): ?>
                            <tr>
                                <!-- ⬇️ CHANGED: Owner column now shows avatar ⬇️ -->
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div class="avatar small" style="overflow:hidden;">
                                            <?php if (!empty($c['avatar'])): ?>
                                                <img src="../<?= htmlspecialchars($c['avatar']) ?>" alt="">
                                            <?php else: ?>
                                                <?= htmlspecialchars(strtoupper(substr($c['username'],0,1))) ?>
                                            <?php endif; ?>
                                        </div>
                                        <strong>@<?= htmlspecialchars($c['username']) ?></strong>
                                    </div>
                                </td>
                                <!-- ⬆️ END CHANGE ⬆️ -->
                                <td><?= htmlspecialchars($c['full_name']) ?></td>
                                <td><?= htmlspecialchars($c['email'] ?: '—') ?></td>
                                <td><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
                                <td><?= htmlspecialchars($c['address'] ?: '—') ?></td>
                                <td><?= htmlspecialchars($c['created_at']) ?></td>
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