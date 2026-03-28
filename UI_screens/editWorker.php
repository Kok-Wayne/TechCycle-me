<?php
include 'php_files/dbConnection.php';
include __DIR__ ."/nav.php";

if (!isset($_GET['id'])) {
    die("No product specified.");
}

$user_id = intval($_GET['id']);

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Worker not found.");
}
?>

    <main id="content">
        <section class="hero">
            <h1>Edit the Product Details</h1>
        </section>

        <section class="card">
            <form action="php_files/editProductProcess.php" method="POST" class="form">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                <label for="name">Worker Name</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>"
                    required>

                <label for="description">Worker Email</label>
                <input name="description" id="description" value="<?php echo htmlspecialchars($product['description']); ?>" required>
    
                <label for="contact">Worker Contact Number</label>
                <input type="email" name="email" id="contact" value="<?php echo htmlspecialchars($product['email']); ?>"
                    required>

                <label>Current Image:</label><br>
                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" alt="Current Product Image"><br>

                <label for="new-image">Upload New Image (optional)</label>
                <input type="file" name="new-image" id="new-image" accept=".jpg,.jpeg,.png,.gif">

                <?php if (isset($_GET['error']) && $_GET['error'] === 'error_saving_product'): ?>
                    <p style="color: red;">Error saving product. Please try again.</p>
                <?php endif; ?>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'error_uploading_image'): ?>
                    <p style="color: red;">Error uploading image. Please try again.</p>
                <?php endif; ?>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_file_type'): ?>
                    <p style="color: red;">Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.</p>
                <?php endif; ?>

                <button type="submit" class="button primary">Update Listing</button>
            </form>
        </section>
    </main>
    <?php include __DIR__ . '/commonUsed_files/footer.php'; ?>