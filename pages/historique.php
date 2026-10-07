<?php
$css_supplementaire = '<link rel="stylesheet" href="/billetterie/assets/css/historique.css" />';
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/header.php';
requireLogin();

$db   = getDB();
$stmt = $db->prepare("
    SELECT r.*, b.categorie, b.numero_siege, b.prix,
           m.equipe_domicile, m.equipe_visiteur, m.date_match, m.stade,
           p.methode, p.reference_transaction, p.statut AS statut_paiement
    FROM reservations r
    JOIN billets b ON r.id_billet = b.id_billet
    JOIN matchs m  ON b.id_match  = m.id_match
    LEFT JOIN paiements p ON r.id_reservation = p.id_reservation
    WHERE r.id_user = ?
    ORDER BY r.date_reservation DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>

<h2 class="historique-titre">🎟️ Mes billets</h2>

<?php if (empty($reservations)): ?>
    <div class="historique-vide">
        <div class="vide-icon">🎫</div>
        <p>Vous n'avez aucune réservation pour le moment.</p>
        <a href="/billetterie/pages/matchs.php" class="btn-voir-matchs">Voir les matchs</a>
    </div>
<?php else: ?>
    <?php foreach ($reservations as $r): ?>
        <div class="billet-card <?= $r['statut'] ?>">
            <div class="billet-card-inner">

                <div class="billet-card-left">
                    <h3>
                        <?= htmlspecialchars($r['equipe_domicile']) ?> vs <?= htmlspecialchars($r['equipe_visiteur']) ?>
                    </h3>
                    <p class="billet-meta">
                        📅 <?= date('d/m/Y à H:i', strtotime($r['date_match'])) ?>
                    </p>
                    <p class="billet-meta">
                        📍 <?= htmlspecialchars($r['stade']) ?>
                    </p>
                    <p class="billet-meta">
                        🪑 Tribune <?= htmlspecialchars($r['categorie']) ?>
                        — Siège <?= htmlspecialchars($r['numero_siege'] ?? 'Libre') ?>
                    </p>
                    <?php if ($r['reference_transaction']): ?>
                        <p class="billet-ref">Réf : <?= htmlspecialchars($r['reference_transaction']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="billet-card-right">
                    <span class="billet-prix">
                        <?= number_format($r['prix'], 0, ',', ' ') ?> FCFA
                    </span>

                    <?php
                    $badges = [
                        'confirmee'  => ['classe' => 'confirmee',  'label' => '✅ Confirmée'],
                        'en_attente' => ['classe' => 'en_attente', 'label' => '⏳ En attente'],
                        'annulee'    => ['classe' => 'annulee',    'label' => '❌ Annulée'],
                    ];
                    $badge = $badges[$r['statut']] ?? ['classe' => '', 'label' => $r['statut']];
                    ?>
                    <span class="badge <?= $badge['classe'] ?>">
                        <?= $badge['label'] ?>
                    </span>

                    <?php if ($r['statut'] === 'en_attente'): ?>
                        <a href="/billetterie/pages/paiement.php?id=<?= $r['id_reservation'] ?>"
                            class="btn-payer">
                            💳 Payer maintenant
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>