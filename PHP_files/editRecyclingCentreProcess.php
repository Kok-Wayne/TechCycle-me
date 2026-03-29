<?php
include __DIR__ . '/dbConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['centreID'];
    $name = $_POST['centreName'];
    $address = $_POST['centreAddress'];
    $mapLink = $_POST['centreMapLink'];

    $stmt = $conn->prepare("
        UPDATE recycling_centre
        SET centreName = ?, centreAddress = ?, centreMapLink = ?
        WHERE centreID = ?
    ");

    $stmt->bind_param("sssi", $name, $address, $mapLink, $id);

    if ($stmt->execute()) {
        header("Location: ../UI_screens/recyclingCentre.php?success=updated");
        exit();
    } else {
        header("Location: ../UI_screens/editRecyclingCentre.php?id=$id&error=failed");
        exit();
    }
}