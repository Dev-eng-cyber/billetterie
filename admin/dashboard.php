<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/header.php';
requireLogin();
requireAdmin();

$db = getDB();

// Statistiques globales
$stats = [
    'users'        => $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'user'")->fetchColumn(),
    'matchs'       => $db->query("SELECT COUNT(*) FROM matchs")->fetchColumn(),
    'reservations' => $db->query("SELECT COUNT(*) FROM reservations WHERE statut = 'confirmee'")->fetchColumn(),
    'revenus'      => $db->query("SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE statut = 'paye'")->fetchColumn(),
];

// Dernières réservations
$dernieres = $db->query("
    SELECT r.id_reservation, r.date_reservation, r.statut,
           u.nom, u.prenom, u.email,
           m.equipe_domicile, m.equipe_visiteur,
           b.categorie, b.prix
    FROM reservations r
    JOIN utilisateurs u ON r.id_user    = u.id_user
    JOIN billets b      ON r.id_billet  = b.id_billet
    JOIN matchs m       ON b.id_match   = m.id_match
    ORDER BY r.date_reservation DESC
    LIMIT 5
")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
    <h2 style="color:#1a1a2e;">📊 Tableau de bord</h2>
    <a href="/billetterie/admin/gestion_matchs.php" class="btn btn-primary">+ Ajouter un match</a>
</div>

<!-- Cartes statistiques -->
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr));