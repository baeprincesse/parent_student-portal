<?php
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    redirect($_SESSION['role'] . '/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role, is_active FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (!$user['is_active']) {
            $error = "Your account has been deactivated. Contact admin.";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
            redirect($user['role'] . '/dashboard.php');
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a3c6e 0%, #2e6da4 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .login-header { background: linear-gradient(135deg, #1a3c6e, #2e6da4); border-radius: 16px 16px 0 0; }
        .btn-login { background: linear-gradient(135deg, #1a3c6e, #2e6da4); border: none; }
        .btn-login:hover { opacity: 0.9; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card login-card">
                <div class="login-header text-white text-center py-4">
                    <i class="fas fa-university fa-3x mb-2"></i>
                    <h4 class="mb-0"><?= SITE_NAME ?></h4>
                    <small>Parent & Student Portal</small>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd()"><i class="fas fa-eye" id="eye"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-login btn-primary w-100 py-2 text-white fw-semibold">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>
                </div>
                <div class="card-footer text-center text-muted py-3">
                    <small>© <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</small>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function togglePwd() {
    const p = document.getElementById('password');
    const e = document.getElementById('eye');
    p.type = p.type === 'password' ? 'text' : 'password';
    e.className = p.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}
</script>
</body>
</html>
