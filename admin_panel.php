<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require "db.php";

if (!isset($_SESSION["admin"])) {
    die("Geen toegang.");
}

// Statistieken ophalen
$users              = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()["c"];
$recepten_count     = $conn->query("SELECT COUNT(*) AS c FROM recepten")->fetch_assoc()["c"];
$likes_total        = $conn->query("SELECT SUM(likes) AS c FROM recepten")->fetch_assoc()["c"] ?? 0;
$reports_count      = $conn->query("SELECT COUNT(*) AS c FROM reports")->fetch_assoc()["c"];
$tags_count         = $conn->query("SELECT COUNT(*) AS c FROM tags")->fetch_assoc()["c"];
$spec_count         = $conn->query("SELECT COUNT(*) AS c FROM specialiteiten")->fetch_assoc()["c"];
$subtags_count      = $conn->query("SELECT COUNT(*) AS c FROM subtags")->fetch_assoc()["c"];

// Recepten ophalen
$recepten = $conn->query("
    SELECT r.*, u.email 
    FROM recepten r
    LEFT JOIN users u ON r.user_id = u.id
    ORDER BY r.id DESC
");

// Rapporten ophalen
$reports = $conn->query("SELECT * FROM reports ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel – Receptify</title>
    <link rel="stylesheet" href="style.css?v=100">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Admin Dashboard</h2>

<div class="admin-container">

    <!-- BOVENSTE KNOPPEN -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <a href="admin_tags.php" class="btn small" style="background:#5fa8ff; margin-right:10px;">Tag beheer</a>
            <a href="admin_specialiteiten.php" class="btn small" style="background:#5fa8ff; margin-right:10px;">Specialiteiten beheer</a>
            <a href="admin_subtags.php" class="btn small" style="background:#5fa8ff;">Ingrediënten beheer</a>
        </div>

        <a href="admin_logout.php" class="admin-logout">Uitloggen</a>
    </div>

    <!-- DASHBOARD -->
    <div class="dashboard">
        <div class="dash-card">👤 Gebruikers<br><strong><?= $users ?></strong></div>
        <div class="dash-card">📚 Recepten<br><strong><?= $recepten_count ?></strong></div>
        <div class="dash-card">❤️ Likes totaal<br><strong><?= $likes_total ?></strong></div>
        <div class="dash-card">🚨 Rapporten<br><strong><?= $reports_count ?></strong></div>
        <div class="dash-card">🏷️ Tags<br><strong><?= $tags_count ?></strong></div>
        <div class="dash-card">🔥 Specialiteiten<br><strong><?= $spec_count ?></strong></div>
        <div class="dash-card">🥕 Ingrediënten<br><strong><?= $subtags_count ?></strong></div>
    </div>

    <!-- RECEPTEN -->
    <h3 class="section-title">Recepten</h3>

    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Titel</th>
            <th>Uploader</th>
            <th>Likes</th>
            <th>Tag</th>
            <th>Acties</th>
        </tr>

        <?php while ($r = $recepten->fetch_assoc()): ?>

        <?php
            // Tag ophalen
            $tag_naam = "Onbekend";
            if (!empty($r["tag_id"])) {
                $tag = $conn->query("SELECT naam FROM tags WHERE id = " . intval($r["tag_id"]));
                if ($tag && $tag->num_rows > 0) {
                    $tag_naam = $tag->fetch_assoc()["naam"];
                }
            }
        ?>

        <tr>
            <td><?= $r["id"] ?></td>
            <td><?= htmlspecialchars($r["titel"]) ?></td>
            <td><?= htmlspecialchars($r["email"] ?? "Onbekend") ?></td>
            <td><?= $r["likes"] ?></td>
            <td><?= htmlspecialchars($tag_naam) ?></td>
            <td>
                <a class="admin-btn view" href="recept.php?id=<?= $r['id'] ?>">Bekijken</a>
                <a class="admin-btn edit" href="edit_recept.php?id=<?= $r['id'] ?>">Bewerken</a>
                <a class="admin-btn delete" href="delete_recept_admin.php?id=<?= $r['id'] ?>"
                   onclick="return confirm('Recept verwijderen?');">
                   Verwijderen
                </a>
            </td>
        </tr>

        <?php endwhile; ?>
    </table>

    <!-- RAPPORTAGES -->
    <h3 class="section-title">Rapportages</h3>

    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Recept ID</th>
            <th>Reden</th>
            <th>Datum</th>
            <th>Actie</th>
        </tr>

        <?php while ($rep = $reports->fetch_assoc()): ?>
        <tr>
            <td><?= $rep["id"] ?></td>
            <td><?= $rep["recept_id"] ?></td>
            <td><?= htmlspecialchars($rep["reason"]) ?></td>
            <td><?= $rep["reported_at"] ?></td>
            <td>
                <a class="admin-btn delete" href="delete_recept_admin.php?id=<?= $rep['recept_id'] ?>"
                   onclick="return confirm('Recept verwijderen?');">
                    Verwijder recept
                </a>
                <a class="admin-btn edit" href="remove_report.php?id=<?= $rep['id'] ?>">
                    Verwijder rapport
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>
// einde T
