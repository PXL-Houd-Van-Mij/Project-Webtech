<?php
session_start();
require "db.php";

// Neppe help center e-mail waar de berichten naartoe gaan.
$HELP_CENTER_EMAIL = "helpcenter@receptify.com";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $bericht = trim($_POST["bericht"]);

    // Validatie
    if ($email === "" || $bericht === "") {
        $error = "Vul zowel je e-mail als een bericht in.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Voer een geldig e-mailadres in.";
    } else {
        // Bericht opbouwen
        $onderwerp = "Nieuw helpdesk bericht van " . $email;
        $body = "Afzender: " . $email . "\n\n" . $bericht;

        // Headers (Reply-To zodat we de gebruiker kunnen terugmailen)
        $headers  = "From: " . $HELP_CENTER_EMAIL . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Versturen naar de help center e-mail
        if (mail($HELP_CENTER_EMAIL, $onderwerp, $body, $headers)) {
            $success = "Bedankt! Je bericht is verstuurd naar ons help center.";
        } else {
            $error = "Verzenden is mislukt. Probeer het later opnieuw.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Helpdesk</h2>

<div class="form-container">

    <p style="margin-bottom:15px; text-align:center; color:#555;">
        Een vraag of probleem? Laat je e-mail en bericht achter,
        dan neemt ons help center contact met je op.
    </p>

    <?php if ($error): ?>
        <p style="color:red; font-weight:600; margin-bottom:10px;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green; font-weight:600; margin-bottom:10px;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Jouw e-mailadres" required>

        <textarea name="bericht" placeholder="Beschrijf hier je vraag of probleem" rows="6" required></textarea>

        <button type="submit" class="btn" style="width:100%;">Versturen</button>
    </form>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=1"></script>
</body>
</html>
// IOT
