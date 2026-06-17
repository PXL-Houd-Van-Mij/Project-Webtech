<?php
session_start();
require "db.php";

if (!isset($_SESSION["admin"])) die("Geen toegang.");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $naam = trim($_POST["naam"]);
    if ($naam !== "") {
        $stmt = $conn->prepare("INSERT INTO specialiteiten (naam) VALUES (?)");
        $stmt->bind_param("s", $naam);
        $stmt->execute();
    }
}

$specialiteiten = $conn->query("SELECT * FROM specialiteiten ORDER BY naam ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Specialiteiten Beheer</title>
    <link rel="stylesheet" href="style.css?v=90">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Specialiteiten Beheer</h2>

<div class="admin-container">

    <form method="POST">
        <input type="text" name="naam" placeholder="Nieuwe specialiteit (bv. Oven)" required>
        <button class="btn small">Toevoegen</button>
    </form>

    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Actie</th>
        </tr>

        <?php while ($s = $specialiteiten->fetch_assoc()): ?>
        <tr>
            <td><?= $s["id"] ?></td>
            <td><?= htmlspecialchars($s["naam"]) ?></td>
            <td>
                <a class="admin-btn delete" href="delete_specialiteit.php?id=<?= $s['id'] ?>"
                   onclick="return confirm('Specialiteit verwijderen?');">
                    Verwijderen
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>