<?php
include __DIR__ . "/nav.php";

// Check if centreID exists
if (!isset($_GET['centreID'])) {
    die("No recycling centre selected.");
}

$centreID = intval($_GET['centreID']);

// Fetch centre details
$stmt = $conn->prepare("SELECT * FROM recycling_centre WHERE centreID = ?");
$stmt->bind_param("i", $centreID);
$stmt->execute();
$result = $stmt->get_result();
$centre = $result->fetch_assoc();
$stmt->close();

if (!$centre) {
    die("Recycling centre not found.");
}
?>

<main id="content">
    <section class="hero">
        <h1>Donate E-Waste</h1>
        <p>Fill in the details of your item for recycling.</p>
    </section>

    <section class="card">
        <form action="../php_files/donateEwasteProcess.php" method="POST" class="form">

            <!-- LOCKED CENTRE DISPLAY -->
            <label>Selected Recycling Centre</label>
            <input type="text" value="<?php echo htmlspecialchars($centre['centreName']); ?>" disabled
                style="background-color:#eee; color:#555;">
            <input type="hidden" name="centreID" value="<?php echo $centre['centreID']; ?>">
            <input type="hidden" name="userID" value="<?php echo $userID; ?>">

            <!-- PRODUCT INFO -->
            <label for="productName">Product Name</label>
            <input type="text" name="productName" id="productName" required>

            <label for="productDesc">Description</label>
            <textarea name="productDesc" id="productDesc" required></textarea>

            <label for="item-category">Category:</label>
            <select id="item-category" name="productCategory" required>
                <option value="">Select Category</option>
                <option value="computers">Computers & Accessories</option>
                <option value="phones">Mobile Phones & Tablets</option>
                <option value="audio">Audio & Video Equipment</option>
                <option value="appliances">Small Appliances</option>
                <option value="cables & chargers">Cables & Chargers</option>
                <option value="other">Other</option>
            </select>

            <!-- Error Messages -->
            <?php if (isset($_GET['error'])): ?>
                <p style="color: red;">Error submitting item. Please try again.</p>
            <?php endif; ?>

            <button type="submit" class="button primary">Submit E-Waste</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>