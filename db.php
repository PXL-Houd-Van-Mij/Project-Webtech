<?php
// Toon fouten tijdens debuggen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// InfinityFree database gegevens
$host = "sql303.infinityfree.com";
$user = "if0_41967688";
$pass = "8UgNR2U2xoL";
$db   = "if0_41967688_db";

// Verbinding maken
$conn = mysqli_connect($host, $user, $pass, $db);

// Check verbinding
if (!$conn) {
    die("Database fout: " . mysqli_connect_error());
}
?>
// einde T
