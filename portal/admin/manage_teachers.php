<?php
require_once '../config/db.php';
requireRole('admin');
$pageTitle = "Manage Teachers – " . SITE_NAME;

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name  = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    $phone = clean($_POST['phone']);
    $pass  = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $conn->query("INSERT INTO users (full_name, email, password, role, phone) VALUES ('$name','$email','$pass','teacher','$phone')");
    $msg = '<div class="alert alert-success">Teacher added successfully!</div>';
}

if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$did AND role='teacher'");
    header("Location: manage_teachers.php?deleted=1");
    exit();
}

$teachers = $conn->query("SELECT u.id, u.full_name, u.email, u.phone, u.is_active, COUNT(c.id) as courses
    FROM users u LEFT JOIN courses c ON c.teacher_id=u.id
    WHERE u.role='teacher' GROUP BY u.id ORDER BY u.full_name");

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manage Teachers</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Teacher</button>
    </div>
    <?= $msg ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Teacher deleted.</div><?php endif; ?>

    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Courses</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($t = $teachers->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($t['full_name']) ?></td>
                <td><?= htmlspecialchars($t['email']) ?></td>
                <td><?= htmlspecialchars($t['phone'] ?? '—') ?></td>
                <td><?= $t['courses'] ?></td>
                <td><span class="badge bg-<?= $t['is_active']?'success':'danger' ?>"><?= $t['is_active']?'Active':'Inactive' ?></span></td>
                <td><a href="?delete=<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add New Teacher</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST">
                <div class="modal-body row g-3">
                    <div class="col-12"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add" class="btn btn-primary">Save Teacher</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
