<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Your E-Waste Donation - TechCycle</title>
    <link rel="stylesheet" href="/UI_screens/styles.css">
</head>
<body>

<div class="container">
    <header role="banner">
        <?php include 'nav.php'; ?>
    </header>

    <main>
        <section class="ewaste-form-card">
            <h1>Submit Your E-Waste Donation</h1>

            <form id="ewaste-form" class="ewaste-form">
                <div id="form-feedback" class="muted" style="text-align:center; margin-bottom:1rem;"></div>

                <label for="user-name">Name:</label>
                <input type="text" id="user-name" name="user-name" required>

                <label for="user-email">Email:</label>
                <input type="email" id="user-email" name="user-email" required>

                <label for="user-phone">Phone:</label>
                <input type="text" id="user-phone" name="user-phone" required>

                <label for="item-name">Item Name:</label>
                <input type="text" id="item-name" name="item-name" required>

                <label for="item-description">Description:</label>
                <textarea id="item-description" name="item-description" rows="3" required></textarea>

                <label for="item-category">Category:</label>
                <select id="item-category" name="item-category" required>
                    <option value="">Select Category</option>
                    <option value="computers">Computers & Accessories</option>
                    <option value="phones">Mobile Phones & Tablets</option>
                    <option value="audio">Audio & Video Equipment</option>
                    <option value="appliances">Small Appliances</option>
                    <option value="other">Other</option>
                </select>

                <label for="condition">Item Condition:</label>
                <select id="condition" name="condition" required>
                    <option value="">Select Condition</option>
                    <option value="working">Working</option>
                    <option value="broken">Broken</option>
                </select>

                <label for="dropoff-date">Estimated Drop-off Date:</label>
                <input type="date" id="dropoff-date" name="dropoff-date" required>

                <button type="submit" class="button primary">Submit Donation</button>
            </form>
        </section>
    </main>

    <aside>
        <div class="callout">
            <h2>How Our E-Waste Management Works</h2>

            <div class="card">
                <h3>1. Drop Off</h3>
                <p class="muted">
                    Bring your unwanted electronics to our designated drop-off center.
                </p>
            </div>

            <div class="card">
                <h3>2. Collection & Sorting</h3>
                <p class="muted">
                    Our team collects and meticulously sorts the e-waste for processing and potential reuse.
                </p>
            </div>

            <div class="card">
                <h3>3. Social Media Showcase</h3>
                <p class="muted">
                    Valuable items are then showcased on our social media platforms, ready for a new home.
                </p>
            </div>

            <div class="card">
                <h3>4. Free Collection</h3>
                <p class="muted">
                    If you see something you like on our social media, you can collect it from the center for free.
                </p>
            </div>
        </div>
    </aside>

    <footer>
        <div class="footer-inner">
            <p>&copy; 2026 TechCycle. All rights reserved.</p>
        </div>
    </footer>
</div>

<script>
document.getElementById('ewaste-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const form = event.target;
    const feedbackDiv = document.getElementById('form-feedback');
    feedbackDiv.textContent = '';

    const itemName = document.getElementById('item-name').value.trim();
    const itemDescription = document.getElementById('item-description').value.trim();
    const itemCategory = document.getElementById('item-category').value;
    const condition = document.getElementById('condition').value;
    const dropoffDate = document.getElementById('dropoff-date').value;

    if (!itemName || !itemDescription || !itemCategory || !condition || !dropoffDate) {
        feedbackDiv.textContent = 'Please fill in all required fields.';
        feedbackDiv.style.color = 'red';
        return;
    }

    const formData = new FormData(form);

    fetch('process_submission.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.href = 'thank_you.php';
        } else {
            feedbackDiv.textContent = data.message || 'Error occurred';
            feedbackDiv.style.color = 'red';
        }
    })
    .catch(error => {
        console.error(error);
        feedbackDiv.textContent = 'Server error';
        feedbackDiv.style.color = 'red';
    });
});
</script>

</body>
</html>
