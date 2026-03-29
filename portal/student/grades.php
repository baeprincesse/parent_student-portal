<?php
require_once '../config/db.php';
requireRole('student');
$pageTitle = "My Grades – " . SITE_NAME;
$uid = $_SESSION['user_id'];

$grades = $conn->query("
    SELECT c.name, c.code, g.midterm, g.final, g.assignment, g.total, g.grade_letter, g.remarks
    FROM grades g JOIN courses c ON g.course_id=c.id
    WHERE g.student_id=$uid ORDER BY c.name
");

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="fw-bold mb-4"><i class="fas fa-star me-2 text-warning"></i>My Grades</h4>
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
