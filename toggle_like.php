<?php
session_start();
require "db.php";

if (!isset($_SESSION["user"])) {
    die("Je moet ingelogd zijn.");
}

if (!isset($_POST["id"])) {
    die("Geen recept ID.");
}

$recept_id = intval($_POST["id"]);

// user_id ophalen
$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $_SESSION["user"]);
$userQuery->execute();
$user_id = $userQuery->get_result()->fetch_assoc()["id"];

// Check of user al geliked heeft
$check = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND recept_id = ?");
$check->bind_param("ii", $user_id, $recept_id);
$check->execute();
$liked = $check->get_result()->num_rows > 0;

if ($liked) {
    // Unlike
    $del = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND recept_id = ?");
    $del->bind_param("ii", $user_id, $recept_id);
    $del->execute();

    $conn->query("UPDATE recepten SET likes = likes - 1 WHERE id = $recept_id");
} else {
    // Like
    $ins = $conn->prepare("INSERT INTO likes (user_id, recept_id) VALUES (?, ?)");
    $ins->bind_param("ii", $user_id, $recept_id);
    $ins->execute();

    $conn->query("UPDATE recepten SET likes = likes + 1 WHERE id = $recept_id");
}

header("Location: recept.php?id=$recept_id");
exit;
// IOT
