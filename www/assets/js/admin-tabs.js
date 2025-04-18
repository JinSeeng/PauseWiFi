document.addEventListener('DOMContentLoaded', function() {
    // Gestion du chargement initial de la page admin
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab') || 'welcome'; // Récupère l'onglet actif depuis l'URL
    
    // Cache tous les contenus et montre seulement celui qui correspond à l'onglet
    document.querySelectorAll('.admin-content > div').forEach(content => {
        content.style.display = 'none';
    });
    
    // Trouve le contenu à afficher selon l'onglet
    const activeContent = document.querySelector(`.admin-content > .${tab}-management, 
                                               .admin-content > .${tab}-form,
                                               .admin-content > .welcome-message`);
    if (activeContent) {
        activeContent.style.display = 'block';
    }
    
    // Gestion des clics sur les boutons du menu
    document.querySelectorAll('.menu-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Met à jour l'URL sans recharger la page
            const tabName = this.getAttribute('href').split('tab=')[1];
            window.history.pushState({}, '', `/?page=admin&tab=${tabName}`);
            
            // Met à jour l'affichage du contenu
            document.querySelectorAll('.admin-content > div').forEach(content => {
                content.style.display = 'none';
            });
            
            const contentToShow = document.querySelector(`.admin-content > .${tabName}-management, 
                                                        .admin-content > .${tabName}-form`);
            if (contentToShow) {
                contentToShow.style.display = 'block';
            }
            
            // Met à jour l'état actif des boutons
            document.querySelectorAll('.menu-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
    
    // Gestion des formulaires admin
    const adminForms = document.querySelectorAll('.admin-form, .delete-form, .role-form');
    adminForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Prépare les données du formulaire
            const formData = new FormData(this);
            const action = this.getAttribute('action');
            const method = this.getAttribute('method') || 'POST';
            
            // Envoie la requête AJAX
            fetch(action, {
                method: method,
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.success) {
                    showAdminAlert('success', data.message || 'Opération réussie');
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } else if (data && data.error) {
                    showAdminAlert('error', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAdminAlert('error', 'Une erreur est survenue');
            });
        });
    });
    
    // Fonction pour afficher les alertes
    function showAdminAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `admin-alert admin-alert-${type}`;
        alertDiv.innerHTML = `
            <p>${message}</p>
            <button class="close-alert">&times;</button>
        `;
        
        // Ajoute l'alerte au DOM
        const container = document.querySelector('.admin-container');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            
            // Bouton de fermeture
            alertDiv.querySelector('.close-alert').addEventListener('click', () => {
                alertDiv.remove();
            });
            
            // Disparition automatique après 5 secondes
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                setTimeout(() => {
                    alertDiv.remove();
                }, 300);
            }, 5000);
        }
    }
    
    // Gestion de la navigation avant/arrière
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab') || 'welcome';
        
        // Met à jour l'affichage selon l'onglet
        document.querySelectorAll('.admin-content > div').forEach(content => {
            content.style.display = 'none';
        });
        
        const activeContent = document.querySelector(`.admin-content > .${tab}-management, 
                                                   .admin-content > .${tab}-form,
                                                   .admin-content > .welcome-message`);
        if (activeContent) {
            activeContent.style.display = 'block';
        }
        
        // Met à jour les boutons actifs
        document.querySelectorAll('.menu-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('href').includes(`tab=${tab}`)) {
                btn.classList.add('active');
            }
        });
    });
});