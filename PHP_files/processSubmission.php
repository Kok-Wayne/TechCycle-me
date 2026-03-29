<?php
include __DIR__ . '/dbConnection.php'; // Include your database connection file

// ** Function to send JSON response **
function sendResponse($status, $message = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit;
}

// ** Basic Server-Side Validation **
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Invalid request method.');
}

// Sanitize and retrieve form data
$itemName        = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING);
$itemDescription = filter_input(INPUT_POST, 'condition', FILTER_SANITIZE_STRING);
$itemCategory    = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
$dropoffDate     = filter_input(INPUT_POST, 'dropoff_date', FILTER_SANITIZE_STRING); // Date format needs server-side check

// Check if required fields are present and not empty after sanitization
if (empty($itemName) || empty($itemDescription) || empty($itemCategory) || empty($dropoffDate)) {
    sendResponse('error', 'All fields are required.');
}

// More specific validation for date (e.g., check format, if it's in the past)
// Using strtotime for a basic check if it's a valid date string
if (strtotime($dropoffDate) === false) {
    sendResponse('error', 'Invalid date format provided.');
}

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    sendResponse('error', 'Database connection failed. Please try again later.');
}

$stmt = $conn->prepare("INSERT INTO ewaste_submissions (item_name, item_description, category, estimated_dropoff_date, submission_time) VALUES (?, ?, ?, ?, NOW())");

if ($stmt === false) {
    error_log("SQL prepare failed: " . $conn->error);
    sendResponse('error', 'Server error preparing data. Please try again.');
}

$stmt->bind_param('ssss', $itemName, $itemDescription, $itemCategory, $dropoffDate);

if ($stmt->execute()) {
    $submissionId = $conn->insert_id; 

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

    // Use mail() function. For production, consider libraries like PHPMailer for better control.
    if (!mail($adminEmail, $subjectAdmin, $messageAdmin, $headersAdmin)) {
        error_log("Failed to send admin email for submission ID: " . $submissionId);
        // Don't fail the whole submission if email fails, but log it.
    }

    // ** Success Response **
    sendResponse('success', 'Submission recorded successfully!');

    // Close statement and connection
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
