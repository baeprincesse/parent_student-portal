<?php
require_once '../config/db.php';
requireRole('parent');
$pageTitle = "Child's Attendance – " . SITE_NAME;
$uid = $_SESSION['user_id'];

$studentId = isset($_GET['student']) ? (int)$_GET['student'] : 0;

$check = $conn->query("SELECT ps.student_id, u.full_name FROM parent_student ps JOIN users u ON ps.student_id=u.id WHERE ps.parent_id=$uid AND ps.student_id=$studentId");
if ($check->num_rows === 0) redirect('parent/dashboard.php');
$studentName = $check->fetch_assoc()['full_name'];

$records = $conn->query("SELECT a.date, a.status, a.note, c.name as course FROM attendance a JOIN courses c ON a.course_id=c.id WHERE a.student_id=$studentId ORDER BY a.date DESC");

$summary = $conn->query("SELECT status, COUNT(*) cnt FROM attendance WHERE student_id=$studentId GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$s = array_column($summary, 'cnt', 'status');
$total = array_sum(array_column($summary, 'cnt'));
$pct = $total > 0 ? round(($s['present']??0) / $total * 100) : 0;

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <a href="dashboard.php" class="btn btn-sm btn-outline-secondary mb-3"><i class="fas fa-arrow-left me-1"></i>Back</a>
    <h4 class="fw-bold mb-1"><i class="fas fa-calendar-check me-2 text-primary"></i>Attendance</h4>
    <p class="text-muted mb-4">Student: <strong><?= htmlspecialchars($studentName) ?></strong></p>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card p-3 text-center"><div class="text-success fw-bold fs-4"><?= $s['present']??0 ?></div><small>Present</small></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><div class="text-danger fw-bold fs-4"><?= $s['absent']??0 ?></div><small>Absent</small></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><div class="text-warning fw-bold fs-4"><?= $s['late']??0 ?></div><small>Late</small></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><div class="text-primary fw-bold fs-4"><?= $pct ?>%</div><small>Attendance Rate</small></div></div>
    </div>

    <div class="card p-3">
        <table class="table table-hover">
            <thead><tr><th>Date</th><th>Course</th><th>Status</th><th>Note</th></tr></thead>
            <tbody>
            <?php if ($records->num_rows === 0): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">No records found.</td></tr>
            <?php endif; ?>
            <?php while ($r = $records->fetch_assoc()): ?>
                <tr>
                    <td><?= date('D, M d Y', strtotime($r['date'])) ?></td>
                    <td><?= htmlspecialchars($r['course']) ?></td>
                    <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                    <td><?= htmlspecialchars($r['note'] ?? '—') ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
