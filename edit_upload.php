<?php
session_start();
require "db.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check login
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

// Check ID
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Ongeldig recept ID.");
}

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
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Recept niet gevonden.");
}

$recept = $result->fetch_assoc();

// Check of ingelogde user eigenaar is
if ($recept["email"] !== $_SESSION["user"]) {
    die("Je mag dit recept niet bewerken.");
}

$error = "";
$success = "";

// UPDATE LOGICA
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titel = trim($_POST["titel"]);
    $beschrijving = trim($_POST["beschrijving"]);
    $ingredienten = trim($_POST["ingredienten"]);
    $bereiding = trim($_POST["bereiding"]);

    $imagePath = $recept["afbeelding"]; // behoud oude afbeelding

    // Nieuwe afbeelding?
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
                $error = "Afbeelding uploaden mislukt.";
            }
        }
    }

    if (!$error) {
        $update = $conn->prepare("
            UPDATE recepten
            SET titel=?, beschrijving=?, ingredienten=?, bereiding=?, afbeelding=?
            WHERE id=?
        ");

        $update->bind_param("sssssi", 
            $titel, 
            $beschrijving, 
            $ingredienten, 
            $bereiding, 
            $imagePath, 
            $id
        );

        if ($update->execute()) {
            $success = "Recept succesvol bijgewerkt!";
            // Refresh data
            $recept["titel"] = $titel;
            $recept["beschrijving"] = $beschrijving;
            $recept["ingredienten"] = $ingredienten;
            $recept["bereiding"] = $bereiding;
            $recept["afbeelding"] = $imagePath;
        } else {
            $error = "Database fout: " . $update->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept Bewerken – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Recept Bewerken</h2>

<div class="form-container" style="max-width:700px;">

    <?php if ($error): ?>
        <p style="color:red; font-weight:600;"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green; font-weight:600;"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="titel" value="<?= htmlspecialchars($recept['titel']) ?>" required>

        <textarea name="beschrijving" rows="3" required><?= htmlspecialchars($recept['beschrijving']) ?></textarea>

        <textarea name="ingredienten" rows="5" required><?= htmlspecialchars($recept['ingredienten']) ?></textarea>

        <textarea name="bereiding" rows="6" required><?= htmlspecialchars($recept['bereiding']) ?></textarea>

        <label style="font-weight:600; margin-bottom:5px; display:block;">Huidige afbeelding:</label>

        <?php if ($recept["afbeelding"]): ?>
            <img src="<?= $recept['afbeelding'] ?>" style="width:200px; border-radius:12px; margin-bottom:10px;">
        <?php else: ?>
            <p>Geen afbeelding geüpload.</p>
        <?php endif; ?>

        <label style="font-weight:600; margin-top:10px; display:block;">Nieuwe afbeelding (optioneel):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" class="btn" style="width:100%; margin-top:15px;">Recept Opslaan</button>
    </form>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

</body>
</html>
// einde T
