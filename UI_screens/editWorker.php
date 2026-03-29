<?php 
include __DIR__ . '/../php_files/dbConnection.php';
include __DIR__ . "/nav.php";

if (!isset($_GET['id'])) {
    die("No worker found.");
}

$user_id = intval($_GET['id']);

// Fetch worker details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'worker'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();
$stmt->close();

if (!$worker) {
    die("Worker not found.");
}
?>

<main id="content">
    <section class="hero">
        <h1>Edit Worker Details</h1>
    </section>

    <section class="card">
        <form action="../php_files/editWorkerProcess.php" method="POST" class="form">

            <!-- Hidden ID -->
            <input type="hidden" name="user_id" value="<?php echo $worker['id']; ?>">

            <!-- Username -->
            <label for="name">Worker Name</label>
            <input type="text" name="username" id="name"
                value="<?php echo htmlspecialchars($worker['username']); ?>" required>

            <!-- Email -->
            <label for="email">Worker Email</label>
            <input type="email" name="email" id="email"
                value="<?php echo htmlspecialchars($worker['email']); ?>" required>

            <!-- Phone -->
            <label for="phonenumber">Worker Contact Number</label>
            <input type="text" name="phonenumber" id="phonenumber"
                value="<?php echo htmlspecialchars($worker['phonenumber']); ?>" required>

            <!-- Error Messages -->
            <?php if (isset($_GET['error'])): ?>
                <p style="color: red;">
                    <?php
                    if ($_GET['error'] === 'error_saving') {
                        echo "Error saving worker details. Please try again.";
                    }
                    ?>
                </p>
            <?php endif; ?>

            <button type="submit" class="button primary">Update Worker</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>