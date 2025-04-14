document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
        
        const inputs = searchForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', performSearch);
        });
    }
});

function performSearch() {
    const searchForm = document.getElementById('search-form');
    const formData = new FormData(searchForm);
    const params = new URLSearchParams();
    
    for (const [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    
    fetch(`/search?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateSearchResults(data);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function updateSearchResults(spots) {
    const spotsList = document.getElementById('spots-list');
    
    if (!spotsList) return;
    
    if (spots.length === 0) {
        spotsList.innerHTML = '<p class="no-results">Aucun spot WiFi trouvé avec ces critères.</p>';
        return;
    }
    
    let html = '';
    
    spots.forEach(spot => {
        html += `
            <div class="spot-card">
                <div class="spot-info">
                    <h2>${spot.site_name}</h2>
                    <p class="address">${spot.address}, ${spot.postal_code}</p>
                    <p class="arrondissement">Arrondissement ${spot.arrondissement}</p>
                    <p class="status ${spot.status.toLowerCase().replace(/ /g, '-')}">
                        ${spot.status}
                    </p>
                    <p class="bornes">${spot.num_bornes} borne(s)</p>
                </div>
                
                <div class="spot-actions">
                    <a href="/spot/${spot.id}" class="btn-details">Voir détails</a>
                    ${isUserLoggedIn() ? `
                        <button class="btn-favorite ${spot.isFavorite ? 'active' : ''}" 
                                data-spot-id="${spot.id}">
                            ♥
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    });
    
    spotsList.innerHTML = html;
    initFavoriteButtons();
}

function isUserLoggedIn() {
    return document.querySelector('.user-greeting') !== null;
}