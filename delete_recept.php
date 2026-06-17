<?php
session_start();
require "db.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check login
if (!isset($_SESSION["user"])) {
    die("Je moet ingelogd zijn.");
}

// Check ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Ongeldig recept ID.");
}

$id = intval($_GET["id"]);

// Recept ophalen + eigenaar checken
$stmt = $conn->prepare("
    SELECT r.*, u.email 
    FROM recepten r
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Recept niet gevonden.");
}

$recept = $res->fetch_assoc();

// Check eigenaar
if ($recept["email"] !== $_SESSION["user"]) {
    die("Je mag dit recept niet verwijderen.");
}

// Verwijderen
$del = $conn->prepare("DELETE FROM recepten WHERE id=?");
$del->bind_param("i", $id);
$del->execute();

// Redirect
header("Location: index.php?deleted=1");
exit;
// IOT
