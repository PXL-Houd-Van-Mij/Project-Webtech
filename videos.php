<?php
session_start();
require "db.php";

// Lijst met recept-video's (YouTube embed-id's).
// Wil je er één toevoegen? Kopieer de code achter "watch?v=" uit de YouTube-URL.
$videos = [
    [
        "titel" => "Verse pasta van nul",
        "beschrijving" => "Leer stap voor stap zelf pasta maken.",
        "youtube" => "hwVnYKrt59A",
    ],
    [
        "titel" => "Perfecte pannenkoeken",
        "beschrijving" => "Luchtige pannenkoeken met maar 3 ingrediënten.",
        "youtube" => "_VR8BYxLhbQ",
    ],
    [
        "titel" => "Zelfgemaakte pizza",
        "beschrijving" => "Een krokante pizzabodem zoals bij de Italiaan.",
        "youtube" => "1VCZPayXxEw",
    ],
    [
        "titel" => "Chocolade brownies",
        "beschrijving" => "Smeuïge brownies die altijd lukken.",
        "youtube" => "8bi1MaUuzcQ",
    ],
];
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video's – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Recept Video's</h2>

<div class="recipe-container">

<?php foreach ($videos as $video): ?>

    <div class="recipe-card">
        <h3><?= htmlspecialchars($video['titel']) ?></h3>

        <div class="video-wrapper">
            <iframe
                src="https://www.youtube-nocookie.com/embed/<?= htmlspecialchars($video['youtube']) ?>"
                title="<?= htmlspecialchars($video['titel']) ?>"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>

        <p><?= htmlspecialchars($video['beschrijving']) ?></p>
    </div>

<?php endforeach; ?>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=1"></script>
</body>
</html>
// IOT
