<?php include __DIR__ . '/preLoginNav.php' ?>
<!-- Main -->
    <main id="content">
        <section class="hero" aria-label="Create an account">
            <!-- Left side: Form -->
            <div class="card">
                <h1>Create an account</h1>
                <form action="../php_files/registerProcess.php" method="POST" class="form">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                    <!-- <?php if (isset($_GET['error']) && $_GET['error'] === 'username_exists'): ?>
                        <p style="color: red;">Username already exists. Please choose a different one.</p>
                    <?php endif; ?> -->

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <!-- <?php if (isset($_GET['error']) && $_GET['error'] === 'email_exists'): ?>
                        <p style="color: red;">That email is already registered. Please use another email.</p>
                    <?php endif; ?> -->

                    <label for="phoneNumber">Phone Number</label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" required>
                        <!-- <?php if (isset($_GET['error']) && $_GET['error'] === 'phone_exists'): ?>
                            <p style="color: red;">That phone number is already registered. Please use another phone number.</p>
                    <?php endif; ?> -->

                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" required>
                        <button type="button" class="toggle-password" aria-label="Show password">👁
                        </button>
                    </div>

                    <button type="submit" class="button primary">Register</button>
                </form>
            </div>

            <div aria-hidden="true">
                <!-- Placeholder illustration block -->
                <div
                    style="aspect-ratio: 4/3; border-radius:12px; background:linear-gradient(135deg, #52AD9C 0%, #6CC551 70%); display:flex; align-items:center; justify-content:center; color:white; font-weight:700;">
                    Sustainable • Community
                </div>
            </div>
            <p class="muted">Already have an account? <a href="login.php">Log In</a></p>
        </section>

        <!-- JavaScript code -->
        <script>
            const toggleBtn = document.querySelector('.toggle-password');
            const passwordInput = document.querySelector('#password');

            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleBtn.textContent = isPassword ? '👁‍🗨' : '👁'; // swap icon
            });
        </script>

<?php include __DIR__ . '/footer.php' ?>