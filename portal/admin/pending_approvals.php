<?php
require_once '../config/db.php';
requireRole('admin');
$pageTitle = "Pending Approvals – " . SITE_NAME;

$msg = '';

// Approve user
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $conn->query("UPDATE users SET is_active=1 WHERE id=$id");
    // Send notification to user
    $conn->query("INSERT INTO notifications (user_id, title, message) VALUES ($id, 'Account Approved', 'Your account has been approved. You can now login.')");
    header("Location: pending_approvals.php?approved=1");
    exit();
}

// Reject/delete user
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $conn->query("DELETE FROM users WHERE id=$id AND is_active=0");
    header("Location: pending_approvals.php?rejected=1");
    exit();
}

// Approve all
if (isset($_GET['approveall'])) {
    $conn->query("UPDATE users SET is_active=1 WHERE is_active=0 AND role != 'admin'");
    header("Location: pending_approvals.php?approved=1");
    exit();
}

$pending = $conn->query("SELECT * FROM users WHERE is_active=0 AND role != 'admin' ORDER BY created_at DESC");
$pendingCount = $pending->num_rows;

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-user-clock me-2 text-warning"></i>Pending Approvals
            <?php if ($pendingCount > 0): ?>
                <span class="badge bg-danger ms-2"><?= $pendingCount ?></span>
            <?php endif; ?>
        </h4>
        <?php if ($pendingCount > 0): ?>
        <a href="?approveall=1" class="btn btn-success" onclick="return confirm('Approve all pending accounts?')">
            <i class="fas fa-check-double me-2"></i>Approve All
        </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_GET['approved'])): ?><div class="alert alert-success"><i class="fas fa-check me-2"></i>Account(s) approved successfully!</div><?php endif; ?>
    <?php if (isset($_GET['rejected'])): ?><div class="alert alert-warning"><i class="fas fa-times me-2"></i>Account rejected and removed.</div><?php endif; ?>

    <div class="card p-3">
        <?php if ($pendingCount === 0): ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle text-success" style="font-size:64px"></i>
                <h5 class="mt-3 fw-bold">All caught up!</h5>
                <p class="text-muted">No pending accounts to approve.</p>
            </div>
        <?php else: ?>
        <table class="table table-hover align-middle">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Registered</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php while ($u = $pending->fetch_assoc()): ?>
            <tr>
                <td>
                    <i class="fas fa-<?= $u['role']==='student' ? 'user-graduate' : ($u['role']==='teacher' ? 'chalkboard-teacher' : 'user-friends') ?> me-2 text-secondary"></i>
                    <?= htmlspecialchars($u['full_name']) ?>
                </td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
                <td>
                    <span class="badge bg-<?= $u['role']==='student' ? 'primary' : ($u['role']==='teacher' ? 'success' : 'warning') ?>">
                        <?= ucfirst($u['role']) ?>
                    </span>
                </td>
                <td><?= date('M d, Y H:i', strtotime($u['created_at'])) ?></td>
                <td>
                    <a href="?approve=<?= $u['id'] ?>" class="btn btn-sm btn-success me-1">
                        <i class="fas fa-check me-1"></i>Approve
                    </a>
                    <a href="?reject=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reject and delete this account?')">
                        <i class="fas fa-times me-1"></i>Reject
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
