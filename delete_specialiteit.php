<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin"])) die("Geen toegang.");

$id = intval($_GET["id"]);

$conn->query("DELETE FROM recept_specialiteiten WHERE specialiteit_id = $id");
$conn->query("DELETE FROM specialiteiten WHERE id = $id");

header("Location: admin_specialiteiten.php");
exit;
// IOT
