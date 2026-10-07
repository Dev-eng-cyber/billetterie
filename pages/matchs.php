<?php
$css_supplementaire = '<link rel="stylesheet" href="/billetterie/assets/css/matchs.css" />';
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/header.php';
?>

<h2 style="margin-bottom:1.5rem; color:#1a1a2e;">🏟️ Matchs disponibles</h2>

<?php
$db     = getDB();
$stmt   = $db->query("SELECT * FROM matchs ORDER BY date_match ASC");
$matchs = $stmt->fetchAll();

if (empty($matchs)): ?>
    <div class="alert alert-error">Aucun match disponible pour le moment.</div>
<?php else: ?>
    <div class="matchs-grid">
        <?php foreach ($matchs as $match): ?>
            <div class="match-card">

                <div class="match-card-top">
                    <span class="match-stade">📍 <?= htmlspecialchars($match['stade']) ?></span>
                    <span class="match-date">📅 <?= date('d/m/Y à H:i', strtotime($match['date_match'])) ?></span>
                </div>

                <div class="match-equipes">
                    <div class="equipe">
                        <span class="equipe-nom"><?= htmlspecialchars($match['equipe_domicile']) ?></span>
                        <span class="equipe-label">Domicile</span>
                    </div>
                    <div class="vs">VS</div>
                    <div class="equipe">
                        <span class="equipe-nom"><?= htmlspecialchars($match['equipe_visiteur']) ?></span>
                        <span class="equipe-label">Visiteur</span>
                    </div>
                </div>

                <div class="match-card-bottom">
                    <span class="match-places">
                        🎟️ <?= number_format($match['places_disponibles'], 0, ',', ' ') ?> places disponibles
                    </span>
                    <?php if (isLoggedIn()): ?>
                        <a href="/billetterie/pages/reservation.php?id=<?= $match['id_match'] ?>"
                            class="btn-reserver">Réserver</a>
                    <?php else: ?>
                        <a href="/billetterie/login.php" class="btn-connexion">Se connecter</a>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>