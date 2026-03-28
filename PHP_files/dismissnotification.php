<?php

session_start();
include 'dbConnection.php';
 
// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$loggedInUserID = $_SESSION['user_id'];
$notificationID = trim($_POST['notificationID'] ?? '');

if (empty($notificationID)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing notificationID']);
    exit;
}

// Security: only allow dismissing YOUR OWN notifications
// userID in notifications = the recipient
$stmt = $conn->prepare("SELECT notificationID FROM notifications WHERE notificationID = ? AND userID = ?");
$stmt->bind_param("ss", $notificationID, $loggedInUserID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Notification not found or not yours']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Mark as read by setting read_at timestamp
$now = date('Y-m-d H:i:s');
$update = $conn->prepare("UPDATE notifications SET read_at = ? WHERE notificationID = ? AND userID = ?");
$update->bind_param("sss", $now, $notificationID, $loggedInUserID);

if ($update->execute()) {
    echo json_encode(['success' => true, 'message' => 'Notification dismissed']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to dismiss notification']);
}

$update->close();
$conn->close();
?>