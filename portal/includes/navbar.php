<?php
$role = $_SESSION['role'];
$base = BASE_URL;
$menus = [
    'admin' => [
        ['icon'=>'fa-tachometer-alt','label'=>'Dashboard','url'=>'admin/dashboard.php'],
        ['icon'=>'fa-user-graduate','label'=>'Manage Students','url'=>'admin/manage_students.php'],
        ['icon'=>'fa-chalkboard-teacher','label'=>'Manage Teachers','url'=>'admin/manage_teachers.php'],
        ['icon'=>'fa-user-friends','label'=>'Manage Parents','url'=>'admin/manage_parent.php'],
        ['icon'=>'fa-book','label'=>'Manage Courses','url'=>'admin/manage_courses.php'],
        ['icon'=>'fa-envelope','label'=>'Messages','url'=>'admin/messages.php'],
    ],
    'teacher' => [
        ['icon'=>'fa-tachometer-alt','label'=>'Dashboard','url'=>'teacher/dashboard.php'],
        ['icon'=>'fa-star','label'=>'Enter Grades','url'=>'teacher/enter_grades.php'],
        ['icon'=>'fa-calendar-check','label'=>'Attendance','url'=>'teacher/mark_attendance.php'],
        ['icon'=>'fa-envelope','label'=>'Messages','url'=>'teacher/messages.php'],
    ],
    'student' => [
        ['icon'=>'fa-tachometer-alt','label'=>'Dashboard','url'=>'student/dashboard.php'],
        ['icon'=>'fa-star','label'=>'My Grades','url'=>'student/grades.php'],
        ['icon'=>'fa-calendar-check','label'=>'My Attendance','url'=>'student/attendance.php'],
        ['icon'=>'fa-envelope','label'=>'Messages','url'=>'student/messages.php'],
    ],
    'parent' => [
        ['icon'=>'fa-tachometer-alt','label'=>'Dashboard','url'=>'parent/dashboard.php'],
        ['icon'=>'fa-star','label'=>"Child's Grades",'url'=>'parent/view_grades.php'],
        ['icon'=>'fa-calendar-check','label'=>"Child's Attendance",'url'=>'parent/view_attendance.php'],
        ['icon'=>'fa-envelope','label'=>'Messages','url'=>'parent/messages.php'],
    ],
];
$currentMenu = $menus[$role] ?? [];
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar bg-dark text-white" style="min-width:220px;min-height:calc(100vh - 56px);">
    <ul class="nav flex-column pt-3">
        <?php foreach ($currentMenu as $item): ?>
        <li class="nav-item">
            <a class="nav-link text-white <?= basename($item['url']) === $currentPage ? 'active bg-primary rounded mx-2' : '' ?>"
               href="<?= $base ?>/<?= $item['url'] ?>">
                <i class="fas <?= $item['icon'] ?> me-2"></i><?= $item['label'] ?>
            </a>
        </li>
        <?php endforeach; ?>
        <li class="nav-item mt-4">
            <a class="nav-link text-danger" href="<?= $base ?>/auth/logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </li>
    </ul>
</div>
