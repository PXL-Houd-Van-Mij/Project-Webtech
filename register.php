<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require "db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $password2 = trim($_POST["password2"] ?? "");

    if ($email === "" || $password === "" || $password2 === "") {
        $error = "Vul alle velden in.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ongeldig e-mailadres.";
    } elseif ($password !== $password2) {
        $error = "Wachtwoorden komen niet overeen.";
    } else {

        // Check of email al bestaat
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Dit e-mailadres is al geregistreerd.";
        } else {

            // Wachtwoord hashen
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // Nieuwe user opslaan
            $insert = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $insert->bind_param("ss", $email, $hash);

            if ($insert->execute()) {
                $success = "Account succesvol aangemaakt! Je kan nu inloggen.";
            } else {
                $error = "Database fout: " . $insert->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren – Receptify</title>
    <link rel="stylesheet" href="style.css?v=600">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Account Aanmaken</h2>

<div class="form-container">

    <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="POST">

        <input type="email" name="email" placeholder="E-mailadres" required>

        <input type="password" name="password" placeholder="Wachtwoord" required>

        <input type="password" name="password2" placeholder="Herhaal wachtwoord" required>

        <button type="submit" class="btn" style="width:100%;">Registreren</button>

    </form>
</div>

<footer>Gemaakt door Tom, Luuk en Stef.</footer>

</body>
</html>
// einde T
