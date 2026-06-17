<?php
session_start();
require "db.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user"])) {
    echo json_encode(["error" => "not_logged_in"]);
    exit;
}

if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
    echo json_encode(["error" => "invalid_id"]);
    exit;
}

$recept_id = intval($_POST["id"]);

// User ID ophalen
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$user_id = $stmt->get_result()->fetch_assoc()["id"];

// Check of al geliked
$check = $conn->prepare("SELECT id FROM likes WHERE user_id=? AND recept_id=? LIMIT 1");
$check->bind_param("ii", $user_id, $recept_id);
$check->execute();
$liked = $check->get_result()->num_rows > 0;

if (!$liked) {
    $add = $conn->prepare("INSERT INTO likes (user_id, recept_id) VALUES (?, ?)");
    $add->bind_param("ii", $user_id, $recept_id);
    $add->execute();
    $conn->query("UPDATE recepten SET likes = likes + 1 WHERE id = $recept_id");
    echo json_encode(["status" => "liked"]);
} else {
    $del = $conn->prepare("DELETE FROM likes WHERE user_id=? AND recept_id=? LIMIT 1");
    $del->bind_param("ii", $user_id, $recept_id);
    $del->execute();
    $conn->query("UPDATE recepten SET likes = likes - 1 WHERE id = $recept_id AND likes > 0");
    echo json_encode(["status" => "unliked"]);
}

exit;
