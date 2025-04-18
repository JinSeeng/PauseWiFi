document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.header__mobile-toggle');
    const mainNav = document.querySelector('.header__nav');
    const html = document.documentElement;
    
    if (menuToggle && mainNav) {
        // Gestion du clic sur le bouton menu mobile
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('is-active');
            
            // Bloque le scroll quand le menu est ouvert
            if (mainNav.classList.contains('active')) {
                html.style.overflow = 'hidden';
                this.setAttribute('aria-expanded', 'true');
            } else {
                html.style.overflow = '';
                this.setAttribute('aria-expanded', 'false');
            }
        });

        // Ferme le menu au clic sur un lien
        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (mainNav.classList.contains('active')) {
                    mainNav.classList.remove('active');
                    menuToggle.classList.remove('is-active');
                    html.style.overflow = '';
                    menuToggle.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Ferme le menu en cliquant à l'extérieur
        document.addEventListener('click', function(e) {
            if (!mainNav.contains(e.target) && !menuToggle.contains(e.target)) {
                mainNav.classList.remove('active');
                menuToggle.classList.remove('is-active');
                html.style.overflow = '';
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Ferme le menu avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mainNav.classList.contains('active')) {
                mainNav.classList.remove('active');
                menuToggle.classList.remove('is-active');
                html.style.overflow = '';
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
});