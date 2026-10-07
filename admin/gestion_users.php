<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/header.php';
requireLogin();
requireAdmin();

$db     = getDB();
$succes = '';
$erreur = '';

// Changer le rôle d'un utilisateur
if (isset($_GET['toggle_role'])) {
    $id   = (int)$_GET['toggle_role'];
    $stmt = $db->prepare("SELECT role FROM utilisateurs WHERE id_user = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if ($user && $id !== (int)$_SESSION['user_id']) {
        $nouveau_role = $user['role'] === 'admin' ? 'user' : 'admin';
        $db->prepare("UPDATE utilisateurs SET role = ? WHERE id_user = ?")->execute([$nouveau_role, $id]);
        $succes = "Rôle mis à jour avec succès.";
    } else {
        $erreur = "Impossible de modifier votre propre rôle.";
    }
}

// Supprimer un utilisateur
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    if ($id !== (int)$_SESSION['user_id']) {
        $db->prepare("DELETE FROM utilisateurs WHERE id_user = ?")->execute([$id]);
        $succes = "Utilisateur supprimé avec succès.";
    } else {
        $erreur = "Vous ne pouvez pas supprimer votre propre compte.";
    }
}

// Recherche
$recherche = trim($_GET['q'] ?? '');
if ($recherche) {
    $stmt = $db->prepare("
        SELECT u.*,
               COUNT(DISTINCT r.id_reservation) AS nb_reservations,
               COALESCE(SUM(p.montant), 0)       AS total_paye
        FROM utilisateurs u
        LEFT JOIN reservations r ON u.id_user = r.id_user
        LEFT JOIN paiements p    ON r.id_reservation = p.id_reservation AND p.statut = 'paye'
        WHERE u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ?
        GROUP BY u.id_user
        ORDER BY u.date_inscription DESC
    ");
    $like = "%$recherche%";
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $db->query("
        SELECT u.*,
               COUNT(DISTINCT r.id_reservation) AS nb_reservations,
               COALESCE(SUM(p.montant), 0)       AS total_paye
        FROM utilisateurs u
        LEFT JOIN reservations r ON u.id_user = r.id_user
        LEFT JOIN paiements p    ON r.id_reservation = p.id_reservation AND p.statut = 'paye'
        GROUP BY u.id_user
        ORDER BY u.date_inscription DESC
    ");
}
$users = $stmt->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h2 style="color:#1a1a2e;">👥 Gestion des utilisateurs</h2>
    <a href="/billetterie/admin/dashboard.php" style="color:#e94560; font-size:0.9rem;">← Dashboard</a>
</div>

<?php if ($erreur): ?>
    <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>
<?php if ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
<?php endif; ?>

<!-- Barre de recherche -->
<div class="card" style="margin-bottom:1.5rem; padding:1rem;">
    <div style="display:flex; gap:1rem; align-items:center;">
        <input type="text" id="recherche" placeholder="🔍 Rechercher par nom, prénom ou email..."
            value="<?= htmlspecialchars($recherche) ?>"
            style="flex:1; padding:0.7rem 1rem; border:1px solid #ddd; border-radius:6px; font-size:0.95rem;">
        <button class="btn btn-primary" onclick="rechercherUser()">Rechercher</button>
        <?php if ($recherche): ?>
            <a href="?" class="btn btn-secondary">Réinitialiser</a>
        <?php endif; ?>
    </div>
</div>

<!-- Stats rapides -->
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem;">
    <div class="card" style="text-align:center; padding:1rem;">
        <div style="font-size:1.5rem; font-weight:bold; color:#3498db;"><?= count($users) ?></div>
        <div style="color:#666; font-size:0.85rem;">Utilisateurs trouvés</div>
    </div>
    <div class="card" style="text-align:center; padding:1rem;">
        <div style="font-size:1.5rem; font-weight:bold; color:#e94560;">
            <?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?>
        </div>
        <div style="color:#666; font-size:0.85rem;">Administrateurs</div>
    </div>
    <div class="card" style="text-align:center; padding:1rem;">
        <div style="font-size:1.5rem; font-weight:bold; color:#27ae60;">
            <?= array_sum(array_column($users, 'nb_reservations')) ?>
        </div>
        <div style="color:#666; font-size:0.85rem;">Réservations au total</div>
    </div>
</div>

<!-- Tableau des utilisateurs -->
<div class="card">
    <?php if (empty($users)): ?>
        <p style="text-align:center; color:#999; padding:2rem;">Aucun utilisateur trouvé.</p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                <thead>
                    <tr style="border-bottom:2px solid #eee; color:#666; text-align:left;">
                        <th style="padding:0.8rem;">Utilisateur</th>
                        <th style="padding:0.8rem;">Email</th>
                        <th style="padding:0.8rem; text-align:center;">Rôle</th>
                        <th style="padding:0.8rem; text-align:center;">Réservations</th>
                        <th style="padding:0.8rem; text-align:right;">Total payé</th>
                        <th style="padding:0.8rem; text-align:center;">Inscrit le</th>
                        <th style="padding:0.8rem; text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr style="border-bottom:1px solid #f5f5f5;">
                            <td style="padding:0.8rem;">
                                <div style="display:flex; align-items:center; gap:0.7rem;">
                                    <div style="width:36px; height:36px; border-radius:50%;
                                                background:#e94560; color:white;
                                                display:flex; align-items:center; justify-content:center;
                                                font-weight:bold; font-size:0.9rem; flex-shrink:0;">
                                        <?= strtoupper(substr($u['prenom'], 0, 1) . substr($u['nom'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div style="font-weight:500;">
                                            <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>
                                        </div>
                                        <?php if ($u['id_user'] == $_SESSION['user_id']): ?>
                                            <div style="font-size:0.75rem; color:#999;">← Vous</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:0.8rem; color:#666;">
                                <?= htmlspecialchars($u['email']) ?>
                            </td>
                            <td style="padding:0.8rem; text-align:center;">
                                <span style="padding:0.2rem 0.7rem; border-radius:20px; font-size:0.8rem; font-weight:500;
                                             background:<?= $u['role'] === 'admin' ? '#fef3e7' : '#eaf4fd' ?>;
                                             color:<?= $u['role'] === 'admin' ? '#e67e22' : '#3498db' ?>;">
                                    <?= $u['role'] === 'admin' ? '⚙️ Admin' : '👤 User' ?>
                                </span>
                            </td>
                            <td style="padding:0.8rem; text-align:center; font-weight:500;">
                                <?= $u['nb_reservations'] ?>
                            </td>
                            <td style="padding:0.8rem; text-align:right; font-weight:600; color:#27ae60;">
                                <?= number_format($u['total_paye'], 0, ',', ' ') ?> FCFA
                            </td>
                            <td style="padding:0.8rem; text-align:center; color:#999; font-size:0.85rem;">
                                <?= date('d/m/Y', strtotime($u['date_inscription'])) ?>
                            </td>
                            <td style="padding:0.8rem; text-align:center;">
                                <div style="display:flex; gap:0.4rem; justify-content:center;">
                                    <?php if ($u['id_user'] != $_SESSION['user_id']): ?>
                                        <a href="?toggle_role=<?= $u['id_user'] ?>"
                                            onclick="return confirm('Changer le rôle de cet utilisateur ?')"
                                            style="padding:0.3rem 0.6rem; background:#f0f0f0; border-radius:4px;
                                                  font-size:0.8rem; text-decoration:none; color:#555;">
                                            <?= $u['role'] === 'admin' ? '👤 User' : '⚙️ Admin' ?>
                                        </a>
                                        <a href="?supprimer=<?= $u['id_user'] ?>"
                                            onclick="return confirm('Supprimer définitivement cet utilisateur ?')"
                                            style="padding:0.3rem 0.6rem; background:#fdedec; border-radius:4px;
                                                  font-size:0.8rem; text-decoration:none; color:#e74c3c;">
                                            🗑️
                                        </a>
                                    <?php else: ?>
                                        <span style="color:#ccc; font-size:0.8rem;">—</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    function rechercherUser() {
        const q = document.getElementById('recherche').value.trim();
        window.location.href = '?q=' + encodeURIComponent(q);
    }

    document.getElementById('recherche').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') rechercherUser();
    });
</script>