<?php
session_start();
require "db.php";

// Recepten ophalen die aan minstens één specialiteit gekoppeld zijn.
// (Nieuw schema: koppeltabel recept_specialiteiten i.p.v. een 'specialiteit'-kolom.)
$q = mysqli_query($conn, "
    SELECT r.id, r.titel, r.beschrijving, r.likes,
           GROUP_CONCAT(s.naam ORDER BY s.naam SEPARATOR ', ') AS specialiteiten
    FROM recepten r
    JOIN recept_specialiteiten rs ON rs.recept_id = r.id
    JOIN specialiteiten s ON s.id = rs.specialiteit_id
    GROUP BY r.id, r.titel, r.beschrijving, r.likes
    ORDER BY r.titel ASC
");
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
            <p style="color:#ff5fa2; font-weight:600;"><?= htmlspecialchars($row['specialiteiten']) ?></p>
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
// IOT
