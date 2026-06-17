<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin"])) {
    die("Geen toegang.");
}

$id = intval($_GET["id"]);

$conn->query("DELETE FROM reports WHERE id = $id");

header("Location: admin_panel.php");
exit;