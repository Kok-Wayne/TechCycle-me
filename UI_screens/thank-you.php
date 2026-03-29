<?php
// thank_you.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - TechCycle</title>
    <link rel="stylesheet" href="/TechCycle/UI_screens/styles.css">
</head>
<body>

<div class="container">
    <header role="banner">
        <?php include 'nav.php'; ?>
    </header>

    <main>
        <section class="ewaste-form-card">
            <h1>Thank You!</h1>

            <p class="muted">Your e-waste submission has been successfully recorded.</p>

            <p class="muted">Please bring your item within 7 days.</p>

            <div class="actions">
                <a href="submit_ewaste.php" class="button primary">Submit Another Item</a>
            </div>
        </section>
    </main>

    <aside>
        <div class="callout">
            <h2>What Happens Next?</h2>

            <div class="card">
                <h3>1. Submission Recorded</h3>
                <p class="muted">Your item details have been saved in the system.</p>
            </div>

            <div class="card">
                <h3>2. Prepare Drop-Off</h3>
                <p class="muted">Bring your item to the recycling center within 7 days.</p>
            </div>

            <div class="card">
                <h3>3. Responsible Processing</h3>
                <p class="muted">Our team will sort and process the e-waste properly.</p>
            </div>
        </div>
    </aside>

    <footer>
        <div class="footer-inner">
            <p>&copy; 2026 TechCycle. All rights reserved.</p>
        </div>
    </footer>
</div>

</body>
</html>
