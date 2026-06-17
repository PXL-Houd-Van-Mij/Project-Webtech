<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin"])) die("Geen toegang.");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $naam = trim($_POST["naam"]);
    if ($naam !== "") {
        $stmt = $conn->prepare("INSERT INTO subtags (naam) VALUES (?)");
        $stmt->bind_param("s", $naam);
        $stmt->execute();
    }
}

$subtags = $conn->query("SELECT * FROM subtags ORDER BY naam ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ingrediënten (Subtags) Beheer</title>
    <link rel="stylesheet" href="style.css?v=90">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Ingrediënten Beheer</h2>

<div class="admin-container">

    <form method="POST">
        <input type="text" name="naam" placeholder="Nieuw ingrediënt (bv. Tomaat)" required>
        <button class="btn small">Toevoegen</button>
    </form>

    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Ingrediënt</th>
            <th>Actie</th>
        </tr>

        <?php while ($t = $subtags->fetch_assoc()): ?>
        <tr>
            <td><?= $t["id"] ?></td>
            <td><?= htmlspecialchars($t["naam"]) ?></td>
            <td>
                <a class="admin-btn delete" href="delete_subtag.php?id=<?= $t['id'] ?>"
                   onclick="return confirm('Ingrediënt verwijderen?');">
                    Verwijderen
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>
// IOT
