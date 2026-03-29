<?php
require_once '../config/db.php';
requireRole('admin');
$pageTitle = "Manage Courses – " . SITE_NAME;

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name    = clean($_POST['name']);
    $code    = clean($_POST['code']);
    $dept    = (int)$_POST['department_id'];
    $teacher = (int)$_POST['teacher_id'];
    $credits = (int)$_POST['credits'];
    $sem     = clean($_POST['semester']);
    $year    = clean($_POST['academic_year']);
    $conn->query("INSERT INTO courses (name, code, department_id, teacher_id, credits, semester, academic_year)
        VALUES ('$name','$code',$dept,$teacher,$credits,'$sem','$year')");
    $msg = '<div class="alert alert-success">Course added!</div>';
}

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM courses WHERE id=" . (int)$_GET['delete']);
    header("Location: manage_courses.php?deleted=1");
    exit();
}

// Enroll student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    $sid = (int)$_POST['student_id'];
    $cid = (int)$_POST['course_id'];
    $conn->query("INSERT IGNORE INTO enrollments (student_id, course_id) VALUES ($sid, $cid)");
    $msg = '<div class="alert alert-success">Student enrolled!</div>';
}

$depts    = $conn->query("SELECT id, name FROM departments ORDER BY name");
$teachers = $conn->query("SELECT id, full_name FROM users WHERE role='teacher' ORDER BY full_name");
$students = $conn->query("SELECT id, full_name FROM users WHERE role='student' ORDER BY full_name");
$courses  = $conn->query("SELECT c.*, d.name as dept, u.full_name as teacher FROM courses c LEFT JOIN departments d ON c.department_id=d.id LEFT JOIN users u ON c.teacher_id=u.id ORDER BY c.name");

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manage Courses</h4>
        <div class="d-flex gap-2">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#enrollModal"><i class="fas fa-user-plus me-2"></i>Enroll Student</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="fas fa-plus me-2"></i>Add Course</button>
        </div>
    </div>
    <?= $msg ?>

    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead><tr><th>Course</th><th>Code</th><th>Department</th><th>Teacher</th><th>Credits</th><th>Semester</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($c = $courses->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><code><?= $c['code'] ?></code></td>
                <td><?= htmlspecialchars($c['dept'] ?? '—') ?></td>
                <td><?= htmlspecialchars($c['teacher'] ?? '—') ?></td>
                <td><?= $c['credits'] ?></td>
                <td><?= $c['semester'] ?> (<?= $c['academic_year'] ?>)</td>
                <td><a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete course?')"><i class="fas fa-trash"></i></a></td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add Course</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST">
                <div class="modal-body row g-3">
                    <div class="col-md-8"><label class="form-label">Course Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="col-md-4"><label class="form-label">Code</label><input type="text" name="code" class="form-control" required></div>
                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <select name="department_id" class="form-select">
                            <?php $depts->data_seek(0); while ($d = $depts->fetch_assoc()): ?>
                            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="0">— Assign Later —</option>
                            <?php $teachers->data_seek(0); while ($t = $teachers->fetch_assoc()): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4"><label class="form-label">Credits</label><input type="number" name="credits" class="form-control" value="3" min="1"></div>
                    <div class="col-md-4">
                        <label class="form-label">Semester</label>
                        <select name="semester" class="form-select"><option value="1">1</option><option value="2">2</option><option value="summer">Summer</option></select>
                    </div>
                    <div class="col-md-4"><label class="form-label">Academic Year</label><input type="text" name="academic_year" class="form-control" value="<?= date('Y') . '-' . (date('Y')+1) ?>" placeholder="2024-2025"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add" class="btn btn-primary">Save Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enroll Modal -->
<div class="modal fade" id="enrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Enroll Student in Course</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST">
                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select" required>
                            <?php $students->data_seek(0); while ($s = $students->fetch_assoc()): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select" required>
                            <?php
                            $allCourses = $conn->query("SELECT id, name, code FROM courses ORDER BY name");
                            while ($c = $allCourses->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= $c['code'] ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="enroll" class="btn btn-success">Enroll</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
