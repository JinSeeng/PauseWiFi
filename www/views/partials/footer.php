</main>

<footer class="main-footer">
    <div class="container">
        <div class="footer-section">
            <h3>Pause WiFi</h3>
            <p>Trouvez les meilleurs spots WiFi gratuits à Paris</p>
        </div>
        
        <div class="footer-section">
            <h3>Navigation</h3>
            <ul>
                <li><a href="/">Accueil</a></li>
                <li><a href="/map">Carte interactive</a></li>
                <li><a href="/about">À propos</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h3>Légal</h3>
            <ul>
                <li><a href="/privacy">Confidentialité</a></li>
                <li><a href="/terms">Conditions d'utilisation</a></li>
            </ul>
        </div>
        
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> Pause WiFi. Tous droits réservés.</p>
            <p>Données fournies par la Ville de Paris</p>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="./assets/js/mobile-menu.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initMobileMenu === 'function') {
        initMobileMenu();
    }
    
    if (document.querySelector('form') && typeof initFormValidation === 'function') {
        initFormValidation();
    }
});
</script>

</body>
</html>