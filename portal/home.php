<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome – University Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }

        /* Hero */
        .hero {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d1f3c 0%, #1a3c6e 50%, #2e6da4 100%);
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        .top-nav {
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .top-nav .brand { color: white; font-size: 20px; font-weight: 700; text-decoration: none; }
        .top-nav .brand i { margin-right: 8px; }
        .top-nav .nav-btns a {
            color: white;
            text-decoration: none;
            margin-left: 16px;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .top-nav .nav-btns .btn-outline-light-custom {
            border: 2px solid rgba(255,255,255,0.6);
        }
        .top-nav .nav-btns .btn-outline-light-custom:hover {
            background: rgba(255,255,255,0.15);
        }
        .top-nav .nav-btns .btn-white {
            background: white;
            color: #1a3c6e;
            font-weight: 600;
        }
        .top-nav .nav-btns .btn-white:hover { background: #f0f0f0; }

        /* Hero Content */
        .hero-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .hero-content h1 {
            color: white;
            font-size: 52px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .hero-content h1 span { color: #64b5f6; }
        .hero-content p {
            color: rgba(255,255,255,0.8);
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.7;
        }
        .hero-btns { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
        .hero-btns a {
            padding: 14px 36px;
            border-radius: 30px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-get-started {
            background: #64b5f6;
            color: #0d1f3c;
        }
        .btn-get-started:hover { background: #42a5f5; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(100,181,246,0.4); }
        .btn-sign-in {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 2px solid rgba(255,255,255,0.4);
            backdrop-filter: blur(10px);
        }
        .btn-sign-in:hover { background: rgba(255,255,255,0.25); transform: translateY(-2px); }

        /* Role Cards */
        .roles-section {
            background: #f8faff;
            padding: 80px 20px;
        }
        .roles-section h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a3c6e;
            margin-bottom: 12px;
        }
        .roles-section .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 50px;
            font-size: 16px;
        }
        .role-card {
            background: white;
            border-radius: 16px;
            padding: 36px 24px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            transition: all 0.3s;
            height: 100%;
            border: 2px solid transparent;
        }
        .role-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }
        .role-card .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            margin: 0 auto 20px;
        }
        .role-card h5 { font-weight: 700; font-size: 18px; margin-bottom: 10px; }
        .role-card p { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 20px; }
        .role-card a {
            display: inline-block;
            padding: 10px 28px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        /* Features Section */
        .features-section {
            background: white;
            padding: 80px 20px;
        }
        .features-section h2 {
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1a3c6e;
            margin-bottom: 50px;
        }
        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 32px;
        }
        .feature-item .f-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .feature-item h6 { font-weight: 700; margin-bottom: 4px; }
        .feature-item p { color: #666; font-size: 14px; margin: 0; }

        /* Footer */
        footer {
            background: #0d1f3c;
            color: rgba(255,255,255,0.6);
            text-align: center;
            padding: 24px;
            font-size: 14px;
        }
        footer span { color: #64b5f6; }
    </style>
</head>
<body>

<!-- HERO SECTION -->
<div class="hero">
    <!-- Top Nav -->
    <div class="top-nav">
        <a href="#" class="brand"><i class="fas fa-university"></i>University Portal</a>
        <div class="nav-btns">
            <a href="auth/login.php" class="btn-outline-light-custom">Login</a>
            <a href="auth/register.php" class="btn-white">Register</a>
        </div>
    </div>

    <!-- Hero Content -->
    <div class="hero-content">
        <div>
            <h1>Your University<br><span>All In One Place</span></h1>
            <p>Access grades, attendance, and messages for students, parents, and teachers — anytime, anywhere.</p>
            <div class="hero-btns">
                <a href="auth/register.php" class="btn-get-started"><i class="fas fa-user-plus me-2"></i>Get Started</a>
                <a href="auth/login.php" class="btn-sign-in"><i class="fas fa-sign-in-alt me-2"></i>Login</a>
            </div>
        </div>
    </div>
</div>

<!-- ROLE CARDS -->
<div class="roles-section">
    <h2>Who is this for?</h2>
    <p class="subtitle">One platform for everyone in your university community</p>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="role-card" style="border-color:#e8f0fb">
                    <div class="icon-wrap" style="background:#e8f0fb;color:#1a3c6e">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h5 style="color:#1a3c6e">Students</h5>
                    <p>View your grades, track your attendance, and communicate with your teachers directly.</p>
                    <a href="auth/register.php" style="background:#e8f0fb;color:#1a3c6e">Join as Student</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="role-card" style="border-color:#e8f5ee">
                    <div class="icon-wrap" style="background:#e8f5ee;color:#198754">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h5 style="color:#198754">Teachers</h5>
                    <p>Enter grades, mark attendance, manage your courses, and message students and parents.</p>
                    <a href="auth/register.php" style="background:#e8f5ee;color:#198754">Join as Teacher</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="role-card" style="border-color:#fff3e0">
                    <div class="icon-wrap" style="background:#fff3e0;color:#e67e22">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h5 style="color:#e67e22">Parents</h5>
                    <p>Stay updated on your child's academic performance, attendance and school communications.</p>
                    <a href="auth/register.php" style="background:#fff3e0;color:#e67e22">Join as Parent</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="role-card" style="border-color:#f3e8fb">
                    <div class="icon-wrap" style="background:#f3e8fb;color:#8e44ad">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h5 style="color:#8e44ad">Administrators</h5>
                    <p>Manage all users, courses, enrollments and approvals from one powerful dashboard.</p>
                    <a href="auth/login.php" style="background:#f3e8fb;color:#8e44ad">Admin Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FEATURES -->
<div class="features-section">
    <div class="container">
        <h2>Everything you need</h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="feature-item">
                    <div class="f-icon" style="background:#e8f0fb;color:#1a3c6e"><i class="fas fa-star"></i></div>
                    <div>
                        <h6>Grades Management</h6>
                        <p>Teachers enter midterm, assignment and final scores. Students and parents view results instantly.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="f-icon" style="background:#e8f5ee;color:#198754"><i class="fas fa-calendar-check"></i></div>
                    <div>
                        <h6>Attendance Tracking</h6>
                        <p>Mark and monitor attendance per course with detailed status: present, absent, late or excused.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="f-icon" style="background:#fff3e0;color:#e67e22"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h6>Internal Messaging</h6>
                        <p>Communicate directly between students, parents, teachers and administrators.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="feature-item">
                    <div class="f-icon" style="background:#fde8e8;color:#c0392b"><i class="fas fa-user-clock"></i></div>
                    <div>
                        <h6>Account Approvals</h6>
                        <p>New registrations are reviewed and approved by the administrator before access is granted.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="f-icon" style="background:#f3e8fb;color:#8e44ad"><i class="fas fa-link"></i></div>
                    <div>
                        <h6>Parent–Student Linking</h6>
                        <p>Parents are linked to their children's accounts to monitor academic progress in real time.</p>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="f-icon" style="background:#e8f0fb;color:#1a3c6e"><i class="fas fa-tachometer-alt"></i></div>
                    <div>
                        <h6>Role-Based Dashboards</h6>
                        <p>Each user sees a personalized dashboard with only the information relevant to their role.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    © <?= date('Y') ?> <span>University Portal</span>. All rights reserved.
</footer>

</body>
</html>
