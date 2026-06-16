<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require "db.php";

$error = "";
$success = "";

// Alleen ingelogde gebruikers mogen uploaden
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titel = trim($_POST["titel"]);
    $beschrijving = trim($_POST["beschrijving"]);
    $ingredienten = trim($_POST["ingredienten"]);
    $bereiding = trim($_POST["bereiding"]);

    // Afbeelding uploaden
    $imagePath = null;

    if (!empty($_FILES["image"]["name"])) {

        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        $allowed = ["jpg", "jpeg", "png"];
        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Alleen JPG, JPEG en PNG zijn toegestaan.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            } else {
                $error = "Afbeelding uploaden mislukt. (move_uploaded_file faalt)";
            }
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("
            INSERT INTO recepten (titel, beschrijving, ingredienten, bereiding, afbeelding, likes, specialiteit)
            VALUES (?, ?, ?, ?, ?, 0, 0)
        ");

        if (!$stmt) {
            die("SQL fout: " . $conn->error);
        }

        $stmt->bind_param("sssss", $titel, $beschrijving, $ingredienten, $bereiding, $imagePath);

        if ($stmt->execute()) {
            $success = "Recept succesvol toegevoegd!";
        } else {
            $error = "Database fout: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept Uploaden – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Nieuw Recept Uploaden</h2>

<div class="form-container">

    <?php if ($error): ?>
        <p style="color:red; font-weight:600; margin-bottom:10px;"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green; font-weight:600; margin-bottom:10px;"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="titel" placeholder="Titel van het recept" required>

        <textarea name="beschrijving" placeholder="Korte beschrijving" rows="3" required></textarea>

        <textarea name="ingredienten" placeholder="Ingrediënten (één per lijn)" rows="5" required></textarea>

        <textarea name="bereiding" placeholder="Bereiding (stappen)" rows="6" required></textarea>

        <label style="font-weight:600; margin-bottom:5px; display:block;">Afbeelding (optioneel):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" class="btn" style="width:100%; margin-top:15px;">Recept Uploaden</button>
    </form>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

</body>
</html>
