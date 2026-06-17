<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require "db.php";

// Alleen normale gebruikers mogen hun eigen recept verwijderen
if (!isset($_SESSION["user"])) {
    die("Je moet ingelogd zijn.");
}

// Check ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Ongeldig recept ID.");
}

$id = intval($_GET["id"]);

// Haal user_id op van ingelogde user
$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $_SESSION["user"]);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    die("Gebruiker niet gevonden.");
}

$loggedUserId = $userResult->fetch_assoc()["id"];

// Recept ophalen
$stmt = $conn->prepare("SELECT * FROM recepten WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Recept niet gevonden.");
}

$recept = $res->fetch_assoc();

// Check eigenaar
if ($recept["user_id"] != $loggedUserId) {
    die("Je mag dit recept niet verwijderen.");
}

// Verwijderen
$del = $conn->prepare("DELETE FROM recepten WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();

// Redirect
header("Location: index.php?deleted=1");
exit;
// einde T
