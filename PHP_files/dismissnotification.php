<?php
session_start();
include 'dbConnection.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../UI_screens/viewUserNotificationsPage.php");
    exit;
}

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../UI_screens/login.php");
    exit;
}

$loggedInUserID = $_SESSION['user_id'];
$notificationID = trim($_POST['notificationID'] ?? '');
$redirect       = trim($_POST['redirect'] ?? 'viewUserNotificationsPage.php');

if (empty($notificationID)) {
    header("Location: ../UI_screens/" . $redirect);
    exit;
}

// Security: only allow dismissing YOUR OWN notifications
$stmt = $conn->prepare("SELECT notificationID FROM notifications WHERE notificationID = ? AND triggeredUserID = ?");
$stmt->bind_param("si", $notificationID, $loggedInUserID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: ../UI_screens/" . $redirect);
    exit;
}
$stmt->close();

// Mark as read
$update = $conn->prepare("UPDATE notifications SET isRead = 1 WHERE notificationID = ? AND triggeredUserID = ?");
$update->bind_param("si", $notificationID, $loggedInUserID);
$update->execute();
$update->close();
$conn->close();

header("Location: ../UI_screens/" . $redirect);
exit;
?>