<?php
$host = "sql303.infinityfree.com";
$user = "if0_41967688";
$pass = "8UgNR2U2xoL";
$db   = "if0_41967688_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database fout: " . mysqli_connect_error());
}
?>