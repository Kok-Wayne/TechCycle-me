<?php
// ============================================================
// sendCollectionNotification.php  —  Process 4: Collected Notification
// ============================================================
// userID          = worker (SENDER)
// triggeredUserID = user (RECEIVER)
// ============================================================

session_start();
include __DIR__ . '/dbConnection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'worker') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Only workers can confirm collections.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$productID      = trim($input['productID']    ?? '');
$ewasteStatus   = trim($input['ewasteStatus'] ?? '');
$workerID       = (int) $_SESSION['user_id']; // sender

$errors = [];
if (empty($productID))     $errors[] = 'Product ID is missing.';
if (empty($ewasteStatus))  $errors[] = 'E-waste status is required.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors), 'reconfirm' => true]);
    exit;
}

// Verify product and get owner
$verifyStmt = $conn->prepare(
    "SELECT pr.userID AS ownerUserID, p.productName
     FROM productrecords pr
     JOIN products p ON p.productID = pr.productID
     WHERE pr.productID = ?
     LIMIT 1"
);
$verifyStmt->bind_param('s', $productID);
$verifyStmt->execute();
$record = $verifyStmt->get_result()->fetch_assoc();
$verifyStmt->close();

if (!$record) {
    echo json_encode(['success' => false, 'message' => 'Product record not found.', 'reconfirm' => true]);
    exit;
}

$ownerUserID = $record['ownerUserID'];
$productName = $record['productName'];

// Update product status
$newStatus = strtolower($ewasteStatus);
$prodUpdate = $conn->prepare("UPDATE products SET status = ? WHERE productID = ?");
$prodUpdate->bind_param('ss', $newStatus, $productID);
$prodUpdate->execute();
$prodUpdate->close();

// Generate message
$message = "Your e-waste item \"{$productName}\" has been {$ewasteStatus} by our worker. "
         . "Current status: {$ewasteStatus}. "
         . "Thank you for contributing to sustainable recycling!";

// Insert notification
// userID = worker (sender)
// triggeredUserID = user (receiver)
$notifID = 'CN-' . uniqid();

$insert = $conn->prepare(
    "INSERT INTO notifications
     (notificationID, userID, triggeredUserID, productID, message, type, isRead, createdAt)
     VALUES (?, ?, ?, ?, ?, 'user', 0, NOW())"
);
$insert->bind_param('siiss', $notifID, $workerID, $ownerUserID, $productID, $message);

if ($insert->execute()) {
    $insert->close();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'User notification sent.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed: ' . $insert->error]);
}
?>