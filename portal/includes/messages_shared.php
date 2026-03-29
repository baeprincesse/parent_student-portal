<?php
require_once '../config/db.php';
requireLogin();
$pageTitle = "Messages – " . SITE_NAME;
$uid = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    $to = (int)$_POST['receiver_id'];
    $subject = clean($_POST['subject']);
    $body = clean($_POST['body']);
    $conn->query("INSERT INTO messages (sender_id, receiver_id, subject, body) VALUES ($uid, $to, '$subject', '$body')");
    // Notify
    $conn->query("INSERT INTO notifications (user_id, title, message) VALUES ($to, 'New Message', 'You have a new message from ".htmlspecialchars($_SESSION['full_name'])."')");
    header("Location: messages.php?sent=1");
    exit();
}

// Mark as read
if (isset($_GET['read'])) {
    $mid = (int)$_GET['read'];
    $conn->query("UPDATE messages SET is_read=1 WHERE id=$mid AND receiver_id=$uid");
}

$inbox = $conn->query("SELECT m.*, u.full_name as sender_name FROM messages m JOIN users u ON m.sender_id=u.id WHERE m.receiver_id=$uid ORDER BY m.sent_at DESC");
$sent  = $conn->query("SELECT m.*, u.full_name as receiver_name FROM messages m JOIN users u ON m.receiver_id=u.id WHERE m.sender_id=$uid ORDER BY m.sent_at DESC");

// Users to message (all except self)
$users = $conn->query("SELECT id, full_name, role FROM users WHERE id != $uid AND is_active=1 ORDER BY role, full_name");

$view = isset($_GET['read']) ? (int)$_GET['read'] : null;
$viewMsg = null;
if ($view) {
    $viewMsg = $conn->query("SELECT m.*, u.full_name as sender_name FROM messages m JOIN users u ON m.sender_id=u.id WHERE m.id=$view")->fetch_assoc();
}

include '../includes/header.php';
include '../includes/navbar.php';
?>
<div class="main-content">
    <h4 class="fw-bold mb-4"><i class="fas fa-envelope me-2 text-primary"></i>Messages</h4>
    <?php if (isset($_GET['sent'])): ?>
        <div class="alert alert-success">Message sent successfully!</div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Compose -->
        <div class="col-lg-4">
            <div class="card p-3">
                <h6 class="fw-bold mb-3">Compose Message</h6>
                <form method="POST">
                    <div class="mb-2">
                        <label class="form-label small">To</label>
                        <select name="receiver_id" class="form-select form-select-sm" required>
                            <option value="">Select recipient…</option>
                            <?php while ($u = $users->fetch_assoc()): ?>
                            <option value="<?= $u['id'] ?>">[<?= ucfirst($u['role']) ?>] <?= htmlspecialchars($u['full_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Subject</label>
                        <input type="text" name="subject" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Message</label>
                        <textarea name="body" class="form-control form-control-sm" rows="4" required></textarea>
                    </div>
                    <button name="send" class="btn btn-primary btn-sm w-100"><i class="fas fa-paper-plane me-1"></i>Send</button>
                </form>
            </div>
        </div>

        <!-- Inbox -->
        <div class="col-lg-8">
            <?php if ($viewMsg): ?>
            <div class="card p-4 mb-3">
                <h6><?= htmlspecialchars($viewMsg['subject']) ?></h6>
                <small class="text-muted">From: <?= htmlspecialchars($viewMsg['sender_name']) ?> · <?= date('M d Y H:i', strtotime($viewMsg['sent_at'])) ?></small>
                <hr>
                <p><?= nl2br(htmlspecialchars($viewMsg['body'])) ?></p>
                <a href="messages.php" class="btn btn-sm btn-outline-secondary">Back to Inbox</a>
            </div>
            <?php endif; ?>

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#inbox">Inbox <span class="badge bg-danger"><?= countUnread($uid) ?></span></a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#sentbox">Sent</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="inbox">
                    <div class="card">
                        <?php while ($m = $inbox->fetch_assoc()): ?>
                        <a href="messages.php?read=<?= $m['id'] ?>" class="list-group-item list-group-item-action <?= !$m['is_read']?'fw-bold':'' ?>">
                            <div class="d-flex justify-content-between">
                                <span><?= htmlspecialchars($m['sender_name']) ?></span>
                                <small class="text-muted"><?= date('M d', strtotime($m['sent_at'])) ?></small>
                            </div>
                            <small><?= htmlspecialchars($m['subject']) ?></small>
                            <?php if (!$m['is_read']): ?><span class="badge bg-danger float-end">New</span><?php endif; ?>
                        </a>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="tab-pane fade" id="sentbox">
                    <div class="card">
                        <?php while ($m = $sent->fetch_assoc()): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <span>To: <?= htmlspecialchars($m['receiver_name']) ?></span>
                                <small class="text-muted"><?= date('M d', strtotime($m['sent_at'])) ?></small>
                            </div>
                            <small><?= htmlspecialchars($m['subject']) ?></small>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
