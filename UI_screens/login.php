<?php include __DIR__ . '/preLoginNav.php' ?>
<body>
    <!-- Main -->
    <main id="content">
        <section class="hero" aria-label="Create an account">
                <!-- Left side: Form -->
                <div class="card">
                    <h1>Log In</h1>
                    <form action="../php_files/loginProcess.php" method="POST" class="form">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>

                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>

                        <label for="password">Password</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" required>
                            <button type="button" class="toggle-password" aria-label="Show password">👁
                            </button>
                        </div>

                    <!-- <?php if (isset($_GET['error']) && $_GET['error'] === 'invalid_credentials'): ?>
                        <p style="color: red;">Invalid username, email or password. Please try again.</p>
                    <?php endif; ?> -->
                        <button type="submit" class="button primary">login</button>
                    </form>
                </div>

                <div aria-hidden="true">
                    <!-- Placeholder illustration block -->
                    <div
                        style="aspect-ratio: 4/3; border-radius:12px; background:linear-gradient(135deg, #52AD9C 0%, #6CC551 70%); display:flex; align-items:center; justify-content:center; color:white; font-weight:700;">
                        Sustainable • Community
                    </div>
                </div>
                <p class="muted">Don't have an account? <a href="register.php">Sign up</a></p>
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