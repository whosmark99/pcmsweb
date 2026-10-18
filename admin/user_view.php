<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_admin_session();

$pdo = get_db();
$id  = (int)($_GET['id'] ?? 0);

$s = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$s->execute([$id]);
$user = $s->fetch();

if (!$user) { header("Location: users.php?err=" . urlencode("User not found.")); exit; }

$contacts        = get_user_contacts_admin($id);
$deletedContacts = get_user_deleted_admin($id);

$pageTitle = 'User: ' . $user['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Details - PCMS</title>
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
                <h1>User: @<?= htmlspecialchars($user['username']) ?></h1>
                <a href="users.php" class="btn btn-secondary">← Back</a>
            </div>

            <div class="section-card">
                <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <!-- ⬇️ CHANGED: avatar now shows uploaded image if available ⬇️ -->
                    <div class="avatar <?= $user['role']==='admin'?'admin':'' ?>" style="width:80px;height:80px;font-size:32px;">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="../<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar">
                        <?php else: ?>
                            <?= htmlspecialchars(strtoupper(substr($user['username'],0,1))) ?>
                        <?php endif; ?>
                    </div>
                    <!-- ⬆️ END CHANGE ⬆️ -->
                    <div style="flex:1;">
                        <h2 style="font-size:24px;margin-bottom:6px;">
                            <?= htmlspecialchars($user['username']) ?>
                            <span class="badge <?= $user['role']==='admin'?'badge-admin':'badge-user' ?>" style="vertical-align:middle;margin-left:8px;"><?= htmlspecialchars($user['role']) ?></span>
                            <?php if ($user['is_active']): ?>
                                <span class="badge badge-success" style="vertical-align:middle;">Active</span>
                            <?php else: ?>
                                <span class="badge badge-danger" style="vertical-align:middle;">Disabled</span>
                            <?php endif; ?>
                        </h2>
                        <p style="color:#6b7280;font-size:13px;margin:0;">
                            <strong>ID:</strong> #<?= (int)$user['id'] ?> ·
                            <strong>Email:</strong> <?= htmlspecialchars($user['email'] ?: '—') ?> ·
                            <strong>Joined:</strong> <?= htmlspecialchars(date('M d, Y H:i', strtotime($user['created_at']))) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card"><h3>Contacts</h3><strong><?= count($contacts) ?></strong></div>
                <div class="stat-card"><h3>Deleted</h3><strong><?= count($deletedContacts) ?></strong></div>
                <div class="stat-card"><h3>Status</h3><strong style="font-size:16px;"><?= $user['is_active']?'Active':'Disabled' ?></strong></div>
                <div class="stat-card"><h3>Role</h3><strong style="font-size:16px;"><?= htmlspecialchars($user['role']) ?></strong></div>
            </div>

            <?php if ($user['role'] !== 'admin'): ?>
                <div class="section-card">
                    <h2>Admin Actions</h2>
                    <div class="form-actions">
                        <a href="user_edit.php?id=<?= (int)$user['id'] ?>" class="btn btn-secondary">Edit</a>
                        <a href="user_delete.php?id=<?= (int)$user['id'] ?>&action=toggle"
                           class="btn <?= $user['is_active']?'btn-warning':'btn-success' ?>"
                           data-confirm="<?= $user['is_active']?'Disable':'Enable' ?> this user?">
                            <?= $user['is_active']?'Disable':'✅ Enable' ?>
                        </a>
                        <a href="user_delete.php?id=<?= (int)$user['id'] ?>&action=delete"
                           class="btn btn-danger"
                           data-confirm="Permanently DELETE this user and ALL their contacts?">
                            Delete User
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="section-card">
                    <div class="alert alert-warning" style="margin:0;">⚠️ Admin account — protected.</div>
                </div>
            <?php endif; ?>

            <div class="section-card">
                <h2>Contacts (<?= count($contacts) ?>)</h2>
                <div class="table-container">
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Created</th></tr></thead>
                        <tbody>
                        <?php if (empty($contacts)): ?>
                            <tr><td colspan="6" class="empty-state">No contacts.</td></tr>
                        <?php else: foreach ($contacts as $c): ?>
                            <tr>
                                <td>#<?= (int)$c['id'] ?></td>
                                <td><strong><?= htmlspecialchars($c['full_name']) ?></strong></td>
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

            <div class="section-card">
                <h2>Deleted (<?= count($deletedContacts) ?>)</h2>
                <div class="table-container">
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Deleted</th></tr></thead>
                        <tbody>
                        <?php if (empty($deletedContacts)): ?>
                            <tr><td colspan="5" class="empty-state">No deleted contacts.</td></tr>
                        <?php else: foreach ($deletedContacts as $c): ?>
                            <tr>
                                <td>#<?= (int)$c['id'] ?></td>
                                <td><strong><?= htmlspecialchars($c['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($c['email'] ?: '—') ?></td>
                                <td><?= htmlspecialchars($c['phone'] ?: '—') ?></td>
                                <td><?= htmlspecialchars($c['deleted_at']) ?></td>
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