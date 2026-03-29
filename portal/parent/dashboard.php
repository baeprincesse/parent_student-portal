<?php
require_once '../config/db.php';
requireRole('parent');
$pageTitle = "Parent Dashboard – " . SITE_NAME;
$uid = $_SESSION['user_id'];

// Get linked children
$children = $conn->query("SELECT u.id, u.full_name, s.student_number, s.level, d.name as dept
    FROM parent_student ps
    JOIN users u ON ps.student_id = u.id
    LEFT JOIN students s ON s.user_id = u.id
    LEFT JOIN departments d ON s.department_id = d.id
    WHERE ps.parent_id = $uid");

$childList = $children->fetch_all(MYSQLI_ASSOC);
$childIds = array_column($childList, 'id');

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="fw-bold mb-4">Parent Dashboard</h4>

    <?php if (empty($childIds)): ?>
        <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No students linked to your account yet. Please contact the administrator.</div>
    <?php endif; ?>

    <div class="row g-4">
        <?php foreach ($childList as $child): ?>
        <?php
            $cid = $child['id'];
            $attSummary = $conn->query("SELECT status, COUNT(*) cnt FROM attendance WHERE student_id=$cid GROUP BY status")->fetch_all(MYSQLI_ASSOC);
            $attMap = array_column($attSummary, 'cnt', 'status');
            $total = array_sum(array_column($attSummary, 'cnt'));
            $pct = $total > 0 ? round(($attMap['present']??0) / $total * 100) : 0;
            $gradeCount = $conn->query("SELECT COUNT(*) c FROM grades WHERE student_id=$cid")->fetch_assoc()['c'];
        ?>
        <div class="col-md-6">
            <div class="card p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon me-3" style="background:#e8f0fb;color:#1a3c6e;font-size:28px;width:56px;height:56px">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($child['full_name']) ?></h5>
                        <small class="text-muted"><?= htmlspecialchars($child['student_number'] ?? '') ?> · <?= htmlspecialchars($child['dept'] ?? '') ?> · <?= htmlspecialchars($child['level'] ?? '') ?></small>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4 text-center">
                        <div class="fw-bold text-success fs-5"><?= $attMap['present']??0 ?></div>
                        <small class="text-muted">Present</small>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-bold text-danger fs-5"><?= $attMap['absent']??0 ?></div>
                        <small class="text-muted">Absent</small>
                    </div>
                    <div class="col-4 text-center">
                        <div class="fw-bold text-primary fs-5"><?= $pct ?>%</div>
                        <small class="text-muted">Rate</small>
                    </div>
                </div>
                <div class="progress mb-3" style="height:8px">
                    <div class="progress-bar bg-<?= $pct>=75?'success':($pct>=50?'warning':'danger') ?>" style="width:<?= $pct ?>%"></div>
                </div>
                <div class="d-flex gap-2">
                    <a href="view_grades.php?student=<?= $cid ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-star me-1"></i>View Grades (<?= $gradeCount ?>)</a>
                    <a href="view_attendance.php?student=<?= $cid ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-calendar-check me-1"></i>Attendance</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
