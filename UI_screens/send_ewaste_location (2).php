<?php
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "root", "", "techcycle_database");

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Only POST allowed
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

// Get form data safely
$user_id        = intval($_POST['user_id']);
$item_name      = trim($_POST['item_name']);
$item_desc      = trim($_POST['item_description']);
$item_category  = trim($_POST['item_category']);
$item_condition = trim($_POST['item_condition']);
$dropoff_date   = $_POST['dropoff_date'];

// Validation
if (empty($user_id) || empty($item_name) || empty($item_desc) || empty($item_category) || empty($item_condition) || empty($dropoff_date)) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

// Prevent past dates
if (strtotime($dropoff_date) < strtotime(date("Y-m-d"))) {
    echo json_encode(["status" => "error", "message" => "Drop-off date cannot be in the past"]);
    exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO ewaste_donations 
(user_id, item_name, item_description, item_category, item_condition, dropoff_date, status) 
VALUES (?, ?, ?, ?, ?, ?, 'Pending')");

$stmt->bind_param("isssss", $user_id, $item_name, $item_desc, $item_category, $item_condition, $dropoff_date);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "E-waste location submitted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to submit"]);
}

$stmt->close();
$conn->close();
?>