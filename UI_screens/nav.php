<?php
include __DIR__ . '/../php_files/dbConnection.php';
session_start();
$role = $_SESSION['role'] ?? 'guest';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=not_logged_in");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCycle</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header role="banner">
        <div class="nav-wrapper">
            <nav class="nav" aria-label="Primary">
                <a class="brand" href="homescreen.php">TechCycle</a>

                <div class="hamburger" id="hamburger">
                    <div></div>
                    <div></div>
                    <div></div>
                </div>

                <div class="nav-links" id="nav-links">
                    <?php if ($role == 'admin'): ?>
                        <a href="adminHome.php">Admin Dashboard</a>
                        <a href="manageUsers.php">Users</a>
                        <a href="manageWorkers.php">Workers</a>
                        <a href="reports.php">Reports</a>
                        <a href="adminLogistics.php">Logistics</a>
                        <a href="../php_files/logoutProcess.php">Logout</a>
                    <?php elseif ($role == 'worker'): ?>
                        <a href="workerDashboard.php">Dashboard</a>
                        <a href="pickups.php">Pickups</a>
                        <a href="inventory.php">Inventory</a>
                        <a href="../php_files/logoutProcess.php">Logout</a>
                    <?php elseif ($role == 'user'): ?>
                        <a href="homescreen.php">Home</a>
                        <a href="#features">Features</a>
                        <a href="recyclingCenters.php">Recycling Center</a>
                        <a href="marketplace.php">Marketplace</a>
                        <a href="../php_files/logoutProcess.php">Logout</a>
                    <?php else: ?>
                        <a href="homescreen.php">Home</a>
                        <a href="#features">Features</a>
                        <a href="login.php">Recycling Center</a>
                        <a href="login.php">Marketplace</a>
                        <a class="cta" href="login.php">Sign in</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <script>
        const hamburger = document.getElementById('hamburger');
        const nav = document.querySelector('.nav');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            nav.classList.toggle('active');
        });
    </script>
</body>

</html>