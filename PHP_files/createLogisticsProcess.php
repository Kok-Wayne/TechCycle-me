<?php
include __DIR__ . '/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $productID = $_POST['productID'];
    $status = $_POST['status'];

    // Get product details
    $stmt = $conn->prepare("SELECT productName, productDesc FROM products WHERE productID = ?");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        die("Product not found.");
    }

    $name = $product['productName'];
    $description = $product['productDesc'];

    // Image upload
    $image = $_FILES['image']['name'];
    $target = "../images/" . basename($image);

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        die("Image upload failed.");
    }

    $imagePath = "images/" . $image;

    // Insert into logistics
    $stmt = $conn->prepare("
        INSERT INTO logistics (name, description, image_path, status) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $name, $description, $imagePath, $status);
    $stmt->execute();

    // Update product market status
    $stmt = $conn->prepare("
        UPDATE products 
        SET marketStatus = 'available' 
        WHERE productID = ?
    ");
    $stmt->bind_param("i", $productID);
    $stmt->execute();

    // Redirect
    header("Location: ../UI_screens/adminLogistics.php?success=added");
    exit();
}