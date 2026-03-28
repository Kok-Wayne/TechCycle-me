<?php
include __DIR__ . '/nav.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT n.notificationID, n.message, n.type, n.createdAt,
            u.username AS senderName
     FROM notifications n
     LEFT JOIN users u ON u.id = n.userID
     WHERE n.triggeredUserID = ? AND n.isRead = 0
     ORDER BY n.createdAt DESC"
);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result     = $stmt->get_result();
$notifCount = $result->num_rows;
$stmt->close();
?>

<style>
    body { min-height: 100vh; display: flex; flex-direction: column; }
    main { flex: 1; }
</style>

<main id="content">

    <section class="hero" aria-label="Notifications">
        <div>
            <h1>My Notifications</h1>
            <?php if ($notifCount > 0): ?>
                <p class="muted">You have <?= $notifCount ?> notification<?= $notifCount > 1 ? 's' : '' ?>.</p>
            <?php endif; ?>
        </div>
    </section>

    <section style="margin-top: 1.5rem;">

        <?php if ($notifCount > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card" style="margin-bottom: 1rem;">

                    <?php if (!empty($row['senderName'])): ?>
                        <p class="muted" style="margin: 0 0 0.25rem; font-size: 0.85rem;">
                            From: <?= htmlspecialchars($row['senderName']) ?>
                            &nbsp;·&nbsp;
                            <?= htmlspecialchars($row['createdAt']) ?>
                        </p>
                    <?php endif; ?>

                    <p style="margin: 0 0 0.75rem 0;">
                        <?= htmlspecialchars($row['message']) ?>
                    </p>

                    <form method="POST" action="../PHP_files/dismissnotification.php">
                        <input type="hidden" name="notificationID" value="<?= htmlspecialchars($row['notificationID']) ?>">
                        <input type="hidden" name="redirect" value="viewWorkerNotificationsPage.php">
                        <button type="submit" class="button ghost">Dismiss</button>
                    </form>

                </div>
            <?php endwhile; ?>

        <?php else: ?>

            <div class="card">
                <p class="muted">No new notifications.</p>
            </div>

        <?php endif; ?>

    </section>

</main>

<?php include __DIR__ . '/footer.php'; ?>