<?php include __DIR__ . '/nav.php'; ?>

<main>
    <section class="hero">
        <div>
            <h1>Turn Your Old Electronics into a New Beginning</h1>
            <p>Join TechCycle in reducing electronic waste. Drop off your unwanted devices and give them a new life
                through our community marketplace.</p>
            <div class="actions">
                <!-- This button now links to the form page -->
                <a href="submit_ewaste.html" class="button primary">Donate E-Waste</a>
                <a href="index.html#marketplace" class="button ghost">Explore Items</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="collected e-waste.avif" alt="E-waste being collected for recycling">
        </div>
    </section>

    <section id="how-it-works" class="callout">
        <h2>How Our E-Waste Management Works</h2>
        <div class="grid">
            <div class="card">
                <h3>1. Drop Off</h3>
                <p>Bring your unwanted electronics to our designated drop-off center.</p>
            </div>
            <div class="card">
                <h3>2. Collection & Sorting</h3>
                <p>Our team collects and meticulously sorts the e-waste for processing and potential reuse.</p>
            </div>
            <div class="card">
                <h3>3. Social Media Showcase</h3>
                <p>Valuable items are then showcased on our social media platforms, ready for a new home.</p>
            </div>
            <div class="card">
                <h3>4. Free Collection</h3>
                <p>If you see something you like on our social media, you can collect it from the center for free!</p>
            </div>
        </div>
    </section>

    <!-- Marketplace and other sections remain here -->
    <section id="marketplace" class="grid">
        <h2>Featured E-Waste Items</h2>
        <!-- Placeholder items -->
        <div class="card">
            <img src="vintage radio.jpg" alt="Featured Item 1">
            <h3>Vintage Radio</h3>
            <p class="muted">Fully functional vintage radio, great for collectors.</p>
            <p><strong>Availability:</strong> At Center</p>
            <a href="#" class="button primary">Learn More</a>
        </div>
        <div class="card">
            <img src="old monitor.jpg" alt="Featured Item 2">
            <h3>Old Monitor</h3>
            <p class="muted">CRT monitor, works but heavy. Good for retro gaming setups.</p>
            <p><strong>Availability:</strong> At Center</p>
            <a href="#" class="button primary">Learn More</a>
        </div>
        <div class="card">
            <img src="desk lamp.webp" alt="Featured Item 3">
            <h3>Desk Lamp</h3>
            <p class="muted">Adjustable desk lamp, minor cosmetic wear.</p>
            <p><strong>Availability:</strong> At Center</p>
            <a href="#" class="button primary">Learn More</a>
        </div>
    </section>

</main>

<aside>
    <div class="callout">
        <h3>Connect With Us!</h3>
        <p>Follow our social media for the latest available e-waste items ready for free collection.</p>
        <div class="actions">
            <a href="#" class="button primary">Facebook</a>
            <a href="#" class="button ghost">Instagram</a>
        </div>
    </div>
    <div class="card">
        <h3>Our Drop-off Center</h3>
        <p><strong>Address:</strong> Jalan 7/23F, Taman Teratai Mewah, 53100 Kuala Lumpur, Wilayah Persekutuan Kuala
            Lumpur, Malaysia</p>
        <p><strong>Hours:</strong> Mon - Fri: 8 AM - 7 PM, Sat: 10 AM - 2 PM</p>
        <p><strong>Phone:</strong> (011) 999-7890</p>
        <a href="#" class="button ghost">Get Directions</a>
    </div>
</aside>

<?php include __DIR__ . '/footer.php' ?>