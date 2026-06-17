<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin"])) die("Geen toegang.");

$id = intval($_GET["id"]);

$conn->query("DELETE FROM recept_tags WHERE tag_id = $id");
$conn->query("DELETE FROM tags WHERE id = $id");

header("Location: admin_tags.php");
exit;
// einde T
