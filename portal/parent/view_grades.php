<?php
require_once '../config/db.php';
requireRole('parent');
$pageTitle = "Child's Grades – " . SITE_NAME;
$uid = $_SESSION['user_id'];

$studentId = isset($_GET['student']) ? (int)$_GET['student'] : 0;

// Verify this parent is linked to this student
$check = $conn->query("SELECT ps.student_id, u.full_name FROM parent_student ps JOIN users u ON ps.student_id=u.id WHERE ps.parent_id=$uid AND ps.student_id=$studentId");
if ($check->num_rows === 0) redirect('parent/dashboard.php');
$studentName = $check->fetch_assoc()['full_name'];

$grades = $conn->query("
    SELECT c.name, c.code, g.midterm, g.final, g.assignment, g.total, g.grade_letter, g.remarks
    FROM grades g JOIN courses c ON g.course_id=c.id
    WHERE g.student_id=$studentId ORDER BY c.name
");

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <a href="dashboard.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <h4 class="fw-bold mb-1"><i class="fas fa-star me-2 text-warning"></i>Grades</h4>
    <p class="text-muted mb-4">Student: <strong><?= htmlspecialchars($studentName) ?></strong></p>
    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr><th>Course</th><th>Midterm (30%)</th><th>Assignment (20%)</th><th>Final (50%)</th><th>Total</th><th>Grade</th><th>Remarks</th></tr>
            </thead>
            <tbody>
            <?php if ($grades->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">No grades recorded yet.</td></tr>
            <?php endif; ?>
            <?php while ($g = $grades->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($g['name']) ?></strong><br><code><?= $g['code'] ?></code></td>
                    <td><?= $g['midterm'] !== null ? number_format($g['midterm'],1) : '—' ?></td>
                    <td><?= $g['assignment'] !== null ? number_format($g['assignment'],1) : '—' ?></td>
                    <td><?= $g['final'] !== null ? number_format($g['final'],1) : '—' ?></td>
                    <td><strong><?= number_format($g['total'],1) ?></strong></td>
                    <td>
                        <?php $gl = $g['grade_letter']; ?>
                        <span class="badge bg-<?= $gl==='A'?'success':($gl==='B'?'primary':($gl==='C'?'info':($gl==='D'?'warning':'danger'))) ?> fs-6"><?= $gl ?></span>
                    </td>
                    <td><?= htmlspecialchars($g['remarks'] ?? '—') ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
