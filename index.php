<?php
session_start();
require "db.php";
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptify – Home</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Ontdek heerlijke recepten</h1>
    <p>Vind inspiratie voor elke maaltijd, elke dag.</p>
</section>

<!-- AANBEVOLEN RECEPTEN -->
<h2 class="section-title">Aanbevolen voor jou</h2>

<div class="recipe-container">

    <?php
    // Voorbeeld: haal 3 willekeurige recepten op
    $q = mysqli_query($conn, "SELECT * FROM recepten ORDER BY RAND() LIMIT 3");

    if (mysqli_num_rows($q) > 0):
        while ($row = mysqli_fetch_assoc($q)):
    ?>

        <div class="recipe-card">
            <h3><?= htmlspecialchars($row['titel']) ?></h3>
            <p><?= htmlspecialchars($row['beschrijving']) ?></p>
            <p class="likes">❤️ <?= $row['likes'] ?> likes</p>
            <a href="recept.php?id=<?= $row['id'] ?>" class="btn small">Bekijk recept</a>
        </div>

    <?php
        endwhile;
    else:
    ?>

        <p style="text-align:center; width:100%;">Geen recepten gevonden.</p>

    <?php endif; ?>

</div>

<!-- FOOTER -->
<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=2"></script>
</body>
</html>