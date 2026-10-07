<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/header.php'; ?>
<link rel="stylesheet" href="/billetterie/assets/css/accueil.css">

<section class="image_entete">
    <div class="hero-overlay">
        <h1>Bienvenue sur Billet Sport</h1>
        <p>Reservez vos places pour les plus grands matchs.</p>
        <a href="/billetterie/pages/matchs.php" class="hero-btn">Voir les matchs</a>
    </div>
</section>

<section>
    <div class="promo-section">
        <div class="promo-content">
            <h2>Reservation en ligne</h2> <br>
            <p>
                Preparez votre aventure en toute simplicite ! Avec notre service de reservation
                en ligne, vous choisissez vos matchs et vos places en quelques clics.
                Rapide, pratique et securise, tout est pense pour vous faire gagner du temps.
            </p>
            <div class="promo-actions">
                <a href="/billetterie/pages/matchs.php" class="promo-btn promo-btn-primary">Essayer maintenant</a>
                <a href="/billetterie/pages/historique.php" class="promo-btn promo-btn-outline">Mes billets</a>
            </div>
        </div>
        <div class="promo-image">
            <img src="/billetterie/assets/img/image_accueil.jpg" alt="Reservation en ligne">
        </div>


    </div>
</section>

<section class="temoignages">
    <div class="temoignages-header">
        <h2>Ce que disent nos utilisateurs</h2>
        <p>Ils ont acheté leurs billets sur notre plateforme et partagent leur expérience</p>
    </div>

    <div class="temoignages-grid">

        <div class="temoignage-card">
            <div class="etoiles">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
            </div>
            <p class="temoignage-texte">
                "Franchement je m'attendais pas à ce que ce soit aussi simple !
                En 3 clics j'avais mon billet pour le match de l'ASEC.
                Le QR code a été scanné à l'entrée sans aucun problème. Je recommande à 100% !"
            </p>
            <div class="temoignage-auteur">
                <div class="auteur-avatar">KD</div>
                <div class="auteur-info">
                    <strong>Kouassi David</strong>
                    <span>Abidjan, Côte d'Ivoire</span>
                </div>
            </div>
        </div>

        <div class="temoignage-card featured">
            <div class="etoiles">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
            </div>
            <p class="temoignage-texte">
                "Plus besoin de faire la queue pendant des heures sous le soleil !
                J'ai payé via Mobile Money depuis mon téléphone et mon billet était
                disponible instantanément. La plateforme est vraiment bien faite."
            </p>
            <div class="temoignage-auteur">
                <div class="auteur-avatar">AT</div>
                <div class="auteur-info">
                    <strong>Awa Traoré</strong>
                    <span>Bouaké, Côte d'Ivoire</span>
                </div>
            </div>
        </div>

        <div class="temoignage-card">
            <div class="etoiles">
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-solid fa-star"></i>
                <i class="fa-regular fa-star"></i>
            </div>
            <p class="temoignage-texte">
                "J'ai acheté 4 billets pour toute ma famille pour la finale.
                Le processus était rapide et sécurisé. J'ai reçu une confirmation
                immédiatement. Très bonne expérience, je reviendrai pour les prochains matchs !"
            </p>
            <div class="temoignage-auteur">
                <div class="auteur-avatar">MB</div>
                <div class="auteur-info">
                    <strong>Mohamed Bamba</strong>
                    <span>San-Pédro, Côte d'Ivoire</span>
                </div>
            </div>
        </div>

    </div>
</section>

<section class="about-section">
    <div class="about-inner">
        <div class="about-header">
            <span class="about-badge">A propos</span>
            <h2>Qui sommes-nous ?</h2>
        </div>

        <p class="about-intro">
            Billet Sport est une plateforme de billetterie en ligne dediee aux passionnes de football.
            Notre objectif est de simplifier l'achat de billets, de la selection du match jusqu'a la confirmation du paiement.
        </p>

        <div class="about-grid">
            <article class="about-card">
                <h3>Reservation rapide</h3>
                <p>Choisissez votre match, votre place et validez votre reservation en quelques clics.</p>
            </article>
            <article class="about-card">
                <h3>Paiement securise</h3>
                <p>Plusieurs methodes de paiement pour garantir une experience fiable et sans stress.</p>
            </article>
            <article class="about-card">
                <h3>Suivi de vos billets</h3>
                <p>Retrouvez facilement vos reservations et l'historique de vos achats dans votre espace.</p>
            </article>
        </div>
    </div>
</section>



<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/billetterie/includes/footer.php'; ?>