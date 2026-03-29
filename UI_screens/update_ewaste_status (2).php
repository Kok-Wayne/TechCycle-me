<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "techcycle_database");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

// Get data
$ewaste_id = intval($_POST['ewaste_id']);
$status    = trim($_POST['status']);

// Validate
$allowed = ["Pending", "Collected", "Recycled"];

if (empty($ewaste_id) || !in_array($status, $allowed)) {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
    exit;
}

// Update query
$stmt = $conn->prepare("UPDATE ewaste_donations SET status=? WHERE ewaste_id=?");
$stmt->bind_param("si", $status, $ewaste_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Status updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update failed"]);
}

$stmt->close();
$conn->close();
?>