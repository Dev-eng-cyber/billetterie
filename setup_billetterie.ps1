# Script de creation de la structure du projet billetterie
# A executer dans : C:\xampp\htdocs\Application de billeterie moderne\billetterie

$dossiers = @(
    "config",
    "includes",
    "pages",
    "admin",
    "assets",
    "assets\css",
    "assets\js",
    "assets\img"
)

$fichiers = @(
    "index.php",
    "login.php",
    "logout.php",
    "register.php",
    "config\database.php",
    "includes\header.php",
    "includes\footer.php",
    "includes\auth.php",
    "pages\accueil.php",
    "pages\matchs.php",
    "pages\reservation.php",
    "pages\paiement.php",
    "pages\historique.php",
    "pages\profil.php",
    "admin\dashboard.php",
    "admin\gestion_matchs.php",
    "admin\gestion_users.php"
)

foreach ($d in $dossiers) {
    New-Item -ItemType Directory -Force -Path $d | Out-Null
    Write-Host "Dossier cree : $d"
}

foreach ($f in $fichiers) {
    New-Item -ItemType File -Force -Path $f | Out-Null
    Write-Host "Fichier cree : $f"
}

Write-Host ""
Write-Host "Structure creee avec succes !" -ForegroundColor Green
