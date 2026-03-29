<?php
include 'dbConnection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to delete a worker.");
}

// Optional: Only admins can delete workers
if ($_SESSION['role'] !== 'admin') {
    die("You are not authorized to delete workers.");
}

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

// Validate POST data
if (!isset($_POST['user_id'])) {
    die("Missing worker ID.");
}

$worker_id = intval($_POST['user_id']);

// Check if worker exists and is a worker
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'worker'");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();
$stmt->close();

if (!$worker) {
    die("Worker not found or already deleted.");
}

// Delete worker
$stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'worker'");
$stmt->bind_param("i", $worker_id);

if ($stmt->execute()) {
    // Redirect to worker list with success message
    header("Location: ../UI_screens/manageWorkers.php?success=deleted");
    exit;
} else {
    echo "Error deleting worker: " . $conn->error;
}

$stmt->close();
$conn->close();
?>