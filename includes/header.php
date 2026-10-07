<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/config/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php if (isset($css_supplementaire)) echo $css_supplementaire; ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/billetterie/assets/css/style.css" />
    <link rel="stylesheet" href="/billetterie/assets/css/header.css" />
    <link rel="stylesheet" href="/billetterie/assets/css/reservation.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <title>Billetterie Football</title>
</head>

<body>
    <header>
        <div class="navbar">
            <div class="logo"> Billet <span>Sport</span></div>
            <ul class="links">
                <li><a href="/billetterie/pages/accueil.php">Accueil</a></li>
                <li><a href="/billetterie/pages/matchs.php">Nos matchs</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="/billetterie/pages/historique.php">Mes billets</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="/billetterie/admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="buttons">
                <?php if (isLoggedIn()): ?>
                    <span class="user-name">
                        👤 <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>
                    </span>
                    <a href="/billetterie/logout.php" class="action-button">Déconnexion</a>
                <?php else: ?>
                    <a href="/billetterie/register.php" class="action-button pro">S'inscrire</a>
                    <a href="/billetterie/login.php" class="action-button">Se connecter</a>
                <?php endif; ?>
            </div>
            <div class="burger_menu_button">
                <i class="fa-solid fa-bars"></i>
            </div>
        </div>

        <div class="burger_menu">
            <ul class="links">
                <li><a href="/billetterie/pages/accueil.php">Accueil</a></li>
                <li><a href="/billetterie/pages/matchs.php">Nos matchs</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="/billetterie/pages/historique.php">Mes billets</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="/billetterie/admin/dashboard.php">Admin</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="divider"></div>
                <div class="buttons_burger_menu">
                    <?php if (isLoggedIn()): ?>
                        <span class="user-name">
                            👤 <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>
                        </span>
                        <a href="/billetterie/logout.php" class="action-button">Déconnexion</a>
                    <?php else: ?>
                        <a href="/billetterie/register.php" class="action-button pro">S'inscrire</a>
                        <a href="/billetterie/login.php" class="action-button">Se connecter</a>
                    <?php endif; ?>
                </div>
            </ul>
        </div>
    </header>
    <script>
        const burgerMenuButton = document.querySelector(".burger_menu_button");
        const burgerMenuButtonIcon = document.querySelector(
            ".burger_menu_button i",
        );
        const burgerMenu = document.querySelector(".burger_menu");

        burgerMenuButton.onclick = function() {
            burgerMenu.classList.toggle("open");
            const isOpen = burgerMenu.classList.contains("open");
            burgerMenuButtonIcon.classList = isOpen ?
                "fa-solid fa-xmark" :
                "fa-solid fa-bars";
        };
    </script>
    <main class="container">