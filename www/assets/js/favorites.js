document.addEventListener('DOMContentLoaded', function() {
    // Déléguer les événements de favoris
    document.addEventListener('click', function(e) {
        const btnFavorite = e.target.closest('.btn-favorite');
        if (btnFavorite) {
            e.preventDefault();
            
            // Vérifier si l'utilisateur est connecté
            if (!document.querySelector('.user-greeting')) {
                window.location.href = '/?page=login';
                return;
            }
            
            handleFavoriteClick(btnFavorite);
        }
    });

    function removeFavorite(spotId) {
        const formData = new FormData();
        formData.append('spot_id', spotId);
        formData.append('action', 'remove'); // Explicitement demander une suppression
    
        fetch('/actions/toggle-favorite.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.status === 401) {
                showLoginModal(); // Afficher une modal au lieu de rediriger
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

    function handleFavoriteClick(button) {
        const spotId = button.dataset.spotId;
        const formData = new FormData();
        formData.append('spot_id', spotId);

        button.disabled = true;
        
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
                button.classList.toggle('active', data.is_favorite);
                // Feedback visuel
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
            button.disabled = false;
        });
    }
        
});