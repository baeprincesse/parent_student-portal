<?php
require_once '../config/db.php';
requireRole('teacher');
$pageTitle = "Enter Grades – " . SITE_NAME;
$uid = $_SESSION['user_id'];

$courseId = isset($_GET['course']) ? (int)$_GET['course'] : 0;
$courses = $conn->query("SELECT id, name, code FROM courses WHERE teacher_id=$uid");

// Save grades
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['grades'] as $sid => $g) {
        $sid = (int)$sid;
        $mid = $g['midterm'] !== '' ? (float)$g['midterm'] : 'NULL';
        $fin = $g['final'] !== '' ? (float)$g['final'] : 'NULL';
        $asg = $g['assignment'] !== '' ? (float)$g['assignment'] : 'NULL';
        $cid = (int)$_POST['course_id'];
        // Compute letter
        $total = ($mid!='NULL'?$mid:0)*0.3 + ($fin!='NULL'?$fin:0)*0.5 + ($asg!='NULL'?$asg:0)*0.2;
        $letter = getLetterGrade($total);
        $conn->query("INSERT INTO grades (student_id, course_id, teacher_id, midterm, final, assignment, grade_letter)
            VALUES ($sid, $cid, $uid, $mid, $fin, $asg, '$letter')
            ON DUPLICATE KEY UPDATE midterm=$mid, final=$fin, assignment=$asg, grade_letter='$letter'");
    }
    header("Location: enter_grades.php?course=$courseId&saved=1");
    exit();
}

$students = [];
if ($courseId) {
    $res = $conn->query("SELECT u.id, u.full_name, s.student_number,
        g.midterm, g.final, g.assignment, g.grade_letter
        FROM enrollments e
        JOIN users u ON e.student_id=u.id
        LEFT JOIN students s ON s.user_id=u.id
        LEFT JOIN grades g ON g.student_id=u.id AND g.course_id=$courseId
        WHERE e.course_id=$courseId ORDER BY u.full_name");
    $students = $res->fetch_all(MYSQLI_ASSOC);
}

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="fw-bold mb-4"><i class="fas fa-star me-2 text-warning"></i>Enter Grades</h4>
    <?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Grades saved!</div><?php endif; ?>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="course" class="form-select" onchange="this.form.submit()">
                <option value="">Select Course…</option>
                <?php while ($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= $courseId==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?> (<?= $c['code'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
    </form>

    <?php if ($courseId && $students): ?>
    <form method="POST">
        <input type="hidden" name="course_id" value="<?= $courseId ?>">
        <div class="card p-3">
            <table class="table table-hover align-middle">
                <thead><tr><th>Student</th><th>ID</th><th>Midterm /100</th><th>Assignment /100</th><th>Final /100</th><th>Letter</th></tr></thead>
                <tbody>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['full_name']) ?></td>
                    <td><code><?= $s['student_number'] ?></code></td>
                    <td><input type="number" name="grades[<?= $s['id'] ?>][midterm]" class="form-control form-control-sm" min="0" max="100" step="0.5" value="<?= $s['midterm'] ?>"></td>
                    <td><input type="number" name="grades[<?= $s['id'] ?>][assignment]" class="form-control form-control-sm" min="0" max="100" step="0.5" value="<?= $s['assignment'] ?>"></td>
                    <td><input type="number" name="grades[<?= $s['id'] ?>][final]" class="form-control form-control-sm" min="0" max="100" step="0.5" value="<?= $s['final'] ?>"></td>
                    <td><span class="badge bg-secondary"><?= $s['grade_letter'] ?? '—' ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Grades</button>
        </div>
    </form>
    <?php elseif ($courseId): ?>
        <p class="text-muted">No students enrolled in this course yet.</p>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
