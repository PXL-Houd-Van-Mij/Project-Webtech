<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require "db.php";

// Alleen admin mag deze pagina gebruiken
if (!isset($_SESSION["admin"])) {
    die("Geen toegang. Je moet admin zijn.");
}

// Check ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Ongeldig recept ID.");
}

$id = intval($_GET["id"]);

// Check of recept bestaat
$stmt = $conn->prepare("SELECT * FROM recepten WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Recept niet gevonden.");
}

// Verwijder recept
$del = $conn->prepare("DELETE FROM recepten WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();

// Redirect terug naar admin panel
header("Location: admin_panel.php?deleted=1");
exit;
// IOT
