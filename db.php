<?php
// Detecteer of we lokaal testen (php -S localhost:8000) of online draaien.
$host_header = $_SERVER['HTTP_HOST'] ?? '';
$is_local = (strpos($host_header, 'localhost') !== false)
         || (strpos($host_header, '127.0.0.1') !== false);

if ($is_local) {
    // Lokale XAMPP MySQL-database (voor testen op je eigen pc)
    $host = "127.0.0.1";
    $user = "root";
    $pass = "";
    $db   = "if0_41967688_db";
} else {
    // Online InfinityFree-database (voor de live website)
    $host = "sql303.infinityfree.com";
    $user = "if0_41967688";
    $pass = "8UgNR2U2xoL";
    $db   = "if0_41967688_db";
}

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database fout: " . mysqli_connect_error());
}
?>
// IOT
