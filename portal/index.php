<?php
require_once 'config/db.php';
if (isset($_SESSION['user_id'])) {
    redirect($_SESSION['role'] . '/dashboard.php');
} else {
    redirect('home.php');
}
?>