<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/header.php';
requireLogin();

$db             = getDB();
$id_reservation = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$erreur         = '';
$succes         = '';

// Récupérer la réservation avec les détails
$stmt = $db->prepare("
    SELECT r.*, b.prix, b.categorie, b.numero_siege,
           m.equipe_domicile, m.equipe_visiteur, m.date_match, m.stade
    FROM reservations r
    JOIN billets b ON r.id_billet = b.id_billet
    JOIN matchs m  ON b.id_match  = m.id_match
    WHERE r.id_reservation = ? AND r.id_user = ?
");
$stmt->execute([$id_reservation, $_SESSION['user_id']]);
$reservation = $stmt->fetch();

if (!$reservation) {
    echo '<div class="alert alert-error">Réservation introuvable.</div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/footer.php';
    exit();
}

// Vérifier si déjà payé
if ($reservation['statut'] === 'confirmee') {
    $succes = "Cette réservation est déjà payée !";
}

// Traitement du paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reservation['statut'] === 'en_attente') {
    $methode = $_POST['methode'];

    if (!in_array($methode, ['carte', 'mobile_money', 'virement'])) {
        $erreur = "Méthode de paiement invalide.";
    } else {
        try {
            $db->beginTransaction();

            // Générer une référence unique
            $reference = 'BIL-' . strtoupper(uniqid());

            // Créer le paiement
            $stmt = $db->prepare("
                INSERT INTO paiements (id_reservation, montant, methode, statut, reference_transaction)
                VALUES (?, ?, ?, 'paye', ?)
            ");
            $stmt->execute([$id_reservation, $reservation['prix'], $methode, $reference]);

            // Confirmer la réservation
            $stmt = $db->prepare("UPDATE reservations SET statut = 'confirmee' WHERE id_reservation = ?");
            $stmt->execute([$id_reservation]);

            // Marquer le billet comme vendu
            $stmt = $db->prepare("UPDATE billets SET statut = 'vendu' WHERE id_billet = ?");
            $stmt->execute([$reservation['id_billet']]);

            $db->commit();

            $succes    = "Paiement effectué avec succès ! Référence : " . $reference;
            $reservation['statut'] = 'confirmee';
        } catch (Exception $e) {
            $db->rollBack();
            $erreur = "Erreur lors du paiement. Veuillez réessayer.";
        }
    }
}
?>

<div style="max-width:600px; margin:0 auto;">

    <h2 style="color:#1a1a2e; margin-bottom:1.5rem;">💳 Paiement</h2>

    <?php if ($erreur): ?>
        <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if ($succes): ?>
        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
    <?php endif; ?>

    <!-- Récapitulatif -->
    <div class="card" style="margin-bottom:1.5rem;">
        <h3 style="margin-bottom:1rem; color:#1a1a2e;">📋 Récapitulatif</h3>

        <div style="display:flex; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #eee;">
            <span style="color:#666;">Match</span>
            <strong><?= htmlspecialchars($reservation['equipe_domicile']) ?> vs <?= htmlspecialchars($reservation['equipe_visiteur']) ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #eee;">
            <span style="color:#666;">Date</span>
            <strong><?= date('d/m/Y à H:i', strtotime($reservation['date_match'])) ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #eee;">
            <span style="color:#666;">Stade</span>
            <strong><?= htmlspecialchars($reservation['stade']) ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #eee;">
            <span style="color:#666;">Tribune</span>
            <strong><?= htmlspecialchars($reservation['categorie']) ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; padding:0.6rem 0; border-bottom:1px solid #eee;">
            <span style="color:#666;">Siège</span>
            <strong><?= htmlspecialchars($reservation['numero_siege'] ?? 'Libre') ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; padding:0.8rem 0; margin-top:0.3rem;">
            <span style="font-size:1.1rem; font-weight:600;">Total à payer</span>
            <span style="font-size:1.3rem; font-weight:bold; color:#e94560;">
                <?= number_format($reservation['prix'], 0, ',', ' ') ?> FCFA
            </span>
        </div>
    </div>

    <!-- Formulaire de paiement -->
    <?php if ($reservation['statut'] === 'en_attente'): ?>
        <form method="POST" class="card">
            <h3 style="margin-bottom:1.2rem; color:#1a1a2e;">Choisissez votre méthode de paiement</h3>

            <div id="methodes">
                <?php
                $methodes = [
                    'carte'        => ['label' => 'Carte bancaire', 'icon' => '💳'],
                    'mobile_money' => ['label' => 'Mobile Money', 'icon' => '📱'],
                    'virement'     => ['label' => 'Virement bancaire', 'icon' => '🏦'],
                ];
                foreach ($methodes as $val => $m): ?>
                    <label style="display:flex; align-items:center; gap:1rem;
                                  padding:1rem; border:2px solid #eee; border-radius:8px;
                                  margin-bottom:0.8rem; cursor:pointer; transition: border 0.2s;"
                        onmouseover="this.style.borderColor='#e94560'"
                        onmouseout="this.style.borderColor= document.querySelector('input[name=methode]:checked')?.value == '<?= $val ?>' ? '#e94560' : '#eee'">
                        <input type="radio" name="methode" value="<?= $val ?>"
                            style="accent-color:#e94560;"
                            onchange="document.querySelectorAll('#methodes label').forEach(l => l.style.borderColor='#eee'); this.closest('label').style.borderColor='#e94560'; document.getElementById('btn-payer').disabled=false;">
                        <span style="font-size:1.4rem;"><?= $m['icon'] ?></span>
                        <span style="font-weight:500; color:#1a1a2e;"><?= $m['label'] ?></span>
                    </label>
                <?php endforeach; ?>
            </div>

            <button id="btn-payer" type="submit" class="btn btn-primary" style="width:100%; margin-top:1rem; padding:0.9rem; font-size:1rem;" disabled>
                Payer maintenant
            </button>
        </form>
    <?php else: ?>
        <div class="card" style="text-align:center;">
            <h3 style="color:#27ae60; margin-top:0;">Paiement confirme</h3>
            <p style="color:#555; margin-bottom:1.2rem;">Votre billet est valide. Vous pouvez consulter votre reservation depuis votre espace.</p>
            <a href="/billetterie/pages/matchs.php" class="btn btn-secondary">Retour aux matchs</a>
        </div>
    <?php endif; ?>

</div>