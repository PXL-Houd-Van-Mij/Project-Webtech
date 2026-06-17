<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require "db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) die("Ongeldig recept ID.");

$id = intval($_GET["id"]);

// Recept ophalen
$stmt = $conn->prepare("
    SELECT r.*, u.email 
    FROM recepten r 
    LEFT JOIN users u ON r.user_id = u.id 
    WHERE r.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$recept = $stmt->get_result()->fetch_assoc();

if (!$recept) die("Recept niet gevonden.");

// Check eigenaar/admin
$isOwner = false;
$isAdmin = isset($_SESSION["admin"]);

if (isset($_SESSION["user"])) {
    $u = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $u->bind_param("s", $_SESSION["user"]);
    $u->execute();
    $user_id = $u->get_result()->fetch_assoc()["id"];

    if ($user_id == $recept["user_id"]) {
        $isOwner = true;
    }
}

// Tag ophalen
$tag_naam = "Onbekend";
if (!empty($recept["tag_id"])) {
    $tag = $conn->query("SELECT naam FROM tags WHERE id = " . intval($recept["tag_id"]));
    if ($tag && $tag->num_rows > 0) {
        $tag_naam = $tag->fetch_assoc()["naam"];
    }
}

// Specialiteiten ophalen
$specialiteiten = $conn->query("
    SELECT s.naam 
    FROM specialiteiten s
    JOIN recept_specialiteiten rs ON rs.specialiteit_id = s.id
    WHERE rs.recept_id = $id
");

// Subtags ophalen
$subtags = $conn->query("
    SELECT sb.naam 
    FROM subtags sb
    JOIN recept_subtags rs ON rs.subtag_id = sb.id
    WHERE rs.recept_id = $id
");

// Check of user al geliked heeft
$liked = false;
if (isset($_SESSION["user"])) {
    $l = $conn->prepare("
        SELECT likes.id 
        FROM likes 
        JOIN users ON users.id = likes.user_id
        WHERE users.email = ? AND likes.recept_id = ?
    ");
    $l->bind_param("si", $_SESSION["user"], $id);
    $l->execute();
    $liked = $l->get_result()->num_rows > 0;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($recept["titel"] ?? '') ?> – Receptify</title>
    <link rel="stylesheet" href="style.css?v=103">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title"><?= htmlspecialchars($recept["titel"] ?? '') ?></h2>

<div class="recipe-detail">

    <!-- AFBEELDING -->
    <?php if (!empty($recept["afbeelding"])): ?>
        <img src="<?= htmlspecialchars($recept['afbeelding'] ?? '') ?>" class="recipe-image">
    <?php endif; ?>

    <!-- INFO BOX -->
    <div class="recipe-info-box">
        <p><strong>Tijd:</strong> <?= htmlspecialchars($recept['tijd'] ?? '') ?> minuten</p>
        <p><strong>Tools:</strong> <?= htmlspecialchars($recept['tools'] ?? '') ?></p>
        <p><strong>Personen:</strong> <?= htmlspecialchars($recept['personen'] ?? '') ?></p>
        <p><strong>Tag:</strong> <?= htmlspecialchars($tag_naam ?? '') ?></p>
    </div>

    <!-- SPECIALITEITEN -->
    <h3 class="recipe-subtitle">Specialiteiten</h3>
    <div class="tag-box">
        <?php while ($s = $specialiteiten->fetch_assoc()): ?>
            <span class="tag-pill"><?= htmlspecialchars($s["naam"] ?? '') ?></span>
        <?php endwhile; ?>
    </div>

    <!-- SUBTAGS / INGREDIËNTEN -->
    <h3 class="recipe-subtitle">Ingrediënten</h3>
    <div class="tag-box">
        <?php while ($sb = $subtags->fetch_assoc()): ?>
            <span class="tag-pill"><?= htmlspecialchars($sb["naam"] ?? '') ?></span>
        <?php endwhile; ?>
    </div>

    <!-- LIKE -->
    <div style="text-align:center; margin:15px 0;">
        <form method="POST" action="like_toggle.php">
            <input type="hidden" name="id" value="<?= $recept['id'] ?>">
            <button type="submit" class="like-heart <?= $liked ? 'active' : '' ?>">
                ❤️ <?= htmlspecialchars($recept["likes"] ?? 0) ?>
            </button>
        </form>
    </div>

    <!-- RAPPORTEREN -->
    <div style="text-align:center; margin-bottom:20px;">
        <form method="POST" action="report_recept.php">
            <input type="hidden" name="id" value="<?= $recept['id'] ?>">
            <button type="submit" class="btn small" style="background:#ff5f5f;">
                Rapporteer recept
            </button>
        </form>
    </div>

    <!-- BEWERKEN (ADMIN + EIGENAAR) -->
    <?php if ($isOwner || $isAdmin): ?>
    <div style="text-align:center; margin-bottom:20px;">
        <a href="edit_recept.php?id=<?= $recept['id'] ?>" class="btn small" style="background:#5fa8ff;">
            Recept bewerken
        </a>
    </div>
    <?php endif; ?>

    <!-- BESCHRIJVING -->
    <h3 class="recipe-subtitle">Beschrijving</h3>
    <p><?= nl2br(htmlspecialchars($recept["beschrijving"] ?? '')) ?></p>

    <!-- INGREDIËNTEN (VOLLEDIGE LIJST) -->
    <h3 class="recipe-subtitle">Ingrediëntenlijst</h3>
    <p><?= nl2br(htmlspecialchars($recept["ingredienten"] ?? '')) ?></p>

    <!-- BEREIDING -->
    <h3 class="recipe-subtitle">Bereiding</h3>
    <p><?= nl2br(htmlspecialchars($recept["bereiding"] ?? '')) ?></p>

    <!-- UPLOADER -->
    <p class="recipe-uploader">
        Geüpload door: <?= htmlspecialchars($recept["email"] ?? 'Onbekend') ?>
    </p>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

</body>
</html>
// einde T
