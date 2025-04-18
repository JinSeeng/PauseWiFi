document.addEventListener('DOMContentLoaded', function() {
    // Gestion déléguée des clics sur les favoris
    document.addEventListener('click', function(e) {
        const btnFavorite = e.target.closest('.btn-favorite');
        if (btnFavorite) {
            e.preventDefault();
            
            // Vérifie si l'utilisateur est connecté
            if (!document.querySelector('.user-greeting')) {
                window.location.href = '/?page=login';
                return;
            }
            
            handleFavoriteClick(btnFavorite);
        }
    });

    // Fonction pour supprimer un favori
    function removeFavorite(spotId) {
        const formData = new FormData();
        formData.append('spot_id', spotId);
        formData.append('action', 'remove'); // Demande explicite de suppression
    
        fetch('/actions/toggle-favorite.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.status === 401) {
                showLoginModal(); // Affiche une modal si non connecté
                return Promise.reject('login_required');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Animation de suppression...
            } else {
                showAlert('error', data.error || 'Erreur inconnue');
            }
        })
        .catch(error => {
            if (error !== 'login_required') {
                showAlert('error', 'Erreur réseau');
            }
        });
    }

    // Gère le clic sur un bouton favori
    function handleFavoriteClick(button) {
        const spotId = button.dataset.spotId;
        const formData = new FormData();
        formData.append('spot_id', spotId);

        button.disabled = true; // Désactive le bouton pendant la requête
        
        fetch('/actions/toggle-favorite.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/?page=login';
                }
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data.error) {
                // Met à jour l'apparence du bouton
                button.classList.toggle('active', data.is_favorite);
                button.innerHTML = data.is_favorite ? '♥' : '♡';
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        })
        .finally(() => {
            button.disabled = false; // Réactive le bouton
        });
    }
});