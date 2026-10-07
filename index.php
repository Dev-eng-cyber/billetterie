<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: /billetterie/pages/accueil.php');
} else {
    header('Location: /billetterie/login.php');
}
exit();
