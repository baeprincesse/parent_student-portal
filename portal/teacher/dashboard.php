<?php
require_once '../config/db.php';
requireRole('teacher');
$pageTitle = "Teacher Dashboard – " . SITE_NAME;
$uid = $_SESSION['user_id'];

$myCourses = $conn->query("SELECT c.*, d.name as dept, COUNT(e.id) as enrolled FROM courses c LEFT JOIN departments d ON c.department_id=d.id LEFT JOIN enrollments e ON c.id=e.course_id WHERE c.teacher_id=$uid GROUP BY c.id");

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="fw-bold mb-4">Teacher Dashboard</h4>
    <div class="row g-4">
        <?php while ($c = $myCourses->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="fw-bold"><?= htmlspecialchars($c['name']) ?></h6>
                    <code><?= $c['code'] ?></code>
                </div>
                <p class="text-muted small mb-2"><?= htmlspecialchars($c['dept'] ?? '') ?> · Semester <?= $c['semester'] ?></p>
                <p class="mb-3"><i class="fas fa-users me-1 text-primary"></i><?= $c['enrolled'] ?> students enrolled</p>
                <div class="d-flex gap-2">
                    <a href="enter_grades.php?course=<?= $c['id'] ?>" class="btn btn-sm btn-outline-warning"><i class="fas fa-star me-1"></i>Grades</a>
                    <a href="mark_attendance.php?course=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-calendar-check me-1"></i>Attendance</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
