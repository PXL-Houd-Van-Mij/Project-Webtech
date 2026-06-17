<?php
session_start();
require "db.php";

// Alleen ingelogde gebruikers
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

// User ID en aanmakingsdatum ophalen
$stmt = $conn->prepare("SELECT id, created_at FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $_SESSION["user"]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($uid, $created_at);
$stmt->fetch();

if ($stmt->num_rows === 0) {
    die("Gebruiker niet gevonden.");
}

// Eigen geüploade recepten ophalen
$recepten_q = $conn->prepare("
    SELECT id, titel, beschrijving, likes 
    FROM recepten 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$recepten_q->bind_param("i", $uid);
$recepten_q->execute();
$recepten_result = $recepten_q->get_result();
$eigen_recepten = $recepten_result->num_rows;

// Favorieten ophalen
$fav_q = $conn->prepare("
    SELECT r.id, r.titel, r.beschrijving, r.likes 
    FROM favorieten f
    JOIN recepten r ON f.recept_id = r.id
    WHERE f.user_id = ?
    ORDER BY r.titel ASC
");
$fav_q->bind_param("i", $uid);
$fav_q->execute();
$fav_result = $fav_q->get_result();
$fav_count = $fav_result->num_rows;

// Datum formatteren
$lid_sinds = date("d F Y", strtotime($created_at));
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Profiel – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
    <style>
        .profiel-header {
            background: #ffe3f4;
            border-bottom: 4px solid #ffb7e0;
            padding: 40px 20px;
            text-align: center;
        }

        .avatar {
            width: 90px;
            height: 90px;
            background: #ff8fcf;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 15px;
            border: 4px solid white;
            box-shadow: 0 4px 0 #d96aaa;
        }

        .profiel-header h2 {
            color: #ff5fa2;
            font-size: 26px;
            margin-bottom: 5px;
        }

        .profiel-header p {
            color: #888;
            font-size: 14px;
        }

        .stats-row {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 25px auto;
            max-width: 500px;
            flex-wrap: wrap;
        }

        .stat-box {
            background: white;
            border: 3px solid #ff8fcf;
            border-radius: 16px;
            box-shadow: 0 4px 0 #ffb7e0;
            padding: 18px 30px;
            text-align: center;
            min-width: 120px;
        }

        .stat-box .getal {
            font-size: 32px;
            font-weight: 700;
            color: #ff5fa2;
        }

        .stat-box .label {
            font-size: 13px;
            color: #888;
            margin-top: 4px;
        }

        .tabs {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px auto 10px;
        }

        .tab-btn {
            padding: 10px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            background: #ffe3f4;
            color: #ff5fa2;
            border: 2px solid #ff8fcf;
            transition: 0.2s;
        }

        .tab-btn.active {
            background: #ff8fcf;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .empty-msg {
            text-align: center;
            width: 100%;
            color: #aaa;
            font-size: 16px;
            padding: 20px;
        }
    </style>
</head>
<body>

<?php include "navbar.php"; ?>

<!-- PROFIEL HEADER -->
<div class="profiel-header">
    <div class="avatar">👤</div>
    <h2><?= htmlspecialchars($_SESSION["user"]) ?></h2>
    <p>Lid sinds <?= $lid_sinds ?></p>
</div>

<!-- STATISTIEKEN -->
<div class="stats-row">
    <div class="stat-box">
        <div class="getal"><?= $eigen_recepten ?></div>
        <div class="label">Recepten geplaatst</div>
    </div>
    <div class="stat-box">
        <div class="getal"><?= $fav_count ?></div>
        <div class="label">Favorieten</div>
    </div>
</div>

<!-- TABS -->
<div class="tabs">
    <button class="tab-btn active" onclick="showTab('mijn-recepten', this)">Mijn recepten</button>
    <button class="tab-btn" onclick="showTab('mijn-favorieten', this)">Mijn favorieten</button>
</div>

<!-- TAB: MIJN RECEPTEN -->
<div id="mijn-recepten" class="tab-content active">
    <div class="recipe-container">
        <?php
        // Reset pointer (al gefetcht hierboven)
        $recepten_q->execute();
        $recepten_result = $recepten_q->get_result();

        if ($recepten_result->num_rows > 0):
            while ($row = $recepten_result->fetch_assoc()):
        ?>
            <div class="recipe-card">
                <h3><?= htmlspecialchars($row['titel']) ?></h3>
                <p><?= htmlspecialchars($row['beschrijving']) ?></p>
                <p class="likes">❤️ <?= $row['likes'] ?> likes</p>
                <a href="recept.php?id=<?= $row['id'] ?>" class="btn small">Bekijk</a>
                <a href="edit_upload.php?id=<?= $row['id'] ?>" class="btn small" style="margin-left:8px;">Bewerken</a>
            </div>
        <?php
            endwhile;
        else:
        ?>
            <p class="empty-msg">Je hebt nog geen recepten geplaatst.<br><a href="upload.php" class="btn small" style="margin-top:10px;">Upload je eerste recept</a></p>
        <?php endif; ?>
    </div>
</div>

<!-- TAB: MIJN FAVORIETEN -->
<div id="mijn-favorieten" class="tab-content">
    <div class="recipe-container">
        <?php if ($fav_count > 0): ?>
            <?php
            $fav_result->data_seek(0);
            while ($row = $fav_result->fetch_assoc()):
            ?>
                <div class="recipe-card">
                    <h3><?= htmlspecialchars($row['titel']) ?></h3>
                    <p><?= htmlspecialchars($row['beschrijving']) ?></p>
                    <p class="likes">❤️ <?= $row['likes'] ?> likes</p>
                    <a href="recept.php?id=<?= $row['id'] ?>" class="btn small">Bekijk recept</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty-msg">Je hebt nog geen favorieten.<br>Like een recept om het hier te zien!</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=1"></script>
<script>
    function showTab(id, btn) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(id).classList.add('active');
        btn.classList.add('active');
    }
</script>

</body>
</html>