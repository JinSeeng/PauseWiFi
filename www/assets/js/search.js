// Récupère les paramètres de recherche depuis le formulaire
function getSearchParams() {
    const searchForm = document.getElementById('search-form');
    const formData = new FormData(searchForm);
    const params = new URLSearchParams();
    
    // Ajoute seulement les paramètres non vides
    for (const [key, value] of formData.entries()) {
        if (value && value !== 'all') {
            params.append(key, value);
        }
    }
    
    return params;
}

// Met à jour l'URL avec les paramètres de recherche
function updateUrlWithSearchParams() {
    const params = getSearchParams();
    const currentPage = document.body.dataset.currentPage || 'list';
    window.history.pushState({}, '', `/?page=${currentPage}&${params.toString()}`);
}

// Effectue la recherche via AJAX
function performSearch() {
    const params = getSearchParams();
    
    fetch(`/?page=search&${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Vérifie si l'utilisateur est admin
        const isAdmin = document.querySelector('.user-greeting')?.textContent.includes('Admin');
        
        // Met à jour les résultats et la carte
        if (typeof updateSearchResults === 'function') {
            updateSearchResults(data, isAdmin);
        }
        
        if (typeof updateMap === 'function') {
            updateMap(data, isAdmin);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Initialise le formulaire de recherche
function initSearchForm() {
    const searchForm = document.getElementById('search-form');
    
    if (searchForm) {
        // Remplit les champs avec les paramètres de l'URL
        const urlParams = new URLSearchParams(window.location.search);
        
        ['search', 'arrondissement', 'status', 'site_type'].forEach(param => {
            const element = searchForm.querySelector(`[name="${param}"]`);
            if (element && urlParams.has(param)) {
                element.value = urlParams.get(param);
            }
        });
        
        // Gestion de la soumission du formulaire
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            updateUrlWithSearchParams();
            performSearch();
        });
        
        // Recherche automatique lors du changement des champs
        const inputs = searchForm.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                updateUrlWithSearchParams();
                performSearch();
            });
        });
        
        // Effectue une recherche initiale si des paramètres existent
        if (urlParams.toString()) {
            performSearch();
        }
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const body = document.body;
    const page = new URLSearchParams(window.location.search).get('page') || 'list';
    body.dataset.currentPage = page;
    
    initSearchForm();
});