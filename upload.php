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

// User ID ophalen
$userQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$userQuery->bind_param("s", $_SESSION["user"]);
$userQuery->execute();
$user_id = $userQuery->get_result()->fetch_assoc()["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titel = trim($_POST["titel"] ?? "");
    $beschrijving = trim($_POST["beschrijving"] ?? "");
    $ingredienten = trim($_POST["ingredienten"] ?? "");
    $bereiding = trim($_POST["bereiding"] ?? "");
    $tijd = intval($_POST["tijd"] ?? 0);
    $tools = trim($_POST["tools"] ?? "");
    $personen = intval($_POST["personen"] ?? 0);

    $tag_id = isset($_POST["tag"]) ? intval($_POST["tag"]) : null;

    if ($tag_id === null) {
        $error = "Je moet een tag selecteren.";
    }

    // Afbeelding uploaden
    $imagePath = null;

    if (!empty($_FILES["image"]["name"])) {

        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

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

        $stmt = $conn->prepare("
            INSERT INTO recepten
            (titel, beschrijving, ingredienten, bereiding, afbeelding, tijd, tools, personen, likes, tag_id, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)
        ");

        $stmt->bind_param("sssssisiii",
            $titel, $beschrijving, $ingredienten, $bereiding, $imagePath,
            $tijd, $tools, $personen,
            $tag_id, $user_id
        );

        if ($stmt->execute()) {

            $recept_id = $stmt->insert_id;

            // Specialiteiten opslaan
            if (!empty($_POST["specialiteiten"])) {
                foreach ($_POST["specialiteiten"] as $spec_id) {
                    $ins = $conn->prepare("INSERT INTO recept_specialiteiten (recept_id, specialiteit_id) VALUES (?, ?)");
                    $ins->bind_param("ii", $recept_id, $spec_id);
                    $ins->execute();
                }
            }

            // Subtags opslaan
            if (!empty($_POST["subtags"])) {
                foreach ($_POST["subtags"] as $subtag_id) {
                    $ins = $conn->prepare("INSERT INTO recept_subtags (recept_id, subtag_id) VALUES (?, ?)");
                    $ins->bind_param("ii", $recept_id, $subtag_id);
                    $ins->execute();
                }
            }

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
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Nieuw Recept Uploaden</h2>

<div class="form-container">

    <?php if ($error): ?><p style="color:red; font-weight:600; margin-bottom:10px;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green; font-weight:600; margin-bottom:10px;"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="titel" placeholder="Titel van het recept" required>

        <textarea name="beschrijving" placeholder="Korte beschrijving" rows="3" required></textarea>

        <textarea name="ingredienten" placeholder="Ingrediënten (één per lijn)" rows="5" required></textarea>

        <textarea name="bereiding" placeholder="Bereiding (stappen)" rows="6" required></textarea>

        <input type="number" name="tijd" placeholder="Tijd in minuten (bv. 30)" required>

        <input type="text" name="tools" placeholder="Benodigde tools (bv. pan, oven)" required>

        <input type="number" name="personen" placeholder="Aantal personen (bv. 4)" required>

        <!-- TAGS -->
        <label style="font-weight:600;">Tag (soort gerecht):</label>
        <div class="tag-box">
        <?php
        $tags = $conn->query("SELECT * FROM tags ORDER BY naam ASC");
        while ($t = $tags->fetch_assoc()):
        ?>
            <label class="tag-check">
                <input type="radio" name="tag" value="<?= $t['id'] ?>" required>
                <?= htmlspecialchars($t['naam'] ?? '') ?>
            </label>
        <?php endwhile; ?>
        </div>

        <!-- SPECIALITEITEN -->
        <label style="font-weight:600;">Specialiteiten:</label>
        <div class="tag-box">
        <?php
        $specs = $conn->query("SELECT * FROM specialiteiten ORDER BY naam ASC");
        while ($s = $specs->fetch_assoc()):
        ?>
            <label class="tag-check">
                <input type="checkbox" name="specialiteiten[]" value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['naam'] ?? '') ?>
            </label>
        <?php endwhile; ?>
        </div>

        <!-- SUBTAGS / INGREDIËNTEN -->
        <label style="font-weight:600;">Ingrediënten (selecteer):</label>
        <div class="tag-box">
        <?php
        $subs = $conn->query("SELECT * FROM subtags ORDER BY naam ASC");
        while ($sb = $subs->fetch_assoc()):
        ?>
            <label class="tag-check">
                <input type="checkbox" name="subtags[]" value="<?= $sb['id'] ?>">
                <?= htmlspecialchars($sb['naam'] ?? '') ?>
            </label>
        <?php endwhile; ?>
        </div>

        <!-- AFBEELDING -->
        <label style="font-weight:600;">Afbeelding (optioneel):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" class="btn" style="width:100%; margin-top:15px;">Recept Uploaden</button>
    </form>

</div>

<footer>Gemaakt door Tom, Luuk en Stef.</footer>

<script src="script.js?v=2"></script>
</body>
</html>
