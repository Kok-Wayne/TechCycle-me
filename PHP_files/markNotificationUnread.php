<?php
session_start();
include 'dbConnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../UI_screens/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../UI_screens/viewUserNotificationsPage.php");
    exit;
}

$userID  = (int) $_SESSION['user_id'];
$notifID = (int) ($_POST['notificationID'] ?? 0);

if ($notifID <= 0) {
    header("Location: ../UI_screens/viewUserNotificationsPage.php");
    exit;
}

// ── Security: only allow marking YOUR OWN notification ────────
$check = $conn->prepare(
    "SELECT notificationID FROM notifications
     WHERE notificationID = ? AND receiverID = ?"
);
$check->bind_param('ii', $notifID, $userID);
$check->execute();
$exists = $check->get_result()->num_rows > 0;
$check->close();

if (!$exists) {
    header("Location: ../UI_screens/viewUserNotificationsPage.php");
    exit;
}

// ── Set isRead = 0 ────────────────────────────────────────────
$update = $conn->prepare(
    "UPDATE notifications SET isRead = 0
     WHERE notificationID = ? AND receiverID = ?"
);
$update->bind_param('ii', $notifID, $userID);
$update->execute();
$update->close();
$conn->close();

// Redirect to list — green dot will reappear
header("Location: ../UI_screens/viewUserNotificationsPage.php");
exit;
?>