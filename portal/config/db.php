<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portal2_db');
define('BASE_URL', 'http://localhost/parent-student-portal2/portal');
define('SITE_NAME', 'University Portal');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: redirect
function redirect($url) {
    header("Location: " . BASE_URL . "/" . $url);
    exit();
}

// Helper: check login
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        redirect('auth/login.php');
    }
}

// Helper: check role
function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        redirect($_SESSION['role'] . '/dashboard.php');
    }
}

// Helper: sanitize input
function clean($data) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}

// Helper: letter grade
function getLetterGrade($score) {
    if ($score >= 90) return 'A';
    if ($score >= 80) return 'B';
    if ($score >= 70) return 'C';
    if ($score >= 60) return 'D';
    return 'F';
}

// Helper: count unread messages
function countUnread($user_id) {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as cnt FROM messages WHERE receiver_id=$user_id AND is_read=0");
    return $result->fetch_assoc()['cnt'];
}
