<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Zorg dat normale user-sessie niet stoort
unset($_SESSION["user"]);

$ADMIN_EMAIL = "admin@receptify.com";
$ADMIN_PASS = "Admin123"; // verander dit!

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $pass = trim($_POST["password"]);

    if ($email === $ADMIN_EMAIL && $pass === $ADMIN_PASS) {
        $_SESSION["admin"] = true;
        header("Location: admin_panel.php");
        exit;
    } else {
        $error = "Ongeldige admin login.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2 class="section-title">Admin Login</h2>

<div class="form-container">

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Admin email" required>
        <input type="password" name="password" placeholder="Admin wachtwoord" required>
        <button class="btn" style="width:100%;">Inloggen</button>
    </form>

</div>

</body>
</html>
// einde T
