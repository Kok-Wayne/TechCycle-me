<?php
include __DIR__ . '/nav.php';
$centres = $conn->query("
SELECT * FROM recycling_centre
");
?>
<!-- Drop-off Recycling Centres Section -->
<section class="hero product-marketplace">
    <h2>Recycling Centre Selection</h2>
    <p>Choose a convenient location to drop off your e-waste.</p>
</section>
<section class="card">
    <h2>Drop-off at Recycling Centres</h2>
    <div class="grid">
        <?php while ($centre = $centres->fetch_assoc()): ?>
            <div class="card">
                <h3><?= htmlspecialchars($centre['centreName']) ?></h3>
                <p class="muted"><?= htmlspecialchars($centre['centreAddress']) ?></p>
                <a href="<?= htmlspecialchars($centre['centreMapLink']) ?>" target="_blank" class="button primary">
                    Open in Google Maps
                </a>
                <div class="button-group">
                    <a href="donateEwaste.php?centreID=<?= $centre['centreID'] ?>" class="button primary">
                        Choose drop-off
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>