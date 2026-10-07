<?php
require_once 'includes/header.php';

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRF();

    $nom    = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email  = trim($_POST['email']);
    $mdp    = $_POST['mot_de_passe'];
    $mdp2   = $_POST['confirm_mdp'];

    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } elseif ($mdp !== $mdp2) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($mdp) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT id_user FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $email, $hash]);
            $succes = "Compte créé avec succès ! Vous pouvez vous connecter.";
        }
    }
}
?>

<link rel="stylesheet" href="/billetterie/assets/css/auth.css">

<section class="auth-layout">
    <div class="auth-visual" aria-hidden="true"></div>

    <div class="auth-content">
        <div class="auth-card">
            <h2 class="auth-title">Creer un compte</h2>
            <p class="auth-subtitle">Inscrivez-vous pour reserver vos billets rapidement.</p>

            <?php if ($erreur): ?>
                <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <?php if ($succes): ?>
                <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCSRFToken()) ?>">

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" placeholder="Votre nom"
                        value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prenom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Votre prenom"
                        value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="votre@email.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="Minimum 6 caracteres" required>
                </div>
                <div class="form-group">
                    <label for="confirm_mdp">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_mdp" name="confirm_mdp" placeholder="Repetez le mot de passe" required>
                </div>

                <button type="submit" class="auth-submit">Inscription</button>
            </form>

            <p class="auth-switch">
                Deja un compte ? <a href="/billetterie/login.php">Connexion</a>
            </p>
        </div>
    </div>
</section>