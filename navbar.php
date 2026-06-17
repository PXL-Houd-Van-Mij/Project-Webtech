<?php
if (!isset($_SESSION)) { session_start(); }
?>

<nav class="navbar">

    <!-- LOGO = HOME BUTTON -->
    <a href="index.php" class="logo-link">
        <div class="logo-area">
            <div class="logo">Receptify</div>
        </div>
    </a>

<!-- NAV LINKS -->
    <div class="nav-links">
        <a href="recept_van_de_dag.php">Recept van de dag</a>
        <a href="favorieten.php">Favorieten</a>
        <a href="specialiteiten.php">Specialiteiten</a>
        <a href="upload.php">Upload</a>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="profiel.php">Profiel</a>
        <?php endif; ?>
    </div>

    <!-- LOGIN / LOGOUT -->
    <div class="nav-right">
        <?php if (!isset($_SESSION['user'])): ?>
            <a href="login.php" class="btn small">Inloggen</a>
            <a href="register.php" class="btn small">Registreren</a>
        <?php else: ?>
            <span class="welcome">Hallo, <?= $_SESSION['user'] ?></span>
            <a href="logout.php" class="btn small">Uitloggen</a>
        <?php endif; ?>
    </div>

</nav>
// einde T
