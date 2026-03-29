<?php
require_once '../config/db.php';
requireRole('admin');
$pageTitle = "Manage Parents – " . SITE_NAME;

$msg = '';

// Add parent
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name  = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $pass  = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $conn->query("INSERT INTO users (full_name, email, password, role, phone) VALUES ('$name','$email','$pass','parent','$phone')");
    $msg = '<div class="alert alert-success">Parent account created successfully!</div>';
}

// Link parent to student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['link'])) {
    $pid = (int)$_POST['parent_id'];
    $sid = (int)$_POST['student_id'];
    $rel = clean($_POST['relationship']);
    $check = $conn->query("SELECT id FROM parent_student WHERE parent_id=$pid AND student_id=$sid");
    if ($check->num_rows > 0) {
        $msg = '<div class="alert alert-warning">This parent is already linked to that student.</div>';
    } else {
        $conn->query("INSERT INTO parent_student (parent_id, student_id, relationship) VALUES ($pid, $sid, '$rel')");
        $msg = '<div class="alert alert-success">Parent linked to student successfully!</div>';
    }
}

// Delete parent
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$did AND role='parent'");
    header("Location: manage_parents.php?deleted=1");
    exit();
}

// Unlink
if (isset($_GET['unlink'])) {
    $lid = (int)$_GET['unlink'];
    $conn->query("DELETE FROM parent_student WHERE id=$lid");
    header("Location: manage_parents.php?unlinked=1");
    exit();
}

$parents  = $conn->query("SELECT u.id, u.full_name, u.email, u.phone, u.is_active,
    COUNT(ps.id) as linked_students
    FROM users u LEFT JOIN parent_student ps ON ps.parent_id=u.id
    WHERE u.role='parent' GROUP BY u.id ORDER BY u.full_name");

$students = $conn->query("SELECT id, full_name FROM users WHERE role='student' ORDER BY full_name");
$allParents = $conn->query("SELECT id, full_name FROM users WHERE role='parent' ORDER BY full_name");

// Linked pairs
$links = $conn->query("SELECT ps.id, p.full_name as parent_name, s.full_name as student_name, ps.relationship
    FROM parent_student ps
    JOIN users p ON ps.parent_id=p.id
    JOIN users s ON ps.student_id=s.id
    ORDER BY p.full_name");

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>Manage Parents</h4>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#linkModal"><i class="fas fa-link me-2"></i>Link Parent to Student</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Parent</button>
        </div>
    </div>

    <?= $msg ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Parent deleted.</div><?php endif; ?>
    <?php if (isset($_GET['unlinked'])): ?><div class="alert alert-success">Link removed.</div><?php endif; ?>

    <!-- Parents Table -->
    <div class="card p-3 mb-4">
        <h6 class="fw-bold mb-3">All Parent Accounts</h6>
        <table class="table table-hover align-middle">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Linked Students</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if ($parents->num_rows === 0): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No parent accounts yet.</td></tr>
            <?php endif; ?>
            <?php while ($p = $parents->fetch_assoc()): ?>
            <tr>
                <td><i class="fas fa-user-circle me-2 text-secondary"></i><?= htmlspecialchars($p['full_name']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['phone'] ?? '—') ?></td>
                <td><span class="badge bg-primary"><?= $p['linked_students'] ?> student(s)</span></td>
                <td><span class="badge bg-<?= $p['is_active'] ? 'success' : 'danger' ?>"><?= $p['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                <td>
                    <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this parent account?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Linked Pairs Table -->
    <div class="card p-3">
        <h6 class="fw-bold mb-3">Parent–Student Links</h6>
        <table class="table table-hover align-middle">
            <thead><tr><th>Parent</th><th>Student</th><th>Relationship</th><th>Action</th></tr></thead>
            <tbody>
            <?php if ($links->num_rows === 0): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">No links created yet.</td></tr>
            <?php endif; ?>
            <?php while ($l = $links->fetch_assoc()): ?>
            <tr>
                <td><i class="fas fa-user me-1 text-warning"></i><?= htmlspecialchars($l['parent_name']) ?></td>
                <td><i class="fas fa-user-graduate me-1 text-primary"></i><?= htmlspecialchars($l['student_name']) ?></td>
                <td><?= htmlspecialchars($l['relationship']) ?></td>
                <td>
                    <a href="?unlink=<?= $l['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this link?')">
                        <i class="fas fa-unlink"></i> Unlink
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Parent Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Parent Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="parent@example.com" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Set a password" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="+237600000000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add" class="btn btn-primary"><i class="fas fa-save me-2"></i>Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Link Modal -->
<div class="modal fade" id="linkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-link me-2"></i>Link Parent to Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Parent</label>
                        <select name="parent_id" class="form-select" required>
                            <option value="">Select Parent…</option>
                            <?php $allParents->data_seek(0); while ($p = $allParents->fetch_assoc()): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['full_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Student</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select Student…</option>
                            <?php $students->data_seek(0); while ($s = $students->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Relationship</label>
                        <select name="relationship" class="form-select">
                            <option value="Parent">Parent</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Guardian">Guardian</option>
                            <option value="Sibling">Sibling</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="link" class="btn btn-success"><i class="fas fa-link me-2"></i>Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
