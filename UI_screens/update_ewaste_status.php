<?php
session_start();
include __DIR__ . '/dbConnection.php';

header("Content-Type: application/json");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// 🔹 Only POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

// 🔹 Receive (DFD 5.1)
$id     = $_POST['submission_id'] ?? '';
$status = trim($_POST['status'] ?? '');

// 🔹 Validate
if (!$id || !$status) {
    echo json_encode(["status" => "error", "message" => "Missing fields"]);
    exit;
}

$allowed = ["Pending", "Collected", "Recycled"];
if (!in_array($status, $allowed)) {
    echo json_encode(["status" => "error", "message" => "Invalid status"]);
    exit;
}

// 🔹 Retrieve (DFD 5.2)
$stmt = $conn->prepare("SELECT status FROM ewaste_submissions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Record not found"]);
    exit;
}

$row = $result->fetch_assoc();

// 🔹 Update (DFD 5.3)
$update = $conn->prepare("UPDATE ewaste_submissions SET status=? WHERE id=?");
$update->bind_param("si", $status, $id);

if ($update->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Status updated successfully",
        "old_status" => $row['status'],
        "new_status" => $status
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Update failed"]);
}

$stmt->close();
$update->close();
$conn->close();
?>