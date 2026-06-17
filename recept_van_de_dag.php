<?php
session_start();
require "db.php";

// ── Recept van de dag via TheMealDB API ──────────────────────────────────────
// Cache in JSON-bestand: vervang alleen als de datum veranderd is
$cacheFile = __DIR__ . "/recept_dag_cache.json";
$vandaag   = date("Y-m-d");
$meal      = null;

if (file_exists($cacheFile)) {
    $cached = json_decode(file_get_contents($cacheFile), true);
    if (isset($cached["date"]) && $cached["date"] === $vandaag) {
        $meal = $cached["meal"];
    }
}

if (!$meal) {
    $json = @file_get_contents("https://www.themealdb.com/api/json/v1/1/random.php");
    if ($json) {
        $data = json_decode($json, true);
        $meal = $data["meals"][0] ?? null;
        if ($meal) {
            file_put_contents($cacheFile, json_encode(["date" => $vandaag, "meal" => $meal]));
        }
    }
}

// Ingrediënten samenvoegen uit strIngredient1..20 + strMeasure1..20
$ingredienten = [];
if ($meal) {
    for ($i = 1; $i <= 20; $i++) {
        $ing = trim($meal["strIngredient$i"] ?? "");
        $msr = trim($meal["strMeasure$i"] ?? "");
        if ($ing !== "") {
            $ingredienten[] = $msr !== "" ? "$msr $ing" : $ing;
        }
    }
}

$dag_label = date("d F Y");
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
        .dag-banner h1 { font-size: 38px; color: #ff5fa2; margin-bottom: 8px; }
        .dag-banner p.datum { color: #999; font-size: 15px; }

        .recept-dag-card {
            max-width: 700px;
            margin: 40px auto;
            background: white;
            border: 3px solid #ff8fcf;
            border-radius: 24px;
            box-shadow: 0 8px 0 #ffb7e0;
            padding: 35px;
        }
        .recept-dag-card h2 { color: #ff5fa2; font-size: 28px; margin-bottom: 8px; text-align: center; }
        .recept-dag-card .meta { text-align: center; color: #aaa; font-size: 14px; margin-bottom: 20px; }
        .recept-dag-card .afbeelding {
            width: 100%; max-height: 280px; object-fit: cover;
            border-radius: 16px; margin-bottom: 25px; border: 3px solid #ffb7e0;
        }
        .recept-dag-card h3 { color: #ff5fa2; margin: 20px 0 8px; font-size: 18px; }
        .recept-dag-card p, .recept-dag-card li { color: #555; line-height: 1.7; }
        .recept-dag-card ul { padding-left: 20px; }
        .recept-dag-card .bereiding { white-space: pre-line; color: #555; line-height: 1.7; }

        .recept-dag-footer {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 25px; flex-wrap: wrap; gap: 15px;
        }
        .countdown { text-align: center; font-size: 14px; color: #bbb; margin: 10px 0 30px; }
        .countdown span { font-weight: 700; color: #ff8fcf; }
        .geen-recept { text-align: center; padding: 60px 20px; color: #aaa; font-size: 18px; }
        .source-link { font-size: 13px; color: #ff8fcf; }
    </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="dag-banner">
    <div class="dag-badge">⭐ Dagelijks wisselend</div>
    <h1>Recept van de Dag</h1>
    <p class="datum"><?= $dag_label ?></p>
</div>

<p class="countdown">Nieuw recept over <span id="timer">--:--:--</span></p>

<?php if ($meal): ?>

<div class="recept-dag-card">
    <h2><?= htmlspecialchars($meal['strMeal']) ?></h2>
    <p class="meta">
        <?= htmlspecialchars($meal['strCategory'] ?? '') ?>
        <?php if (!empty($meal['strArea'])): ?>
          &nbsp;·&nbsp; <?= htmlspecialchars($meal['strArea']) ?>
        <?php endif; ?>
    </p>

    <?php if (!empty($meal['strMealThumb'])): ?>
        <img src="<?= htmlspecialchars($meal['strMealThumb']) ?>" alt="Afbeelding" class="afbeelding">
    <?php endif; ?>

    <h3>Ingrediënten</h3>
    <ul>
        <?php foreach ($ingredienten as $ing): ?>
            <li><?= htmlspecialchars($ing) ?></li>
        <?php endforeach; ?>
    </ul>

    <h3>Bereiding</h3>
    <p class="bereiding"><?= htmlspecialchars($meal['strInstructions'] ?? '') ?></p>

    <div class="recept-dag-footer">
        <div>
            <?php if (!empty($meal['strYoutube'])): ?>
                <a href="<?= htmlspecialchars($meal['strYoutube']) ?>" target="_blank" class="btn">▶ Video bekijken</a>
            <?php endif; ?>
            <?php if (!empty($meal['strSource'])): ?>
                <a href="<?= htmlspecialchars($meal['strSource']) ?>" target="_blank" class="source-link" style="margin-left:10px;">Origineel recept</a>
            <?php endif; ?>
        </div>
        <span style="color:#aaa; font-size:13px;">Bron: TheMealDB</span>
    </div>
</div>

<?php else: ?>
    <div class="geen-recept">
        <p>😔 Kon geen recept ophalen. Controleer de internetverbinding.</p>
    </div>
<?php endif; ?>

<footer>Gemaakt door Tom, Luuk en Stef.</footer>

<script>
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
// IOT
