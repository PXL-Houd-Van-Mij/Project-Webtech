<?php
session_start();
require "db.php";

header("Content-Type: application/json");

// Alleen ingelogde gebruikers
if (!isset($_SESSION["user"])) {
    echo json_encode(["error" => "not_logged_in"]);
    exit;
}

// Check recept ID
if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
    echo json_encode(["error" => "invalid_id"]);
    exit;
}

$recept_id = intval($_POST["id"]);

// User ID ophalen
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($user_id);
$stmt->fetch();

// Check of al favoriet
$check = $conn->prepare("SELECT id FROM favorieten WHERE user_id=? AND recept_id=? LIMIT 1");
$check->bind_param("i", $user_id, $recept_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    // Toevoegen
    $add = $conn->prepare("INSERT INTO favorieten (user_id, recept_id) VALUES (?, ?)");
    $add->bind_param("i", $user_id, $recept_id);
    $add->execute();

    $conn->query("UPDATE recepten SET likes = likes + 1 WHERE id = $recept_id");

    echo json_encode(["status" => "liked"]);
} else {
    // Verwijderen
    $del = $conn->prepare("DELETE FROM favorieten WHERE user_id=? AND recept_id=? LIMIT 1");
    $del->bind_param("i", $user_id, $recept_id);
    $del->execute();

    $conn->query("UPDATE recepten SET likes = likes - 1 WHERE id = $recept_id AND likes > 0");

    echo json_encode(["status" => "unliked"]);
}

exit;