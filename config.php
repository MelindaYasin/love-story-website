<?php
// config.php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "mensiversary_db";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Simple authentication check
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>