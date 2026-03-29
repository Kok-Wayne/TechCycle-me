<?php
session_start();
include 'dbConnection.php'; // Make sure this connects correctly

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form values safely
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepare query to check if user exists by both username and email
    $sql = "SELECT * FROM users WHERE username = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            // Store user info in session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['profile_image'] = $row['profile_image'];
            $_SESSION['role'] = $row['role']; 

            // Redirect based on user role
            if ($row['role'] === 'admin') {
                header("Location: ../UI_screens/adminHome.php");
            } elseif ($row['role'] === 'worker') {
                header("Location: ../UI_screens/workerHome.php");
            } else {
                header("Location: ../UI_screens/index.php");
            }

            exit();
        } else {
            // Incorrect password
            $stmt->close();
            $conn->close();
            header("Location: ../UI_screens/login.php?error=invalid_credentials");
            exit();
        }
    } else {
        // Wrong username or email
        $stmt->close();
        $conn->close();
        header("Location: ../UI_screens/login.php?error=invalid_credentials");
        exit();
    }
}
?>