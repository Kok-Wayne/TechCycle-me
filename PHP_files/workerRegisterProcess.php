<?php
session_start();
include 'dbConnection.php'; // Make sure this connects correctly

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $phone = trim($_POST['phoneNumber']);

    // Check if username already exists
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: ../UI_screens/createWorkerAcc.php?error=username_exists");
        echo "Username already exists. Please choose a different username.";
        $check_stmt->close();
        $conn->close();
        exit();
    } else {

        // Check if email already exists
        $email_sql = "SELECT * FROM users WHERE email = ?";
        $email_stmt = $conn->prepare($email_sql);
        $email_stmt->bind_param("s", $email);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();
        if ($email_result->num_rows > 0) {
            header("Location: ../UI_screens/createWorkerAcc.php?error=email_exists");
            $email_stmt->close();
            $conn->close();
            exit();

            // Check if phone number already exists 
            $phone_sql = "SELECT * FROM users WHERE phoneNumber = ?";
            $phone_stmt = $conn->prepare($phone_sql);
            $phone_stmt->bind_param("s", $phone);
            $phone_stmt->execute();
            $phone_result = $phone_stmt->get_result();

            if ($phone_result->num_rows > 0) {
                header("Location: ../UI_screens/createWorkerAcc.php?error=phone_exists");
                $phone_stmt->close();
                $conn->close();
                exit();
            }

        } else {

            // Hash password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into database
            $role = 'worker'; // Set role to worker for this registration
            $insert_sql = "INSERT INTO users (username, email, password, role, phoneNumber) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $role, $phone);

            if ($email_result->num_rows > 0) {
                header("Location: ../UI_screens/createWorkerAcc.php?error=email_exists");
                exit();
            }
            if ($stmt->execute()) {
                // Account created successfully
                header("Location: ../UI_screens/manageWorkers.php");
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
    $check_stmt->close();
    $conn->close();
}