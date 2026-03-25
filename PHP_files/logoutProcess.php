<?php
session_start(); // start the session

// Unset all session variables, to clear session data
$_SESSION = [];

// Destroy the session, to log the user out
session_destroy();

// Redirect to the homepage
header("Location: ../UI_screens/homescreen.php");
exit;
?>