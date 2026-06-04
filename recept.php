<?php
session_start();
require "db.php";

// Check ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Ongeldig recept.");
}

$id = intval($_GET['id']);

// Recept ophalen
$stmt = $conn->prepare("SELECT * FROM recepten WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Recept niet gevonden.");
}

$recept = $result->fetch_assoc();

// Check of gebruiker al geliked heeft
$isFav = false;

if (isset($_SESSION["user"])) {
    $checkFav = $conn->prepare("
        SELECT id FROM favorieten 
        WHERE user_id=(SELECT id FROM users WHERE email=?) 
        AND recept_id=? LIMIT 1
    ");
    $checkFav->bind_param("si", $_SESSION["user"], $id);
    $checkFav->execute();
    $checkFav->store_result();

    if ($checkFav->num_rows > 0) {
        $isFav = true;
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recept['titel']) ?> – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title"><?= htmlspecialchars($recept['titel']) ?></h2>

<div class="form-container" style="max-width:700px;">

    <h3 style="color:#ff5fa2;">Beschrijving</h3>
    <p><?= nl2br(htmlspecialchars($recept['beschrijving'])) ?></p>

    <h3 style="color:#ff5fa2; margin-top:20px;">Ingrediënten</h3>
    <p style="white-space:pre-line;"><?= htmlspecialchars($recept['ingredienten']) ?></p>

    <h3 style="color:#ff5fa2; margin-top:20px;">Bereiding</h3>
    <p style="white-space:pre-line;"><?= htmlspecialchars($recept['bereiding']) ?></p>

    <!-- LIKE BUTTON -->
    <div id="like-btn"
         data-id="<?= $recept['id'] ?>"
         class="like-heart <?= $isFav ? 'active' : '' ?>">
        ❤️
    </div>

    <p id="like-count" style="font-weight:600;">
        <?= $recept['likes'] ?> likes
    </p>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=1"></script>
</body>
</html>