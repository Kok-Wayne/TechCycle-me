<?php
include __DIR__ . '/nav.php';

// Only users can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['user_id'];

// triggeredUserID = user (receiver)
// userID = worker (sender)
$stmt = $conn->prepare(
    "SELECT n.notificationID, n.message, n.type, n.createdAt,
            u.username AS workerName,
            p.productName
     FROM notifications n
     LEFT JOIN users    u ON u.id        = n.userID
     LEFT JOIN products p ON p.productID = n.productID
     WHERE n.triggeredUserID = ? AND n.isRead = 0
     ORDER BY n.createdAt DESC"
);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result     = $stmt->get_result();
$notifCount = $result->num_rows;
$stmt->close();
?>

<main id="content">

    <section class="hero" aria-label="Notifications">
        <div>
            <h1>My Notifications</h1>
            <p class="muted">
                <?php if ($notifCount > 0): ?>
                    You have <?= $notifCount ?> notification<?= $notifCount > 1 ? 's' : '' ?>.
                <?php else: ?>
                    You are all caught up.
                <?php endif; ?>
            </p>
        </div>
    </section>

    <section style="margin-top: 1.5rem;">

        <?php if ($notifCount > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card" style="margin-bottom: 1rem;">

                    <?php if (!empty($row['productName'])): ?>
                        <p style="margin: 0 0 0.25rem; font-weight: 600; color: var(--green-dark);">
                            <?= htmlspecialchars($row['productName']) ?>
                        </p>
                    <?php endif; ?>

                    <p style="margin: 0 0 0.5rem 0;">
                        <?= htmlspecialchars($row['message']) ?>
                    </p>

                    <p class="muted" style="margin: 0 0 0.75rem; font-size: 0.85rem;">
                        <?php if (!empty($row['workerName'])): ?>
                            From: <?= htmlspecialchars($row['workerName']) ?>
                            &nbsp;·&nbsp;
                        <?php endif; ?>
                        <?= htmlspecialchars($row['createdAt']) ?>
                    </p>

                    <form method="POST" action="../PHP_files/dismissnotification.php">
                        <input type="hidden" name="notificationID" value="<?= htmlspecialchars($row['notificationID']) ?>">
                        <input type="hidden" name="redirect" value="viewUserNotificationsPage.php">
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