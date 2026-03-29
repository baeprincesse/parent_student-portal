<?php
require_once '../config/db.php';

if (isset($_SESSION['user_id'])) {
    redirect($_SESSION['role'] . '/dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = clean($_POST['full_name']);
    $email    = clean($_POST['email']);
    $phone    = clean($_POST['phone']);
    $role     = clean($_POST['role']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Validations
    if (!in_array($role, ['student', 'teacher', 'parent'])) {
        $error = 'Invalid role selected.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check email exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'This email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            // Create account as inactive (pending admin approval)
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, phone, is_active) VALUES (?, ?, ?, ?, ?, 0)");
            $stmt->bind_param("sssss", $name, $email, $hash, $role, $phone);
            if ($stmt->execute()) {
                $newId = $conn->insert_id;

                // If student, create student record
                if ($role === 'student') {
                    $snum = 'STU' . str_pad($newId, 5, '0', STR_PAD_LEFT);
                    $conn->query("INSERT INTO students (user_id, student_number) VALUES ($newId, '$snum')");
                }

                $success = true;
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a3c6e 0%, #2e6da4 100%); min-height: 100vh; display: flex; align-items: center; padding: 30px 0; }
        .register-card { border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .register-header { background: linear-gradient(135deg, #1a3c6e, #2e6da4); border-radius: 16px 16px 0 0; }
        .btn-register { background: linear-gradient(135deg, #1a3c6e, #2e6da4); border: none; }
        .btn-register:hover { opacity: 0.9; }
        .role-card { border: 2px solid #dee2e6; border-radius: 10px; padding: 15px; cursor: pointer; transition: all 0.2s; text-align: center; }
        .role-card:hover { border-color: #2e6da4; background: #f0f6ff; }
        .role-card.selected { border-color: #1a3c6e; background: #e8f0fb; }
        .role-card i { font-size: 28px; margin-bottom: 8px; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card register-card">
                <div class="register-header text-white text-center py-4">
                    <i class="fas fa-university fa-3x mb-2"></i>
                    <h4 class="mb-0"><?= SITE_NAME ?></h4>
                    <small>Create Your Account</small>
                </div>
                <div class="card-body p-4">

                    <?php if ($success): ?>
                    <!-- Success Message -->
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success" style="font-size:64px"></i>
                        <h5 class="mt-3 fw-bold">Registration Successful!</h5>
                        <p class="text-muted">Your account has been created and is <strong>pending approval</strong> by the administrator. You will be able to login once your account is activated.</p>
                        <a href="login.php" class="btn btn-primary mt-2"><i class="fas fa-sign-in-alt me-2"></i>Go to Login</a>
                    </div>

                    <?php else: ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" id="registerForm">

                        <!-- Role Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">I am a…</label>
                            <div class="row g-3">
                                <div class="col-4">
                                    <div class="role-card <?= (isset($_POST['role']) && $_POST['role']==='student') ? 'selected' : '' ?>" onclick="selectRole('student')">
                                        <i class="fas fa-user-graduate text-primary d-block"></i>
                                        <strong>Student</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="role-card <?= (isset($_POST['role']) && $_POST['role']==='teacher') ? 'selected' : '' ?>" onclick="selectRole('teacher')">
                                        <i class="fas fa-chalkboard-teacher text-success d-block"></i>
                                        <strong>Teacher</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="role-card <?= (isset($_POST['role']) && $_POST['role']==='parent') ? 'selected' : '' ?>" onclick="selectRole('parent')">
                                        <i class="fas fa-user-friends text-warning d-block"></i>
                                        <strong>Parent</strong>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="role" id="roleInput" value="<?= isset($_POST['role']) ? $_POST['role'] : '' ?>" required>
                            <div id="roleError" class="text-danger small mt-1" style="display:none">Please select a role.</div>
                        </div>

                        <!-- Full Name -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone <span class="text-muted fw-normal">(optional)</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" name="phone" class="form-control" placeholder="+237600000000" value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Min. 6 characters" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password','eye1')"><i class="fas fa-eye" id="eye1"></i></button>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Repeat your password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('confirm_password','eye2')"><i class="fas fa-eye" id="eye2"></i></button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-register btn-primary w-100 py-2 text-white fw-semibold">
                            <i class="fas fa-user-plus me-2"></i>Create Account
                        </button>
                    </form>
                    <?php endif; ?>

                </div>
                <div class="card-footer text-center py-3">
                    Already have an account? <a href="login.php" class="text-primary fw-semibold">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectRole(role) {
    document.getElementById('roleInput').value = role;
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('roleError').style.display = 'none';
}

function togglePwd(fieldId, eyeId) {
    const p = document.getElementById(fieldId);
    const e = document.getElementById(eyeId);
    p.type = p.type === 'password' ? 'text' : 'password';
    e.className = p.type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
}

document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    if (!document.getElementById('roleInput').value) {
        e.preventDefault();
        document.getElementById('roleError').style.display = 'block';
    }
});
</script>
</body>
</html>
