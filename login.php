<?php
session_start();
require "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check of gebruiker bestaat
    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($uid, $uemail, $hash);
    $stmt->fetch();

    if ($stmt->num_rows === 1 && password_verify($password, $hash)) {
        $_SESSION["user"] = $uemail;
        header("Location: index.php");
        exit;
    } else {
        $error = "Email of wachtwoord klopt niet.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen – Receptify</title>
    <link rel="stylesheet" href="style.css?v=1">
</head>
<body>

<?php include "navbar.php"; ?>

<h2 class="section-title">Inloggen</h2>

<div class="form-container">

    <?php if ($error): ?>
        <p style="color:red; font-weight:600; margin-bottom:10px;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Wachtwoord" required>

        <button type="submit" class="btn" style="width:100%;">Inloggen</button>
    </form>

    <p style="margin-top:15px; text-align:center;">
        Nog geen account? <a href="register.php">Registreren</a>
    </p>

</div>

<footer>
    Gemaakt door Tom, Luuk en Stef.
</footer>

<script src="script.js?v=1"></script>
</body>
</html>
// einde T
