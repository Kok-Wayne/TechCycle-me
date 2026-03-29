<?php
include __DIR__ . '/nav.php';

$centres = $conn->query("
    SELECT * FROM recycling_centre
");
?>

<!-- Manage/Add Recycling Centres Section -->
<section class="hero product-marketplace">
    <h2>Recycling Centre Management</h2>
    <p>Manage and maintain recycling centres.</p>
    <a href="createRecyclingCentre.php" class="button primary">Add Recycling Centre</a>
</section>

<section class="card">
    <h2>Manage Recycling Centres</h2>

    <div class="grid">
        <?php while ($centre = $centres->fetch_assoc()): ?>

            <div class="card">
                <h3><?= htmlspecialchars($centre['centreName']) ?></h3>
                <p class="muted"><?= htmlspecialchars($centre['centreAddress']) ?></p>

                <a href="<?= htmlspecialchars($centre['centreMapLink']) ?>" target="_blank" class="button primary">
                    Open in Google Maps
                </a>

                <div class="button-group">
                    <a href="editRecyclingCentre.php?id=<?= $centre['centreID'] ?>" class="button primary">Edit</a>
                    <a href="deleteRecyclingCentre.php?id=<?= $centre['centreID'] ?>" class="button ghost">Remove</a>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>