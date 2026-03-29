<?php
include __DIR__ . "/nav.php";

if (!isset($_GET['id'])) {
    die("No centre specified.");
}

$centre_id = intval($_GET['id']);

// Fetch centre details
$stmt = $conn->prepare("SELECT * FROM recycling_centre WHERE centreID = ?");
$stmt->bind_param("i", $centre_id);
$stmt->execute();
$result = $stmt->get_result();
$centre = $result->fetch_assoc();
$stmt->close();

if (!$centre) {
    die("Centre not found.");
}
?>

<main id="content">
    <section class="hero">
        <h1>Edit Recycling Centre</h1>
    </section>

    <section class="card">
        <form action="../php_files/editRecyclingCentreProcess.php" method="POST" class="form">

            <input type="hidden" name="centreID" value="<?php echo $centre['centreID']; ?>">

            <label for="centreName">Centre Name</label>
            <input type="text" name="centreName" id="centreName"
                value="<?php echo htmlspecialchars($centre['centreName']); ?>" required>

            <label for="centreAddress">Centre Address</label>
            <textarea name="centreAddress" id="centreAddress" required><?php echo htmlspecialchars($centre['centreAddress']); ?></textarea>

            <label for="centreMapLink">Google Maps Link</label>
            <input type="url" name="centreMapLink" id="centreMapLink"
                value="<?php echo htmlspecialchars($centre['centreMapLink']); ?>">

            <!-- Error handling -->
            <?php if (isset($_GET['error'])): ?>
                <p style="color: red;">Error updating centre. Please try again.</p>
            <?php endif; ?>

            <button type="submit" class="button primary">Update Centre</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>