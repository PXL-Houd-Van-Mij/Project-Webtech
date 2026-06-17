<?php
// Toon fouten tijdens debuggen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// InfinityFree database gegevens
$host = "localhost"; // of "127.0.0.1"
$user = "root";
$password = "";      // laat leeg als je XAMPP gebruikt
$database = "receptify";

// Verbinding maken
$conn = mysqli_connect($host, $user, $password, $database);

// Check verbinding
if (!$conn) {
    die("Database fout: " . mysqli_connect_error());
}
?>
// einde T
