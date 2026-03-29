<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "techcycle_database");

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit();
}

// Get form data
$userName = $_POST['user-name'] ?? '';
$userEmail = $_POST['user-email'] ?? '';
$userPhone = $_POST['user-phone'] ?? '';
$itemName = $_POST['item-name'] ?? '';
$itemDescription = $_POST['item-description'] ?? '';
$itemCategory = $_POST['item-category'] ?? '';
$condition = $_POST['condition'] ?? '';
$dropoffDate = $_POST['dropoff-date'] ?? '';

// Validation
if (
    empty($userName) || empty($userEmail) || empty($userPhone) ||
    empty($itemName) || empty($itemDescription) ||
    empty($itemCategory) || empty($condition) || empty($dropoffDate)
) {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required"
    ]);
    exit();
}

// Date validation
if (strtotime($dropoffDate) < strtotime(date('Y-m-d'))) {
    echo json_encode([
        "status" => "error",
        "message" => "Drop-off date cannot be in the past"
    ]);
    exit();
}

// Check if user exists
$checkUser = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkUser->bind_param("s", $userEmail);
$checkUser->execute();
$result = $checkUser->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userId = $row['id'];
} else {

    $defaultPassword = password_hash("temp123", PASSWORD_DEFAULT);

    $insertUser = $conn->prepare("
        INSERT INTO users (username, email, password, phonenumber)
        VALUES (?, ?, ?, ?)
    ");

    $insertUser->bind_param("ssss", $userName, $userEmail, $defaultPassword, $userPhone);

    if (!$insertUser->execute()) {
        echo json_encode([
            "status" => "error",
            "message" => "Error creating user: " . $insertUser->error
        ]);
        exit();
    }

    $userId = $insertUser->insert_id;
    $insertUser->close();
}

$checkUser->close();

$stmt = $conn->prepare("
    INSERT INTO ewaste_donations
    (user_id, item_name, item_description, item_category, item_condition, dropoff_date, status)
    VALUES (?, ?, ?, ?, ?, ?, 'Pending')
");

$stmt->bind_param(
    "isssss",
    $userId,
    $itemName,
    $itemDescription,
    $itemCategory,
    $condition,
    $dropoffDate
);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Submission saved successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Error saving data: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
