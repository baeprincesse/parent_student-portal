<?php
require_once '../config/db.php';
requireRole('student');
$pageTitle = "My Attendance – " . SITE_NAME;
$uid = $_SESSION['user_id'];

$filterCourse = isset($_GET['course']) ? (int)$_GET['course'] : 0;

$courseList = $conn->query("SELECT c.id, c.name FROM enrollments e JOIN courses c ON e.course_id=c.id WHERE e.student_id=$uid");

$where = "WHERE a.student_id=$uid";
if ($filterCourse) $where .= " AND a.course_id=$filterCourse";

$records = $conn->query("SELECT a.date, a.status, a.note, c.name as course FROM attendance a JOIN courses c ON a.course_id=c.id $where ORDER BY a.date DESC");

$summary = $conn->query("SELECT status, COUNT(*) cnt FROM attendance WHERE student_id=$uid GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$s = array_column($summary, 'cnt', 'status');
$total = array_sum(array_column($summary, 'cnt'));
$pct = $total > 0 ? round(($s['present']??0) / $total * 100) : 0;

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="fw-bold mb-4"><i class="fas fa-calendar-check me-2 text-primary"></i>My Attendance</h4>

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card p-3 text-center"><div class="text-success fw-bold fs-4"><?= $s['present']??0 ?></div><small>Present</small></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><div class="text-danger fw-bold fs-4"><?= $s['absent']??0 ?></div><small>Absent</small></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><div class="text-warning fw-bold fs-4"><?= $s['late']??0 ?></div><small>Late</small></div></div>
        <div class="col-md-3"><div class="card p-3 text-center"><div class="text-primary fw-bold fs-4"><?= $pct ?>%</div><small>Attendance Rate</small></div></div>
    </div>

    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Attendance Records</h6>
            <form method="GET" class="d-flex gap-2">
                <select name="course" class="form-select form-select-sm">
                    <option value="">All Courses</option>
                    <?php while ($c = $courseList->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= $filterCourse==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>
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
