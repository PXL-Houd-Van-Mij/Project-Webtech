<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin"])) {
    die("Geen toegang.");
}

$recepten = $conn->query("
    SELECT r.*, u.email 
    FROM recepten r
    LEFT JOIN users u ON r.user_id = u.id
    ORDER BY r.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel – Receptify</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2 class="section-title">Admin Panel</h2>

<div style="width:90%; margin:auto;">

    <a href="admin_logout.php" class="btn small" style="background:#ff5fa2;">Uitloggen</a>

    <table style="width:100%; margin-top:20px; border-collapse:collapse;">
        <tr style="background:#ffb7e0; color:white;">
            <th style="padding:10px;">ID</th>
            <th>Titel</th>
            <th>Uploader</th>
            <th>Acties</th>
        </tr>

        <?php while ($r = $recepten->fetch_assoc()): ?>
        <tr style="background:white; border-bottom:2px solid #ffb7e0;">
            <td style="padding:10px;"><?= $r["id"] ?></td>
            <td><?= htmlspecialchars($r["titel"]) ?></td>
            <td><?= htmlspecialchars($r["email"] ?? "Onbekend") ?></td>
            <td>
                <a class="btn small" href="recept.php?id=<?= $r['id'] ?>">Bekijken</a>
                <a class="btn small" href="edit_upload.php?id=<?= $r['id'] ?>">Bewerken</a>
                <a class="btn small" style="background:#ff5f5f;" 
                   href="delete_recept.php?id=<?= $r['id'] ?>"
                   onclick="return confirm('Weet je zeker dat je dit recept wil verwijderen?');">
                   Verwijderen
                </a>
            </td>
        </tr>
        <?php endwhile; ?>

    </table>

</div>

</body>
</html>