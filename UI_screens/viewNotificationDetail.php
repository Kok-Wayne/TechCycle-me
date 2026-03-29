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

<div class="notif-page">
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

                <h1 class="nd-title"><?= htmlspecialchars($notif['title']) ?></h1>

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
</div>

<?php include __DIR__ . '/footer.php'; ?>