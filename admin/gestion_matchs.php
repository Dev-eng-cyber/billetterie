<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/header.php';
requireLogin();
requireAdmin();

$db     = getDB();
$erreur = '';
$succes = '';

// Suppression d'un match
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    $db->prepare("DELETE FROM matchs WHERE id_match = ?")->execute([$id]);
    $succes = "Match supprimé avec succès.";
}

// Ajout ou modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_match        = (int)($_POST['id_match'] ?? 0);
    $equipe_dom      = trim($_POST['equipe_domicile']);
    $equipe_vis      = trim($_POST['equipe_visiteur']);
    $date_match      = $_POST['date_match'];
    $stade           = trim($_POST['stade']);
    $capacite        = (int)$_POST['capacite_totale'];
    $prix_vip        = (float)$_POST['prix_vip'];
    $prix_nord       = (float)$_POST['prix_nord'];
    $prix_sud        = (float)$_POST['prix_sud'];

    if (empty($equipe_dom) || empty($equipe_vis) || empty($date_match) || empty($stade) || $capacite <= 0) {
        $erreur = "Tous les champs sont obligatoires.";
    } else {
        if ($id_match > 0) {
            // Modification
            $stmt = $db->prepare("UPDATE matchs SET equipe_domicile=?, equipe_visiteur=?, date_match=?, stade=?, capacite_totale=? WHERE id_match=?");
            $stmt->execute([$equipe_dom, $equipe_vis, $date_match, $stade, $capacite, $id_match]);
            $succes = "Match modifié avec succès.";
        } else {
            // Création du match
            $stmt = $db->prepare("INSERT INTO matchs (equipe_domicile, equipe_visiteur, date_match, stade, capacite_totale, places_disponibles) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$equipe_dom, $equipe_vis, $date_match, $stade, $capacite, $capacite]);
            $id_nouveau = $db->lastInsertId();

            // Génération automatique des billets
            $categories = [
                ['VIP',          $prix_vip,  round($capacite * 0.1)],
                ['Tribune Nord', $prix_nord, round($capacite * 0.45)],
                ['Tribune Sud',  $prix_sud,  round($capacite * 0.45)],
            ];

            $stmt = $db->prepare("INSERT INTO billets (id_match, categorie, numero_siege, prix, statut) VALUES (?,?,?,?,'libre')");
            foreach ($categories as [$cat, $prix, $nb]) {
                for ($i = 1; $i <= $nb; $i++) {
                    $siege = strtoupper(substr($cat, 0, 1)) . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
                    $stmt->execute([$id_nouveau, $cat, $siege, $prix]);
                }
            }

            $succes = "Match créé avec succès avec " . $capacite . " billets générés !";
        }
    }
}

// Liste des matchs
$matchs = $db->query("SELECT * FROM matchs ORDER BY date_match DESC")->fetchAll();
?>

<h2 style="margin-bottom:1.5rem; color:#1a1a2e;">🏟️ Gestion des matchs</h2>

<?php if ($erreur): ?>
    <div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div>
<?php endif; ?>
<?php if ($succes): ?>
    <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
<?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem; align-items:start;">

    <!-- Formulaire -->
    <div class="card">
        <h3 style="margin-bottom:1.2rem; color:#1a1a2e;">➕ Ajouter un match</h3>

        <div class="form-group">
            <label>Équipe domicile</label>
            <input type="text" name="equipe_domicile" id="equipe_domicile" placeholder="Ex: ASEC Mimosas">
        </div>
        <div class="form-group">
            <label>Équipe visiteur</label>
            <input type="text" name="equipe_visiteur" id="equipe_visiteur" placeholder="Ex: Africa Sports">
        </div>
        <div class="form-group">
            <label>Date et heure</label>
            <input type="datetime-local" name="date_match" id="date_match">
        </div>
        <div class="form-group">
            <label>Stade</label>
            <input type="text" name="stade" id="stade" placeholder="Ex: Stade Félix Houphouët-Boigny">
        </div>
        <div class="form-group">
            <label>Capacité totale</label>
            <input type="number" name="capacite_totale" id="capacite_totale" placeholder="Ex: 45000" min="1">
        </div>

        <div style="background:#f8f9fa; border-radius:8px; padding:1rem; margin-bottom:1rem;">
            <p style="font-size:0.85rem; color:#666; margin-bottom:0.8rem;">💡 Prix des billets (générés automatiquement)</p>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.8rem;">
                <div class="form-group" style="margin:0;">
                    <label style="font-size:0.8rem;">VIP (FCFA)</label>
                    <input type="number" name="prix_vip" id="prix_vip" placeholder="15000" min="0">
                </div>
                <div class="form-group" style="margin:0;">
                    <label style="font-size:0.8rem;">Tribune Nord</label>
                    <input type="number" name="prix_nord" id="prix_nord" placeholder="8000" min="0">
                </div>
                <div class="form-group" style="margin:0;">
                    <label style="font-size:0.8rem;">Tribune Sud</label>
                    <input type="number" name="prix_sud" id="prix_sud" placeholder="5000" min="0">
                </div>
            </div>
        </div>

        <input type="hidden" name="id_match" id="id_match" value="0">
        <button class="btn btn-primary" style="width:100%;" onclick="soumettreMatch()">
            Créer le match
        </button>
    </div>

    <!-- Liste des matchs -->
    <div>
        <?php foreach ($matchs as $m): ?>
            <div class="card" style="margin-bottom:1rem; border-left:4px solid #e94560;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <div style="font-weight:600; color:#1a1a2e; margin-bottom:0.3rem;">
                            <?= htmlspecialchars($m['equipe_domicile']) ?> vs <?= htmlspecialchars($m['equipe_visiteur']) ?>
                        </div>
                        <div style="font-size:0.85rem; color:#666;">
                            📅 <?= date('d/m/Y à H:i', strtotime($m['date_match'])) ?>
                        </div>
                        <div style="font-size:0.85rem; color:#666;">
                            📍 <?= htmlspecialchars($m['stade']) ?>
                        </div>
                        <div style="font-size:0.85rem; color:#27ae60; margin-top:0.3rem;">
                            🎟️ <?= $m['places_disponibles'] ?> / <?= $m['capacite_totale'] ?> places
                        </div>
                    </div>
                    <div style="display:flex; gap:0.5rem; flex-direction:column;">
                        <a href="?supprimer=<?= $m['id_match'] ?>"
                            onclick="return confirm('Supprimer ce match ?')"
                            class="btn btn-primary" style="font-size:0.8rem; padding:0.3rem 0.8rem; background:#e74c3c;">
                            🗑️ Supprimer
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function soumettreMatch() {
        const champs = ['equipe_domicile', 'equipe_visiteur', 'date_match', 'stade', 'capacite_totale', 'prix_vip', 'prix_nord', 'prix_sud', 'id_match'];
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        champs.forEach(name => {
            const el = document.getElementById(name);
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = el ? el.value : '';
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
</script>