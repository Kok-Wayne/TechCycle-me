<?php
session_start();
include 'dbConnection.php';
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Fetch POST data
$username = trim($_POST['username'] ?? '');
$phonenumber = trim($_POST['phonenumber'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Basic validation
if (empty($username) || empty($phonenumber)) {
    die("Username and phone number cannot be empty.");
}

// Password validation
if (!empty($password) && $password !== $confirm_password) {
    die("Passwords do not match.");
}

// Handle profile image upload
$profile_image_path = null;
if (!empty($_FILES['new-image']['name'])) {
    $file = $_FILES['new-image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
    if (!in_array($file['type'], $allowed_types)) {
        die("Only JPG, PNG, and GIF images are allowed.");
    }
    
    if ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
        die("File size must be less than 2MB.");
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = 'Uploads/profile_' . $user_id . '_' . time() . '.' . $ext;
    
    if (!move_uploaded_file($file['tmp_name'], '../' . $new_filename)) {
        die("Failed to upload image.");
    }
    
    $profile_image_path = $new_filename;
}

// Update database
$fields = "username = ?, phonenumber = ?";
$params = [$username, $phonenumber];
$types = "ss";

if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $fields .= ", password = ?";
    $params[] = $hashed_password;
    $types .= "s";
}

if ($profile_image_path) {
    $fields .= ", profile_image = ?";
    $params[] = $profile_image_path;
    $types .= "s";
}

$stmt = $conn->prepare("UPDATE users SET $fields WHERE id = ?");
$params[] = $user_id;
$types .= "i";

// Bind parameters dynamically
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    $stmt->close();

    // Update session values immediately
    $_SESSION['username'] = $username;
    $_SESSION['phonenumber'] = $phonenumber;

    if ($profile_image_path) {
        $_SESSION['profile_image'] = $profile_image_path;
    }

    $_SESSION['success_message'] = "Profile updated successfully!";
    header("Location: ../UI_screens/profileSettingsScreen.php");
    exit();
} else {
    die("Error updating profile: " . $conn->error);
}

if ($profile_image_path) {
    $_SESSION['profile_image'] = $profile_image_path;
}
?>