<?php
include __DIR__ . '/nav.php';
$workers = $conn->query("
    SELECT * FROM users 
    WHERE role = 'worker'
");
?>

<!-- Manage/Add more workers Section -->
<section class="hero product-marketplace">
    <h2>Worker Management</h2>
    <p>Manage and maintain worker accounts.</p>
    <a href="createWorkerAcc.php" class="button primary">Create Worker Account</a>
</section>

<section class="card">
    <h2>Manage Workers</h2>

    <div class="grid">
        <?php while ($worker = $workers->fetch_assoc()): ?>

            <div class="card">
                <h3><?= htmlspecialchars($worker['username']) ?></h3>
                <p class="muted"><?= htmlspecialchars($worker['email']) ?></p>
                <p class="muted"><?= htmlspecialchars($worker['phonenumber']) ?></p>

                <!-- BUTTON GROUP -->
                <div class="button-group">
                    <a href="editWorker.php?id=<?= $worker['id'] ?>" class="button primary">Edit</a>
                    <a href="deleteWorker.php?id=<?= $worker['id'] ?>" class="button ghost">Remove</a>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
</section>

<?php include __DIR__ . '/footer.php' ?>