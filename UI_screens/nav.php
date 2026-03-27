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
                        <div class="profile-wrapper">
                            <div class="profile-circle" id="profileCircle">
                                <?php
                                // Check if user has a profile image
                                $profileImage = $_SESSION['profile_image'] ?? '';

                                if (!empty($profileImage) && file_exists("../uploads/profiles/$profileImage")) {
                                    echo "<img src='../uploads/profiles/$profileImage' alt='Profile' class='profile-img'>";
                                } else {
                                    echo "<img src='../Website_Images_Files/default-avatar.png' alt='Profile' class='profile-img'>";
                                }
                                ?>
                            </div>
                            <div class="profile-dropdown" id="profileDropdown">
                                <a href="userProfile.php">Settings</a>
                                <a href="viewUserNotificationsPage.php">Notifications</a>
                                <a href="../php_files/logoutProcess.php">Logout</a>
                            </div>
                        </div>
                    <?php elseif ($role == 'worker'): ?>
                        <a href="workerDashboard.php">Dashboard</a>
                        <a href="pickups.php">Pickups</a>
                        <a href="inventory.php">Inventory</a>
                        <a href="viewWorkerNotificationsPage.php">Notifications</a>
                        <div class="profile-wrapper">
                            <div class="profile-circle" id="profileCircle">
                                <?php
                                // Check if user has a profile image
                                $profileImage = $_SESSION['profile_image'] ?? '';

                                if (!empty($profileImage) && file_exists("../uploads/profiles/$profileImage")) {
                                    echo "<img src='../uploads/profiles/$profileImage' alt='Profile' class='profile-img'>";
                                } else {
                                    echo "<img src='../Website_Images_Files/default-avatar.png' alt='Profile' class='profile-img'>";
                                }
                                ?>
                            </div>
                            <div class="profile-dropdown" id="profileDropdown">
                                <a href="userProfile.php">Settings</a>
                                <a href="viewUserNotificationsPage.php">Notifications</a>
                                <a href="../php_files/logoutProcess.php">Logout</a>
                            </div>
                        </div>
                    <?php elseif ($role == 'user'): ?>
                        <a href="userHome.php">Home</a>
                        <a href="#features">Features</a>
                        <a href="recyclingCenters.php">Recycling Center</a>
                        <a href="marketplace.php">Marketplace</a>
                        <div class="profile-wrapper">
                            <div class="profile-circle" id="profileCircle">
                                <?php
                                $profileImage = $_SESSION['profile_image'] ?? '';
                                if (!empty($profileImage) && file_exists("../uploads/profiles/$profileImage")) {
                                    echo "<img src='../uploads/profiles/$profileImage' alt='Profile' class='profile-img'>";
                                } else {
                                    echo "<img src='../Website_Images_Files/default-avatar.png' alt='Profile' class='profile-img'>";
                                }
                                ?>
                            </div>

                            <div class="profile-dropdown" id="profileDropdown">
                                <a href="userProfile.php">Settings</a>
                                <a href="cart.php">Cart</a>
                                <a href="viewUserNotificationsPage.php">Notifications</a>
                                <a href="../php_files/logoutProcess.php">Logout</a>
                            </div>
                        </div>
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
        const profileCircle = document.getElementById('profileCircle');
        if (profileCircle) {
            const profileWrapper = profileCircle.parentElement;
            profileCircle.addEventListener('click', () => {
                profileWrapper.classList.toggle('active');
            });

            window.addEventListener('click', function(e) {
                if (!profileWrapper.contains(e.target)) {
                    profileWrapper.classList.remove('active');
                }
            });
        }

        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('nav-links');
        const profileDropdown = document.getElementById('profileDropdown');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');

            // Only on mobile
            if (window.innerWidth <= 768 && profileDropdown) {
                // Remove previous clones
                document.querySelectorAll('.mobile-profile-link').forEach(e => e.remove());

                // Clone links and prepend them at the top
                Array.from(profileDropdown.children).forEach(link => {
                    const clone = link.cloneNode(true);
                    clone.classList.add('mobile-profile-link');
                    navLinks.insertBefore(clone, navLinks.firstChild);
                });
            }
        });

        // Remove mobile clones on resize back to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                document.querySelectorAll('.mobile-profile-link').forEach(e => e.remove());
                navLinks.classList.remove('active'); // collapse mobile menu if open
                hamburger.classList.remove('active');
            }
        });
    </script>
</body>

</html>