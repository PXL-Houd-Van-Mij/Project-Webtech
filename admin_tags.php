<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin"])) die("Geen toegang.");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $naam = trim($_POST["naam"]);
    if ($naam !== "") {
        $stmt = $conn->prepare("INSERT INTO tags (naam) VALUES (?)");
        $stmt->bind_param("s", $naam);
        $stmt->execute();
    }
}

$tags = $conn->query("SELECT * FROM tags ORDER BY naam ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tag Beheer</title>
    <link rel="stylesheet" href="style.css?v=90">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Tag Beheer</h2>

<div class="admin-container">

    <form method="POST">
        <input type="text" name="naam" placeholder="Nieuwe tag (bv. Vlees)" required>
        <button class="btn small">Toevoegen</button>
    </form>

    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Actie</th>
        </tr>

        <?php while ($t = $tags->fetch_assoc()): ?>
        <tr>
            <td><?= $t["id"] ?></td>
            <td><?= htmlspecialchars($t["naam"]) ?></td>
            <td>
                <a class="admin-btn delete" href="delete_tag.php?id=<?= $t['id'] ?>"
                   onclick="return confirm('Tag verwijderen?');">
                    Verwijderen
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>
