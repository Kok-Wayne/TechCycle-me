<?php
header("Content-Type: application/json");

// 🔹 Database connection
$conn = new mysqli("localhost", "root", "", "techcycle_database");

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}

// 🔹 Ensure POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
    exit;
}

// 🔹 Determine action
$action = $_POST['action'] ?? '';

switch ($action) {

    case "send_location":
        sendLocation($conn);
        break;

    case "update_status":
        updateStatus($conn);
        break;

    default:
        echo json_encode([
            "status" => "error",
            "message" => "Invalid action"
        ]);
}

// ================= FUNCTION 2 =================
function sendLocation($conn) {

    // 🔹 2.1 Receive Location Details
    $itemName = trim($_POST['item_name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $dropoff  = $_POST['dropoff_date'] ?? '';

    // 🔹 2.2 Validate Input
    if (empty($itemName) || empty($category) || empty($location) || empty($dropoff)) {
        echo json_encode([
            "status" => "error",
            "message" => "All fields are required"
        ]);
        return;
    }

    // 🔹 Validate date
    if (strtotime($dropoff) < strtotime(date("Y-m-d"))) {
        echo json_encode([
            "status" => "error",
            "message" => "Drop-off date cannot be in the past"
        ]);
        return;
    }

    // 🔹 Validate category (D1)
    $check = $conn->prepare("SELECT id FROM ewaste_categories WHERE category_name = ?");
    if (!$check) {
        echo json_encode(["status" => "error", "message" => "Server error (category check)"]);
        return;
    }

    $check->bind_param("s", $category);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid category selected"
        ]);
        return;
    }

    // 🔹 2.3 Store Location Information (D2)
    $stmt = $conn->prepare("
        INSERT INTO ewaste_submissions 
        (item_name, category, location, estimated_dropoff_date, status, submission_time)
        VALUES (?, ?, ?, ?, 'Pending', NOW())
    ");

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Server error (prepare failed)"]);
        return;
    }

    $stmt->bind_param("ssss", $itemName, $category, $location, $dropoff);

    if ($stmt->execute()) {
        // 🔹 2.4 Send Location Confirmation
        echo json_encode([
            "status" => "success",
            "message" => "Location submitted successfully",
            "submission_id" => $conn->insert_id
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to store data"
        ]);
    }

    $stmt->close();
}

// ================= FUNCTION 5 =================
function updateStatus($conn) {

    // 🔹 5.1 Receive Status Update Request
    $submissionId = $_POST['submission_id'] ?? '';
    $newStatus    = trim($_POST['status'] ?? '');

    if (empty($submissionId) || empty($newStatus)) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields"
        ]);
        return;
    }

    // 🔹 Validate status
    $allowedStatus = ["Pending", "Collected", "Recycled"];

    if (!in_array($newStatus, $allowedStatus)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid status value"
        ]);
        return;
    }

    // 🔹 5.2 Retrieve Current Status (D2)
    $stmt = $conn->prepare("SELECT status FROM ewaste_submissions WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Server error (retrieve failed)"]);
        return;
    }

    $stmt->bind_param("i", $submissionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Submission not found"
        ]);
        return;
    }

    $row = $result->fetch_assoc();
    $currentStatus = $row['status'];

    // 🔹 5.3 Update E-Waste Status
    $update = $conn->prepare("
        UPDATE ewaste_submissions 
        SET status = ? 
        WHERE id = ?
    ");

    if (!$update) {
        echo json_encode(["status" => "error", "message" => "Server error (update failed)"]);
        return;
    }

    $update->bind_param("si", $newStatus, $submissionId);

    if ($update->execute()) {
        // 🔹 5.4 Notify Stakeholders
        echo json_encode([
            "status" => "success",
            "message" => "Status updated successfully",
            "old_status" => $currentStatus,
            "new_status" => $newStatus
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update status"
        ]);
    }

    $stmt->close();
    $update->close();
}

$conn->close();
?>