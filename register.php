<?php
session_start();
require "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $password2 = trim($_POST["password2"]);

    // Wachtwoorden gelijk?
    if ($password !== $password2) {
        $error = "Wachtwoorden komen niet overeen.";
    } else {
        // Bestaat email al?
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Dit emailadres is al geregistreerd.";
        } else {
            // Nieuwe gebruiker opslaan
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $hash);

            if ($stmt->execute()) {
                $success = "Account succesvol aangemaakt! Je kunt nu inloggen.";
            } else {
                $error = "Er ging iets mis. Probeer opnieuw.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Registreren</h2>

<div class="form-container">

    <?php if ($error): ?>
        <p style="color:red; font-weight:600; margin-bottom:10px;"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green; font-weight:600; margin-bottom:10px;"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Wachtwoord" required>

        <input type="password" name="password2" placeholder="Herhaal wachtwoord" required>

        <button type="submit" class="btn" style="width:100%;">Account aanmaken</button>
    </form>

    <p style="margin-top:15px; text-align:center;">
        Al een account? <a href="login.php">Inloggen</a>
    </p>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=1"></script>
</body>
</html>