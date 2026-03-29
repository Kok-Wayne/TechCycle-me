<?php

// ============================================================
// sendWorkerNotification.php  —  Process 3: Send Notification
// ============================================================
// userID          = admin/recycling centre (SENDER)
// triggeredUserID = worker (RECEIVER)
// ============================================================

session_start();
include __DIR__ . '/dbConnection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$workerID       = intval($input['workerID']      ?? 0);
$productID      = trim($input['productID']       ?? '');
$pickupLocation = trim($input['pickupLocation']  ?? '');
$pickupDatetime = trim($input['pickupDatetime']  ?? '');
$senderUserID   = (int) $_SESSION['user_id'];

// Validate
$errors = [];
if (empty($pickupLocation))  $errors[] = 'Pickup location is required.';
if (empty($pickupDatetime))  $errors[] = 'Pickup datetime is required.';
if ($workerID <= 0)          $errors[] = 'A valid worker must be assigned.';
if (empty($productID))       $errors[] = 'Product ID is required.';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

if (strlen($pickupLocation) < 3) {
    echo json_encode(['success' => false, 'message' => 'Invalid location.']);
    exit;
}

// Verify worker exists
$workerCheck = $conn->prepare("SELECT id, username FROM users WHERE id = ? AND role = 'worker'");
$workerCheck->bind_param('i', $workerID);
$workerCheck->execute();
$workerRow = $workerCheck->get_result()->fetch_assoc();
$workerCheck->close();

if (!$workerRow) {
    echo json_encode(['success' => false, 'message' => 'Worker not found.']);
    exit;
}

// Generate message
$formattedDatetime = date('d M Y, h:i A', strtotime($pickupDatetime));
$message = "You have been assigned a new e-waste pickup. "
         . "Location: {$pickupLocation}. "
         . "Scheduled: {$formattedDatetime}. "
         . "Product ID: {$productID}.";

// Insert notification
$notifID = 'WN-' . uniqid();

$insert = $conn->prepare(
    "INSERT INTO notifications 
     (notificationID, userID, triggeredUserID, productID, message, type, isRead, createdAt)
     VALUES (?, ?, ?, ?, ?, 'worker', 0, NOW())"
);
$insert->bind_param('siiss', $notifID, $senderUserID, $workerID, $productID, $message);

if ($insert->execute()) {
    $insert->close();
    $conn->close();
    echo json_encode(['success' => true, 'message' => 'Worker notification sent.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed: ' . $insert->error]);
}
?>