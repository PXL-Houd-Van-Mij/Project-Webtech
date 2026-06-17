<?php
session_start();
require "db.php";

if (!isset($_GET["id"])) die("Geen tag ID.");

$tag_id = intval($_GET["id"]);

$tag = $conn->query("SELECT * FROM tags WHERE id = $tag_id")->fetch_assoc();

$recepten = $conn->query("
    SELECT r.* FROM recepten r
    JOIN recept_tags rt ON rt.recept_id = r.id
    WHERE rt.tag_id = $tag_id
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recepten met tag <?= htmlspecialchars($tag['naam']) ?></title>
    <link rel="stylesheet" href="style.css?v=30">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Recepten met tag: <?= htmlspecialchars($tag['naam']) ?></h2>

<div class="recipe-container">
<?php while ($r = $recepten->fetch_assoc()): ?>
    <div class="recipe-card">
        <h3><?= htmlspecialchars($r["titel"]) ?></h3>
        <a class="btn small" href="recept.php?id=<?= $r['id'] ?>">Bekijk</a>
    </div>
<?php endwhile; ?>
</div>

</body>
</html>
// einde T
