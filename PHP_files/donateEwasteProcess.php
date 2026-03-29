<?php
include __DIR__ . '/dbConnection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to donate e-waste.");
}

$userID = $_SESSION['user_id'];

// Check required POST fields
if (!isset($_POST['productName'], $_POST['productDesc'], $_POST['productCategory'], $_POST['centreID'])) {
    die("Missing required fields.");
}

// Sanitize input
$productName = $_POST['productName'];
$productDesc = $_POST['productDesc'];
$productCategory = $_POST['productCategory'];
$centreID = intval($_POST['centreID']);


// Insert product into products table
$stmt = $conn->prepare("
    INSERT INTO products 
    (productName, productDesc, productCategory, centreID, userID, pickupStatus, marketStatus)
    VALUES (?, ?, ?, ?, ?, 'pending', 'unlisted')
");

$stmt->bind_param("sssii", $productName, $productDesc, $productCategory, $centreID, $userID);

if (!$stmt->execute()) {
    header("Location: ../UI_screens/donateEwaste.php?centreID=$centreID&error=failed");
    exit();
}
$stmt->close();


// Award 5 points to the user
// Check if user already has a points record
$stmt = $conn->prepare("SELECT points FROM user_points WHERE userID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update points
    $stmt = $conn->prepare("UPDATE user_points SET points = points + 5 WHERE userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
} else {
    // Insert new points row
    $stmt = $conn->prepare("INSERT INTO user_points (userID, points) VALUES (?, 5)");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
}

$stmt->close();

// Redirect back 
header("Location: ../UI_screens/dropoffRecyclingCentreSelection.php?success=submitted");
exit();