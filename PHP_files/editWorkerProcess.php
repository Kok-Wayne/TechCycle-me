<?php
include __DIR__ . '/dbConnection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to edit a worker.");
}

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

// Validate required fields (match your form)
if (!isset($_POST['user_id'], $_POST['username'], $_POST['email'], $_POST['phonenumber'])) {
    die("Missing required fields.");
}

// Get form data
$worker_id = intval($_POST['user_id']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$phonenumber = trim($_POST['phonenumber']);

// Update worker in users table
$stmt = $conn->prepare("
    UPDATE users 
    SET username = ?, email = ?, phonenumber = ?
    WHERE id = ? AND role = 'worker'
");

$stmt->bind_param("sssi", $username, $email, $phonenumber, $worker_id);

if ($stmt->execute()) {
    // Redirect after success
    header("Location: ../UI_screens/manageWorkers.php?id=" . $worker_id);
    exit;
} else {
    echo "Error updating worker: " . $conn->error;
}

$stmt->close();
$conn->close();
?>