<?php
include 'nav.php';

if (!isset($_SESSION['user_id'])) {
    die("No user specified.");
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

$defaultAvatar = '/TechCycle/Website_Images_Files/default-avatar.png';
$profileImage = !empty($user['profile_image'])
    ? '/TechCycle/' . ltrim($user['profile_image'], '/')
    : $defaultAvatar;
?>



<main class="profile-page">
    <!-- Avatar + basic info -->
    <div class="profile-preview">
        <div class="profile-avatar"
            style="background-image: url('<?php echo htmlspecialchars($profileImage); ?>');">
        </div>
        <h2><?php echo htmlspecialchars($user['username']); ?></h2>
        <p><?php echo htmlspecialchars($user['email']); ?></p>
        <p><?php echo htmlspecialchars($user['phonenumber']); ?></p>
    </div>

    <!-- Profile form -->
    <div class="profile-form-card">
        <h2>Manage Profile</h2>
        <form action="../PHP_files/editProfileProcess.php" method="POST" enctype="multipart/form-data">
            <div class="form-field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>

            <div class="form-field">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phonenumber" value="<?php echo htmlspecialchars($user['phonenumber']); ?>" required>
            </div>

            <div class="form-field">
                <label for="new-image">Change Profile Picture</label>
                <input type="file" id="new-image" name="new-image" accept=".jpg,.jpeg,.png,.gif">
            </div>

            <div class="form-field">
                <label for="password">New Password (optional)</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="New password">
                    <button type="button" class="toggle-password" data-target="password">👁</button>
                </div>
            </div>

            <div class="form-field">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password">
                    <button type="button" class="toggle-password" data-target="confirm_password">👁</button>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button primary">Save changes</button>
                <a href="userHomePage.php" class="button ghost">Cancel</a>
            </div>
        </form>
    </div>
</main>

<script>
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.textContent = input.type === 'password' ? '👁' : '👁‍🗨';
        });
    });
</script>

<?php include 'footer.php'; ?>