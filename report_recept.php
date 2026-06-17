<?php
session_start();
require "db.php";

if (!isset($_POST["id"])) {
    die("Geen recept ID.");
}

$id = intval($_POST["id"]);

$stmt = $conn->prepare("INSERT INTO reports (recept_id, reason) VALUES (?, 'Automatisch rapport')");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: recept.php?id=$id&reported=1");
exit;