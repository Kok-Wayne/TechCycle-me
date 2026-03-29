<?php
session_start();
include __DIR__ . '/dbConnection.php';

header('Content-Type: application/json');

// Must be logged in as worker
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    echo json_encode(['success' => false, 'message' => 'Unauthorised.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$workerID  = (int) $_SESSION['user_id'];
$productID = (int) ($_POST['productID'] ?? 0);

if ($productID <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
    exit;
}

// Make sure it is still pending (prevent double collect)
$check = $conn->prepare("SELECT pickupStatus FROM products WHERE productID = ?");
$check->bind_param('i', $productID);
$check->execute();
$row = $check->get_result()->fetch_assoc();
$check->close();

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

if ($row['pickupStatus'] === 'collected') {
    echo json_encode(['success' => false, 'message' => 'Already collected.']);
    exit;
}

// Update: mark as collected by this worker right now
$update = $conn->prepare(
    "UPDATE products
     SET pickupStatus = 'collected',
         collectedByWorkerID = ?,
         collectedAt = NOW()
     WHERE productID = ? AND pickupStatus = 'pending'"
);
$update->bind_param('ii', $workerID, $productID);

if ($update->execute() && $update->affected_rows > 0) {
    $update->close();
    $conn->close();
    echo json_encode(['success' => true]);
} else {
    $update->close();
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Could not update. It may have already been collected.']);
}
?>