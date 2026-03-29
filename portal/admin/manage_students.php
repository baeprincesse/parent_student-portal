<?php
require_once '../config/db.php';
requireRole('admin');
$pageTitle = "Manage Students – " . SITE_NAME;

$msg = '';

// Add student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name   = clean($_POST['full_name']);
    $email  = clean($_POST['email']);
    $phone  = clean($_POST['phone']);
    $pass   = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $snum   = clean($_POST['student_number']);
    $dept   = (int)$_POST['department_id'];
    $level  = clean($_POST['level']);
    $year   = (int)$_POST['enrollment_year'];

    $conn->query("INSERT INTO users (full_name, email, password, role, phone) VALUES ('$name','$email','$pass','student','$phone')");
    $newId = $conn->insert_id;
    $conn->query("INSERT INTO students (user_id, student_number, department_id, level, enrollment_year) VALUES ($newId,'$snum',$dept,'$level',$year)");
    $msg = '<div class="alert alert-success">Student added successfully!</div>';
}

// Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$did AND role='student'");
    header("Location: manage_students.php?deleted=1");
    exit();
}

$depts = $conn->query("SELECT id, name FROM departments ORDER BY name");
$students = $conn->query("SELECT u.id, u.full_name, u.email, u.phone, u.is_active, s.student_number, s.level, d.name as dept
    FROM users u LEFT JOIN students s ON s.user_id=u.id LEFT JOIN departments d ON s.department_id=d.id
    WHERE u.role='student' ORDER BY u.full_name");

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manage Students</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Student</button>
    </div>
    <?= $msg ?>
    <?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Student deleted.</div><?php endif; ?>

    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead><tr><th>Name</th><th>Student No.</th><th>Email</th><th>Department</th><th>Level</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($s = $students->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($s['full_name']) ?></td>
                <td><code><?= $s['student_number'] ?></code></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= htmlspecialchars($s['dept'] ?? '—') ?></td>
                <td><?= htmlspecialchars($s['level'] ?? '—') ?></td>
                <td><span class="badge bg-<?= $s['is_active']?'success':'danger' ?>"><?= $s['is_active']?'Active':'Inactive' ?></span></td>
                <td>
                    <a href="?delete=<?= $s['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this student?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add New Student</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Student Number</label><input type="text" name="student_number" class="form-control" required></div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="0">— None —</option>
                                <?php $depts->data_seek(0); while ($d = $depts->fetch_assoc()): ?>
                                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6"><label class="form-label">Level (e.g. Year 1)</label><input type="text" name="level" class="form-control"></div>
                        <div class="col-md-6"><label class="form-label">Enrollment Year</label><input type="number" name="enrollment_year" class="form-control" value="<?= date('Y') ?>"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
