<?php
// 🔹 Run logic only when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = new mysqli("localhost", "root", "", "techcycle_database");

    if ($conn->connect_error) {
        die("Database connection failed");
    }

    $action = $_POST['action'] ?? '';

    if ($action == "send_location") {
        sendLocation($conn);
    } elseif ($action == "update_status") {
        updateStatus($conn);
    }
}

// ================= FUNCTION 2 =================
function sendLocation($conn) {

    // 2.1 Receive Location Details
    $itemName = $_POST['item_name'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $dropoff  = $_POST['dropoff_date'];

    // 2.2 Validate
    if (!$itemName || !$category || !$location || !$dropoff) {
        echo "<p style='color:red;'>All fields required</p>";
        return;
    }

    // Validate category (D1)
    $check = $conn->prepare("SELECT * FROM ewaste_categories WHERE category_name=?");
    $check->bind_param("s", $category);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        echo "<p style='color:red;'>Invalid category</p>";
        return;
    }

    // 2.3 Store (D2)
    $stmt = $conn->prepare("
        INSERT INTO ewaste_submissions 
        (item_name, category, location, estimated_dropoff_date)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $itemName, $category, $location, $dropoff);

    if ($stmt->execute()) {
        // 2.4 Confirmation
        echo "<p style='color:green;'>Location submitted successfully!</p>";
    } else {
        echo "<p style='color:red;'>Insert failed</p>";
    }
}

// ================= FUNCTION 5 =================
function updateStatus($conn) {

    // 5.1 Receive Request
    $id = $_POST['submission_id'];
    $status = $_POST['status'];

    // 5.2 Retrieve Current Status
    $stmt = $conn->prepare("SELECT status FROM ewaste_submissions WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<p style='color:red;'>Record not found</p>";
        return;
    }

    // 5.3 Update Status
    $update = $conn->prepare("UPDATE ewaste_submissions SET status=? WHERE id=?");
    $update->bind_param("si", $status, $id);

    if ($update->execute()) {
        // 5.4 Notify
        echo "<p style='color:green;'>Status updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>Update failed</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>E-Waste Management</title>
</head>
<body>

<h2>Send E-Waste Materials Location</h2>
<form method="POST">
    <input type="hidden" name="action" value="send_location">

    Item Name: <input type="text" name="item_name"><br><br>

    Category:
    <select name="category">
        <option>Computers & Accessories</option>
        <option>Mobile Phones & Tablets</option>
        <option>Batteries</option>
    </select><br><br>

    Location: <input type="text" name="location"><br><br>

    Drop-off Date: <input type="date" name="dropoff_date"><br><br>

    <button type="submit">Submit Location</button>
</form>

<hr>

<h2>Update E-Waste Status</h2>
<form method="POST">
    <input type="hidden" name="action" value="update_status">

    Submission ID: <input type="number" name="submission_id"><br><br>

    Status:
    <select name="status">
        <option>Pending</option>
        <option>Collected</option>
        <option>Recycled</option>
    </select><br><br>

    <button type="submit">Update Status</button>
</form>

</body>
</html>