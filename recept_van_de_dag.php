<?php
session_start();
require "db.php";

// Recept van de dag: bepaald op basis van de huidige datum
// Gebruik DAYOFYEAR zodat het elke dag wisselt maar stabiel is gedurende de dag
$dag = date("z"); // 0–365

// Tel het aantal recepten
$count_q = mysqli_query($conn, "SELECT COUNT(*) as totaal FROM recepten");
$count_row = mysqli_fetch_assoc($count_q);
$totaal = (int) $count_row['totaal'];

if ($totaal === 0) {
    $recept = null;
} else {
    // Kies een recept op basis van dag % totaal (stabiel per dag)
    $offset = $dag % $totaal;

    $stmt = $conn->prepare("SELECT * FROM recepten ORDER BY id ASC LIMIT 1 OFFSET ?");
    $stmt->bind_param("i", $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $recept = $result->fetch_assoc();
}

// Check of ingelogde gebruiker dit als favoriet heeft
$isFav = false;
if ($recept && isset($_SESSION["user"])) {
    $checkFav = $conn->prepare("
        SELECT id FROM favorieten 
        WHERE user_id=(SELECT id FROM users WHERE email=?) 
        AND recept_id=? LIMIT 1
    ");
    $checkFav->bind_param("si", $_SESSION["user"], $recept['id']);
    $checkFav->execute();
    $checkFav->store_result();
    if ($checkFav->num_rows > 0) {
        $isFav = true;
    }
}

$dag_label = date("d F Y"); // bv. "17 June 2026"
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept van de Dag – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
    <style>
        .dag-banner {
            background: linear-gradient(135deg, #ffe3f4, #ffb7e0);
            border-bottom: 4px solid #ff8fcf;
            padding: 50px 20px 30px;
            text-align: center;
        }

        .dag-badge {
            display: inline-block;
            background: #ff8fcf;
            color: white;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 20px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 15px;
            box-shadow: 0 3px 0 #d96aaa;
        }

        .dag-banner h1 {
            font-size: 38px;
            color: #ff5fa2;
            margin-bottom: 8px;
        }

        .dag-banner p.datum {
            color: #999;
            font-size: 15px;
        }

        .recept-dag-card {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            border: 3px solid #ff8fcf;
            border-radius: 24px;
            box-shadow: 0 8px 0 #ffb7e0;
            padding: 35px;
        }

        .recept-dag-card h2 {
            color: #ff5fa2;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
        }

        .recept-dag-card .afbeelding {
            width: 100%;
            max-height: 280px;
            object-fit: cover;
            border-radius: 16px;
            margin-bottom: 25px;
            border: 3px solid #ffb7e0;
        }

        .recept-dag-card h3 {
            color: #ff5fa2;
            margin: 20px 0 8px;
            font-size: 18px;
        }

        .recept-dag-card p {
            color: #555;
            line-height: 1.7;
            white-space: pre-line;
        }

        .recept-dag-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .like-heart {
            font-size: 40px;
            cursor: pointer;
            user-select: none;
            transition: transform 0.2s ease;
        }

        .like-heart:hover { transform: scale(1.2); }
        .like-heart.active { color: red; transform: scale(1.3); }

        .geen-recept {
            text-align: center;
            padding: 60px 20px;
            color: #aaa;
            font-size: 18px;
        }

        .countdown {
            text-align: center;
            font-size: 14px;
            color: #bbb;
            margin: 10px 0 30px;
        }

        .countdown span {
            font-weight: 700;
            color: #ff8fcf;
        }
    </style>
</head>
<body>

<?php include "navbar.php"; ?>

<!-- BANNER -->
<div class="dag-banner">
    <div class="dag-badge">⭐ Dagelijks wisselend</div>
    <h1>Recept van de Dag</h1>
    <p class="datum"><?= $dag_label ?></p>
</div>

<!-- COUNTDOWN -->
<p class="countdown">Nieuw recept over <span id="timer">--:--:--</span></p>

<!-- RECEPT -->
<?php if ($recept): ?>

<div class="recept-dag-card">
    <h2><?= htmlspecialchars($recept['titel']) ?></h2>

    <?php if (!empty($recept['afbeelding']) && file_exists($recept['afbeelding'])): ?>
        <img src="<?= htmlspecialchars($recept['afbeelding']) ?>" alt="Afbeelding" class="afbeelding">
    <?php endif; ?>

    <h3>Beschrijving</h3>
    <p><?= nl2br(htmlspecialchars($recept['beschrijving'])) ?></p>

    <h3>Ingrediënten</h3>
    <p><?= htmlspecialchars($recept['ingredienten']) ?></p>

    <h3>Bereiding</h3>
    <p><?= htmlspecialchars($recept['bereiding']) ?></p>

    <div class="recept-dag-footer">

        <!-- LIKE KNOP -->
        <div>
            <div id="like-btn"
                 data-id="<?= $recept['id'] ?>"
                 class="like-heart <?= $isFav ? 'active' : '' ?>">
                ❤️
            </div>
            <p id="like-count" style="font-weight:600; text-align:center;">
                <?= $recept['likes'] ?> likes
            </p>
        </div>

        <a href="recept.php?id=<?= $recept['id'] ?>" class="btn">Volledige pagina bekijken</a>

    </div>
</div>

<?php else: ?>
    <div class="geen-recept">
        <p>😔 Er zijn nog geen recepten in de database.</p>
        <a href="upload.php" class="btn" style="margin-top:20px;">Upload het eerste recept!</a>
    </div>
<?php endif; ?>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=1"></script>
<script>
    // Countdown naar middernacht (wanneer het recept wisselt)
    function updateCountdown() {
        const now = new Date();
        const midnight = new Date();
        midnight.setHours(24, 0, 0, 0);
        const diff = midnight - now;

        const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
        const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
        const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');

        document.getElementById('timer').textContent = `${h}:${m}:${s}`;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>

</body>
</html>