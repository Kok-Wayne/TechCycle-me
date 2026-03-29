<?php
include __DIR__ . '/../php_files/dbConnection.php';

$centre_id = intval($_GET['id']);

// Fetch centre info
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

<?php include __DIR__ . "/nav.php"; ?>

<main id="content">
    <section class="hero">
        <h1>Delete Recycling Centre</h1>
    </section>

    <section class="card">
        <form action="../php_files/deleteRecyclingCentreProcess.php" method="POST" class="form">
            <!-- Hidden ID -->
            <input type="hidden" name="centreID" value="<?php echo $centre['centreID']; ?>">

            <label>Centre Name</label>
            <input type="text" value="<?php echo htmlspecialchars($centre['centreName']); ?>"
                disabled style="background-color: #eee; color: #555;">

            <label>Centre Address</label>
            <textarea disabled style="background-color: #eee; color: #555;"><?php echo htmlspecialchars($centre['centreAddress']); ?></textarea>

            <label>Google Maps Link</label>
            <input type="text" value="<?php echo htmlspecialchars($centre['centreMapLink']); ?>"
                disabled style="background-color: #eee; color: #555;">

            <!-- Delete button -->
            <button type="submit" class="button danger">Delete Centre</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>