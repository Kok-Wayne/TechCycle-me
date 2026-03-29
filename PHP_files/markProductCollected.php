<?php
session_start();
include __DIR__ . '/dbConnection.php';

header('Content-Type: application/json');

// ── Auth ─────────────────────────────────────────────────────
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

// ── Fetch product + centre + owner, check status ─────────────
$check = $conn->prepare(
    "SELECT p.pickupStatus, p.productName, p.userID, rc.centreName
     FROM products p
     LEFT JOIN recycling_centre rc ON rc.centreID = p.centreID
     WHERE p.productID = ?"
);
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

$productName = $row['productName'];
$ownerUserID = (int) $row['userID'];
$centreName  = $row['centreName'] ?? 'the recycling centre';

// ── Mark product as collected ─────────────────────────────────
$update = $conn->prepare(
    "UPDATE products
     SET pickupStatus        = 'collected',
         collectedByWorkerID = ?,
         collectedAt         = NOW()
     WHERE productID = ? AND pickupStatus = 'pending'"
);
$update->bind_param('ii', $workerID, $productID);

if (!$update->execute() || $update->affected_rows === 0) {
    $update->close();
    echo json_encode(['success' => false, 'message' => 'Could not update. It may have already been collected.']);
    exit;
}
$update->close();

// ── Insert notification ───────────────────────────────────────
// senderID   = worker   (who collected)
// receiverID = user     (who submitted the product)
$title   = $productName;
$message = "Your item \"{$productName}\" from {$centreName} has been collected.";

$notif = $conn->prepare(
    "INSERT INTO notifications (senderID, receiverID, productID, title, message, isRead, createdAt)
     VALUES (?, ?, ?, ?, ?, 0, NOW())"
);
$notif->bind_param('iiiss', $workerID, $ownerUserID, $productID, $title, $message);

if ($notif->execute()) {
    $notif->close();
    $conn->close();
    echo json_encode(['success' => true]);
} else {
    $err = $notif->error;
    $notif->close();
    $conn->close();
    // Product collected successfully, only notification failed
    echo json_encode(['success' => true, 'warning' => 'Collected but notification failed: ' . $err]);
}
?>