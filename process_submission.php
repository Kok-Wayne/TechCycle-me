<?php
header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if (strtotime($dropoffDate) < time()) {
    sendResponse('error', 'Drop-off date cannot be in the past.');
}

// 3. Database Credentials
$dbHost     = 'localhost'; 
$dbUsername = 'root';      
$dbPassword = '';          
$dbName     = 'techcycle_db'; 

// 4. Email Configuration (for sending notifications)
$adminEmail = 'your-admin-email@example.com'; 
$siteName   = 'TechCycle';

// ** Function to send JSON response **
function sendResponse($status, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
}

$itemName        = filter_input(INPUT_POST, 'item-name', FILTER_SANITIZE_STRING);
$itemDescription = filter_input(INPUT_POST, 'item-description', FILTER_SANITIZE_STRING);
$itemCategory    = filter_input(INPUT_POST, 'item-category', FILTER_SANITIZE_STRING);
$dropoffDate     = filter_input(INPUT_POST, 'dropoff-date', FILTER_SANITIZE_STRING);


// Check if required fields are present and not empty after sanitization
if (empty($itemName) || empty($itemDescription) || empty($itemCategory) || empty($dropoffDate)) {
    sendResponse('error', 'All fields are required.');
}


if (strtotime($dropoffDate) < time()) {
    sendResponse('error', 'Drop-off date cannot be in the past.');
}
$dropoffDate = filter_input(INPUT_POST, 'dropoff_date', FILTER_SANITIZE_STRING);


$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    sendResponse('error', 'Database connection failed. Please try again later.');
}

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO ewaste_submissions (item_name, item-description, category, dropoff_date, submission_time) VALUES (?, ?, ?, ?, NOW())");

if ($stmt === false) {
    error_log("SQL prepare failed: " . $conn->error);
    sendResponse('error', 'Server error preparing data. Please try again.');
}

$stmt->bind_param('ssss', $itemName, $itemDescription, $itemCategory, $dropoffDate);

if ($stmt->execute()) {
    $submissionId = $conn->insert_id; // Get the ID of the newly inserted row

    // ** Send Email Notification (Admin) **
    $subjectAdmin = "New E-waste Submission Received - " . $siteName;
    $messageAdmin = "A new e-waste item has been submitted:\n\n";
    $messageAdmin .= "Item Name: " . htmlspecialchars($itemName) . "\n";
    $messageAdmin .= "Description: " . htmlspecialchars($itemDescription) . "\n";
    $messageAdmin .= "Category: " . htmlspecialchars($itemCategory) . "\n";
    $messageAdmin .= "Estimated Drop-off Date: " . htmlspecialchars($dropoffDate) . "\n";
    $messageAdmin .= "Submission ID: " . $submissionId . "\n";
    $messageAdmin .= "Submitted on: " . date('Y-m-d H:i:s') . "\n";

    $headersAdmin = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headersAdmin .= "Reply-To: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headersAdmin .= "X-Mailer: PHP/" . phpversion();


    if (!mail($adminEmail, $subjectAdmin, $messageAdmin, $headersAdmin)) {
        error_log("Failed to send admin email for submission ID: " . $submissionId);
    }


    $userEmail = filter_input(INPUT_POST, 'user-email', FILTER_SANITIZE_EMAIL);
    if ($userEmail && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        $subjectUser = "Your E-waste Submission Confirmation - " . $siteName;
        $messageUser = "Thank you for submitting your e-waste item to " . $siteName . "!\n\n";
        $messageUser .= "Item Name: " . htmlspecialchars($itemName) . "\n";
        $messageUser .= "We have received your submission and look forward to your drop-off.\n\n";
        $messageUser .= "Please bring your item to our center within 7 days.\n";

        $headersUser = "From: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headersUser .= "Reply-To: no-reply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headersUser .= "X-Mailer: PHP/" . phpversion();

        mail($userEmail, $subjectUser, $messageUser, $headersUser);
    }
    
    sendResponse('success', 'Submission recorded successfully!');

    $stmt->close();
    $conn->close();

} else {
    // Log this error on the server for debugging!
    error_log("SQL execution failed: " . $stmt->error);
    sendResponse('error', 'Failed to save submission. Please try again.');
    $stmt->close();
    $conn->close();
}
?>
