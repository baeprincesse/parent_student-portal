<?php
require_once '../config/db.php';
requireRole('student');
$pageTitle = "Student Dashboard – " . SITE_NAME;
$uid = $_SESSION['user_id'];

// Get student info
$student = $conn->query("SELECT s.*, d.name as dept FROM students s LEFT JOIN departments d ON s.department_id=d.id WHERE s.user_id=$uid")->fetch_assoc();

// Enrolled courses
$courses = $conn->query("SELECT c.name, c.code, u.full_name as teacher FROM enrollments e JOIN courses c ON e.course_id=c.id LEFT JOIN users u ON c.teacher_id=u.id WHERE e.student_id=$uid");

// Recent grades
$grades = $conn->query("SELECT c.name, g.total, g.grade_letter FROM grades g JOIN courses c ON g.course_id=c.id WHERE g.student_id=$uid ORDER BY g.updated_at DESC LIMIT 5");

// Attendance summary
$att = $conn->query("SELECT status, COUNT(*) cnt FROM attendance WHERE student_id=$uid GROUP BY status")->fetch_all(MYSQLI_ASSOC);
$attMap = array_column($att, 'cnt', 'status');

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="mb-1 fw-bold">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> 👋</h4>
    <p class="text-muted mb-4"><?= htmlspecialchars($student['dept'] ?? '') ?> · <?= htmlspecialchars($student['level'] ?? '') ?></p>

    <div class="row g-4 mb-4">
        <?php
        $cards = [
            ['label'=>'Enrolled Courses','value'=>$courses->num_rows,'icon'=>'fa-book','color'=>'#1a3c6e','bg'=>'#e8f0fb'],
            ['label'=>'Present Days','value'=>$attMap['present']??0,'icon'=>'fa-check-circle','color'=>'#198754','bg'=>'#e8f5ee'],
            ['label'=>'Absent Days','value'=>$attMap['absent']??0,'icon'=>'fa-times-circle','color'=>'#dc3545','bg'=>'#fde8e8'],
            ['label'=>'Unread Messages','value'=>countUnread($uid),'icon'=>'fa-envelope','color'=>'#e67e22','bg'=>'#fef3e2'],
        ];
        foreach ($cards as $c): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="card stat-card p-3 d-flex flex-row align-items-center gap-3">
                <div class="stat-icon" style="background:<?= $c['bg'] ?>;color:<?= $c['color'] ?>">
                    <i class="fas <?= $c['icon'] ?>"></i>
                </div>
                <div>
                    <div class="text-muted small"><?= $c['label'] ?></div>
                    <div class="fs-4 fw-bold"><?= $c['value'] ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card p-3">
                <h6 class="fw-bold mb-3"><i class="fas fa-star me-2 text-warning"></i>Recent Grades</h6>
                <table class="table table-sm">
                    <thead><tr><th>Course</th><th>Score</th><th>Grade</th></tr></thead>
                    <tbody>
                    <?php while ($g = $grades->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($g['name']) ?></td>
                            <td><?= number_format($g['total'], 1) ?></td>
                            <td><span class="badge bg-<?= $g['grade_letter'] === 'A' ? 'success' : ($g['grade_letter'] === 'F' ? 'danger' : 'primary') ?>"><?= $g['grade_letter'] ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="grades.php" class="btn btn-sm btn-outline-primary">View All Grades</a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3">
                <h6 class="fw-bold mb-3"><i class="fas fa-book-open me-2 text-primary"></i>My Courses</h6>
                <ul class="list-group list-group-flush">
                    <?php
                    // Re-run query since pointer exhausted
                    $courses2 = $conn->query("SELECT c.name, c.code, u.full_name as teacher FROM enrollments e JOIN courses c ON e.course_id=c.id LEFT JOIN users u ON c.teacher_id=u.id WHERE e.student_id=$uid");
                    while ($row = $courses2->fetch_assoc()): ?>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span><?= htmlspecialchars($row['name']) ?> <code class="ms-1"><?= $row['code'] ?></code></span>
                        <small class="text-muted"><?= htmlspecialchars($row['teacher'] ?? '—') ?></small>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
