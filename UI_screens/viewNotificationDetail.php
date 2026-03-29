<?php
include __DIR__ . '/nav.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$userID  = $_SESSION['user_id'];
$notifID = (int) ($_GET['id'] ?? 0);

if ($notifID <= 0) {
    header("Location: viewUserNotificationsPage.php");
    exit();
}

// ── Fetch notification (must belong to this user) ─────────────
$stmt = $conn->prepare(
    "SELECT n.notificationID, n.title, n.message, n.createdAt, n.isRead,
            u.username AS workerName
     FROM notifications n
     LEFT JOIN users u ON u.id = n.senderID
     WHERE n.notificationID = ? AND n.receiverID = ?
     LIMIT 1"
);
$stmt->bind_param('ii', $notifID, $userID);
$stmt->execute();
$notif = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$notif) {
    header("Location: viewUserNotificationsPage.php");
    exit();
}

// ── Auto-mark as read ─────────────────────────────────────────
if ($notif['isRead'] == 0) {
    $mark = $conn->prepare(
        "UPDATE notifications SET isRead = 1
         WHERE notificationID = ? AND receiverID = ?"
    );
    $mark->bind_param('ii', $notifID, $userID);
    $mark->execute();
    $mark->close();
    $notif['isRead'] = 1;
}

$workerName = !empty($notif['workerName'])
              ? htmlspecialchars($notif['workerName'])
              : 'A worker';
$timestamp  = date('d M Y, h:i A', strtotime($notif['createdAt']));
?>

<main id="content">
<div class="nd-wrapper">

    <!-- Back -->
    <a href="viewUserNotificationsPage.php" class="nd-back">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Notifications
    </a>

    <!-- Detail card -->
    <div class="nd-card">

        <div class="nd-status-row">
            <span class="nd-badge nd-badge--read">&#10003; Read</span>
        </div>

        <!-- Title -->
        <h1 class="nd-title"><?= htmlspecialchars($notif['title']) ?></h1>

        <!-- Meta -->
        <div class="nd-meta">
            <span class="nd-meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <?= $workerName ?>
            </span>
            <span class="nd-meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <?= $timestamp ?>
            </span>
        </div>

        <hr class="nd-divider">

        <!-- Full message -->
        <p class="nd-message"><?= htmlspecialchars($notif['message']) ?></p>

    </div>

    <!-- Mark as Unread -->
    <form action="../PHP_files/markNotificationUnread.php" method="POST" class="nd-unread-form">
        <input type="hidden" name="notificationID" value="<?= $notif['notificationID'] ?>">
        <button type="submit" class="nd-btn-unread">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Mark as Unread
        </button>
    </form>

</div>
</main>

<style>
.nd-wrapper {
    max-width: 620px;
    margin: 2rem auto;
    padding: 0 1.25rem 3rem;
}
.nd-back {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--text-muted);
    text-decoration: none;
    margin-bottom: 1.5rem;
}
.nd-back svg   { width: 16px; height: 16px; }
.nd-back:hover { color: var(--green-dark); }
.nd-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 1.75rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
    margin-bottom: 1rem;
}
.nd-status-row  { margin-bottom: 0.85rem; }
.nd-badge {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 20px;
    padding: 0.15rem 0.65rem;
}
.nd-badge--read {
    background: #e8f5e0;
    color: var(--green-dark);
    border: 1px solid #b6dda0;
}
.nd-title {
    font-size: clamp(1.2rem, 3vw, 1.6rem);
    font-weight: 800;
    color: var(--green-dark);
    margin: 0 0 0.85rem;
}
.nd-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}
.nd-meta-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.82rem;
    color: var(--text-muted);
}
.nd-meta-item svg { width: 14px; height: 14px; color: var(--teal); }
.nd-divider {
    border: none;
    border-top: 1px solid #e5e7eb;
    margin: 0.75rem 0 1.25rem;
}
.nd-message {
    font-size: 0.97rem;
    color: var(--text-dark);
    line-height: 1.7;
    margin: 0;
}
.nd-unread-form { margin-top: 0.5rem; }
.nd-btn-unread {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: none;
    border: 1.5px solid #d1d5db;
    border-radius: 10px;
    padding: 0.6rem 1.1rem;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--text-muted);
    cursor: pointer;
    transition: border-color 0.18s, color 0.18s, background 0.18s;
}
.nd-btn-unread svg   { width: 16px; height: 16px; }
.nd-btn-unread:hover {
    border-color: var(--green-dark);
    color: var(--green-dark);
    background: #f3f7f0;
}
</style>

<?php include __DIR__ . '/footer.php'; ?>