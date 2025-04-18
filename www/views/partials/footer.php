</main>

<footer class="footer">
    <div class="footer__container">
        <div class="footer__section">
            <h3 class="footer__title">Pause WiFi</h3>
            <p class="footer__text">Trouvez les meilleurs spots WiFi gratuits à Paris</p>
        </div>
        
        <div class="footer__section">
            <h3 class="footer__title">Navigation</h3>
            <ul class="footer__nav-list">
                <li class="footer__nav-item">
                    <a href="/?page=map" class="footer__nav-link">Carte interactive</a>
                </li>
                <li class="footer__nav-item">
                    <a href="/?page=list" class="footer__nav-link">Tous les spots</a>
                </li>
            </ul>
        </div>
        
        <div class="footer__section">
            <h3 class="footer__title">Informations</h3>
            <ul class="footer__nav-list">
                <li class="footer__nav-item">
                    <a href="/?page=about" class="footer__nav-link">À propos</a>
                </li>
                <li class="footer__nav-item">
                    <a href="/?page=contact" class="footer__nav-link">Contact</a>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="footer__copyright">
        <p class="footer__copyright-text">&copy; <?= date('Y') ?> Pause WiFi. Tous droits réservés.</p>
        <p class="footer__copyright-text">Données fournies par la <a href="https://opendata.paris.fr/pages/home/" class="footer__copyright-link">Ville de Paris</a></p>
    </div>
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script src="/assets/js/mobile-menu.js"></script>
</body>
</html>