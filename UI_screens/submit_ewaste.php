<?php include 'nav.php'; ?>

<h2>Submit E-Waste</h2>

<form method="POST" action="process_submission.php">

    <input type="text" name="user-name" placeholder="Your Name" required>
    <input type="email" name="user-email" placeholder="Your Email" required>
    <input type="text" name="user-phone" placeholder="Your Phone" required>

    <input type="text" name="item-name" placeholder="Item Name" required>

    <textarea name="item-description" placeholder="Description" required></textarea>

    <select name="item-category" required>
        <option value="">Select Category</option>
        <option value="computers">Computers</option>
        <option value="phones">Phones</option>
        <option value="audio">Audio</option>
        <option value="appliances">Appliances</option>
    </select>

    <!-- Added to match DFD -->
    <select name="condition" required>
        <option value="">Item Condition</option>
        <option value="working">Working</option>
        <option value="broken">Broken</option>
    </select>

    <input type="date" name="dropoff-date" required>

    <button type="submit">Submit</button>

</form>