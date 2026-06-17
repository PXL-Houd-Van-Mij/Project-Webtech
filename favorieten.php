<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require "db.php";

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

// User ID ophalen
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($uid);
$stmt->fetch();

if ($stmt->num_rows === 0) die("Gebruiker niet gevonden.");

// Favorieten ophalen
$q = $conn->prepare("
    SELECT r.id, r.titel, r.beschrijving, r.likes, r.afbeelding
    FROM favorieten f
    JOIN recepten r ON f.recept_id = r.id
    WHERE f.user_id = ?
    ORDER BY r.titel ASC
");
$q->bind_param("i", $uid);
$q->execute();
$result = $q->get_result();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Favorieten – Receptify</title>
    <link rel="stylesheet" href="style.css?v=110">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Jouw Favorieten</h2>

<div class="recipe-container">

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="recipe-card">
            <?php if (!empty($row["afbeelding"])): ?>
                <img src="<?= htmlspecialchars($row['afbeelding']) ?>" class="recipe-thumb">
            <?php endif; ?>

            <h3><?= htmlspecialchars($row['titel']) ?></h3>
            <p><?= htmlspecialchars($row['beschrijving']) ?></p>
            <p class="likes">❤️ <?= $row['likes'] ?></p>

            <a href="recept.php?id=<?= $row['id'] ?>" class="btn small">Bekijk recept</a>
        </div>
    <?php endwhile; ?>

<?php else: ?>
    <p style="text-align:center; width:100%;">Je hebt nog geen favorieten.</p>
<?php endif; ?>

</div>

<footer>Gemaakt door Tom, Luuk en Stef.</footer>

</body>
</html>
