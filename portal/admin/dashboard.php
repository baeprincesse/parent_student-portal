<?php
require_once '../config/db.php';
requireRole('admin');
$pageTitle = "Admin Dashboard – " . SITE_NAME;

$stats = [
    'students' => $conn->query("SELECT COUNT(*) c FROM users WHERE role='student'")->fetch_assoc()['c'],
    'teachers' => $conn->query("SELECT COUNT(*) c FROM users WHERE role='teacher'")->fetch_assoc()['c'],
    'parents'  => $conn->query("SELECT COUNT(*) c FROM users WHERE role='parent'")->fetch_assoc()['c'],
    'courses'  => $conn->query("SELECT COUNT(*) c FROM courses")->fetch_assoc()['c'],
];
include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="mb-4 fw-bold">Admin Dashboard</h4>
    <div class="row g-4 mb-4">
        <?php
        $cards = [
            ['label'=>'Students','value'=>$stats['students'],'icon'=>'fa-user-graduate','color'=>'#1a3c6e','bg'=>'#e8f0fb'],
            ['label'=>'Teachers','value'=>$stats['teachers'],'icon'=>'fa-chalkboard-teacher','color'=>'#198754','bg'=>'#e8f5ee'],
            ['label'=>'Parents','value'=>$stats['parents'],'icon'=>'fa-users','color'=>'#e67e22','bg'=>'#fef3e2'],
            ['label'=>'Courses','value'=>$stats['courses'],'icon'=>'fa-book','color'=>'#8e44ad','bg'=>'#f3e8fb'],
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
                <h6 class="fw-bold mb-3"><i class="fas fa-clock me-2 text-primary"></i>Recent Users</h6>
                <table class="table table-sm table-hover">
                    <thead><tr><th>Name</th><th>Role</th><th>Joined</th></tr></thead>
                    <tbody>
                    <?php
                    $recent = $conn->query("SELECT full_name, role, created_at FROM users ORDER BY created_at DESC LIMIT 8");
                    while ($row = $recent->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><span class="badge bg-secondary"><?= $row['role'] ?></span></td>
                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card p-3">
                <h6 class="fw-bold mb-3"><i class="fas fa-book me-2 text-primary"></i>Active Courses</h6>
                <table class="table table-sm table-hover">
                    <thead><tr><th>Course</th><th>Code</th><th>Teacher</th></tr></thead>
                    <tbody>
                    <?php
                    $courses = $conn->query("SELECT c.name, c.code, u.full_name FROM courses c LEFT JOIN users u ON c.teacher_id=u.id LIMIT 8");
                    while ($row = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><code><?= $row['code'] ?></code></td>
                            <td><?= htmlspecialchars($row['full_name'] ?? '—') ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
