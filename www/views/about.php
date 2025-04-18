<?php 
// Inclure l'en-tête de la page
require_once __DIR__ . '/partials/header.php'; 
?>

<div class="about">
    <h1 class="about__title">À propos de Pause Wi-Fi</h1>

    <!-- Section Mission -->
    <section class="about__section">
        <h2 class="about__section-title">Notre mission</h2>
        <p class="about__section-text">
            Pause Wi-Fi est un site web conçu pour vous permettre de <strong>trouver facilement des points d'accès Wi-Fi gratuits à Paris</strong>. Que vous soyez en déplacement, étudiant, en télétravail ou simplement curieux, notre objectif est de vous aider à rester connecté à tout moment.
        </p>
    </section>

    <!-- Section Fonctionnalités -->
    <section class="about__section">
        <h2 class="about__section-title">Ce que le site propose</h2>
        <p class="about__section-text">
            Le site met à disposition :
        </p>
        <ul class="about__section-text">
            <li>Une <strong>carte interactive</strong> qui affiche tous les spots Wi-Fi gratuits disponibles à Paris</li>
            <li>Une <strong>recherche avancée</strong> par arrondissement, accessibilité, et type de lieu</li>
            <li>La possibilité de <strong>gérer vos favoris</strong> pour retrouver facilement vos lieux préférés</li>
            <li>Un <strong>espace personnel</strong> pour modifier vos informations et suivre vos activités</li>
            <li>Une interface claire, intuitive, et <strong>adaptée à tous les appareils</strong></li>
        </ul>
    </section>

    <!-- Section Données -->
    <section class="about__section">
        <h2 class="about__section-title">Données utilisées</h2>
        <p class="about__section-text">
            Les données sont issues de la plateforme <strong>Open Data Paris</strong> et enrichies pour offrir une meilleure expérience utilisateur. Cela permet une information actualisée et fiable sur les points Wi-Fi publics disponibles.
        </p>
    </section>

    <!-- Section Public cible -->
    <section class="about__section">
        <h2 class="about__section-title">Pour qui est-ce utile ?</h2>
        <p class="about__section-text">
            Pause Wi-Fi s'adresse à :
        </p>
        <ul class="about__section-text">
            <li>Les <strong>étudiants</strong> en quête d'un lieu pour travailler en ligne</li>
            <li>Les <strong>freelances ou télétravailleurs</strong> souhaitant une connexion fiable</li>
            <li>Les <strong>touristes et habitants</strong> qui veulent se connecter gratuitement en ville</li>
        </ul>
    </section>

    <!-- Section Projet -->
    <section class="about__section about__team">
        <h2 class="about__section-title">À propos du projet</h2>
        <p class="about__section-text">
            Ce site a été réalisé dans le cadre d'une <strong>épreuve certifiante en apprentissage de développement web</strong>. Le projet a été entièrement conçu, développé et structuré par une étudiante passionnée, dans le but de combiner <strong>utilité, accessibilité et design</strong>.
        </p>
    </section>

    <!-- Call to Action -->
    <div class="about__cta">
        <p class="about__section-text">
            Vous avez une suggestion ou une question ? Rendez-vous sur la <a href="/?page=contact" class="about__cta-btn">page de contact</a> !
        </p>
    </div>
</main>

<?php 
// Inclure le pied de page
require_once __DIR__ . '/partials/footer.php'; 
?>