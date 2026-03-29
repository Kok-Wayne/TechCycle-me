<?php
include __DIR__ . '/dbConnection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['centreName'];
    $address = $_POST['centreAddress'];
    $mapLink = $_POST['centreMapLink'];

    $stmt = $conn->prepare("
        INSERT INTO recycling_centre (centreName, centreAddress, centreMapLink)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("sss", $name, $address, $mapLink);

    if ($stmt->execute()) {
        header("Location: ../UI_screens/recyclingCentre.php?success=added");
        exit();
    } else {
        header("Location: ../UI_screens/createRecyclingCentre.php?error=failed");
        exit();
    }
}