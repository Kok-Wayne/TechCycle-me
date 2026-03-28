<?php
include __DIR__ . '/nav.php';

// Only workers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header("Location: login.php");
    exit(); 
}

$userID = $_SESSION['user_id'];

// Fetch notifications for this worker
$stmt = $conn->prepare("SELECT * FROM notifications WHERE userID = ?");
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();
$notifCount = $result->num_rows;
?>

<main id="content">

    <!-- Page Header -->
    <section class="hero" aria-label="Notifications">
        <div>
            <h1>Worker Notifications</h1>
            <p class="muted">
                <?php if ($notifCount > 0): ?>
                    You have <?= $notifCount ?> notification<?= $notifCount > 1 ? 's' : '' ?>.
                <?php else: ?>
                    You are all caught up.
                <?php endif; ?>
            </p>
        </div>
    </section>

    <!-- Notifications List -->
    <section style="margin-top: 1.5rem;">

        <?php if ($notifCount > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card" style="margin-bottom: 1rem;">

                    <p style="margin: 0 0 0.75rem 0;">
                        <?= htmlspecialchars($row['message']) ?>
                    </p>

                    <!-- Dismiss button -->
                    <form method="POST" action="../php_files/dismissNotification.php">
                        <input type="hidden" name="notificationID" value="<?= htmlspecialchars($row['notificationID']) ?>">
                        <input type="hidden" name="redirect" value="viewWorkerNotificationsPage.php">
                        <button type="submit" class="button ghost">Dismiss</button>
                    </form>

                </div>
            <?php endwhile; ?>

        <?php else: ?>

            <div class="card">
                <p class="muted">No notifications to show.</p>
            </div>

        <?php endif; ?>

    </section>

</main>

<?php include __DIR__ . '/footer.php'; ?>