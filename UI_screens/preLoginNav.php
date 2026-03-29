<?php include __DIR__. '/../php_files/dbConnection.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                    <a href="homescreen.php">Home</a>
                    <a href="#features">Features</a>
                    <a href="login.php">Recycling Center</a>
                    <a href="login.php">Trade-ins</a>
                    <a class="cta" href="login.php">Sign in</a>
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
