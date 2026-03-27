<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$conn = new mysqli("localhost", "root", "", "techcycle_database");

if ($conn->connect_error) {
    die("Database connection failed");
}

// Get data
$userName = $_POST['user-name'] ?? '';
$userEmail = $_POST['user-email'] ?? '';
$userPhone = $_POST['user-phone'] ?? '';
$itemName = $_POST['item-name'] ?? '';
$itemDescription = $_POST['item-description'] ?? '';
$itemCategory = $_POST['item-category'] ?? '';
$condition = $_POST['condition'] ?? '';
$dropoffDate = $_POST['dropoff-date'] ?? '';

// 1.2 VALIDATION
if (
    empty($userName) || empty($userEmail) || empty($userPhone) ||
    empty($itemName) || empty($itemDescription) ||
    empty($itemCategory) || empty($condition) || empty($dropoffDate)
) {
    die("All fields are required");
}

// Validate date
if (strtotime($dropoffDate) < time()) {
    die("Drop-off date cannot be in the past");
}

// 1.3 RECORD DATA
$stmt = $conn->prepare("INSERT INTO ewaste_donations 
(user_name, user_email, user_phone, item_name, item_description, item_category, item_condition, dropoff_date, status) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");

$stmt->bind_param(
    "ssssssss",
    $userName,
    $userEmail,
    $userPhone,
    $itemName,
    $itemDescription,
    $itemCategory,
    $condition,
    $dropoffDate
);

if ($stmt->execute()) {
    // 1.4 REDIRECT TO CONFIRMATION
    header("Location: thank_you.php");
    exit();
} else {
    echo "Error saving data";
}

$stmt->close();
$conn->close();
?>