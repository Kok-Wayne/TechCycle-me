<?php include __DIR__ . '/preLoginNav.php' ?>
<!-- Main -->
    <main id="content">
        <!-- Hero -->
        <section class="hero" aria-label="Introduction">
            <div>
                <h1>One Hub, One Planet</h1>
                <p>
                    Find local recycling schedules, cut energy use with proven tips, join community gardens, and swap
                    eco-friendly items with your neighbours — all in one place.
                </p>
                <div class="actions">
                    <a class="button primary" href="#features">Explore features</a>
                    <a class="button ghost" href="register.php">Create an account</a>
                </div>
            </div>
            <div aria-hidden="true">
                <!-- Placeholder illustration block -->
                <div
                    style="aspect-ratio: 4/3; border-radius:12px; background:linear-gradient(135deg, #52AD9C 0%, #6CC551 70%); display:flex; align-items:center; justify-content:center; color:white; font-weight:700;">
                    Sustainable • Community
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="grid" aria-label="Key features">
            <article class="card">
                <h3>Recycling Centre</h3>
                <p class="muted">Drop off your e-waste at designated centres! or create new designated centres for other users.</p>
                <a class="button ghost" href="register.php">Find a recycling centre</a>
            </article>

            <article class="card">
                <h3>Marketplace</h3>
                <p class="muted">Get unwanted E-waste through the marketplace for free!</p>
                <a class="button ghost" href="register.php">Browse marketplace</a>
            </article>

            <article class="card">
                <h3>Point Systems</h3>
                <p class="muted">Gain points through E-waste drop off and use them for marketplace purchases.</p>
                <a class="button ghost" href="register.php">Check your points</a>
            </article>

            <article class="card" id="swap">
                <h3>Join Today!</h3>
                <p class="muted">Make the earth greener!</p>
                <a class="button ghost" href="register.php">Sign Up</a>
            </article>
        </section>

        <!-- Gentle callout -->
        <aside class="callout" aria-label="Callout">
            <strong>Tip:</strong> Start small. Replace five high-use bulbs with LEDs and save up to <em>20%</em> on
            lighting energy. Track your progress in your profile.
        </aside>
    </main>

<?php include __DIR__ . '/footer.php' ?>