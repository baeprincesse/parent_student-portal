<?php
require_once '../config/db.php';
requireRole('teacher');
$pageTitle = "Mark Attendance – " . SITE_NAME;
$uid = $_SESSION['user_id'];

$courseId = isset($_GET['course']) ? (int)$_GET['course'] : 0;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$courses = $conn->query("SELECT id, name, code FROM courses WHERE teacher_id=$uid");

// Save attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid  = (int)$_POST['course_id'];
    $d    = clean($_POST['date']);
    foreach ($_POST['status'] as $sid => $status) {
        $sid    = (int)$sid;
        $status = in_array($status, ['present','absent','late','excused']) ? $status : 'present';
        $note   = clean($_POST['note'][$sid] ?? '');
        $conn->query("INSERT INTO attendance (student_id, course_id, teacher_id, date, status, note)
            VALUES ($sid, $cid, $uid, '$d', '$status', '$note')
            ON DUPLICATE KEY UPDATE status='$status', note='$note'");
    }
    header("Location: mark_attendance.php?course=$cid&date=$d&saved=1");
    exit();
}

$students = [];
if ($courseId) {
    $res = $conn->query("SELECT u.id, u.full_name, s.student_number,
        a.status, a.note
        FROM enrollments e
        JOIN users u ON e.student_id=u.id
        LEFT JOIN students s ON s.user_id=u.id
        LEFT JOIN attendance a ON a.student_id=u.id AND a.course_id=$courseId AND a.date='$date'
        WHERE e.course_id=$courseId ORDER BY u.full_name");
    $students = $res->fetch_all(MYSQLI_ASSOC);
}

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="fw-bold mb-4"><i class="fas fa-calendar-check me-2 text-primary"></i>Mark Attendance</h4>
    <?php if (isset($_GET['saved'])): ?><div class="alert alert-success">Attendance saved!</div><?php endif; ?>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <select name="course" class="form-select">
                <option value="">Select Course…</option>
                <?php while ($c = $courses->fetch_assoc()): ?>
                <option value="<?= $c['id'] ?>" <?= $courseId==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?> (<?= $c['code'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" value="<?= $date ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Load</button>
        </div>
    </form>

    <?php if ($courseId && $students): ?>
    <form method="POST">
        <input type="hidden" name="course_id" value="<?= $courseId ?>">
        <input type="hidden" name="date" value="<?= $date ?>">
        <div class="card p-3">
            <div class="d-flex justify-content-between mb-3">
                <h6 class="fw-bold mb-0">Date: <?= date('l, F d Y', strtotime($date)) ?></h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success me-1" onclick="markAll('present')">All Present</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="markAll('absent')">All Absent</button>
                </div>
            </div>
            <table class="table table-hover align-middle">
                <thead><tr><th>Student</th><th>Status</th><th>Note</th></tr></thead>
                <tbody>
                <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['full_name']) ?> <small class="text-muted"><?= $s['student_number'] ?></small></td>
                    <td>
                        <select name="status[<?= $s['id'] ?>]" class="form-select form-select-sm status-select" style="width:130px">
                            <?php foreach (['present','absent','late','excused'] as $st): ?>
                            <option value="<?= $st ?>" <?= ($s['status']??'present')===$st?'selected':'' ?>><?= ucfirst($st) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="note[<?= $s['id'] ?>]" class="form-control form-control-sm" placeholder="Optional note" value="<?= htmlspecialchars($s['note']??'') ?>"></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Attendance</button>
        </div>
    </form>
    <script>
    function markAll(status) {
        document.querySelectorAll('.status-select').forEach(s => s.value = status);
    }
    </script>
    <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
