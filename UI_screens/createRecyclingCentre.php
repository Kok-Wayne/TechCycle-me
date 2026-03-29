<?php include __DIR__ . '/nav.php' ?>

<!-- Main -->
<main id="content">
    <section class="hero" aria-label="Register a recycling centre">

        <!-- Left side: Form -->
        <div class="card">
            <h1>Add Recycling Centre</h1>

            <form action="../php_files/createRecyclingCentreProcess.php" method="POST" class="form">

                <label for="centreName">Centre Name</label>
                <input type="text" id="centreName" name="centreName" required>

                <label for="centreAddress">Centre Address</label>
                <textarea id="centreAddress" name="centreAddress" required></textarea>

                <label for="centreMapLink">Google Maps Link</label>
                <input type="url" id="centreMapLink" name="centreMapLink">

                <button type="submit" class="button primary">Add Centre</button>
            </form>
        </div>

        <!-- Right side: Design block -->
        <div aria-hidden="true">
            <div
                style="aspect-ratio: 4/3; border-radius:12px; background:linear-gradient(135deg, #52AD9C 0%, #6CC551 70%); display:flex; align-items:center; justify-content:center; color:white; font-weight:700;">
                Eco • Recycling • Community
            </div>
        </div>

    </section>
</main>

<?php include __DIR__ . '/footer.php' ?>