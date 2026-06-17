<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require "db.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) die("Ongeldig ID.");

$id = intval($_GET["id"]);

// Recept ophalen
$stmt = $conn->prepare("SELECT * FROM recepten WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$recept = $stmt->get_result()->fetch_assoc();

if (!$recept) die("Recept niet gevonden.");

// Check eigenaar/admin
$isAdmin = isset($_SESSION["admin"]);
$isOwner = false;

if (isset($_SESSION["user"])) {
    $u = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $u->bind_param("s", $_SESSION["user"]);
    $u->execute();
    $user_id = $u->get_result()->fetch_assoc()["id"];
    if ($user_id == $recept["user_id"]) $isOwner = true;
}

if (!$isAdmin && !$isOwner) die("Geen rechten.");

// Huidige specialiteiten
$currentSpecs = [];
$specQ = $conn->query("SELECT specialiteit_id FROM recept_specialiteiten WHERE recept_id = $id");
while ($row = $specQ->fetch_assoc()) $currentSpecs[] = $row["specialiteit_id"];

// Huidige subtags
$currentSubs = [];
$subQ = $conn->query("SELECT subtag_id FROM recept_subtags WHERE recept_id = $id");
while ($row = $subQ->fetch_assoc()) $currentSubs[] = $row["subtag_id"];

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Null-safe POST values
    $titel = trim($_POST["titel"] ?? "");
    $beschrijving = trim($_POST["beschrijving"] ?? "");
    $ingredienten = trim($_POST["ingredienten"] ?? "");
    $bereiding = trim($_POST["bereiding"] ?? "");
    $tijd = intval($_POST["tijd"] ?? 0);
    $tools = trim($_POST["tools"] ?? "");
    $personen = intval($_POST["personen"] ?? 0);

    // Tag null-safe
    $tag_id = isset($_POST["tag"]) ? intval($_POST["tag"]) : null;

    if ($tag_id === null) {
        $error = "Je moet een tag selecteren.";
    }

    // Afbeelding
    $imagePath = $recept["afbeelding"];

    if (!empty($_FILES["image"]["name"])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        $allowed = ["jpg", "jpeg", "png"];
        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            }
        }
    }

    if (!$error) {

        // Update recept
        $stmt = $conn->prepare("
            UPDATE recepten 
            SET titel=?, beschrijving=?, ingredienten=?, bereiding=?, afbeelding=?, tijd=?, tools=?, personen=?, tag_id=?
            WHERE id=?
        ");
        $stmt->bind_param("sssssisiii",
            $titel, $beschrijving, $ingredienten, $bereiding, $imagePath,
            $tijd, $tools, $personen,
            $tag_id, $id
        );
        $stmt->execute();

        // Specialiteiten resetten
        $conn->query("DELETE FROM recept_specialiteiten WHERE recept_id = $id");
        if (!empty($_POST["specialiteiten"])) {
            foreach ($_POST["specialiteiten"] as $spec_id) {
                $ins = $conn->prepare("INSERT INTO recept_specialiteiten (recept_id, specialiteit_id) VALUES (?, ?)");
                $ins->bind_param("ii", $id, $spec_id);
                $ins->execute();
            }
        }

        // Subtags resetten
        $conn->query("DELETE FROM recept_subtags WHERE recept_id = $id");
        if (!empty($_POST["subtags"])) {
            foreach ($_POST["subtags"] as $subtag_id) {
                $ins = $conn->prepare("INSERT INTO recept_subtags (recept_id, subtag_id) VALUES (?, ?)");
                $ins->bind_param("ii", $id, $subtag_id);
                $ins->execute();
            }
        }

        $success = "Recept succesvol bijgewerkt!";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Recept Bewerken – Receptify</title>
    <link rel="stylesheet" href="style.css?v=105">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Recept Bewerken</h2>

<div class="form-container">

    <?php if ($error): ?><p style="color:red;"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green;"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="text" name="titel" value="<?= htmlspecialchars($recept['titel'] ?? '') ?>" required>

        <textarea name="beschrijving" rows="3" required><?= htmlspecialchars($recept['beschrijving'] ?? '') ?></textarea>

        <textarea name="ingredienten" rows="5" required><?= htmlspecialchars($recept['ingredienten'] ?? '') ?></textarea>

        <textarea name="bereiding" rows="6" required><?= htmlspecialchars($recept['bereiding'] ?? '') ?></textarea>

        <input type="number" name="tijd" value="<?= htmlspecialchars($recept['tijd'] ?? '') ?>" required>

        <input type="text" name="tools" value="<?= htmlspecialchars($recept['tools'] ?? '') ?>" required>

        <input type="number" name="personen" value="<?= htmlspecialchars($recept['personen'] ?? '') ?>" required>

        <!-- TAGS -->
        <label style="font-weight:600;">Tag (soort gerecht):</label>
        <div class="tag-box">
        <?php
        $tags = $conn->query("SELECT * FROM tags ORDER BY naam ASC");
        while ($t = $tags->fetch_assoc()):
        ?>
            <label class="tag-check">
                <input type="radio" name="tag" value="<?= $t['id'] ?>"
                    <?= ($t['id'] == ($recept['tag_id'] ?? -1)) ? "checked" : "" ?>>
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
                <input type="checkbox" name="specialiteiten[]" value="<?= $s['id'] ?>"
                    <?= in_array($s['id'], $currentSpecs) ? "checked" : "" ?>>
                <?= htmlspecialchars($s['naam'] ?? '') ?>
            </label>
        <?php endwhile; ?>
        </div>

        <!-- SUBTAGS -->
        <label style="font-weight:600;">Ingrediënten:</label>
        <div class="tag-box">
        <?php
        $subs = $conn->query("SELECT * FROM subtags ORDER BY naam ASC");
        while ($sb = $subs->fetch_assoc()):
        ?>
            <label class="tag-check">
                <input type="checkbox" name="subtags[]" value="<?= $sb['id'] ?>"
                    <?= in_array($sb['id'], $currentSubs) ? "checked" : "" ?>>
                <?= htmlspecialchars($sb['naam'] ?? '') ?>
            </label>
        <?php endwhile; ?>
        </div>

        <!-- AFBEELDING -->
        <label style="font-weight:600;">Nieuwe afbeelding (optioneel):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" class="btn" style="width:100%; margin-top:15px;">Opslaan</button>
    </form>

</div>

<footer>Gemaakt door Tom, Luuk en Stef.</footer>

</body>
</html>