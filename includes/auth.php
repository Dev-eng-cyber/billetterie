<?php
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: /billetterie/login.php');
        exit();
    }
}

function requireAdmin()
{
    if (!isAdmin()) {
        header('Location: /billetterie/index.php');
        exit();
    }
}

// Génère un token CSRF unique par session
function getCSRFToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifie le token CSRF envoyé dans un formulaire
function verifyCSRF()
{
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
    ) {
        http_response_code(403);
        die("Action non autorisée. <a href='/billetterie/'>Retour</a>");
    }
}
