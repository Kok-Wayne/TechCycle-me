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

// 🔹 Receive inputs (DFD 2.1)
$itemName = trim($_POST['item_name'] ?? '');
$category = trim($_POST['category'] ?? '');
$location = trim($_POST['location'] ?? '');
$dropoff  = $_POST['dropoff_date'] ?? '';
$desc     = trim($_POST['item_description'] ?? '');

// 🔹 Validate (DFD 2.2)
if (!$itemName || !$category || !$location || !$dropoff || !$desc) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

if (strtotime($dropoff) < strtotime(date("Y-m-d"))) {
    echo json_encode(["status" => "error", "message" => "Invalid drop-off date"]);
    exit;
}

// 🔹 Validate category (D1)
$check = $conn->prepare("SELECT id FROM ewaste_categories WHERE category_name = ?");
$check->bind_param("s", $category);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid category"]);
    exit;
}

// 🔹 Store (DFD 2.3, D2)
$stmt = $conn->prepare("
    INSERT INTO ewaste_submissions
    (item_name, description, category, location, estimated_dropoff_date, status, submission_time)
    VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
");

$stmt->bind_param("sssss", $itemName, $desc, $category, $location, $dropoff);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Location submitted successfully",
        "submission_id" => $conn->insert_id
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Database insert failed"]);
}

$stmt->close();
$conn->close();
?>