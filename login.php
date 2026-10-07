<?php
require_once 'includes/header.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRF();

    $email = trim($_POST['email']);
    $mdp   = $_POST['mot_de_passe'];

    if (empty($email) || empty($mdp)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['prenom']  = $user['prenom'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: /billetterie/admin/dashboard.php');
            } else {
                header('Location: /billetterie/pages/accueil.php');
            }
            exit();
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<link rel="stylesheet" href="/billetterie/assets/css/auth.css">

<section class="auth-layout">
    <div class="auth-visual" aria-hidden="true"></div>

    <div class="auth-content">
        <div class="auth-card">
            <h2 class="auth-title">Bienvenue !</h2>
            <p class="auth-subtitle">Connectez-vous pour gerer vos billets.</p>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCSRFToken()) ?>">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="votre@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Votre mot de passe" required>
                </div>

                <button type="submit" class="auth-submit">Connexion</button>
            </form>

            <p class="auth-switch">
                Pas encore de compte ? <a href="/billetterie/register.php">Inscription</a>
            </p>
        </div>
    </div>
</section>