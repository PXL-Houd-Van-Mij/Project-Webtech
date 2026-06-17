<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin"])) die("Geen toegang.");

$id = intval($_GET["id"]);

$conn->query("DELETE FROM recept_subtags WHERE subtag_id = $id");
$conn->query("DELETE FROM subtags WHERE id = $id");

header("Location: admin_subtags.php");
exit;
// IOT
