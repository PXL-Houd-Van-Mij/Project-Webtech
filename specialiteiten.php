<?php
session_start();
require "db.php";

// Specialiteiten ophalen (bijv. categorie = 'specialiteit')
$q = mysqli_query($conn, "SELECT * FROM recepten WHERE specialiteit = 1 ORDER BY titel ASC");
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Specialiteiten – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Onze Specialiteiten</h2>

<div class="recipe-container">

<?php if (mysqli_num_rows($q) > 0): ?>

    <?php while ($row = mysqli_fetch_assoc($q)): ?>

        <div class="recipe-card">
            <h3><?= htmlspecialchars($row['titel']) ?></h3>
            <p><?= htmlspecialchars($row['beschrijving']) ?></p>
            <p class="likes">❤️ <?= $row['likes'] ?> likes</p>
            <a href="recept.php?id=<?= $row['id'] ?>" class="btn small">Bekijk recept</a>
        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p style="text-align:center; width:100%;">Geen specialiteiten gevonden.</p>

<?php endif; ?>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=1"></script>
</body>
</html>