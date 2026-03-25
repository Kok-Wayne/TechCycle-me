<?php include __DIR__ . '/b4loginnav.php' ?>
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
                <p class="muted">Look up local recycling rules, pickup days, and how to participate correctly.</p>
                <a class="button ghost" href="/recycling">View schedules</a>
            </article>

            <article class="card">
                <h3>Marketplace</h3>
                <p class="muted">Actionable steps for home and office: LEDs, standby killers, and more.</p>
                <a class="button ghost" href="/tips">Browse tips</a>
            </article>

            <article class="card">
                <h3>Point Systems</h3>
                <p class="muted">Join gardens, attend events, and share seasonal planting advice.</p>
                <a class="button ghost" href="/gardening">Find a garden</a>
            </article>

            <article class="card" id="swap">
                <h3>Join Today!</h3>
                <p class="muted">Give items a second life. List, browse, and trade with neighbors.</p>
                <a class="button ghost" href="swapMarketplace.html">Start trading</a>
            </article>
        </section>

        <!-- Gentle callout -->
        <aside class="callout" aria-label="Callout">
            <strong>Tip:</strong> Start small. Replace five high-use bulbs with LEDs and save up to <em>20%</em> on
            lighting energy. Track your progress in your profile.
        </aside>
    </main>

<?php include __DIR__ . '/footer.php' ?>