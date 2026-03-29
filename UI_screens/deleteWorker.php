<?php
include __DIR__ . '/../php_files/dbConnection.php';

$worker_id = intval($_GET['id']);

// Fetch worker info (only workers)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'worker'");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();
$stmt->close();

if (!$worker) {
    die("Worker not found.");
}
?>

<?php include __DIR__ ."/nav.php"; ?>

<main id="content">
    <section class="hero">
        <h1>Delete Worker</h1>
    </section>

    <section class="card">
        <form action="../php_files/deleteWorkerProcess.php" method="POST" class="form">
            <!-- Hidden ID for backend -->
            <input type="hidden" name="user_id" value="<?php echo $worker['id']; ?>">

            <label for="worker-username">Worker Name</label>
            <input type="text" id="worker-username" value="<?php echo htmlspecialchars($worker['username']); ?>"
                disabled style="background-color: #eee; color: #555;">

            <label for="worker-email">Worker Email</label>
            <input type="email" id="worker-email" value="<?php echo htmlspecialchars($worker['email']); ?>"
                disabled style="background-color: #eee; color: #555;">

            <label for="worker-phone">Contact Number</label>
            <input type="text" id="worker-phone" value="<?php echo htmlspecialchars($worker['phonenumber']); ?>"
                disabled style="background-color: #eee; color: #555;">

            <!-- Confirm delete button -->
            <button type="submit" class="button danger">Delete Worker</button>
        </form>
    </section>
</main>

<?php include __DIR__ . '/footer.php'; ?>