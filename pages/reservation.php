<?php
$css_supplementaire = '<link rel="stylesheet" href="/billetterie/assets/css/reservation.css" />';
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/header.php';
requireLogin();

$db       = getDB();
$id_match = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$erreur   = '';
$succes   = '';

$stmt = $db->prepare("SELECT * FROM matchs WHERE id_match = ?");
$stmt->execute([$id_match]);
$match = $stmt->fetch();

if (!$match) {
    echo '<div class="alert alert-error">Match introuvable.</div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/footer.php';
    exit();
}

$stmt = $db->prepare("SELECT * FROM billets WHERE id_match = ? AND statut = 'libre' ORDER BY categorie, prix");
$stmt->execute([$id_match]);
$billets = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_billet = isset($_POST['id_billet']) ? (int)$_POST['id_billet'] : 0;

    if ($id_billet <= 0) {
        $erreur = "Veuillez sélectionner un billet.";
    } else {
        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT id_billet FROM billets WHERE id_billet = ? AND id_match = ? AND statut = 'libre'");
            $stmt->execute([$id_billet, $id_match]);
            $billet = $stmt->fetch();

            if (!$billet) {
                $db->rollBack();
                $erreur = "Ce billet n'est plus disponible.";
            } else {
                $stmt = $db->prepare("UPDATE billets SET statut = 'reserve' WHERE id_billet = ? AND statut = 'libre'");
                $stmt->execute([$id_billet]);

                if ($stmt->rowCount() !== 1) {
                    $db->rollBack();
                    $erreur = "Ce billet vient d'être réservé par quelqu'un d'autre.";
                } else {
                    $stmt = $db->prepare("INSERT INTO reservations (id_user, id_billet, statut) VALUES (?, ?, 'en_attente')");
                    $stmt->execute([$_SESSION['user_id'], $id_billet]);
                    $id_reservation = $db->lastInsertId();
                    $db->commit();

                    header("Location: /billetterie/pages/paiement.php?id=" . $id_reservation);
                    exit();
                }
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $erreur = "Une erreur est survenue. Veuillez réessayer.";
        }
    }
}
?>

<h2 class="reservation-titre">🎟️ Réservation de billet</h2>

<!-- Infos du match -->
<div class="reservation-match-card">
    <h3><?= htmlspecialchars($match['equipe_domicile']) ?> vs <?= htmlspecialchars($match['equipe_visiteur']) ?></h3>
    <p>📍 Stade : <span><?= htmlspecialchars($match['stade']) ?></span></p>
    <p>📅 Date : <span><?= date('d/m/Y à H:i', strtotime($match['date_match'])) ?></span></p>
    <p>🎟️ Places disponibles : <span><?= number_format($match['places_disponibles'], 0, ',', ' ') ?></span></p>
</div>

<?php if (!empty($erreur)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>

<?php if (!empty($succes)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
<?php endif; ?>

<?php if (empty($billets)): ?>
    <div class="alert alert-error">Aucun billet disponible pour ce match.</div>
<?php else: ?>
    <form method="POST" class="billets-form">
        <h3>Choisissez votre billet</h3>

        <div>
            <?php foreach ($billets as $billet): ?>
                <label class="billet-label">
                    <input type="radio" name="id_billet" value="<?= (int)$billet['id_billet'] ?>" required>
                    <div class="billet-info">
                        <span class="billet-categorie">Tribune <?= htmlspecialchars($billet['categorie']) ?></span>
                        <span class="billet-siege">Siège : <?= htmlspecialchars($billet['numero_siege'] ?? 'Libre') ?></span>
                    </div>
                    <span class="billet-prix"><?= number_format((float)$billet['prix'], 0, ',', ' ') ?> FCFA</span>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="reservation-actions">
            <button type="submit" class="btn-primary">Confirmer la réservation →</button>
            <a href="/billetterie/pages/matchs.php" class="btn-secondary">← Retour aux matchs</a>
        </div>
    </form>
<?php endif; ?>